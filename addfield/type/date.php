<?php
/**
 * Contains the date-class for the additional-fields
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a date as additional field
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_AddField_Type_Date extends FWS_AddField_Type_Default
{
	public function get_value_from_formular($default = null)
	{
		$input = FWS_Props::get()->input();

		$field_name = $this->_data->get_name();
		$day = $input->get_var('add_'.$field_name.'_day','post',FWS_Input::INTEGER);
		$month = $input->get_var('add_'.$field_name.'_month','post',FWS_Input::INTEGER);
		$year = $input->get_var('add_'.$field_name.'_year','post',FWS_Input::INTEGER);
		if($day === null || $month === null || $year === null || $day == -1 || $month == -1 || $year == -1)
			return $default !== null ? $default : '0000-00-00';

		return $year.'-'.$month.'-'.$day;
	}
	
	protected function get_formular_field_impl($formular,$value)
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
	
	protected function is_valid_value_impl($value)
	{
		if(!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/',$value))
			return false;

		$parts = explode('-',$value);
		return checkdate($parts[1],$parts[2],$parts[0]);
	}

	public function get_default_value()
	{
		return FWS_Date::get_formated_date('Y-m-d');
	}
	
	public function is_empty($value)
	{
		return $value == '0000-00-00';
	}
	
	protected function get_display_value($value)
	{
		$locale = FWS_Props::get()->locale();

		$parts = explode('-',$value);
		// invalid date?
		if(!is_array($parts) || count($parts) != 3)
			return '';
		
		$comps = array();
		$date_order = $locale->get_date_order();
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
		
		return implode($locale->get_date_separator(),$comps);
	}
	
	public function get_value_to_store($value)
	{
		if(!$this->is_valid_value_impl($value))
			return '0000-00-00';
		
		return $value;
	}
}
?>