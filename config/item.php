<?php
/**
 * Contains the config-item-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	config
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
 * The interface for all config-item-types
 *
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Config_Item
{
	/**
	 * Should return the data of this item
	 *
	 * @return FWS_Config_Data the data of the item
	 */
	public function get_data();
	
	/**
	 * Should return the control for changing the value
	 * 
	 * @param FWS_HTML_Formular $form the formular
	 * @return string the HTML-code for the control
	 */
	public function get_control($form);
	
	/**
	 * Should return wether the value has changed. That means if the stored value is different
	 * from the value read from POST.
	 *
	 * @return boolean true if it has changed
	 */
	public function has_changed();
	
	/**
	 * Should read the value from post and return the value to store in the database
	 * 
	 * @return mixed the value to store in the database
	 */
	public function get_value();
}
?>