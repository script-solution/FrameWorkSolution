<?php
/**
 * Contains the resetbutton-class
 *
 * @version			$Id: resetbutton.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents the reset-button:
 * <code>
 * 	<input type="reset" name="..." value="..." />
 * </code>
 *
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_HTML_ResetButton extends PLIB_HTML_Button
{
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
	
	public function to_html()
	{
		$html = '<input type="reset"'.$this->_get_default_attr_html();
		$html .= ' value="'.$this->get_used_value().'" />';
		return $html;
	}
	
	protected function _get_print_vars()
	{
		return array_merge(parent::_get_print_vars(),get_object_vars($this));
	}
}
?>