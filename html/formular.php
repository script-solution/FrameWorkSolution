<?php
/**
 * Contains the formular-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A utility class to build formulars. You can specify a condition which controls
 * wether the values of the formular-elements should be retrieved from $_POST or
 * if the default values should be displayed.
 * <br>
 * The class contains many helper-functions which can build the formular-elements
 * and select automaticly the corresponding value.
 * <br>
 * It is also possible to set a CSS-class and CSS-attributes for all form-elements.
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_Formular extends FWS_Object
{
	/**
	 * The condition which will be used to determine if the value from $_POST should be displayed
	 *
	 * @var boolean
	 */
	private $_condition;
	
	/**
	 * The class of all elements
	 *
	 * @var string
	 */
	private $_class = null;
	
	/**
	 * CSS-attributes for all elements
	 *
	 * @var array
	 */
	private $_style = array();
	
	/**
	 * Custom attributes for all elements
	 *
	 * @var array
	 */
	private $_custom = array();

	/**
	 * Constructor
	 *
	 * @param boolean $condition the condition which will be used to determine if the value
	 * 	from $_POST should be displayed
	 */
	public function __construct($condition)
	{
		parent::__construct();
		
		$this->_condition = (bool)$condition;
	}
	
	/**
	 * @return boolean the condition
	 */
	public final function get_condition()
	{
		return $this->_condition;
	}
	
	/**
	 * Sets the condition to given value. If the condition is true the values will be grabbed
	 * from POST. Otherwise the default value will be used.
	 *
	 * @param boolean $cond the new value
	 */
	public final function set_condition($cond)
	{
		$this->_condition = (bool)$cond;
	}
	
	/**
	 * Returns the value for the element with given name. If the condition is true
	 * the value from POST will be used or otherwise the default-value.
	 * Note that stripslashes will be performed on the value from POST!
	 *
	 * @param string $name the name of the element
	 * @param mixed $default the default-value
	 * @return mixed the value to use
	 */
	public final function get_input_value($name,$default = '')
	{
		$input = FWS_Props::get()->input();

		if($this->_condition)
		{
			$val = $input->get_var($name,'post');
			if(is_string($val))
				$val = stripslashes($val);
			return $val;
		}
		
		return $default;
	}
	
	/**
	 * Returns ' checked="checked"' or '' depending on the given default value
	 * and wether the condition is true. If the condition is true and the checkbox
	 * has been selected ' checked="checked"' will be returned. Otherwise
	 * this will be returned if $default is true. In all other cases '' will be returned.
	 *
	 * @param string $name the name of the element
	 * @param boolean $default the default-value
	 * @return string the value for the checkbox
	 */
	public final function get_checkbox_value($name,$default = false)
	{
		$input = FWS_Props::get()->input();

		$val = $default;
		if($this->_condition)
			$val = $input->isset_var($name,'post');
		
		return $val ? ' checked="checked"' : '';
	}
	
	/**
	 * Returns ' checked="checked"' or '' depending on the given default value
	 * and wether the condition is true. If the condition is true and the radiobutton
	 * with given value has been selected ' checked="checked"' will be returned. Otherwise
	 * this will be returned if $default is true. In all other cases '' will be returned.
	 *
	 * @param string $name the name of the element
	 * @param string the value of this radio-button
	 * @param boolean $default the default-value
	 * @return string the value for the radio-button
	 */
	public final function get_radio_value($name,$value,$default = false)
	{
		$input = FWS_Props::get()->input();

		$val = $default;
		if($this->_condition)
			$val = $input->get_var($name,'post') == $value;
		
		return $val ? ' checked="checked"' : '';
	}
	
	/**
	 * @return string the class for all formular elements (null = none)
	 */
	public final function get_class()
	{
		return $this->_class;
	}
	
	/**
	 * Sets the class that will be used for all formular elements (null = none).
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 *
	 * @param string $class the new value
	 */
	public final function set_class($class)
	{
		$this->_class = $class;
	}
	
	/**
	 * Clears the CSS-attributes.
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 */
	public final function clear_css_attributes()
	{
		$this->_style = array();
	}
	
	/**
	 * Sets the CSS-attribute with given name to given value for all formular elements.
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 *
	 * @param string $name the name of the attribute
	 * @param mixed $value the value of the attribute
	 */
	public final function set_css_attribute($name,$value)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		$this->_style[$name] = $value;
	}
	
	/**
	 * Removes the given CSS-attribute.
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 *
	 * @param string $name the name of the attribute
	 */
	public final function remove_css_attribute($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(isset($this->_style[$name]))
			unset($this->_style[$name]);
	}
	
	/**
	 * Clears the custom-attributes. The custom attributes
	 * will be added to all elements!
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 */
	public final function clear_custom_attributes()
	{
		$this->_custom = array();
	}
	
	/**
	 * Sets the custom attribute with given name to given value. The custom attributes
	 * will be added to all elements!
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 *
	 * @param string $name the name
	 * @param string $value the value
	 */
	public final function set_custom_attribute($name,$value)
	{
		if(!is_scalar($name))
			FWS_Helper::def_error('scalar','name',$name);
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		if(!is_scalar($value))
			FWS_Helper::def_error('scalar','value',$value);
		
		$this->_custom[$name] = $value;
	}
	
	/**
	 * Removes the custom-attribute with given name. The custom attributes
	 * will be added to all elements!
	 * Note that this does only affect elements that are created <b>afterwards</b>!
	 *
	 * @param string $name the name
	 * @see clear_custom_attributes()
	 */
	public final function remove_custom_attribute($name)
	{
		if(!is_scalar($name))
			FWS_Helper::def_error('scalar','name',$name);
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(isset($this->_custom[$name]))
			unset($this->_custom[$name]);
	}

	/**
	 * Generates a date-chooser
	 *
	 * @param string $prefix the prefix for the combo-boxes.
	 * 	so the final names will be:
	 * 	<code><prefix>day, <prefix>month, ...</code>
	 * @param mixed $default the default value of the date-chooser. if it is an array,
	 * 	it has to have the following form:
	 * 	<code>array(<day>,<month>,<year>)</code>
	 * 	otherwise it will be treaten as a timestamp, where 0 is the current time
	 * @param boolean $add_time do you want to add the time to the date-chooser?
	 * @param boolean $add_no_value do you want to put a "no-value" option to each combo?
	 * @param int $start_year the year to start with (default 1990)
	 * @return string the combo-boxes
	 */
	public final function get_date_chooser($prefix,$default = 0,$add_time = true,$add_no_value = false,
		$start_year = 1990)
	{
		$locale = FWS_Props::get()->locale();

		if(!FWS_Helper::is_integer($start_year) || $start_year < 0 || $start_year > 2019)
			FWS_Helper::def_error('numbetween','start_year',0,2019,$start_year);
		
		$hour = -1;
		$minute = -1;
		if(is_array($default))
		{
			if(count($default) != 3)
				FWS_Helper::error('$default has not 3 elements!');
			
			$add_time = false;
			list($day,$month,$year) = $default;
		}
		else
		{
			if(!FWS_Helper::is_integer($default) || $default < 0)
				FWS_Helper::def_error('intge0','default',$default);
			
			if($default == 0 && !$add_no_value)
				$default = time();

			if($default != 0 || !$add_no_value)
			{
				$date = new FWS_Date($default);
				$day = $date->get_day(false);
				$month = $date->get_month(false);
				$year = $date->get_year();
				$hour = $date->get_hour(false);
				$minute = $date->get_min();
			}
		}

		$result = '';
		$result .= $this->get_js_calendar($prefix,$start_year,$add_no_value);
		
		$separator = $locale->get_date_separator();
		$date_order = $locale->get_date_order();
		foreach($date_order as $k => $comp)
		{
			switch($comp)
			{
				case 'd':
					$cb = new FWS_HTML_ComboBox($prefix.'day',null,null,$day);
					$cb->set_options($this->_get_options(1,31,$add_no_value));
					$this->_apply_defaults($cb);
					if($this->_condition)
						$cb->set_value($this->get_input_value($prefix.'day'));
					$result .= $cb->to_html();
					break;
				
				case 'm':
					$cb = new FWS_HTML_ComboBox($prefix.'month',null,null,$month);
					$cb->set_options($this->_get_options(1,12,$add_no_value));
					$this->_apply_defaults($cb);
					if($this->_condition)
						$cb->set_value($this->get_input_value($prefix.'month'));
					$result .= $cb->to_html();
					break;
				
				case 'Y':
					$cb = new FWS_HTML_ComboBox($prefix.'year',null,null,$year);
					$cb->set_options($this->_get_options($start_year,2020,$add_no_value));
					$this->_apply_defaults($cb);
					if($this->_condition)
						$cb->set_value($this->get_input_value($prefix.'year'));
					$result .= $cb->to_html();
					break;
			}
			
			if($k < count($date_order) - 1)
				$result .= $separator.' ';
		}
		
		$result .= $this->get_js_calendar_link($prefix);

		if($add_time)
		{
			$result .= ' , ';
			
			$cb = new FWS_HTML_ComboBox($prefix.'hour',null,null,$hour);
			$cb->set_options($this->_get_options(0,23,$add_no_value));
			$this->_apply_defaults($cb);
			if($this->_condition)
				$cb->set_value($this->get_input_value($prefix.'hour'));
			$result .= $cb->to_html();
			
			$result .= ' : ';
			
			$cb = new FWS_HTML_ComboBox($prefix.'min',null,null,$minute);
			$cb->set_options($this->_get_options(0,59,$add_no_value));
			$this->_apply_defaults($cb);
			if($this->_condition)
				$cb->set_value($this->get_input_value($prefix.'min'));
			$result .= $cb->to_html();
		}

		return $result;
	}
	
	/**
	 * Builds a date-chooser as simple text-box.
	 *
	 * @param string $name the name of the text-box
	 * @param string $default the default date
	 * @return string the HTML-code
	 */
	public function get_date_chooser_textbox($name,$default = '')
	{
		$result = '';
		
		static $added_calendar = false;
		$fwspath = FWS_Path::client_fw();
		$calendar_style = $this->get_js_calendar_style();
		if(!$added_calendar)
		{
			$js = FWS_Javascript::get_instance();
			$caljs = $js->get_file('js/calendar.js','fws');
			$result .= '<script type="text/javascript" src="'.$caljs.'"></script>'."\n";
			$result .= '<script type="text/javascript" src="'.$this->get_js_calendar_lang().'"></script>'."\n";
			//$result .= '<link rel="stylesheet" type="text/css" href="'.$this->get_js_calendar_style().'" />'."\n";
			$added_calendar = true;
		}
		
		$result .=<<<EOF
<script type="text/javascript">
<!--
var cal_{$name} = new FWS_Calendar('{$fwspath}js/','{$name}',function(date) {
	var input = FWS_getElement(this.inputId);
	var val = this.get2Digits(date.getDate()) + ".";
	val += this.get2Digits((date.getMonth() + 1)) + "." + date.getFullYear();
	input.value = val;
});
cal_{$name}.setCSSFile('{$calendar_style}');
cal_{$name}.setStartUpFunction(function() {
	var input = FWS_getElement(this.inputId);
	if(input.value != '')
	{
		var parts = input.value.split('.');
		if(parts.length == 3)
		{
			if(isNaN(Number(parts[0])))
				parts[0] = now.getDate();
			if(isNaN(Number(parts[1])))
				parts[1] = now.getMonth() + 1;
			if(isNaN(Number(parts[2])))
				parts[2] = now.getFullYear();
			this.setSelectedDate(parts[2],parts[1],parts[0]);
		}
	}
});
cal_{$name}.setMinYear(1900);
cal_{$name}.setMaxYear(2020);
//-->
</script>
EOF;
		
		$result .= $this->get_textbox($name,$default,12,10);
		$result .= ' '.$this->get_js_calendar_link($name);
		
		return $result;
	}
	
	/**
	 * @return string the CSS-file for the js-calendar
	 */
	protected function get_js_calendar_style()
	{
		return FWS_Path::client_fw().'js/calendarstyle.css';
	}
	
	/**
	 * @return string the language-file for the js-calendar
	 */
	protected function get_js_calendar_lang()
	{
		return FWS_Javascript::get_instance()->get_file('js/calendar_lang_en.js','fws');
	}
	
	/**
	 * @return string the image-file for the js-calendar
	 */
	protected function get_js_calendar_image()
	{
		return FWS_Path::client_fw().'js/calendar.png';
	}
	
	/**
	 * Builds the javascript-calendar
	 *
	 * @param string $prefix the date-prefix
	 * @param int $start_year the start-year of the year-combo
	 * @param boolean $add_no_value wether a "no-value"-entry should be added
	 * @return string the js-calendar
	 */
	protected function get_js_calendar($prefix,$start_year,$add_no_value)
	{
		static $added_calendar = false;
		$fwspath = FWS_Path::client_fw();
		$calendar_style = $this->get_js_calendar_style();
		$result = '';
		if(!$added_calendar)
		{
			$js = FWS_Javascript::get_instance();
			$caljs = $js->get_file('js/calendar.js','fws');
			$result .= '<script type="text/javascript" src="'.$caljs.'"></script>'."\n";
			$result .= '<script type="text/javascript" src="'.$this->get_js_calendar_lang().'"></script>'."\n";
			//$result .= '<link rel="stylesheet" type="text/css" href="'.$this->get_js_calendar_style().'" />'."\n";
			$added_calendar = true;
		}
		
		$add_no_value = $add_no_value ? '1' : '0';
		$result .=<<<EOF
<script type="text/javascript">
<!--
var cal_{$prefix} = new FWS_Calendar('{$fwspath}js/','mycal',function(date) {
	var day = FWS_getElement('{$prefix}day');
	var month = FWS_getElement('{$prefix}month');
	var year = FWS_getElement('{$prefix}year');
	day.options.selectedIndex = {$add_no_value} ? date.getDate() : date.getDate() - 1;
	month.options.selectedIndex = {$add_no_value} ? date.getMonth() + 1 : date.getMonth();
	year.options.selectedIndex = ({$add_no_value} ? 1 : 0) + date.getFullYear() - {$start_year};
});
cal_{$prefix}.setCSSFile('{$calendar_style}');
cal_{$prefix}.setStartUpFunction(function() {
	var day = FWS_getElement('{$prefix}day');
	var month = FWS_getElement('{$prefix}month');
	var year = FWS_getElement('{$prefix}year');
	var selDay = day.options[day.options.selectedIndex].text;
	var selMonth = month.options[month.options.selectedIndex].text;
	var selYear = year.options[year.options.selectedIndex].text;
	var now = new Date();
	if(isNaN(Number(selDay)))
		selDay = now.getDate();
	if(isNaN(Number(selMonth)))
		selMonth = now.getMonth() + 1;
	if(isNaN(Number(selYear)))
		selYear = now.getFullYear();
	this.setSelectedDate(selYear,selMonth,selDay);
});
cal_{$prefix}.setMinYear({$start_year});
cal_{$prefix}.setMaxYear(2020);
//-->
</script>
EOF;

		return $result;
	}
	
	/**
	 * Builds the link for the js-calendar
	 *
	 * @param string $prefix the date-prefix
	 * @return string the link
	 */
	protected function get_js_calendar_link($prefix)
	{
		$image  = $this->get_js_calendar_image();
		$result = '<a href="javascript:cal_'.$prefix.'.display(\'image_cal_'.$prefix.'\');">';
		$result .= '<img id="image_cal_'.$prefix.'" src="'.$image.'" alt="Calendar" /></a>';
		return $result;
	}

	/**
	 * Calculates the timestamp of the date-chooser-result
	 * the comboboxes have to have the following names:
	 * <code>
	 * 	<prefix>year, <prefix>month, <prefix>day, <prefix>min, <prefix>hour
	 * </code>
	 * Assumes that the time has been selected in the timezone of the user and returns the
	 * timestamp in GMT.
	 *
	 * @param string $prefix the prefix of the combobox-names
	 * @param boolean $add_time is the time specifyable?
	 * @return int the timestamp in GMT of the chosen date
	 * @see get_date_chooser()
	 */
	public final function get_date_chooser_timestamp($prefix,$add_time = true)
	{
		$input = FWS_Props::get()->input();

		$year = $input->get_var($prefix.'year','post',FWS_Input::INTEGER);
		$month = $input->get_var($prefix.'month','post',FWS_Input::INTEGER);
		$day = $input->get_var($prefix.'day','post',FWS_Input::INTEGER);
		if($add_time)
		{
			$min = $input->get_var($prefix.'min','post',FWS_Input::INTEGER);
			$hour = $input->get_var($prefix.'hour','post',FWS_Input::INTEGER);
		}
		else
		{
			$min = 0;
			$hour = 0;
		}

		if($year === -1 || $month === -1 || $day === -1 || $min === -1 || $hour === -1)
			return 0;

		if($year === null || $month === null || $day === null || $min === null || $hour === null)
			return 0;

		// TODO is this correct?
		/*if($hour == 0)
		{
			$hour = 24;
			$day--;
		}*/

		return FWS_Date::get_timestamp(array($hour,$min,0,$month,$day,$year));
	}

	/**
	 * generates a textarea
	 *
	 * @param string $name the name of the textarea
	 * @param string $default the default value of the textarea
	 * @param string $width the width of the textarea (CSS-attribute "width"!)
	 * @param string $height the height of the textarea (CSS-attribute "height"!)
	 * @param boolean $disabled is the element disabled?
	 * @return string the textarea
	 */
	public final function get_textarea($name,$default = '',$width = '90%',$height = 250,
		$disabled = false)
	{
		$tb = new FWS_HTML_TextArea($name,null,null,$default);
		$this->_apply_defaults($tb);
		$tb->set_css_attribute('width',$width);
		$tb->set_css_attribute('height',$height);
		$tb->set_disabled($disabled);
		if($this->_condition)
			$tb->set_value($this->get_input_value($name));
		
		return $tb->to_html();
	}

	/**
	 * Generates an text-box with given options
	 *
	 * @param string $name the name of the input-box
	 * @param string $default the default value of the box
	 * @param int $size the size
	 * @param int $maxlength the maximum length
	 * @param boolean $disabled is the element disabled?
	 * @return string the text-box
	 */
	public final function get_textbox($name,$default = '',$size = 15,$maxlength = null,$disabled = false)
	{
		$tb = new FWS_HTML_TextBox($name,null,null,$default,FWS_Helper::is_integer($size) ? $size : 15,$maxlength);
		if(is_string($size) && FWS_String::ends_with($size,'%'))
			$tb->set_css_attribute('width',$size);
		$this->_apply_defaults($tb);
		$tb->set_disabled($disabled);
		if($this->_condition)
			$tb->set_value($this->get_input_value($name));
		
		return $tb->to_html();
	}

	/**
	 * Generates an password-box with given options
	 *
	 * @param string $name the name of the input-box
	 * @param string $default the default value of the box
	 * @param int $size the size
	 * @param int $maxlength the maximum length
	 * @param boolean $disabled is the element disabled?
	 * @return string the password-box
	 */
	public final function get_passwordbox($name,$default = '',$size = 15,$maxlength = null,
		$disabled = false)
	{
		$pb = new FWS_HTML_PasswordBox($name,null,$default,$size,$maxlength);
		$this->_apply_defaults($pb);
		$pb->set_disabled($disabled);
		return $pb->to_html();
	}

	/**
	 * Generates a combobox with the given options
	 *
	 * @param string $name the name of the combobox
	 * @param array $options an associative array with the options
	 * @param string $default the default value; can also be an array for multiple selections
	 * @param boolean $multiple is the combobox a multiple-combo?
	 * @param int $size the number of rows
	 * @param boolean $disabled is the combobox disabled?
	 * @return string the combobox
	 */
	public final function get_combobox($name,$options,$default = null,$multiple = false,$size = 1,
		$disabled = false)
	{
		$combo = new FWS_HTML_ComboBox($name,null,null,$default,$size,$multiple);
		$this->_apply_defaults($combo);
		$combo->set_options($options);
		$combo->set_disabled($disabled);
		
		if($this->_condition)
			$combo->set_value($this->get_input_value($name));
		
		return $combo->to_html();
	}

	/**
	 * Generates a checkbox with label
	 *
	 * @param string $name the name of the checkbox
	 * @param boolean $default the default value of the checkbox (selected or not!)
	 * @param string $value the value of the checkbox
	 * @param string $text the text of the checkbox
	 * @param boolean $disabled is the element disabled?
	 * @return string the checkbox
	 */
	public final function get_checkbox($name,$default = false,$value = '',$text = '',$disabled = false)
	{
		$input = FWS_Props::get()->input();

		$cb = new FWS_HTML_Checkbox($name,null,null,$default,$text,$value);
		$this->_apply_defaults($cb);
		$cb->set_disabled($disabled);
		
		if($this->_condition)
			$cb->set_value($input->isset_var($name,'post'));
		
		return $cb->to_html();
	}

	/**
	 * Generates yes-no radioboxes
	 *
	 * @param string $name the name of the radioboxes
	 * @param int $default 1 or 0 for the default selected value
	 * @param boolean $disabled is the element disabled?
	 * @return string the radio-boxes
	 */
	public final function get_radio_yesno($name,$default,$disabled = false)
	{
		$locale = FWS_Props::get()->locale();

		$options = array(
			'1' => $locale->lang('yes'),
			'0' => $locale->lang('no')
		);
		return $this->get_radio_boxes($name,$options,$default,' ',$disabled);
	}

	/**
	 * Generates radio-boxes
	 *
	 * @param string $name the name of the radio-boxes
	 * @param array $options an associative array with the options
	 * @param string $default the key of the default-selected radiobox
	 * @param boolean $separator the separator between the options
	 * @param boolean $disabled is the element disabled?
	 * @return string the radioboxes
	 */
	public final function get_radio_boxes($name,$options,$default,$separator = '<br />',
		$disabled = false)
	{
		$rbg = new FWS_HTML_RadioButtonGroup($name,null,null,$default,$separator);
		$this->_apply_defaults($rbg);
		$rbg->set_options($options);
		$rbg->set_disabled($disabled);
		
		if($this->_condition)
			$rbg->set_value($this->get_input_value($name));
		
		return $rbg->to_html();
	}
	
	/**
	 * Applies the default stuff to the element (class and css-attributes)
	 *
	 * @param FWS_HTML_FormElement $element the element
	 */
	private function _apply_defaults($element)
	{
		foreach($this->_style as $k => $v)
			$element->set_css_attribute($k,$v);
		foreach($this->_custom as $k => $v)
			$element->set_custom_attribute($k,$v);
		$element->set_class($this->_class);
	}
	
	/**
	 * Builds the options for days, months and years
	 *
	 * @param int $from the start-number
	 * @param int $to the end-number
	 * @param boolean $add_no_value add a "no-value" item?
	 * @return array the options
	 */
	private function _get_options($from,$to,$add_no_value)
	{
		$values = range($from,$to);
		if($add_no_value)
			array_unshift($values,-1);
		$res = array();
		foreach($values as $v)
		{
			if($v == -1)
				$res[$v] = '--';
			else
				$res[$v] = str_pad($v,2,'0',STR_PAD_LEFT);
		}
		return $res;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>