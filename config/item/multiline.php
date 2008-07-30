<?php
/**
 * Contains the config-item-multiline class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "multiline". That means the user will get a textarea
 * (multiple lines) to specify the value of the item.
 *
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Item_MultiLine extends FWS_Config_Item_Default
{
	public function get_control($form)
	{
		$props = $this->_data->get_properties();
		$str = $form->get_textarea(
			$this->_data->get_name(),$this->_data->get_value(),$props['width'],$props['height']
		);
		$str .= $this->get_suffix();
		return $str;
	}

	public function get_value()
	{
		$input = FWS_Props::get()->input();

		return trim($input->get_var($this->_data->get_name(),'post',FWS_Input::STRING));
	}
}
?>