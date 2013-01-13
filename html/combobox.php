<?php
/**
 * Contains the combobox-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	html
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
 * Represents a combobox:
 * <code>
 * 	<select name="..."><option value="...">...</option>...</select>
 * </code>
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_ComboBox extends FWS_HTML_List
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
		if(!FWS_Helper::is_integer($size) || $size <= 0)
			FWS_Helper::def_error('intgt0','size',$size);
		
		$this->_size = $size;
	}
	
	public function to_html()
	{
		$html = '<select'.$this->get_default_attr_html();
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
			if($this->_multiple && is_array($selected) && in_array($k,$selected))
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
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>