<?php
/**
 * Contains the default locale
 * 
 * @package			FrameWorkSolution
 * @subpackage	locale
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
 * The default locale (en)
 * 
 * @package			FrameWorkSolution
 * @subpackage	locale
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Locale_EN extends FWS_Object implements FWS_Locale
{
	/**
	 * The default language-entries
	 *
	 * @var array
	 */
	private $_lang = array(
		// some general time- and date-entries
		'x_minutes_ago' => '%d minutes ago',
		'1_minute_ago' => '1 minute ago',
		'x_seconds_ago' => '%d seconds ago',
		'yesterday' => 'Yesterday',
		'today' => 'Today',
		'tomorrow' => 'Tomorrow',
		
		// months and week-days
		'january' => 'January',
		'february' => 'February',
		'march' => 'March',
		'april' => 'April',
		'may' => 'May',
		'june' => 'June',
		'july' => 'July',
		'august' => 'August',
		'september' => 'September',
		'october' => 'October',
		'november' => 'November',
		'december' => 'December',
		'monday' => 'Monday',
		'tuesday' => 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday' => 'Thursday',
		'friday' => 'Friday',
		'saturday' => 'Saturday',
		'sunday' => 'Sunday',
		
		// for the page-split and other
		'back' => 'Back',
		'forward' => 'Forward',
		'firstpage' => 'First page',
		'lastpage' => 'Last page',
		'gotopage' => 'Go to page',
		'viewing' => 'Viewing',
		'total' => 'Total',
		'notavailable' => 'n/a',
		
		// other general stuff
		'error_occurred' => 'The following errors are occurred',
		'permission_denied' => 'Permission denied',
		'invalid_page' => 'Invalid page',
		'information' => 'Information',
		'errors' => 'Errors',
		'warnings' => 'Warnings',
		'notices' => 'Notices',
		'and' => 'and',
		'yes' => 'Yes',
		'no' => 'No',
	);
	
	/**
	 * The timezone
	 *
	 * @var float
	 */
	private $_timezone = 'Europe/Berlin';
	
	public function contains_lang($name)
	{
		return isset($this->_lang[$name]);
	}
	
	public function lang($name,$mark_missing = true)
	{
		if(isset($this->_lang[$name]))
			return $this->_lang[$name];
		
		if($mark_missing)
			return '&lt;'.$name.'&gt;';
		
		return $name;
	}
	
	public function get_dateformat($type)
	{
		switch($type)
		{
			case FWS_Locale::FORMAT_DATE:
				return 'm/d/Y';
			
			case FWS_Locale::FORMAT_DATE_SHORT:
				return 'm/d/y';
			
			case FWS_Locale::FORMAT_DATE_LONG:
				return '%\W, d. %\M Y';
			
			case FWS_Locale::FORMAT_TIME:
				return 'g:i A';
			
			case FWS_Locale::FORMAT_TIME_SEC:
				return 'g:i:s A';
			
			case FWS_Locale::FORMAT_DATE_TIME_SEP:
				return ' ';
			
			default:
				FWS_Helper::error('Invalid type $type!');
				return '';
		}
	}
	
	public function get_dec_separator()
	{
		return '.';
	}
	
	public function get_thousands_separator()
	{
		return ',';
	}
	
	public function get_date_order()
	{
		return array('m','d','Y');
	}
	
	public function get_date_separator()
	{
		return '/';
	}
	
	public function get_timezone()
	{
		return $this->_timezone;
	}
	
	public function set_timezone($timezone)
	{
		if(!is_string($timezone))
			FWS_Helper::def_error('string','timezone',$timezone);
		
		$this->_timezone = $timezone;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>