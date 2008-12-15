<?php
/**
 * Contains the css-ruleset-block-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A ruleset-block. That means for example:
 * <pre>
 * p a:hover {
 *   color: red;
 *   margin: 1px;
 * }
 * </pre>
 *
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Block_Ruleset extends FWS_Object implements FWS_CSS_Block
{
	/**
   * Returns the default value for the given property
   *
   * @param string $propname the property-name
   * @return string the default-value
   */
	public static function get_def_prop_value($propname)
	{
		switch($propname)
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
	 * The name of this block
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * The media of this ruleset
	 *
	 * @var array
	 */
	private $_media;
	
	/**
	 * The selectors of this ruleset
	 *
	 * @var array
	 */
	private $_selectors;
	
	/**
	 * All properties of this ruleset:
	 * <code>array(<name> => <value>, ...)</code>
	 *
	 * @var array
	 */
	private $_properties;
	
	/**
	 * Constructor
	 *
	 * @param array $selectors all selectors for this ruleset
	 * @param array $properties an associative array with all properties
	 * @param mixed $media an array with media-types or null
	 */
	public function __construct($selectors,$properties = array(),$media = null)
	{
		parent::__construct();
		
		$this->set_selectors($selectors);
		$this->set_media($media);
		$this->set_properties($properties);
	}
	
	/**
	 * Returns the name of the ruleset. It will be determined by the media-types and selectors.
	 * Note that it may be not unique!
	 * 
	 * @return string the name
	 */
	public function get_name()
	{
		return ($this->_media === null ? '' : implode(',',$this->_media).':')
			.implode(',',$this->_selectors);
	}
	
	/**
	 * @return mixed an array with the media-types (null if no media set)
	 */
	public function get_media()
	{
		return $this->_media;
	}
	
	/**
	 * Sets the media
	 *
	 * @param array $media the media-types (null = no media set)
	 */
	public function set_media($media)
	{
		if($media !== null && !is_array($media))
			FWS_Helper::def_error('array','media',$media);
		
		$this->_media = $media;
	}
	
	/**
	 * Returns the selector with given index
	 *
	 * @param int $index the index
	 * @return FWS_CSS_Selector the selector
	 */
	public function get_selector($index)
	{
		if(isset($this->_selectors[$index]))
			return $this->_selectors[$index];
		return null;
	}
	
	/**
	 * @return array a numeric array with all selectors
	 */
	public function get_selectors()
	{
		return $this->_selectors;
	}
	
	/**
	 * Collects all selectors and sub-selectors and returns them
	 *
	 * @return array the selectors
	 */
	public function get_all_selectors()
	{
		$sels = array();
		foreach($this->_selectors as $sel)
			$this->_add_selector($sels,$sel);
		return $sels;
	}
	
	/**
	 * Adds the given one and all "sub-selectors" to the given list
	 *
	 * @param array $sels the selector-list
	 * @param FWS_CSS_Selector $sel the selector
	 */
	private function _add_selector(&$sels,$sel)
	{
		$sels[] = $sel;
		if($sel instanceof FWS_CSS_Selector_Pseudo)
			$this->_add_selector($sels,$sel->get_selector());
		else if($sel instanceof FWS_CSS_Selector_Connector)
		{
			$this->_add_selector($sels,$sel->get_left_selector());
			$this->_add_selector($sels,$sel->get_right_selector());
		}
	}
	
	/**
	 * Sets the selectors for this ruleset
	 *
	 * @param array $selectors the new selectors
	 */
	public function set_selectors($selectors)
	{
		if(!is_array($selectors) || count($selectors) == 0)
			FWS_Helper::def_error('array>0','selectors',$selectors);
		
		$this->_selectors = $selectors;
	}
	
	/**
	 * Checks wether the given property exists
	 *
	 * @param string $name the name
	 * @return boolean true if so
	 */
	public function contains_property($name)
	{
		return isset($this->_properties[$name]);
	}
	
	/**
	 * Returns the value of the given property
	 *
	 * @param string $name the name
	 * @return mixed the value or false if not existing
	 */
	public function get_property($name)
	{
		if(!isset($this->_properties[$name]))
			return false;
		return $this->_properties[$name];
	}
	
	/**
	 * @return array all properties of this ruleset
	 */
	public function get_properties()
	{
		return $this->_properties;
	}
	
	/**
	 * Sets the property with given name to given value
	 *
	 * @param string $name the property-name
	 * @param mixed $value the value
	 */
	public function set_property($name,$value)
	{
		if(!preg_match('/^[a-z\-_][a-z\-_0-9]*$/i',$name))
			FWS_Helper::error('The name has to be an identifier! (got "'.$name.'")');
		
		$this->_properties[$name] = $value;
	}
	
	/**
	 * Sets the properties
	 *
	 * @param array $properties the new properties (an associative array)
	 */
	public function set_properties($properties)
	{
		if(!is_array($properties))
			FWS_Helper::def_error('array','properties',$properties);
		
		$this->_properties = $properties;
	}
	
	/**
	 * Removes the given property
	 *
	 * @param string $name the name
	 */
	public function remove_property($name)
	{
		unset($this->_properties[$name]);
	}
	
	/**
	 * Removes all properties
	 */
	public function clear_properties()
	{
		$this->_properties = array();
	}

	/**
	 * @see FWS_CSS_Block::get_type()
	 *
	 * @return int
	 */
	public function get_type()
	{
		return self::RULESET;
	}
	
	/**
	 * Builds the string-representation of this ruleset. This will be valid CSS
	 *
	 * @param string $indent the indent for the string
	 * @return string the CSS-code
	 */
	public function __toString($indent = '')
	{
		$str = $indent.implode(', ',$this->_selectors).' {'."\n";
		foreach($this->_properties as $name => $value)
			$str .= $indent."\t".$name.': '.$value.";\n";
		$str .= $indent.'}';
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