<?php
/**
 * Contains the interface for the additional fields
 * 
 * @package			FrameWorkSolution
 * @subpackage	addfield
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
 * The interface for all additional fields
 * 
 * @package			FrameWorkSolution
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_AddField_Field
{
	/**
	 * @return FWS_AddField_Data the data-object of this field
	 */
	public function get_data();
	
	/**
	 * @return string the title of this field
	 */
	public function get_title();
	
	/**
	 * Should read the value of this field from post and return it
	 *
	 * @param mixed $default the default value
	 * @return mixed the entered value
	 */
	public function get_value_from_formular($default = null);
	
	/**
	 * Should build the control for the formular which allows the user to edit this field.
	 *
	 * @param FWS_HTML_Formular $formular the formular that should be used
	 * @param mixed $value the default value
	 * @return string the HTML-code for the formular-control
	 */
	public function get_formular_field($formular,$value);
	
	/**
	 * Should build the HTML-code for the given value that should be displayed
	 * 
	 * @param mixed $value the value of this field
	 * @param string $link_class the CSS-class of the links
	 * @param string $text_class the CSS-class for the text
	 * @param int $limit if > 0 the max. number of visible characters
	 * @return string the HTML-code to display
	 */
	public function get_display($value,$link_class,$text_class,$limit = 0);
	
	/**
	 * Should return a default value for this field
	 *
	 * @return mixed the default value
	 */
	public function get_default_value();
	
	/**
	 * Should check if the given value is valid for this field. Returns the error-message
	 * if it is not or an empty string. The possible return-types are:
	 * <ul>
	 * 	<li>value_missing</li>
	 * 	<li>value_invalid</li>
	 * </ul>
	 *
	 * @param mixed $value the entered value (from {@link get_value_from_formular()})
	 * @return string the error-message or an empty string
	 */
	public function is_valid_value($value);
	
	/**
	 * Checks if the given value should be considered as empty for this field
	 *
	 * @param mixed $value the entered value
	 * @return boolean true if it is empty
	 */
	public function is_empty($value);
	
	/**
	 * Should build the value which should be stored from the given value.
	 *
	 * @param mixed $value the entered value
	 * @return string the value to store
	 */
	public function get_value_to_store($value);
}
?>