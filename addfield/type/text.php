<?php
/**
 * Contains the text-class for the additional-fields
 * 
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
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
 * Represents a multi-line-text as additional field
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_AddField_Type_Text extends FWS_AddField_Type_Default
{
	protected function get_formular_field_impl($formular,$value)
	{
		return $formular->get_textarea('add_'.$this->_data->get_name(),$value,'90%',5);
	}
	
	protected function is_valid_value_impl($value)
	{
		$regex = $this->_data->get_validation();
		if($regex == '' || preg_match($regex,$value) == 1)
			return true;
		
		return false;
	}
}
?>