<?php
/**
 * Contains a CSS-attributes-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains and manages CSS-attributes
 *
 * @package			PHPLib
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_CSS_Attributes extends PLIB_Object
{
	/**
	 * The attribute-array
	 *
	 * @var array
	 */
	private $_attributes = array();
	
	/**
	 * Constructor
	 * 
	 * @param array $attr the attributes to set
	 */
	public function __construct($attrs = array())
	{
		parent::__construct();
		
		if(!is_array($attrs))
			PLIB_Helper::def_error('array','attrs',$attrs);
		
		foreach($attrs as $k => $v)
			$this->add_attribute($k,$v);
	}
	
	/**
	 * @return int the number of attributes
	 */
	public function get_attr_count()
	{
		return count($this->_attributes);
	}
	
	/**
	 * Returns the value of the given attribute
	 *
	 * @param string $name the name of the attribute
	 * @return mixed the value or null if not found
	 */
	public function get_attribute($name)
	{
		if(isset($this->_attributes[$name]))
			return $this->_attributes[$name];
		
		return null;
	}
	
	/**
	 * Sets the attribute with given name to given value
	 *
	 * @param string $name the name of the attribute
	 * @param mixed $value the value of the attribute
	 */
	public function set_attribute($name,$value)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
		$this->_attributes[$name] = $value;
	}
	
	/**
	 * Removes the attribute with given name from the container
	 *
	 * @param string $name the name of the attribute
	 */
	public function remove_attribute($name)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
		unset($this->_attributes[$name]);
	}
	
	/**
	 * Returns the string-representation of the CSS-attributes
	 *
	 * @return string the string-representation
	 */
	public function get_css()
	{
		$css = '';
		foreach($this->_attributes as $name => $value)
			$css .= $name.': '.$value.'; ';
		return rtrim($css);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>