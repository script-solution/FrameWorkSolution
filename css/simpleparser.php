<?php
/**
 * Contains the simple-css-parser class
 * 
 * @version			$Id: simpleparser.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A simple parser for CSS-files which supports just the basic CSS syntax. The reason for that
 * is that the end-user should be able to edit the design and therefore everything should be simple
 * so that the user don't have to have much knowledge about CSS.
 * 
 * TODO Please don't use this class yet because it will be rewritten to support more of the CSS-
 * standard!
 * 
 * @package			PHPLib
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_CSS_SimpleParser extends PLIB_FullObject
{
	/**
	 * The lines of the CSS-file
	 *
	 * @var array
	 */
	private $_content = array();
	
	/**
	 * The array with all found CSS-classes and attributes
	 *
	 * @var array
	 */
	private $_parsed_content = array();
	
	/**
	 * Alle names of the classes in the CSS-file
	 *
	 * @var array
	 */
	private $_name_classes = array();
	
	/**
	 * The order of the elements. Will be used to keep the structure of the CSS-file
	 *
	 * @var array
	 */
	private $_order = array();
	
	/**
	 * The source-file
	 *
	 * @var string
	 */
	private $_source;
	
	/**
	 * The target-file
	 *
	 * @var string
	 */
	private $_target;
	
	/**
	 * Stores wether the content has been changed
	 *
	 * @var boolean
	 */
	private $_changed = false;
	
	/**
	 * Constructor
	 * 
	 * @param string $source the source-file
	 * @param string $target the source-file
	 */
	public function __construct($source,$target)
	{
		parent::__construct();
		
		$this->_source = $source;
		$this->_target = $target;

		$this->_content = file($this->_source);
		$this->_parse();
	}
	
	/**
	 * @return boolean wether something has changed
	 */
	public function has_changed()
	{
		return $this->_changed;
	}
	
	/**
   * returns the default value for the given attribute
   *
   * @param string $attribute the attribute-name
   * @return string the default-value
   */
	public function get_default_value($attribute)
	{
		switch($attribute)
		{
			case 'font-size':
				return '9pt';
			case 'text-decoration':
				return 'none';
			case 'font-weight':
				return 'normal';
			case 'font-style':
				return 'normal';
			case 'color':
				return '#FFFFFF';
			case 'background-color':
				return '#FFFFFF';
			default:
				return '';
		}
	}

	/**
   * returns the classes in the file
   *
   * @param array $skip an numeric array with the classes to skip
   * @return array all classes
   */
	public function get_classes($skip = null)
	{
		if($skip != null)
		{
			$ret = array();
			$len = count($this->_name_classes);
			for($i = 0;$i < $len;$i++)
			{
				if(!in_array($this->_name_classes[$i],$skip))
					$ret[] = $this->_name_classes[$i];
			}
			return $ret;
		}
		
		return $this->_name_classes;
	}

	/**
	 * returns all groups of classes in the file
	 *
	 * @param string $group_name the name of the group
	 * @return array an associative array with the classes
	 */
	public function get_group_classes($group_name)
	{
		$res = array();
		foreach(array_keys($this->_parsed_content) as $name)
		{
			if(preg_match('/^([a-z0-9]*\.)?'.preg_quote($group_name).':?[a-z]*$/i',$name))
				$res[$name] = $this->_parsed_content[$name];
		}
		return $res;
	}
	
	/**
	 * returns true if the attribute $attribute_name in $class_name exists
	 *
	 * @param string $class_name the name of the class
	 * @param string $attribute_name the name of the attribute
	 * @return boolean true if the attribute exists in the given class
	 */
	public function class_attribute_exists($class_name,$attribute_name)
	{
		return isset($this->_parsed_content[$class_name][$attribute_name]);
	}
	
	/**
	 * sets the content of the css-class $class_name to $value
	 *
	 * @param string $class_name the name of the class
	 * @param string $value the value to set
	 */
	public function set_class($class_name,$value)
	{
		if(isset($this->_parsed_content[$class_name]))
		{
			$old = isset($this->_parsed_content[$class_name]) ? $this->_parsed_content[$class_name] : null;
			$this->_parsed_content[$class_name] = $this->_parse_attributes($value);
			$this->_changed |= $old != $this->_parsed_content[$class_name];
		}
		else
		{
			$this->_parsed_content[$class_name] = array();
			$this->_parsed_content[$class_name] = $this->_parse_attributes($value);
			$this->_changed = true;
		}
	}
	
	/**
	 * sets the attribute $attribute_name in the css-class $class_name to $value
	 *
	 * @param string $class_name the name of the class
	 * @param string $attribute_name the name of the attribute
	 * @param string $value the new value
	 */
	public function set_class_attribute($class_name,$attribute_name,$value)
	{
		$old = isset($this->_parsed_content[$class_name][$attribute_name]) ?
			$this->_parsed_content[$class_name][$attribute_name] : null;
		$this->_parsed_content[$class_name][$attribute_name] = $value;
		$this->_changed |= $old != $this->_parsed_content[$class_name][$attribute_name];
	}
	
	/**
	 * if the css-class $class_name exists $class_name will be deleted
	 *
	 * @param string $class_name the name of the class to remove
	 * @return boolean true if successfull
	 */
	public function remove_class($class_name)
	{
		if(isset($this->_parsed_content[$class_name]))
		{
			unset($this->_parsed_content[$class_name]);
			$num = count($this->_order);
			for($i = 0;$i < $num;$i++)
			{
				if($this->_order[$i][1] == $class_name)
				{
					unset($this->_order[$i]);
					$this->_changed = true;
					return true;
				}
			}
			return false;
		}
		
		return false;
	}
	
	/**
	 * removes the attribute $attribute_name in $class_name if existing
	 *
	 * @param string $class_name the name of the class
	 * @param string $attribute_name the name of the attribute
	 * @return boolean true if successfull
	 */
	public function remove_class_attribute($class_name,$attribute_name)
	{
		if(isset($this->_parsed_content[$class_name][$attribute_name]))
		{
			unset($this->_parsed_content[$class_name][$attribute_name]);
			if(count($this->_parsed_content[$class_name]) == 0)
				$this->remove_class($class_name);
			
			$this->_changed = true;
			return true;
		}
		
		return false;
	}
	
	/**
	 * writes everything back to the given destination
	 *
	 * @return boolean true if successfull
	 */
	public function write()
	{
		$content = '';
		$num = count($this->_order);
		for($i = 0;$i < $num;$i++)
		{
			if(isset($this->_order[$i]))
			{
				if($this->_order[$i][0] == 0)
				{
					$content .= $this->_order[$i][1]."\n";
					if(PLIB_String::strpos($this->_order[$i][1],'*/') !== false)
						$content .= "\n";
				}
				else
				{
					$content .= $this->_order[$i][1].' {'."\n";
					foreach($this->_parsed_content[$this->_order[$i][1]] as $att_name => $att_value)
						$content .= '	'.$att_name.': '.$att_value.';'."\n";
					$content .= '}'."\n\n";
				}
			}
		}
		
		return PLIB_FileUtils::write($this->_target,trim($content));
	}
	
	/**
	 * adds the given class-name to the class-list
	 * 
	 * @param string the class-name
	 */
	private function _add_to_classes($string)
	{
		if(PLIB_String::strpos($string,'.') !== false)
		{
			$split = explode('.',$string);
			$split[1] = trim($split[1]);
			if(PLIB_String::strpos($split[1],':') !== false)
				$split[1] = strtok($split[1],':');
		}
		else
			$split[1] = $string;
		
		if(!in_array(trim($split[1]),$this->_name_classes))
			$this->_name_classes[] = trim($split[1]);
	}
	
	/**
	 * parses the file content
	 */
	private function _parse()
	{
		$in_string = false;
		$num = count($this->_content);
		for($i = 0;$i < $num;$i++)
		{
			if(trim($this->_content[$i]) == '')
				continue;
		
			if(!$in_string)
			{
				if(PLIB_String::strpos($this->_content[$i],'/*') !== false)
				{
					$this->_order[] = array(0,trim($this->_content[$i]));
					if(PLIB_String::strpos($this->_content[$i],'*/') === false)
						$in_string = true;
					continue;
				}
				
				$matches = array();
				preg_match('/([\.,a-z0-9-_:\s]+)\s*{/i',$this->_content[$i],$matches);
				if(isset($matches[1]))
				{
					$compl_classes[] = trim($matches[1]);
					$this->_order[] = array(1,trim($matches[1]));
					$this->_add_to_classes($matches[1]);
				}
			}
			else
			{
				$this->_order[] = array(0,trim($this->_content[$i]));
				if(PLIB_String::strpos($this->_content[$i],'*/') !== false)
					$in_string = false;
			}
		}
		
		$temp = array();
		preg_match_all('/{(.*?)}/si',implode("\n",$this->_content),$temp);
		$temp_num = count($temp[1]);
		for($i = 0;$i < $temp_num;$i++)
		{
			if(isset($compl_classes[$i]))
				$this->_parsed_content[$compl_classes[$i]] = $this->_parse_attributes(trim($temp[1][$i]));
		}
	}
	
	/**
	 * parses the attributes from the given string
	 * 
	 * @param string $string the attributes to parse
	 * @return an associative array of the form: array(&lt;name&gt; => &lt;value&gt;)
	 */
	private function _parse_attributes($string)
	{
		if(!PLIB_String::strpos($string,';'))
		{
			$split = explode(':',$string);
			if(count($split) >= 2)
				$matches = array(array($string),array($split[0]),array($split[1]));
			else
				$matches = array(array(),array(),array());
		}
		else
		{
			$matches = array();
			preg_match_all('/([a-z0-9_\-]+)\s*:\s*(.*?);/i',$string,$matches);
		}
		
		$num = count($matches[1]);
		$res = array();
		for($x = 0;$x < $num;$x++)
			$res[$matches[1][$x]] = $matches[2][$x];
		
		return $res;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>