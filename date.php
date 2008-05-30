<?php
/**
 * Contains the date-class
 *
 * @version			$Id: date.php 736 2008-05-23 18:24:22Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The date-class to format dates for the output. You may use this for timestamps or
 * in general for any date.
 * <br>
 * This class depends on {@link PLIB_Locale}. That means that $base->locale will be used
 * for texts, date-formats, timezone and so on.
 * <br>
 * PLIB_Date lets you specify an 'input-timezone' and an 'output-timezone'. That means you can
 * choose in which timezone your date is and in what you want to interpret it. So for example
 * you have a timestamp in GMT and want to display it in the user-timezone.
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Date extends PLIB_FullObject
{
	/**
	 * Represents the timezone of the user
	 * 
	 * @see PLIB_Locale
	 */
	const TZ_USER		= 0;
	
	/**
	 * Represents the timezone GMT
	 */
	const TZ_GMT		= 1;
	
	/**
	 * The timestamp of today
	 *
	 * @var integer
	 */
	private static $_today;

	/**
	 * The timestamp of yesterday
	 *
	 * @var integer
	 */
	private static $_yesterday;
	
	/**
	 * The timestamp of tomorrow
	 *
	 * @var integer
	 */
	private static $_tomorrow;
	
	/**
	 * The GMT-timezone
	 *
	 * @var DateTimeZone
	 */
	private static $_timezone_gmt;
	
	/**
	 * The timezone of the user
	 *
	 * @var DateTimeZone
	 */
	private static $_timezone_user;
	
	/**
	 * A convenience method for:
	 * <code>
	 * 	$d = new PLIB_Date($date);
	 * 	return $d->to_date($show_time,$relative);
	 * </code>
	 * 
	 * @param mixed $date the date
	 * @param boolean $show_time show the time?
	 * @param boolean $relative do you want to show a relative date like "Today", "2 minutes ago", ...
	 * 	if applicable?
	 * @return string the formated date to print
	 */
	public static function get_date($date = 'now',$show_time = true,$relative = true)
	{
		$d = new PLIB_Date($date);
		return $d->to_date($show_time,$relative);
	}
	
	/**
	 * Builds the timestamp for 'now' in GMT
	 *
	 * @return int the timestamp
	 */
	public static function get_current_timestamp()
	{
		return time();
	}
	
	/**
	 * Creates a timestamp in GMT. You can choose the input-timezone
	 * This is a convenience-method for:
	 * <code>
	 * 	$d = new PLIB_Date($date,$input_tz);
	 * 	return $d->to_timestamp();
	 * </code>
	 * You may also modify the date before the timestamp-creation. For example with '+1day'.
	 *
	 * @param mixed $date the date
	 * @param int $input_tz the input-timezone. That means the timezone of the date you specify. By
	 * 	default it is self::TZ_USER.
	 * @param string $mod the parameter for #modify(). will modify the date before the timestamp
	 * 	is returned. If $mod is not empty #modify() will be called.
	 * @return int the timestamp
	 */
	public static function get_timestamp($date,$input_tz = self::TZ_USER,$mod = '')
	{
		$d = new PLIB_Date($date,$input_tz);
		if($mod != '')
			$d->modify($mod);
		return $d->to_timestamp();
	}
	
	/**
	 * A convenience method for:
	 * <code>
	 * 	$d = new PLIB_Date($date);
	 * 	return $d->to_format($format);
	 * </code>
	 * 
	 * @param string $format the date-format
	 * @param mixed $date the date ('now' by default)
	 * @return string the formated date to print
	 * @see to_format
	 */
	public static function get_formated_date($format,$date = 'now')
	{
		$d = new PLIB_Date($date);
		return $d->to_format($format);
	}
		
	/**
	 * Checks wether the given timestamp is valid
	 * 
	 * @param mixed $timestamp the timestamp to check
	 * @return boolean true if valid
	 */
	public static function is_valid_timestamp($timestamp)
	{
		if(!PLIB_Helper::is_integer($timestamp))
			return false;
		
		return $timestamp >= 0 && $timestamp < ~(1 << 31);
	}
	
	/**
	 * checks wether the given date is valid
	 *
	 * @param int $day the day of the date
	 * @param int $month the month of the date
	 * @param int $year the year of the date
	 * @return boolean true if it's valid
	 */
	public static function is_valid($day,$month,$year)
	{
		return checkdate((int)$month,(int)$day,(int)$year);
	}
	
	/**
	 * The DateTime-object which represents the date and time
	 * <br>
	 * Just to make the usage of DateTime and DateTimeZone clear:
	 * <ul>
	 * 	<li>Generally it depends on the default-timezone. That is date_default_timezone_get(). If
	 * 	no other timezone is specified this one will be used</li>
	 * 	<li>DateTime::__construct($date,$tz):
	 * 		<ul>
	 * 			<li>The first parameter is the date that should be used. For example:
	 * 				<ul>
	 * 					<li>'now': That is the current date in the default-timezone</li>
	 * 					<li>'now GMT': That is the current date in the default-timezone as GMT. That means
	 * 					if you have GMT+1 as default timezone and your current time is '14:18 +0100' DateTime
	 * 					stores '13:18 +0000'</li>
	 * 					<li>'@<timestamp>': Timestamps are always in GMT. That means it doesn't matter if
	 * 					you append 'GMT' to the string or something similar. So it simply stores the date
	 * 					at the timestamp in the GMT-timezone.</li>
	 * 				</ul>
	 * 			</li>
	 * 			<li>The second parameter is the timezone of that date. This is an alternative to appending
	 * 			the timezone to the first string-argument. If the first parameter has no timezone
	 * 			specified this one will be used.</li>
	 * 		</ul>
	 * 	</li>
	 * 	<li>DateTime::setTimezone($tz):<br>
	 * 		This method sets the timezone for which you want to "build" the date. So in some way
	 * 		it is the output-timezone.
	 * 	</li>
	 * 	<li>By default the "output-timezone" is the same as the "input-timezone". That means if you
	 * 		don't call setTimezone() the input-timezone will be used. For timestamps that will be GMT,
	 * 		for the other input-forms the timezone in the string or the second parameter of the
	 * 		constructor.
	 * 	</li>
	 * 	<li>Additionally DateTime takes care of daylight-saving-time depending on the timezone.
	 * 		Every timezone may have daylight-saving-time or not and may use it between different dates.
	 * 		That means if you have the date '2008-01-25 10:00' in the timezone 'GMT+1' the offset to
	 * 		GMT will be +1 hour. If you have '2008-07-25 10:00' in the same timezone the offset to GMT
	 * 		will be +2 hours.
	 * 	</li>
	 * </ul>
	 * 
	 * <b>Examples:</b><br>
	 * Lets assume that date_default_timezone_get() is America/Nome (GMT-9).
	 * And <var>$date</var> is '2008-01-24 14:03:22'.
	 * <ul>
	 * 	<li>The date as local printed as local:
	 * 		<code>
	 * 		$d = new DateTime($date);
	 * 		// the following line will print 'Thu, 24 Jan 2008 14:03:22 -0900'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>The date as local printed as GMT:
	 * 		<code>
	 * 		$d = new DateTime($date);
	 * 		$d->setTimezone(new DateTimeZone('GMT'));
	 * 		// the following line will print 'Thu, 24 Jan 2008 23:03:22 +0000'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>The date as GMT printed as GMT:
	 * 		<code>
	 * 		$d = new DateTime($date,new DateTimeZone('GMT'));
	 * 		$d->setTimezone(new DateTimeZone('GMT'));
	 * 		// the following line will print 'Thu, 24 Jan 2008 14:03:22 +0000'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>The date as GMT printed as local:
	 * 		<code>
	 * 		$d = new DateTime($date,new DateTimeZone('GMT'));
	 * 		$d->setTimezone(new DateTimeZone('America/Nome'));
	 * 		// the following line will print 'Thu, 24 Jan 2008 05:03:22 -0900'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>A no-daylight-saving date as local printed as local:
	 * 		<code>
	 * 		$d = new DateTime('2008-01-25 15:10',new DateTimeZone('Europe/Berlin'));
	 * 		$d->setTimezone(new DateTimeZone('Europe/Berlin'));
	 * 		// the following line will print 'Fri, 25 Jan 2008 15:10:00 +0100'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>A daylight-saving date as local printed as local:
	 * 		<code>
	 * 		$d = new DateTime('2008-07-25 15:10',new DateTimeZone('Europe/Berlin'));
	 * 		$d->setTimezone(new DateTimeZone('Europe/Berlin'));
	 * 		// the following line will print 'Fri, 25 Jul 2008 15:10:00 +0200'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>A no-daylight-saving date as local printed as GMT:
	 * 		<code>
	 * 		$d = new DateTime('2008-01-25 15:10',new DateTimeZone('Europe/Berlin'));
	 * 		$d->setTimezone(new DateTimeZone('GMT'));
	 * 		// the following line will print 'Fri, 25 Jan 2008 14:10:00 +0000'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * 	<li>A daylight-saving date as local printed as GMT:
	 * 		<code>
	 * 		$d = new DateTime('2008-07-25 15:10',new DateTimeZone('Europe/Berlin'));
	 * 		$d->setTimezone(new DateTimeZone('GMT'));
	 * 		// the following line will print 'Fri, 25 Jul 2008 13:10:00 +0000'
	 * 		echo $d->format(DATE_RFC2822);
	 * 		</code>
	 * 	</li>
	 * </ul>
	 *
	 * @var DateTime
	 */
	private $_date;

	/**
	 * Stores which output timezone should be used. Uses the constants instead of the timezone
	 * object.
	 *
	 * @var int
	 */
	private $_output_tz = self::TZ_GMT;
	
	/**
	 * Constructor. By default the input-timezone is GMT and the output-timezone the one of the user.
	 * See {@link PLIB_Locale}.<br>
	 * Note that it may cause trouble if you use a string as first argument and append the timezone
	 * to it! Because the class assumes that the date is either represented in GMT or the timezone
	 * of the user.
	 * <br>
	 * <var>$date</var> may be a string for strtotime(), a timestamp or an array with the date-
	 * components. That are:
	 * <code>
	 * 	array([hour,minutes,seconds,month,day,year])
	 * </code>
	 * Note that all arguments are optional. Not given arguments are considered as the current one.
	 * So if you specify just hour, minute and seconds the current date is used for the remaining 3.
	 * 
	 * @param mixed $date the date to set (default 'now')
	 * @param int $input_tz the input-timezone. That means the timezone of the date you specify
	 * @param int $output_tz the output-timezone. That means the timezone in which you want to
	 * 	print the date or something like that.
	 */
	public function __construct($date = 'now',$input_tz = self::TZ_GMT,$output_tz = self::TZ_USER)
	{
		parent::__construct();
		
		// init static fields
		if(!PLIB_Date::$_timezone_gmt)
		{
			// we set the default timezone to the user-tz, too, to prevent problems with 'now'
			// and other stuff.
			date_default_timezone_set($this->locale->get_timezone());
			
			PLIB_Date::$_timezone_gmt = new DateTimeZone('GMT');
			PLIB_Date::$_timezone_user = new DateTimeZone($this->locale->get_timezone());
			
			// store today, yesterday and tomorrow
			$d = new DateTime('@'.time());
			
			PLIB_Date::$_today = $d->format('dmY');
			$d->modify('-1 day');
			PLIB_Date::$_yesterday = $d->format('dmY');
			$d->modify('+2 days');
			PLIB_Date::$_tomorrow = $d->format('dmY');
		}
		
		// is it a timestamp?
		if(PLIB_Helper::is_integer($date))
		{
			// if we timestamp should be considered in the user-timezone we have to substract the offset
			// of the user-timezone to GMT.
			if($input_tz == self::TZ_USER)
			{
				$offset = PLIB_Date::$_timezone_user->getOffset(
					new DateTime('@'.$date,PLIB_Date::$_timezone_gmt)
				);
				$ts = $date - $offset;
				$date = gmdate('Y-m-d H:i:s',$ts);
				
				// now the input-timezone is GMT because it is a timestamp
				$input_tz = self::TZ_GMT;
			}
			else
				$date = '@'.$date;
		}
		else if(is_array($date))
		{
			switch(count($date))
			{
				case 0:
					$date = 'now';
					break;
				
				case 1:
					$date[] = date('i');
					// fall through
				case 2:
					$date[] = date('s');
					// fall through
				case 3:
					$date[] = date('m');
					// fall through
				case 4:
					$date[] = date('d');
					// fall through
				case 5:
					$date[] = date('Y');
					// fall through
				case 6:
					$date = sprintf('%d-%d-%d %d:%d:%d',$date[5],$date[3],$date[4],$date[0],$date[1],$date[2]);
					break;
				
				default:
					PLIB_Helper::error('Invalid number of array-elements! 0..6 are allowed.');
					break;
			}
		}
		
		// determine timezone
		if($input_tz == self::TZ_USER)
			$input_tz = PLIB_Date::$_timezone_user;
		else
			$input_tz = PLIB_Date::$_timezone_gmt;
		
		// create internal date-object
		$this->_date = new DateTime($date,$input_tz);
		
		// set output timezone
		if($output_tz == self::TZ_USER)
			$this->_date->setTimezone(PLIB_Date::$_timezone_user);
		else
			$this->_date->setTimezone(PLIB_Date::$_timezone_gmt);
		
		$this->_output_tz = $output_tz;
	}
	
	/**
	 * Modifies the date by given value
	 *
	 * @param string $mod the modification
	 * @see DateTime::modify()
	 */
	public function modify($mod)
	{
		$this->_date->modify($mod);
	}
	
	/**
	 * @return boolean wether the specified date is today
	 */
	public function is_today()
	{
		return $this->_date->format('dmY') == PLIB_Date::$_today;
	}
	
	/**
	 * @return boolean wether the specified date is yesterday
	 */
	public function is_yesterday()
	{
		return $this->_date->format('dmY') == PLIB_Date::$_yesterday;
	}
	
	/**
	 * @return boolean wether the specified date is tomorrow
	 */
	public function is_tomorrow()
	{
		return $this->_date->format('dmY') == PLIB_Date::$_tomorrow;
	}
	
	/**
	 * @return int the year of the date (full format)
	 */
	public function get_year()
	{
		return $this->_date->format('Y');
	}
	
	/**
	 * @param boolean $leading_zeros add leading zeros?
	 * @return int the month of the date (starting with 1)
	 */
	public function get_month($leading_zeros = true)
	{
		return $this->_date->format($leading_zeros ? 'm' : 'n');
	}
	
	/**
	 * @param boolean $leading_zeros add leading zeros?
	 * @return int the day of the date (starting with 1)
	 */
	public function get_day($leading_zeros = true)
	{
		return $this->_date->format($leading_zeros ? 'd' : 'j');
	}
	
	/**
	 * @param boolean $leading_zeros add leading zeros?
	 * @return int the hour of the date (24 hours)
	 */
	public function get_hour($leading_zeros = true)
	{
		return $this->_date->format($leading_zeros ? 'H' : 'G');
	}
	
	/**
	 * @return int the minutes of the date
	 */
	public function get_min()
	{
		return $this->_date->format('i');
	}
	
	/**
	 * @return int the seconds of the date
	 */
	public function get_sec()
	{
		return $this->_date->format('s');
	}
	
	/**
	 * Builds a timestamp for this date and takes care of the selected timezone and daylight-
	 * saving-time. So that you will get it in GMT and with no daylight-saving.
	 *
	 * @return int the timestamp in GMT with no daylight-saving
	 */
	public function to_timestamp()
	{
		$offset = $this->_output_tz != self::TZ_GMT ? $this->_date->getOffset() : 0;
		
		// do it this way because calling the methods of this class is too slow
		list($h,$i,$s,$m,$d,$y) = explode(',',$this->_date->format('H,i,s,m,d,Y'));
		$ts = gmmktime($h,$i,$s,$m,$d,$y);
		return $ts - $offset;
	}

	/**
	 * Returns a formated date.
	 * You can choose between the following predefined (language-dependend) formats:
	 * <ul>
	 * 	<li>date:						just the date in default format</li>
	 * 	<li>datetime:				date and time in default format</li>
	 * 	<li>longdate:				the long date format</li>
	 * 	<li>longdatetime:		the long date and the time</li>
	 * 	<li>shortdate:			the short date format</li>
	 * 	<li>shortdatetime:	the short date and the time</li>
	 * 	<li>time:						just the time</li>
	 * 	<li>a custom format (analog to the date()-format)</li>
	 * </ul>
	 * Every "time" can also be "time_s" to add the seconds!
	 *
	 * For the custom format:
	 * You have 2 additional "keywords" to format the date:
	 * <pre>
	 * 	%W = the complete weekday-name in the chosen language
	 * 	%M = the complete month-name in the chosen language
	 * </pre>
	 * Please take care that you escape the W and M to prevent that gmdate() replaces it
	 * Note that you have to turn $replace_wm on to use this!
	 *
	 * @param string $format the format of the date (see above)
	 * @param boolean $replace_wm do you want to replace weekday-names and month-names?
	 * @return string the date
	 */
	public function to_format($format,$replace_wm = false)
	{
		static $weekdays = null,$months = null;

		// ensure that we initialize it if the entries are available
		if($weekdays === null && $this->locale->contains_lang('sunday'))
		{
			$weekdays = array(
				$this->locale->lang('sunday'),
				$this->locale->lang('monday'),
				$this->locale->lang('tuesday'),
				$this->locale->lang('wednesday'),
				$this->locale->lang('thursday'),
				$this->locale->lang('friday'),
				$this->locale->lang('saturday')
			);

			$months = array(
				1 => $this->locale->lang('january'),
				$this->locale->lang('february'),
				$this->locale->lang('march'),
				$this->locale->lang('april'),
				$this->locale->lang('may'),
				$this->locale->lang('june'),
				$this->locale->lang('july'),
				$this->locale->lang('august'),
				$this->locale->lang('september'),
				$this->locale->lang('october'),
				$this->locale->lang('november'),
				$this->locale->lang('december')
			);
		}

		switch($format)
		{
			case 'date':
				$date = $this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE));
				break;
			case 'shortdate':
				$format = $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_SHORT);
				$date = $this->_date->format($format);
				break;
			case 'shortdatetime':
				$format = $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_SHORT);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_TIME_SEP);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME_SEC);
				$date = $this->_date->format($format);
				break;
			case 'datetime':
				$format = $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_TIME_SEP);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME);
				$date = $this->_date->format($format);
				break;
			case 'datetime_s':
				$format = $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_TIME_SEP);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME_SEC);
				$date = $this->_date->format($format);
				break;
			case 'longdate':
				$date = $this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_LONG));
				break;
			case 'longdatetime':
				$format = $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_LONG);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_TIME_SEP);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME);
				$date = $this->_date->format($format);
				break;
			case 'longdatetime_s':
				$format = $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_LONG);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE_TIME_SEP);
				$format .= $this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME_SEC);
				$date = $this->_date->format($format);
				break;
			case 'time':
				$date = $this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME));
				break;
			case 'time_s':
				$date = $this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME_SEC));
				break;

			default:
				$date = $this->_date->format($format);
				break;
		}

		if($replace_wm)
		{
			if($weekdays === null)
			{
				PLIB_Helper::error('As it seems you\'ve called this method before the required '
					.'language-entries have been loaded');
			}
			
			$weekday = $this->_date->format('w');
			$month = $this->_date->format('m');
			$date = str_replace('%W',$weekdays[(int)$weekday],$date);
			$date = str_replace('%M',$months[(int)$month],$date);
		}

		return $date;
	}

	/**
	 * Builds a date in the default format
	 *
	 * @param boolean $show_time do you want to show the time?
	 * @param boolean $relative do you want to show a relative date like "Today", "2 minutes ago", ...
	 * 	if applicable? Note that this will be relative to the output-timezone, NOT to the
	 * 	user-timezone
	 * @return string the date-string
	 */
	public function to_date($show_time = true,$relative = true)
	{
		if($relative)
		{
			$date = $this->_date->format('dmY');
			if($date == PLIB_Date::$_today)
			{
				if($show_time)
				{
					// generate timestamp for now and this date
					$ts = PLIB_Date::get_current_timestamp();
					$thists = $this->to_timestamp();
					
					$diff = $ts - $thists;
					if($diff >= 0 && $diff < 3600)
					{
						$ago = (int)($diff / 60);
						if($ago == 1)
							return '<b>'.$this->locale->lang('1_minute_ago').'</b>';
						if($ago < 1)
							return '<b>'.sprintf($this->locale->lang('x_seconds_ago'),$diff).'</b>';

						return '<b>'.sprintf($this->locale->lang('x_minutes_ago'),$ago).'</b>';
					}
				}

				$string = '<b>'.$this->locale->lang('today').'</b>';
			}
			else if($date == PLIB_Date::$_yesterday)
				$string = $this->locale->lang('yesterday');
			else if($date == PLIB_Date::$_tomorrow)
				$string = $this->locale->lang('tomorrow');
			else
				$string = $this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE));
		}
		else
			$string = $this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_DATE));

		if($show_time)
			$string .= ', '.$this->_date->format($this->locale->get_dateformat(PLIB_Locale::FORMAT_TIME));

		return $string;
	}
	
	protected function _get_print_vars()
	{
		// we provide the fields this way because DateTime has no __toString()-method
		$vars = get_object_vars($this);
		$vars['_date'] = 'DateTime['.$this->to_format(DATE_RFC2822).']';
		return $vars;
	}
}
?>