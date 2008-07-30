<?php
/**
 * Contains the config-item-int class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "int". That means the user will get a textbox (a single
 * line) to specify the value of the item and may only enter an integer.
 *
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Item_Int extends FWS_Config_Item_Line
{
	public function get_value()
	{
		$input = FWS_Props::get()->input();

		return (int)$input->get_var($this->_data->get_name(),'post',FWS_Input::INTEGER);
	}
}
?>