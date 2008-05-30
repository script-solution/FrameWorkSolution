<?php
/**
 * Contains the locale-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The locale-interface which contains all stuff that may be different in different countries,
 * areas and so on.
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Locale
{
	/**
	 * Represents the format for a date (without time)
	 */
	const FORMAT_DATE						= 'date';
	
	/**
	 * Represents the format for a short date (with time and 2 digits for the year)
	 */
	const FORMAT_DATE_SHORT			= 'shortdate';
	
	/**
	 * Represents the format for a long date (without time)
	 */
	const FORMAT_DATE_LONG			= 'longdate';
	
	/**
	 * Represents the format for a time
	 */
	const FORMAT_TIME						= 'time';
	
	/**
	 * Represents the format for a time with seconds
	 */
	const FORMAT_TIME_SEC				= 'timesec';
	
	/**
	 * Represents the separator between date and time
	 */
	const FORMAT_DATE_TIME_SEP	= 'sep';
	
	/**
	 * Checks wether the given language-entry exists.
	 *
	 * @param string $name the name of the entry
	 * @return boolean true if it exists
	 */
	public function contains_lang($name);
	
	/**
	 * Returns the value for the given language-entry
	 * 
	 * @param string $name the name of the entry
	 * @param boolean $mark_missing should the method mark not existing entries?
	 * @return string the value of that entry
	 */
	public function lang($name,$mark_missing = true);
	
	/**
	 * @return string the decimal separator
	 */
	public function get_dec_separator();
	
	/**
	 * @return string the thousands-separator
	 */
	public function get_thousands_separator();
	
	/**
	 * Should return an array with the order of a date.
	 * Possible values are: 'Y', 'm' and 'd'
	 * 
	 * @return array the date-order
	 */
	public function get_date_order();
	
	/**
	 * Should return the separator for the date-parts.
	 * So for example '/' or '.'.
	 * 
	 * @return string the date-separator
	 */
	public function get_date_separator();
	
	/**
	 * Returns the date-format of given type for this locale
	 * (in {@link PLIB_Date::to_format()}-syntax).
	 * 
	 * @param string $type the type of format
	 * @return string the format
	 * @see FORMAT_DATE
	 * @see FORMAT_DATE_SHORT
	 * @see FORMAT_DATE_LONG
	 * @see FORMAT_TIME
	 * @see FORMAT_TIME_SEC
	 * @see FORMAT_DATE_TIME_SEP
	 */
	public function get_dateformat($type);
	
	/**
	 * @return float the specified timezone
	 */
	public function get_timezone();
	
	/**
	 * Sets the timezone to given value
	 * 
	 * @param float $timezone the new value
	 */
	public function set_timezone($timezone);
}
?>