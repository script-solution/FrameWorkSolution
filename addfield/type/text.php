<?php
/**
 * Contains the text-class for the additional-fields
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a multi-line-text as additional field
 *
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AddField_Type_Text extends PLIB_AddField_Type_Default
{
	protected function _get_formular_field($formular,$value)
	{
		return $formular->get_textarea('add_'.$this->_data->get_name(),$value,'90%',5);
	}
	
	protected function _is_valid_value($value)
	{
		$regex = $this->_data->get_validation();
		if($regex == '' || preg_match($regex,$value) == 1)
			return true;
		
		return false;
	}
}
?>