<?php
/**
 * Contains the password-box-class
 *
 * @version			$Id: passwordbox.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a password-box:
 * <code>
 * 	<input type="password" name="..." />
 * </code>
 *
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_HTML_PasswordBox extends PLIB_HTML_TextBox
{
	/**
	 * Constructor
	 * 
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $default the default value
	 * @param mixed $cols the cols of the element
	 * @param mixed $maxlength the maximum length
	 */
	public function __construct($name,$id = null,$default = '',$cols = 15,$maxlength = null)
	{
		parent::__construct($name,$id,null,$default,$cols,$maxlength);
	}
	
	public function to_html()
	{
		$html = '<input type="password"'.$this->_get_default_attr_html();
		$html .= ' size="'.$this->get_cols().'" value="'.$this->get_used_value().'"';
		if($this->get_maxlength() !== null)
			$html .= ' maxlength="'.$this->get_maxlength().'"';
		$html .= ' />';
		return $html;
	}
	
	protected function _get_print_vars()
	{
		return array_merge(parent::_get_print_vars(),get_object_vars($this));
	}
}
?>