<?php
/**
 * Contains the config-item-yesno class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "yesno". That means you may either choose "yes" or "no".
 *
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Item_YesNo extends FWS_Config_Item_Default
{
	public function get_control($form)
	{
		$str = $form->get_radio_yesno($this->_data->get_name(),$this->_data->get_value());
		$str .= $this->get_suffix();
		return $str;
	}

	public function get_value()
	{
		$input = FWS_Props::get()->input();

		return $input->get_var($this->_data->get_name(),'post',FWS_Input::INT_BOOL);
	}
}
?>