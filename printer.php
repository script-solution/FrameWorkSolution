<?php
/**
 * Contains the printer-class
 * 
 * @package			FrameWorkSolution
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * This class can print objects, arrays and other types in a nice and readable way.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Printer extends FWS_Object
{
	/**
	 * Helper to store the layer. Note that we have to store it here instead of passing
	 * it by parameter because we call the get_dump() methods of the objects which
	 * would cause that we loose the layer.
	 *
	 * @var int
	 */
	private static $_layer = 1;
	
	/**
	 * A stack to prevent recursion
	 *
	 * @var array
	 */
	private static $_stack = array();
	
	/**
	 * Convenience-method for:
	 * <code>
	 * $p = new FWS_Printer($var);
	 * $p->set_use_html($use_html);
	 * $p->set_multiline($ml);
	 * $p->set_dump_only($dump_only);
	 * return (string)$p;
	 * </code>
	 *
	 * @param mixed $var the value
	 * @param boolean $use_html do you want to use HTML? (-1 decide by SAPI)
	 * @param boolean $ml build a multiline string?
	 * @param boolean $dump_only wether only dump() should be used and not __toString()
	 * @return string the string representation
	 */
	public static function to_string($var,$use_html = -1,$ml = true,$dump_only = false)
	{
		$p = new FWS_Printer($var);
		if($use_html === -1)
			$p->set_use_html(php_sapi_name() != 'cli');
		else
			$p->set_use_html((bool)$use_html);
		$p->set_multiline($ml);
		$p->set_dump_only($dump_only);
		return (string)$p;
	}
	
	/**
	 * Wether HTML is used
	 *
	 * @var boolean
	 */
	private $_use_html = true;
	
	/**
	 * Wether multiple lines are created
	 *
	 * @var boolean
	 */
	private $_multiline = true;
	
	/**
	 * Wether just dump() instead of __toString() will be used
	 *
	 * @var boolean
	 */
	private $_dump_only = false;
	
	/**
	 * The variable to print
	 *
	 * @var mixed
	 */
	private $_var;
	
	/**
	 * Constructor
	 *
	 * @param mixed $var the stuff to print
	 */
	public function __construct($var)
	{
		parent::__construct();
		
		$this->_var = $var;
	}

	/**
	 * @return boolean wether just dump() instead of __toString() will be used
	 */
	public final function get_dump_only()
	{
		return $this->_dump_only;
	}

	/**
	 * Sets wether just dump() instead of __toString() will be used
	 * 
	 * @param boolean $val the new value
	 */
	public final function set_dump_only($val)
	{
		$this->_dump_only = $val ? true : false;
	}

	/**
	 * @return boolean wether multiple lines are created
	 */
	public final function get_multiline()
	{
		return $this->_multiline;
	}

	/**
	 * Sets wether multiple lines are created
	 * 
	 * @param boolean $val the new value
	 */
	public final function set_multiline($val)
	{
		$this->_multiline = $val ? true : false;
	}

	/**
	 * @return boolean wether HTML is used
	 */
	public final function get_use_html()
	{
		return $this->_use_html;
	}

	/**
	 * Sets wether HTML is used
	 * 
	 * @param boolean $val the new value
	 */
	public final function set_use_html($val)
	{
		$this->_use_html = $val ? true : false;
	}
	
	/**
	 * Builds the string-representation of the variable
	 *
	 * @return string the result
	 */
	public function __toString()
	{
		try
		{
			$str = $this->_build_string($this->_var);
			if($this->_use_html)
				$str = $this->_to_html($str);
		}
		catch(Exception $e)
		{
			$str = $e->__toString();
		}
		return $str;
	}
	
	/**
	 * Builds a string representation of <var>$var</var> recursivly
	 * 
	 * @param mixed $var the value
	 * @return string the string-representation
	 */
	private function _build_string($var)
	{
		$indent = '';
		if($this->_multiline)
		{
			for($i = 0;$i < self::$_layer;$i++)
				$indent .= "\t";
		}
		
		$str = '';
		if(is_array($var))
		{
			self::$_layer++;
			
			$str .= '{'.($this->_multiline ? "\n" : '');
			foreach($var as $k => $v)
			{
				$str .= $indent.htmlspecialchars($k).' = '.$this->_build_string($v);
				if($this->_multiline)
					$str .= "\n";
				else
					$str .= ';';
			}
			$str .= FWS_String::substr($indent,0,-1).'}';
			
			self::$_layer--;
		}
		else
		{
			$color = $this->_use_html ? $this->_get_type_color($var) : null;
			if(is_string($var) && $this->_use_html)
				$var = htmlspecialchars($var,ENT_QUOTES);
			
			if($var instanceof FWS_Object)
			{
				// detect recursion
				if(in_array($var->get_object_id(),self::$_stack))
					return '<span style="color: red;"><i>*RECURSION*</i></span>';
				
				array_push(self::$_stack,$var->get_object_id());
				if(!$this->_dump_only && method_exists($var,'__ToString'))
					$str .= $var->__ToString();
				else
					$str .= $var->get_dump($this->_use_html);
				array_pop(self::$_stack);
			}
			else if(is_object($var))
			{
				$classname = get_class($var);
				$str .= $classname.'[';
				$str .= $this->_build_string(get_object_vars($var));
				$str .= ']';
			}
			else if(!is_object($var) && $this->_use_html)
			{
				$str .= '<span style="color: '.$color.';">';
				if(is_bool($var))
					$str .= $var ? 'true' : 'false';
				else if($var === null)
					$str .= 'NULL';
				else
					$str .= $var;
				$str .= '</span>';
			}
			else
				$str .= @strval($var);
		}
		
		return $str;
	}

	/**
	 * Returns the color for the given variable
	 *
	 * @param mixed $var the variable
	 * @return string the color for the variable
	 */
	protected function _get_type_color($var)
	{
		if($var === null)
			return '#000; font-style: italic';
		
		$type = gettype($var);
		switch($type)
		{
			case 'string':
				return '#FF00FF';
			case 'boolean':
				return '#FF0000';
			case 'integer':
				return '#000080';
			case 'double':
				return '#800000';
			case 'resource':
				return '#008000';
			default:
				return '#000000';
		}
	}
	
	/**
	 * Converts the given string to HTML
	 *
	 * @param string $str the input-string
	 * @return string the result
	 */
	protected function _to_html($str)
	{
		$str = str_replace("\n",'<br />',$str);
		$str = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$str);
		if(self::$_layer == 1)
		{
			$inline = !$this->_multiline ? 'display: inline; ' : '';
			$str = '<div style="'.$inline.'font-family: monospace; font-size: 11px;">'.$str.'</div>';
		}
		return $str;
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>