<?php
/**
 * Contains the config-item-int class
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