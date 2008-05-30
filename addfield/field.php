<?php
/**
 * Contains the interface for the additional fields
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all additional fields
 * 
 * @package			PHPLib
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_AddField_Field
{
	/**
	 * @return PLIB_AddField_Data the data-object of this field
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
	 * @param PLIB_HTML_Formular the formular that should be used
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