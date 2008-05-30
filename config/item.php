<?php
/**
 * Contains the config-item-interface
 *
 * @version			$Id: item.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all config-item-types
 *
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Config_Item
{
	/**
	 * Should return the data of this item
	 *
	 * @return PLIB_Config_Data the data of the item
	 */
	public function get_data();
	
	/**
	 * Should return the control for changing the value
	 * 
	 * @param PLIB_HTML_Formular $form the formular
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