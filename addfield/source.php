<?php
/**
 * Contains the addfield-source-interface
 *
 * @version 		$Id$
 * @package			PHPLib
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The source-interface for the additional-fields. This allows you to load the additional
 * fields from different sources.
 *
 * @package			PHPLib
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_AddField_Source
{
	/**
	 * Should load all additional fields and return them. This fields have to be sorted
	 * by the sort-key!
	 *
	 * @return array an array of {@link PLIB_AddField_Data} objects
	 */
	public function get_fields();
	
	/**
	 * Should return the corresponding field-object for the given data
	 *
	 * @param PLIB_AddField_Data $data the data-object
	 * @return PLIB_AddField_Field the field-object for that data depending on the type
	 */
	public function get_field($data);
}
?>