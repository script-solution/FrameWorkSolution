<?php
/**
 * Contains the job-listener-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	job
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
 * Can be implemented to get notified about finished jobs
 *
 * @package			FrameWorkSolution
 * @subpackage	job
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Job_Listener
{
	/**
	 * Will be called if the given job could not be started. Note that you may throw an exception
	 * in this method. The manager takes care, that all resources are free'd in this case.
	 * 
	 * @param FWS_Job_Data $job the job
	 */
	public function start_failed($job);
	
	/**
	 * Will be called each time before we're going to sleep
	 */
	public function before_sleep();
	
	/**
	 * Will be called each time after we woke up
	 */
	public function after_sleep();
	
	/**
	 * Will be called if a job has been finished
	 * 
	 * @param FWS_Job_Data $job the job
	 */
	public function finished($job);
}
?>