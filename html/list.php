<?php
/**
 * Contains the list-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The abstract base-class for all list-elements (combobox, radiogroup, ...)
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_HTML_List extends FWS_HTML_FormElement
{
	/**
	 * The options of the list
	 *
	 * @var array
	 */
	private $_options = array();
	
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
		parent::__construct($name,$id,$value,$default);
	}
	
	/**
	 * @return array all options of the list
	 */
	public final function get_options()
	{
		return $this->_options;
	}
	
	/**
	 * Sets the options of the list to the given array.
	 *
	 * @param array $options a associative array of the form:
	 * 	<code>array(<option> => <title>)</code>
	 */
	public final function set_options($options)
	{
		if(!is_array($options))
			FWS_Helper::def_error('array','options',$options);
		
		$this->_options = array();
		foreach($options as $k => $v)
			$this->add_option($k,$v);
	}
	
	/**
	 * Adds the given option to the list
	 *
	 * @param mixed $option the value
	 * @param string $title the title of the value
	 */
	public final function add_option($option,$title)
	{
		if($option === null)
			FWS_Helper::def_error('notnull','option',$option);
		
		$this->_options[$option] = $title;
	}
	
	/**
	 * Removes the given option from the list
	 *
	 * @param mixed $option the option
	 */
	public final function remove_option($option)
	{
		if(isset($this->_options[$option]))
			unset($this->_options[$option]);
	}
}
?>