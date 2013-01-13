<?php
/**
 * Contains the profiler-class
 * 
 * @package			FrameWorkSolution
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
 * The profiler-class measures the time and the memory-usage.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Profiler extends FWS_Object
{
	/**
	 * The memory-usage at the beginning
	 *
	 * @var int
	 */
	private $_start_mem = 0;
	
	/**
	 * Contains the start-timestamp
	 *
	 * @var integer
	 */
	private $_start_time = 0;
	
	/**
	 * Starts the profiling
	 */
	public function start()
	{
		$this->_start_time = explode(' ',microtime());
		$this->_start_mem = function_exists('memory_get_usage') ? memory_get_usage() : 0;
	}
	
	/**
	 * Determines the time from the call of {@link start} until now
	 * 
	 * @param int $accuracy the accuracy for the time (default = 6)
	 * @return float the taken time
	 */
	public function get_time($accuracy = 6)
	{
		if(!FWS_Helper::is_integer($accuracy) || $accuracy < 0)
			FWS_Helper::def_error('intge0','accuracy',$accuracy);

		$stop_time = explode(' ',microtime());
		$time = $stop_time[0] - $this->_start_time[0] + $stop_time[1] - $this->_start_time[1];
		return round($time,$accuracy);
	}
	
	/**
	 * Determines the memory-usage from the call of {@link start} until now.
	 * Note that the result may be 0 if the function memory_get_usage() is not available!
	 *
	 * @return int the memory usage in bytes
	 */
	public function get_memory_usage()
	{
		$mem = function_exists('memory_get_usage') ? memory_get_usage() : 0;
		return $mem - $this->_start_mem;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>