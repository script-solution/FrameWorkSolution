<?php
/**
 * Contains the config-item-enum class
 * 
 * @package			FrameWorkSolution
 * @subpackage	config.item
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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