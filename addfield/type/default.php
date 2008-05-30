<?php
/**
 * Contains the default-addfield-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The default additional field
 *
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_AddField_Type_Default extends PLIB_FullObject implements PLIB_AddField_Field
{
	/**
	 * The data of this field
	 *
	 * @var PLIB_AddField_Data
	 */
	protected $_data;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_AddField_Data $data the data of the field
	 */
	public function __construct($data)
	{
		parent::__construct();
		
		if(!($data instanceof PLIB_AddField_Data))
			PLIB_Helper::def_error('instance','data','PLIB_AddField_Data',$data);
		
		$this->_data = $data;
	}
	
	public function get_data()
	{
		return $this->_data;
	}
	
	public function get_title()
	{
		return $this->_data->get_title();
	}
	
	public function get_value_from_formular($default = null)
	{
		$val = $this->input->get_var('add_'.$this->_data->get_name(),'post',PLIB_Input::STRING);
		return $val !== null ? $val : $default;
	}
	
	public function get_formular_field($formular,$value)
	{
		$html = $this->_get_formular_field($formular,$value);
		$html .= $this->_data->get_value_suffix();
		if($this->_data->get_edit_suffix())
			$html .= '<div style="padding-top: 4px;">'.$this->_data->get_edit_suffix().'</div>';
		
		return $html;
	}
	
	/**
	 * Should generate just the formular field, without edit-suffix
	 *
	 * @param PLIB_HTML_Formular the formular that should be used
	 * @param mixed $value the default value
	 * @return string the HTML-code for the formular-control
	 */
	protected abstract function _get_formular_field($formular,$value);
	
	public function is_valid_value($value)
	{
		// empty values are allowed
		if($this->is_empty($value))
		{
			if(!$this->_data->is_required())
				return '';
			
			return 'value_missing';
		}
		
		$valid = $this->_is_valid_value($value);
		if($valid)
			return '';
		
		return 'value_invalid';
	}
	
	/**
	 * Checks wether the given value is correct. You can assume that the value is not empty.
	 *
	 * @param mixed $value the entered value
	 * @return boolean true if the value is valid
	 */
	protected abstract function _is_valid_value($value);
	
	public function is_empty($value)
	{
		return empty($value);
	}
	
	/**
	 * Should build the HTML-code for the given value that should be displayed
	 * 
	 * @param mixed $value the value of this field
	 * @param string $link_class the CSS-class of the links
	 * @param string $text_class the CSS-class for the text
	 * @param int $limit if > 0 the max. number of visible characters
	 * @return string the HTML-code to display
	 */
	public function get_display($value,$link_class,$text_class,$limit = 0)
	{
		$display = $this->_get_display_value($value);

		$custom = $this->_data->get_custom_display();
		if($custom)
		{
			$display = str_replace('{link_class}',$link_class,$custom);
			$display = str_replace('{text_class}',$text_class,$display);
			$display = str_replace('{value}',$value,$display);
		}
		
		if($limit)
		{
			$lhs = new PLIB_HTML_LimitedString($display,30);
			$short = $lhs->get();
			if($lhs->has_cut())
				$display = '<span title="'.strip_tags($display).'">'.$short.'</span>';
			else
				$display = $short;
		}

		$display .= $this->_data->get_value_suffix();
		return $display;
	}
	
	/**
	 * Should return the value which will be used by {@link get_display}. By default
	 * it simply returns the value. You may change this behavior by overwriting this method.
	 *
	 * @param mixed $value the value
	 * @return mixed the value to use
	 */
	protected function _get_display_value($value)
	{
		return $value;
	}
	
	public function get_value_to_store($value)
	{
		$validation = $this->_data->get_validation();
		if($validation && !preg_match($validation,$value))
			return '';
		
		return $value;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>