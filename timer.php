<?php
/**
 * Contains the timer-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The timer-class to measure the time BS needs to render a page
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Timer extends PLIB_FullObject
{
	/**
	 * Contains the start-timestamp
	 *
	 * @var integer
	 */
	private $_start_time;

	/**
	 * Constructor
	 * Will start the timer!
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->start();
	}

	/**
	 * (re)starts the timer
	 */
	public function start()
	{
		$this->_start_time = explode(' ',microtime());
	}

	/**
	 * stops the timer and returns the measured time
	 *
	 * @param int $accuracy the number of decimal places
	 * @return string the taken time
	 */
	public function stop($accuracy = 6)
	{
		if(!PLIB_Helper::is_integer($accuracy) || $accuracy < 0)
			PLIB_Helper::def_error('intge0','accuracy',$accuracy);

		$stop_time = explode(' ',microtime());
		$time = $stop_time[0] - $this->_start_time[0] + $stop_time[1] - $this->_start_time[1];
		return round($time,$accuracy);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>