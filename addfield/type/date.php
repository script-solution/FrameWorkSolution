<?php
/**
 * Contains the date-class for the additional-fields
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a date as additional field
 *
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AddField_Type_Date extends PLIB_AddField_Type_Default
{
	public function get_value_from_formular($default = null)
	{
		$field_name = $this->_data->get_name();
		$day = $this->input->get_var('add_'.$field_name.'_day','post',PLIB_Input::INTEGER);
		$month = $this->input->get_var('add_'.$field_name.'_month','post',PLIB_Input::INTEGER);
		$year = $this->input->get_var('add_'.$field_name.'_year','post',PLIB_Input::INTEGER);
		if($day === null || $month === null || $year === null || $day == -1 || $month == -1 || $year == -1)
			return $default !== null ? $default : '0000-00-00';

		return $year.'-'.$month.'-'.$day;
	}
	
	protected function _get_formular_field($formular,$value)
	{
		$dateVal = array(-1,-1,-1);
		if($value != '')
		{
			$parts = explode('-',$value);
			if(count($parts) == 3)
				$dateVal = array($parts[2],$parts[1],$parts[0]);
		}

		return $formular->get_date_chooser('add_'.$this->_data->get_name().'_',$dateVal,false,true,1900);
	}
	
	protected function _is_valid_value($value)
	{
		if(!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/',$value))
			return false;

		$parts = explode('-',$value);
		return checkdate($parts[1],$parts[2],$parts[0]);
	}
	
	public function is_empty($value)
	{
		return $value == '0000-00-00';
	}
	
	protected function _get_display_value($value)
	{
		$parts = explode('-',$value);
		// invalid date?
		if(!is_array($parts) || count($parts) != 3)
			return '';
		
		$comps = array();
		$date_order = $this->locale->get_date_order();
		foreach($date_order as $element)
		{
			switch($element)
			{
				case 'Y':
					$comps[] = $parts[0];
					break;
				case 'm':
					$comps[] = $parts[1];
					break;
				case 'd':
					$comps[] = $parts[2];
					break;
			}
		}
		
		return implode($this->locale->get_date_separator(),$comps);
	}
	
	public function get_value_to_store($value)
	{
		if(!$this->_is_valid_value($value))
			return '0000-00-00';
		
		return $value;
	}
}
?>