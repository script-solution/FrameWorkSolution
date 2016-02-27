<?php
/**
 * Contains a CSS-attribute-container
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
 * Contains and manages CSS-attributes
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_HTML_FormElement extends FWS_Object
{
	/**
	 * The id of the element
	 *
	 * @var mixed
	 */
	private $_id;
	
	/**
	 * The name of the element
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * The CSS-class of the element
	 *
	 * @var string
	 */
	private $_class = null;
	
	/**
	 * The CSS-Attributes for the style-attribute
	 *
	 * @var array
	 */
	private $_style = array();
	
	/**
	 * The value of the element
	 *
	 * @var mixed
	 */
	private $_value;
	
	/**
	 * The default-value of the element
	 *
	 * @var mixed
	 */
	private $_default;
	
	/**
	 * Indicates whether the form-element is disabled
	 *
	 * @var boolean
	 */
	private $_disabled = false;
	
	/**
	 * An array of custom attributes for the HTML-tag
	 *
	 * @var array
	 */
	private $_custom = array();
	
	/**
	 * Constructor
	 * 
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 */
	public function __construct($name,$id = null,$value = null,$default = '')
	{
		parent::__construct();
		
		$this->set_name($name);
		$this->set_id($id !== null ? $id : $this->_generate_id($name));
		$this->set_value($value);
		$this->set_default($default);
	}

	/**
	 * @return mixed the id of the element (null if not set)
	 */
	public final function get_id()
	{
		return $this->_id;
	}

	/**
	 * Sets the id of the form-element. Null indiciates that the element has no id.
	 * 
	 * @param mixed $id the new value
	 */
	public final function set_id($id)
	{
		$this->_id = $id;
	}

	/**
	 * @return string the name of the form-element
	 */
	public final function get_name()
	{
		return $this->_name;
	}

	/**
	 * Sets the name of the form-element
	 * 
	 * @param string $name the new value
	 */
	public final function set_name($name)
	{
		if(!is_string($name))
			FWS_Helper::def_error('string','name',$name);
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		$this->_name = $name;
	}
	
	/**
	 * Note that <var>null</var> indicates that the element has no class.
	 * 
	 * @return string the CSS-class of the element
	 */
	public final function get_class()
	{
		return $this->_class;
	}
	
	/**
	 * Sets the CSS-class of the element. <var>null</var> indicates that the element has no class.
	 *
	 * @param string $class the new value
	 */
	public final function set_class($class)
	{
		if($class !== null && empty($class))
			FWS_Helper::def_error('notempty','class',$class);
		
		$this->_class = $class;
	}
	
	/**
	 * @return array the CSS-attributes of this element
	 */
	public final function get_style()
	{
		return $this->_style;
	}
	
	/**
	 * Sets the given attribute to given value
	 *
	 * @param string $name the name of the attribute
	 * @param mixed $value the value of the attribute
	 */
	public final function set_css_attribute($name,$value)
	{
		$this->_style[$name] = $value;
	}

	/**
	 * Note that this might be an array, too.
	 * Controls which allow multiple values will store them in an array.
	 * 
	 * @return mixed the current value of the form-element
	 */
	public final function get_value()
	{
		return $this->_value;
	}

	/**
	 * Sets the current value of the control. Note that this might be an array, too.
	 * Controls which allow multiple values will store them in an array.
	 * 
	 * @param mixed $value the new value
	 */
	public final function set_value($value)
	{
		$this->_value = $value;
	}

	/**
	 * Returns the value the form-element has when no value has been specified
	 * 
	 * @return mixed the default-value
	 */
	public final function get_default()
	{
		return $this->_default;
	}

	/**
	 * Sets the value the form-element has when no value has been specified
	 * 
	 * @param mixed $default the new value
	 */
	public final function set_default($default)
	{
		$this->_default = $default;
	}
	
	/**
	 * Returns the value that should be used. This will be the default one if no value
	 * has been specified or the value otherwise.
	 *
	 * @return mixed the value to use
	 */
	public function get_used_value()
	{
		if($this->_value === null)
			return $this->_default;
		
		return $this->_value;
	}
	
	/**
	 * @return boolean whether the form-element is disabled
	 */
	public final function is_disabled()
	{
		return $this->_disabled;
	}
	
	/**
	 * Sets whether the form-element is disabled
	 *
	 * @param boolean $disabled the new value
	 */
	public final function set_disabled($disabled)
	{
		$this->_disabled = (bool)$disabled;
	}
	
	/**
	 * Sets the custom attribute with given name to given value
	 *
	 * @param string $name the name
	 * @param string $value the value
	 */
	public final function set_custom_attribute($name,$value)
	{
		if(!is_scalar($name))
			FWS_Helper::def_error('scalar','name',$name);
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		if(!is_scalar($value))
			FWS_Helper::def_error('scalar','value',$value);
		
		$this->_custom[$name] = $value;
	}
	
	/**
	 * Removes the custom-attribute with given name
	 *
	 * @param string $name the name
	 */
	public final function remove_custom_attribute($name)
	{
		if(!is_scalar($name))
			FWS_Helper::def_error('scalar','name',$name);
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(isset($this->_custom[$name]))
			unset($this->_custom[$name]);
	}
	
	/**
	 * Generates the HTML-code for the default attributes. That are:
	 * id,name,class,style and disabled.
	 *
	 * @return string the HTML-code
	 */
	protected function get_default_attr_html()
	{
		$html = ' id="'.$this->_id.'" name="'.$this->_name.'"';
		if($this->_class !== null)
			$html .= ' class="'.$this->_class.'"';
		if(count($this->_style) > 0)
		{
			$html .= ' style="';
			foreach($this->_style as $k => $v)
				$html .= $k.': '.$v.';';
			$html .= '"';
		}
		if($this->_disabled)
			$html .= ' disabled="disabled"';
		foreach($this->_custom as $k => $v)
			$html .= ' '.$k.'="'.$v.'"';
		return $html;
	}
	
	/**
	 * Generates the HTML-code for this element
	 *
	 * @return string the HTML-code
	 */
	public abstract function to_html();
	
	/**
	 * Generates an id for the given name
	 * 
	 * @param string $name the name of the element
	 * @return string the id
	 */
	private function _generate_id($name)
	{
		if(FWS_String::starts_with($name,'.'))
			$name = 'p'.$name;
		return preg_replace('/[^a-z0-9_\-\.:]/i','_',$name);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>