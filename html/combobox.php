<?php
/**
 * Contains the combobox-class
 *
 * @version			$Id: combobox.php 736 2008-05-23 18:24:22Z nasmussen $
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a combobox:
 * <code>
 * 	<select name="..."><option value="...">...</option>...</select>
 * </code>
 *
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_HTML_ComboBox extends PLIB_HTML_List
{
	/**
	 * The size of the combobox
	 *
	 * @var int
	 */
	protected $_size;
	
	/**
	 * Does the combobox allow multi-selections?
	 *
	 * @var boolean
	 */
	protected $_multiple;
	
	/**
	 * Custom styles for options:
	 * <code>
	 * 	array(
	 * 		<key1> => array(<name1> => <value1>,...),
	 * 		...
	 * 	)
	 * </code>
	 *
	 * @var array
	 */
	protected $_option_styles = array();
	
	/**
	 * Constructor
	 *
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 * @param int $size the number of entries to display without scrolling
	 * @param boolean $multiple are multi-selections possible?
	 */
	public function __construct($name,$id = null,$value = null,$default = '',$size = 1,
		$multiple = false)
	{
		parent::__construct($name,$id,$value,$default);
		
		$this->set_size($size);
		$this->set_multiple($multiple);
	}
	
	/**
	 * Sets the given CSS-attribute for the option with given key to the given value
	 *
	 * @param mixed $option the key of the option
	 * @param string $name the name of the CSS-attribute
	 * @param mixed $value the value of the CSS-attribute
	 */
	public final function set_option_style($option,$name,$value)
	{
		if(!isset($this->_option_styles[$option]))
			$this->_option_styles[$option] = array();
		$this->_option_styles[$option][$name] = $value;
	}

	/**
	 * @return boolean wether multi-selections are possible
	 */
	public final function is_multiple()
	{
		return $this->_multiple;
	}

	/**
	 * Sets wether multi-selections are possible
	 * 
	 * @param boolean $multiple the new value
	 */
	public final function set_multiple($multiple)
	{
		$this->_multiple = (bool)$multiple;
	}

	/**
	 * @return int the size of the combobox
	 */
	public final function get_size()
	{
		return $this->_size;
	}

	/**
	 * Sets the size of the combobox
	 * 
	 * @param int $size the new value
	 */
	public final function set_size($size)
	{
		if(!PLIB_Helper::is_integer($size) || $size <= 0)
			PLIB_Helper::def_error('intgt0','size',$size);
		
		$this->_size = $size;
	}
	
	public function to_html()
	{
		$html = '<select'.$this->_get_default_attr_html();
		if($this->_multiple)
		{
			$html .= ' multiple="multiple"';
			$html .= ' size="'.$this->_size.'"';
		}
		$html .= '>'."\n";
		$selected = $this->get_used_value();
		foreach($this->get_options() as $k => $v)
		{
			$html .= '	<option value="'.$k.'"';
			if($this->_multiple && in_array($k,$selected))
				$html .= ' selected="selected"';
			else if(!$this->_multiple && $k == $selected)
				$html .= ' selected="selected"';
			if(isset($this->_option_styles[$k]))
			{
				$html .= ' style="';
				foreach($this->_option_styles[$k] as $skey => $sval)
					$html .= $skey.': '.$sval.';';
				$html .= '"';
			}
			$html .= '>'.$v.'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>