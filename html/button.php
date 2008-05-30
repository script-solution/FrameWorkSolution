<?php
/**
 * Contains the button-class
 *
 * @version			$Id: button.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for all buttons (submit, reset, ...)
 *
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_HTML_Button extends PLIB_HTML_FormElement
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
}
?>