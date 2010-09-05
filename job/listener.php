<?php
/**
 * Contains the job-listener-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	job
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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