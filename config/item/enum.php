<?php
/**
 * Contains the config-item-enum class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "enum". That means the user will get a combobox
 * or radio-boxes to specify the value of the item.
 *
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Item_Enum extends FWS_Config_Item_Default
{
	public function get_control($form)
	{
		$props = $this->_data->get_properties();
		$options = $this->get_items($props);
		if($props['type'] == 'combo')
			$str = $form->get_combobox($this->_data->get_name(),$options,$this->_data->get_value());
		else
		{
			$str = $form->get_radio_boxes(
				$this->_data->get_name(),$options,$this->_data->get_value(),'&nbsp;'
			);
		}
		$str .= $this->get_suffix();
		return $str;
	}
	
	/**
	 * Builds the items for the combobox or radioboxes
	 *
	 * @param array $props the properties of the item
	 * @return array all items
	 */
	protected function get_items($props)
	{
		$locale = FWS_Props::get()->locale();

		$options = array();
		foreach($props as $k => $v)
		{
			if($k === 'type')
				continue;
			$options[$k] = $locale->lang($v,false);
		}
		return $options;
	}

	public function get_value()
	{
		$input = FWS_Props::get()->input();

		$options = $this->get_items($this->_data->get_properties());
		return $input->correct_var(
			$this->_data->get_name(),'post',FWS_Input::STRING,array_keys($options),key($options)
		);
	}
}
?>