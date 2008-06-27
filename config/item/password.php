<?php
/**
 * Contains the config-item-password class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "password". That means the user will get a password-box.
 *
 * @package			PHPLib
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Config_Item_Password extends PLIB_Config_Item_Default
{
	public function get_control($form)
	{
		$props = $this->_data->get_properties();
		$str = $form->get_passwordbox(
			$this->_data->get_name(),$this->_data->get_value(),$props['size'],$props['maxlen']
		);
		$str .= $this->_get_suffix();
		return $str;
	}

	public function get_value()
	{
		return $this->input->get_var($this->_data->get_name(),'post',PLIB_Input::STRING);
	}
}
?>