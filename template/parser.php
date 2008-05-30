<?php
/**
 * Contains the template-parser
 *
 * @version			$Id: parser.php 775 2008-05-26 08:15:32Z nasmussen $
 * @package			PHPLib
 * @subpackage	template
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The template-parser which gets a string and converts the "template-code" to
 * valid PHP-code which can be executed.
 *
 * @package			PHPLib
 * @subpackage	template
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Template_Parser extends PLIB_FullObject
{
	/**
	 * The regular expression for an ident
	 *
	 * @var string
	 */
	private $_regex_ident;
	
	/**
	 * The regular expression for a comparation
	 *
	 * @var string
	 */
	private $_regex_cmp;
	
	/**
	 * The regular expression for a variable or array (without $)
	 *
	 * @var string
	 */
	private $_regex_var;
	
	/**
	 * The regular expression for a variable or array (without $) or a numeric value
	 *
	 * @var string
	 */
	private $_regex_numvar;
	
	/**
	 * The regular expression for a value. That may be <var>$this->_regex_var</var>,
	 * a number or a string
	 *
	 * @var string
	 */
	private $_regex_value;
	
	/**
	 * The regular expression for a concatination. That is a number or a
	 * abitrarly number of variables and strings
	 *
	 * @var string
	 */
	private $_regex_concat;
	
	/**
	 * The regular expression for a call of a method of an object.
	 *
	 * @var string
	 */
	private $_regex_objcall;
	
	/**
	 * The regular expression for a variable, number, string or object-call
	 *
	 * @var string
	 */
	private $_regex_objval;
	
	/**
	 * The regular expression for an math-operator (+,-,*,/)
	 *
	 * @var string
	 */
	private $_regex_math_operator;
	
	/**
	 * The regular expression for a math-operation: <operand> (<operator> <operand>)?
	 *
	 * @var string
	 */
	private $_regex_math;
	
	/**
	 * The instance of the template-handler
	 *
	 * @var PLIB_Template_Handler
	 */
	private $_tpl;
	
	/**
	 * The template-file to parse
	 *
	 * @var string
	 */
	private $_filename;
	
	/**
	 * constructor
	 *
	 * @param PLIB_Template_Handler $tpl the template-handler
	 * @param string $filename the template-file to parse
	 */
	public function __construct($tpl,$filename)
	{
		parent::__construct();
		
		if(!($tpl instanceof PLIB_Template_Handler))
			PLIB_Helper::def_error('instance','tpl','PLIB_Template_Handler',$tpl);
		if(empty($filename))
			PLIB_Helper::def_error('empty','filename',$filename);
		
		$this->_tpl = $tpl;
		$this->_filename = $filename;
		
		// define some regexs to parse the templates
		
		// the basic ones
		$this->_regex_ident = '(?:[A-Za-z_]\w*)';
		$this->_regex_cmp = '(?:==|===|!=|!==|>|<|>=|<=)';
		$regex_num = '(?:-?\d+(?:\.\d+)?)';
		$regex_dstr = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
		$regex_sstr = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
		
		// more advanced
		$regex_str = '(?:'.$regex_dstr.'|'.$regex_sstr.')';
		$regex_array = '(?::(?:'.$this->_regex_ident.'|'.$regex_num.')){0,3}';
		$regex_array_props = '(?:last|current|length)';
		$this->_regex_var = $this->_regex_ident.$regex_array.'(?:\.'.$regex_array_props.')?';
		$this->_regex_numvar = '(?:'.$this->_regex_var.'|'.$regex_num.')';
		$this->_regex_math = '(?:'.$this->_regex_numvar.'\s*(?:[\*+\/\-%]\s*'.$this->_regex_numvar.')?)';
		$this->_regex_value = '(?:'.$this->_regex_var.'|'.$this->_regex_math.'|'.$regex_str.')';
		$regex_inc = '(?:'.$this->_regex_var.'|'.$regex_str.')';
		$this->_regex_concat = '(?:'.$regex_num.'|(?:'.$regex_inc
			.'(?:\s*~\s*'.$regex_inc.')*))';
		$this->_regex_objcall = $this->_regex_ident.'\.'.$this->_regex_ident
			.'\((?:'.$this->_regex_concat.')?(?:,'.$this->_regex_concat.')*\)';
		$this->_regex_objval = '(?:'.$this->_regex_value.'|'.$this->_regex_objcall.')';
		$this->_regex_math_operator = '[\*+\/\-%]';
	}
	
	/**
	 * Compiles the given template and stores the result to the given cache-file
	 *
	 * @param string $template the template-file
	 * @param string $cache_file the file where to store the result to
	 * @param string $content the content to parse
	 * @return string the parsed result if an error occurred or an empty string
	 */
	public function compile_template($template,$cache_file,$content)
	{
		// parse conditions
		if($this->_tpl->get_conditions_enabled())
		{
			// {if (<operand> <operator> <operand> (&& | ||)?)* }
			$content = preg_replace(
				'/{IF\s+(.*?)}/ie',
				'"\nEOF;\n".$this->_parse_if(stripslashes(\'\\1\'))." {\n\\\$html .= <<<EOF\n"',
				$content
			);

			// else
			$content = preg_replace(
				'/{ELSE}/i',
				"\nEOF;\n".'} else {'."\n".'$html .= <<<EOF'."\n",
				$content
			);

			// endif
			$content = preg_replace(
				'/{ENDIF}/i',
				"\nEOF;\n}\n".'$html .= <<<EOF'."\n",
				$content
			);
		}

		if($this->_tpl->get_loops_enabled())
		{
			// {loop <item> as <key> => <value>}
			$content = preg_replace(
				'/{LOOP\s+('.$this->_regex_var.')\s+as\s+(?:('.$this->_regex_ident.')\s*=>\s*)?'
					.'('.$this->_regex_ident.')}/ie',
				'"\nEOF;\n".$this->_parse_loop(true,stripslashes(\'\\1\'),stripslashes(\'\\2\'),'
					.'stripslashes(\'\\3\'))."\n\\\$html .= <<<EOF\n"',
				$content
			);
			
			// {loopbw <item> as <key> => <value>}
			$content = preg_replace(
				'/{LOOPBW\s+('.$this->_regex_var.')\s*as\s*(?:('.$this->_regex_ident.')\s*=>\s*)?'
					.'('.$this->_regex_ident.')}/ie',
				'"\nEOF;\n".$this->_parse_loop(false,stripslashes(\'\\1\'),stripslashes(\'\\2\'),'
					.'stripslashes(\'\\3\'))."\n\\\$html .= <<<EOF\n"',
				$content
			);
			
			// {loop x in 1..limit} / {loop x in start..end} / ...
			$content = preg_replace(
				'/{LOOP\s+('.$this->_regex_ident.')\s+in\s+('.$this->_regex_numvar.')\s*\.\.\s*'
					.'('.$this->_regex_numvar.')\s*}/ie',
				'"\nEOF;\n".$this->_parse_custom_loop(stripslashes(\'\\1\'),stripslashes(\'\\2\'),'
					.'stripslashes(\'\\3\'))."\n\\\$html .= <<<EOF\n"',
				$content
			);

			// end-foreach
			$content = preg_replace(
				'/{ENDLOOP}/i',
				"\nEOF;\n}\n".'$html .= <<<EOF'."\n",
				$content
			);
		}
		
		// {* ..comment.. *}
		$content = preg_replace(
			'/{\*.+?\*}/s',
			'',
			$content
		);
		
		// {set var=value}
		$content = preg_replace(
			'/{SET\s+('.$this->_regex_ident.')\s*=\s*('.$this->_regex_concat.')}/ie',
			'"\nEOF;\n".$this->_parse_var(stripslashes(\'\\1\')).\' = \''
				.'.$this->_parse_concat(stripslashes(\'\\2\')).";\n"'
				.'.\'$html .= <<<EOF\'."\n"',
			$content
		);
		
		// {arrayName(:entry)*}
		// {object.func(<param1>[,<param2>,...])}
		if($this->_tpl->get_method_calls_enabled())
			$var_regex = '/{('.$this->_regex_math.'|'.$this->_regex_objcall.')}/ie';
		else
			$var_regex = '/{'.$this->_regex_math.'}/ie';
		$content = preg_replace(
			$var_regex,
			'"\nEOF;\n".\'$html .= \'.$this->_parse_objvar(stripslashes(\'\\1\')).";\n"'
				.'.\'$html .= <<<EOF\'."\n"',
			$content
		);
		
		if($this->_tpl->get_includes_enabled())
		{
			// {include "file.htm"}
			// {include "folder"~var~"file.htm"}
			// ...
			$content = preg_replace(
				'/{INCLUDE\s+('.$this->_regex_concat.')}/ie',
				'"\nEOF;\n".\'$html .= $base->tpl->parse_template(\''
				.'.$this->_parse_concat(stripslashes(\'\\1\')).",false);\n".'
				.'\'$html .= <<<EOF\'."\n"',
				$content
			);
		}

		// build php-file
		$result = '<?php'."\n"
		 .'function '.$this->_tpl->get_function_name($template).'($base) {'."\n"
		 .'$tplvars = &$base->tpl->get_variables(\''.$template.'\');'."\n";
		$result .= '$html = "";'."\n"
		 .'$html .=<<<EOF'."\n"
		 .$content."\n".'EOF;'."\n"
		 .'return $html;'."\n".'}'."\n"
		 .'?>';

		// write to file
		$written = PLIB_FileUtils::write($cache_file,$result);
		if($written > 0)
			return '';
		
		return $result;
	}
	
	/**
	 * Parses a custom-loop-statement. The methods gets the name of the loop-variable and the
	 * start and end of the loop.
	 * A few examples:
	 * {loop x in 1..10}
	 * {loop y in start..array:end}
	 * {loop y in 1..end}
	 * ...
	 *
	 * @param string $name the name of the loop-variable
	 * @param int $start the start-value
	 * @param int $end the end-value
	 * @return unknown
	 */
	private function _parse_custom_loop($name,$start,$end)
	{
		$res = '';
		$s = $this->_parse_value($start);
		$e = $this->_parse_value($end);
		$lc = '$tplvars[\''.$name.'\']';
		$res .= 'for($x = 0,'.$lc.' = '.$s.';$x == 0 || '.$lc.' != '.$e.';$x++)'."\n";
		$res .= '{'."\n";
		$res .= $lc.' += $x > 0 ? '.$lc.' < '.$e.' ? 1 : -1 : 0;'."\n";
		return $res;
	}
	
	/**
	 * Parses the loop-statement. The method gets the chosen item, the key and the value for
	 * the foreach-loop and transforms them to PHP-code.
	 * A few examples:
	 * {loop item as key => value}
	 * {loop array:foo as value}
	 * {loop array:sub:subsub as k => v}
	 * ...
	 * 
	 * @param boolean $forward wether we loop forward
	 * @param string $item the item of the foreach-loop
	 * @param string $key the optional key for the loop
	 * @param string $value the value for the loop
	 * @return string the PHP-code
	 */
	private function _parse_loop($forward,$item,$key,$value)
	{
		$res = '';
		if($forward)
		{
			$res .= '$'.$this->_get_loop_counter_name($item).' = -1;'."\n";
			$res .= 'foreach('.$this->_parse_var($item).' as ';
			if($key)
				$res .= '$tplvars[\''.$key.'\'] => ';
			$res .= '$tplvars[\''.$value.'\']) {'."\n";
			$res .= '$'.$this->_get_loop_counter_name($item).'++;'."\n";
		}
		else
		{
			$lc = $this->_get_loop_counter_name($item);
			$res .= '$'.$lc.'_array = '.$this->_parse_var($item).';'."\n";
			$res .= '$'.$lc.'_keys = array_keys($'.$lc.'_array);'."\n";
			$res .= 'for($'.$lc.' = count($'.$lc.'_array) - 1;$'.$lc.' >= 0;$'.$lc.'--) {'."\n";
			if($key)
				$res .= '$tplvars[\''.$key.'\'] = $'.$lc.'_keys[$'.$lc.'];'."\n";
			$res .= '$tplvars[\''.$value.'\'] = $'.$lc.'_array[$'.$lc.'_keys[$'.$lc.']];'."\n";
		}
		return $res;
	}
	
	/**
	 * Parses an if-condition. It may contain multiple compares or boolean values concatenated
	 * by '&&' or '||'.
	 * {IF var == 2}
	 * {IF var != bla && true}
	 * {IF var &gt; 2 && 1 == 2}
	 * {IF var &lt; 2.0 || 1 || 2 && 4}
	 * ..
	 * 
	 * @param string $op1 the first operand
	 * @param string $cmp the optional operator
	 * @param string $op2 the optional second operand
	 * @return string the PHP-code
	 */
	private function _parse_if($condition)
	{
		$res = 'if(';
		
		$regex = '/^(?P<obr>\(*)(?P<op1>'.$this->_regex_objval.')(?:\s*(?P<cmp>'
			.$this->_regex_cmp.')\s*(?P<op2>'.$this->_regex_objval.'))?(?P<cbr>\)*)$/i';
		
		$parts = preg_split('/\s*(&&|\|\|)\s*/',$condition,0,PREG_SPLIT_DELIM_CAPTURE);
		foreach($parts as $part)
		{
			$tpart = trim($part);
			$matches = array();
			if($tpart == '||' || $tpart == '&&')
				$res .= ' '.$tpart.' ';
			else if(preg_match($regex,$tpart,$matches))
			{
				if(isset($matches['obr']))
					$res .= $matches['obr'];
				
				if(isset($matches['cmp']))
					$res .= $this->_parse_cmp($matches['op1'],$matches['cmp'],$matches['op2']);
				else
					$res .= $this->_parse_cmp($matches['op1']);
				
				if(isset($matches['cbr']))
					$res .= $matches['cbr'];
			}
			else
				PLIB_Helper::error('Invalid condition-part "'.$part.'"!');
		}
		
		$res .= ')';
		return $res;
	}
	
	/**
	 * Parses a mathematical operation. For example:
	 * var1 + var2
	 * var + 1
	 * 2 + var
	 * 3 * 1
	 * 4 / var
	 * 1 - 1
	 * 3 % 1
	 * ...
	 *
	 * @param string $op1 the first operand
	 * @param string $operation the operation (+,-,*,/)
	 * @param string $op2 the second operand
	 * @return string the result
	 */
	private function _parse_math_op($op1,$operation,$op2)
	{
		$dummy = false;
		$res = $this->_parse_value($op1,$dummy);
		$res .= ' '.$operation.' ';
		$res .= $this->_parse_value($op2,$dummy);
		return $res;
	}
	
	/**
	 * Parses a condition. It gets either two operands and one operator or just one operand.
	 * A few examples:
	 * var == 2
	 * var != bla
	 * var &gt; 2
	 * var &lt; 2.0
	 * foo:bar:foo == -2
	 * 2 &gt; 4
	 * "abc" == "def"
	 * 'haha' == 2
	 * var.test()
	 * 4 &gt; var.test(2,4,6)
	 * var.test("aaa",abc:def,2) == 2
	 * 
	 * @param string $op1 the first operand
	 * @param string $cmp the optional operator
	 * @param string $op2 the optional second operand
	 * @return string the PHP-code
	 */
	private function _parse_cmp($op1,$cmp = '',$op2 = '')
	{
		$pop1 = $this->_parse_objvar($op1);
		$res = '(';
		
		if($pop1[0] == '$' && strpbrk($pop1,'+-*/%') === false &&
				$pop1[PLIB_String::strlen($pop1) - 1] != ')')
			$res .= 'isset('.$pop1.') && ';
		
		if($cmp != '' && $op2 != '')
		{
			$pop2 = $this->_parse_objvar($op2);
			if($pop2[0] == '$' && strpbrk($pop2,'+-*/%') === false &&
					$pop2[PLIB_String::strlen($pop2) - 1] != ')')
				$res .= 'isset('.$pop2.') && ';
		}
		
		$res .= $pop1;
		
		if($cmp != '' && $op2 != '')
			$res .= ' '.$cmp.' '.$pop2;
		$res .= ')';
		
		return $res;
	}
	
	/**
	 * Parses an method-call or a value.
	 * A few examples:
	 * var.test()
	 * var.test("aaa",abc:def,2,"blub"~test~"bla")
	 * var.test(2,4,6)
	 * 
	 * For the value examples see _parse_value()
	 * 
	 * @param string $value the value to parse
	 * @return string the PHP-code
	 */
	private function _parse_objvar($value)
	{
		$value = trim($value);
		if($this->_tpl->get_method_calls_enabled() &&
			preg_match('/^'.$this->_regex_objcall.'$/',$value))
		{
			$dot = PLIB_String::strpos($value,'.');
			$var = PLIB_String::substr($value,0,$dot);
			$other = PLIB_String::substr($value,$dot + 1);
			$bracket = PLIB_String::strpos($other,'(');
			$func = PLIB_String::substr($other,0,$bracket);
			$arguments = PLIB_String::substr($other,$bracket + 1,-1);
			$arguments = trim($arguments);
			
			$res = '($base->tpl->check_allowed_method(\''.$var.'\',\''.$func.'\')?';
			$res .= '$tplvars[\''.$var.'\']->'.$func.'(';
			$matches = array();
			preg_match_all('/(?P<con>'.$this->_regex_concat.'),?/',$arguments,$matches);
			if(isset($matches['con']) && is_array($matches['con']))
			{
				$i = 0;
				$len = count($matches['con']);
				foreach($matches['con'] as $arg)
				{
					$res .= $this->_parse_concat($arg);
					if($i++ < $len - 1)
						$res .= ',';
				}
			}
			$res .= '):\'\')';
		}
		else
		{
			$dummy = false;
			$res = $this->_parse_value($value,$dummy);
		}
		
		return $res;
	}
	
	/**
	 * Parses a concatenation of strings and/or variables.
	 * A few examples:
	 * "file.php"
	 * variable ~ "file.php"
	 * "abc/" ~ folder ~ "/file.php"
	 * "bla" ~ 1 ~ "abc" ~ var
	 * 
	 * @param string $value the $value to parse
	 * @return string the php-concatenation-string
	 */
	private function _parse_concat($value)
	{
		$parts = explode('~',$value);
		$res = '';
		$i = 0;
		$len = count($parts);
		foreach($parts as $p)
		{
			$p = trim($p);
			if(preg_match('/^'.$this->_regex_value.'$/',$p))
				$res .= $this->_parse_value($p);
			else
				$res .= $p;
			
			if($i++ < $len - 1)
				$res .= '.';
		}
		
		return $res;
	}

	/**
	 * Parses a value.
	 * A few examples:
	 * "string"
	 * 123
	 * 12.5
	 * -1.2
	 * 
	 * For the variable-examples see _parse_var().
	 * 
	 * @param string $value the value to parse
	 * @return string the PHP-code
	 */
	private function _parse_value($value)
	{
		$res = '';
		$value = trim($value);
		
		$matches = array();
		if($value == 'false' || $value == 'true' || $value == 'null')
			$res = $value;
		else if(preg_match('/^('.$this->_regex_numvar.')\s*('.$this->_regex_math_operator.')'
				.'\s*('.$this->_regex_numvar.')$/i',$value,$matches))
		{
			$res = $this->_parse_value($matches[1]);
			$res .= ' '.$matches[2].' ';
			$res .= $this->_parse_value($matches[3]);
		}
		else if(preg_match('/^'.$this->_regex_var.'$/',$value))
		{
			$dotpos = strrpos($value,'.');
			if($dotpos === false)
				$res .= $this->_parse_var($value);
			else
			{
				$varname = PLIB_String::substr($value,0,$dotpos);
				$prop = PLIB_String::substr($value,$dotpos + 1);
				if($prop == 'length')
					$res .= 'count('.$this->_parse_var($varname).')';
				else if($prop == 'last')
					$res .= 'count('.$this->_parse_var($varname).') - 1';
				else if($prop == 'current')
					$res .= '$'.$this->_get_loop_counter_name($varname);
			}
		}
		else
			$res = $value;
		
		return $res;
	}
	
	/**
	 * Parses a variable.
	 * A few examples:
	 * test
	 * test:sub
	 * test:sub:subsub
	 * 
	 * @param string $value the value to parse
	 * @return string the PHP-code
	 */
	private function _parse_var($value)
	{
		$parts = explode(':',$value);
		$res = '$tplvars';
		foreach($parts as $p)
			$res .= '[\''.$p.'\']';
		return $res;
	}
	
	/**
	 * Determines the name for the loop-counter
	 *
	 * @param string $item the item (without the ".xxx")
	 * @return string the name for the loop-counter
	 */
	private function _get_loop_counter_name($item)
	{
		return preg_replace('/[^a-z0-9_]/','_',$item).'_c';
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>