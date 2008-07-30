<?php
/**
 * Contains the FWS_DateTest test
 *
 * @version			$Id: PLIB_DateTest.php 25 2008-07-30 12:41:15Z nasmussen $
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_Date test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_DateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		$locale = FWS_Props::get()->locale();
		
		// ensure that we have the expected timezone
		$locale->set_timezone('Europe/Berlin');
		date_default_timezone_set('Europe/Berlin');
		parent::setUp();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Tests FWS_Date->to_date()
	 */
	public function testTo_date()
	{
		$locale = FWS_Props::get()->locale();
		
		$d = new FWS_Date();
		self::assertEquals($d->to_date(),'<b>0 seconds ago</b>');
		$d->modify('-1minute');
		self::assertEquals($d->to_date(),'<b>1 minute ago</b>');
		$d->modify('+1second');
		self::assertEquals($d->to_date(),'<b>59 seconds ago</b>');
		
		// it is relative to the output-timezone, so the result is the same
		$d = new FWS_Date('now',FWS_Date::TZ_GMT,FWS_Date::TZ_GMT);
		self::assertEquals($d->to_date(),'<b>0 seconds ago</b>');
		$d->modify('-1minute');
		self::assertEquals($d->to_date(),'<b>1 minute ago</b>');
		$d->modify('+1second');
		self::assertEquals($d->to_date(),'<b>59 seconds ago</b>');
		
		// determine offset to GMT
		// during daylightsaving-time this is 7200 and 3600 otherwise
		$tz = new DateTimeZone($locale->get_timezone());
		$offset = $tz->getOffset(new DateTime('@'.time(),new DateTimeZone('GMT')));
		
		// local 'today 0:00'
		$d = new FWS_Date(array(0,0,0),FWS_Date::TZ_USER,FWS_Date::TZ_USER);
		self::assertEquals($d->to_date(),'<b>Today</b>, 12:00 AM');
		
		// local 'today 23:59'
		$d = new FWS_Date(array(23,59,59),FWS_Date::TZ_USER,FWS_Date::TZ_USER);
		self::assertEquals($d->to_date(),'<b>Today</b>, 11:59 PM');
		
		// local 'today 0:00' viewed from GMT
		$d = new FWS_Date(array(0,0,0),FWS_Date::TZ_USER,FWS_Date::TZ_GMT);
		if($offset == 7200)
			self::assertEquals($d->to_date(),'Yesterday, 10:00 PM');
		else
			self::assertEquals($d->to_date(),'Yesterday, 11:00 PM');
		
		// local 'today 23:59' viewed from GMT
		$d = new FWS_Date(array(23,59,59),FWS_Date::TZ_USER,FWS_Date::TZ_GMT);
		if($offset == 7200)
			self::assertEquals($d->to_date(),'<b>Today</b>, 9:59 PM');
		else
			self::assertEquals($d->to_date(),'<b>Today</b>, 10:59 PM');
		
		// GMT 'today 0:00' viewed from local
		$d = new FWS_Date(array(0,0,0),FWS_Date::TZ_GMT,FWS_Date::TZ_USER);
		if($offset == 7200)
			self::assertEquals($d->to_date(),'<b>Today</b>, 2:00 AM');
		else
			self::assertEquals($d->to_date(),'<b>Today</b>, 1:00 AM');
		
		// GMT 'today 23:59' viewed from local
		$d = new FWS_Date(array(23,59,59),FWS_Date::TZ_GMT,FWS_Date::TZ_USER);
		if($offset == 7200)
			self::assertEquals($d->to_date(),'Tomorrow, 1:59 AM');
		else
			self::assertEquals($d->to_date(),'Tomorrow, 12:59 AM');
		
		// GMT 'today 0:00' viewed from GMT
		$d = new FWS_Date(array(0,0,0),FWS_Date::TZ_GMT,FWS_Date::TZ_GMT);
		self::assertEquals($d->to_date(),'<b>Today</b>, 12:00 AM');
		
		// GMT 'today 23:59' viewed from GMT
		$d = new FWS_Date(array(23,59,59),FWS_Date::TZ_GMT,FWS_Date::TZ_GMT);
		self::assertEquals($d->to_date(),'<b>Today</b>, 11:59 PM');
	}

	/**
	 * Tests FWS_Date->to_timestamp()
	 */
	public function testTo_timestamp()
	{
		$date = array(10,15,12,1,25,2008);
		list($h,$i,$s,$m,$d,$y) = $date;
		$ts = mktime($h,$i,$s,$m,$d,$y);
		$gts = gmmktime($h,$i,$s,$m,$d,$y);
		
		$d = new FWS_Date($date,FWS_Date::TZ_GMT,FWS_Date::TZ_GMT);
		self::assertEquals($d->to_timestamp(),$gts);
		
		// the output-timezone has to be irrelevant because timestamps are always returned as GMT
		$d = new FWS_Date($date,FWS_Date::TZ_GMT,FWS_Date::TZ_USER);
		self::assertEquals($d->to_timestamp(),$gts);
		
		$d = new FWS_Date($date,FWS_Date::TZ_USER,FWS_Date::TZ_GMT);
		self::assertEquals($d->to_timestamp(),$ts);
		
		// the output-timezone has to be irrelevant because timestamps are always returned as GMT
		$d = new FWS_Date($date,FWS_Date::TZ_USER,FWS_Date::TZ_USER);
		self::assertEquals($d->to_timestamp(),$ts);
	}

	/**
	 * Tests FWS_Date->__construct()
	 */
	public function test__construct()
	{
		$date = array(10,15,12,1,25,2008);
		list($h,$i,$s,$m,$d,$y) = $date;
		$ts = mktime($h,$i,$s,$m,$d,$y);
		$gts = gmmktime($h,$i,$s,$m,$d,$y);
		$df = 'Y-m-d H:i:s';
		
		$d = new FWS_Date($ts,FWS_Date::TZ_GMT,FWS_Date::TZ_GMT);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),gmdate($df,$ts));
		
		$d = new FWS_Date($ts,FWS_Date::TZ_GMT,FWS_Date::TZ_USER);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),date($df,$ts));
		
		// ts = seconds from '2008-01-25 09:15:12 +0000' to '1970-01-01 00:00:00 +0000'
		// now we assume that the timestamp is already in user-time. therefore:
		// ts = <2008-01-25 09:15:12 +0100> or <2008-01-25 08:15:12 +0000>
		// that means we will get 1 hour less compared to assuming an GMT timestamp
		$d = new FWS_Date($ts,FWS_Date::TZ_USER,FWS_Date::TZ_GMT);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),gmdate($df,strtotime('@'.$ts.' -1hour')));
		
		// the same as above, but now interpreted in user-time, therefore 1 hour less
		// compared to assuming an GMT-timestamp, but using date instead of gmdate.
		$d = new FWS_Date($ts,FWS_Date::TZ_USER,FWS_Date::TZ_USER);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),date($df,strtotime('@'.$ts.' -1hour')));
		
		
		// now the same, but using the date-array instead of the timestamp
		$d = new FWS_Date($date,FWS_Date::TZ_GMT,FWS_Date::TZ_GMT);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),gmdate($df,$gts));
		
		$d = new FWS_Date($date,FWS_Date::TZ_GMT,FWS_Date::TZ_USER);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),date($df,$gts));
		
		$d = new FWS_Date($date,FWS_Date::TZ_USER,FWS_Date::TZ_GMT);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),gmdate($df,$ts));
		
		$d = new FWS_Date($date,FWS_Date::TZ_USER,FWS_Date::TZ_USER);
		echo $d->to_format($df)."<br>";
		self::assertEquals($d->to_format($df),date($df,$ts));
		
		
		// some tests to an array as first argument
		$current = time();
		$c = explode(',',gmdate('Y,m,d,H,i,s',$current));
		self::assertEquals(
			FWS_Date::get_formated_date($df,array()),date($df,$current)
		);
		self::assertEquals(
			FWS_Date::get_formated_date($df,array($c[3])),date($df,$current)
		);
		self::assertEquals(
			FWS_Date::get_formated_date($df,array($c[3],$c[4])),date($df,$current)
		);
		self::assertEquals(
			FWS_Date::get_formated_date($df,array($c[3],$c[4],$c[5])),date($df,$current)
		);
		self::assertEquals(
			FWS_Date::get_formated_date($df,array($c[3],$c[4],$c[5],$c[1])),date($df,$current)
		);
		self::assertEquals(
			FWS_Date::get_formated_date($df,array($c[3],$c[4],$c[5],$c[1],$c[2])),date($df,$current)
		);
		self::assertEquals(
			FWS_Date::get_formated_date($df,array($c[3],$c[4],$c[5],$c[1],$c[2],$c[0])),date($df,$current)
		);
	}
}
?>