<?php
/**
 * Contains the job-manager-class
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
 * A class that manages jobs, i.e. it runs them in parallel via proc_open() and can notify
 * the user about finished jobs.
 *
 * @package			FrameWorkSolution
 * @subpackage	job
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Job_Manager extends FWS_Object
{
	/**
	 * The jobs to execute
	 * 
	 * @var array
	 */
	private $jobs = array();
	/**
	 * A job that is run after all others are finished
	 * 
	 * @var FWS_Job_Data
	 */
	private $finalizer = null;
	/**
	 * The currently running processes
	 * 
	 * @var array
	 */
	private $running = array();
	/**
	 * The listener to notify on finished jobs
	 * 
	 * @var array
	 */
	private $listener = array();
	/**
	 * The number of jobs to run in parallel
	 * 
	 * @var int
	 */
	private $parallel_count = 4;
	/**
	 * The number of microseconds to wait between the polls
	 * 
	 * @var int
	 */
	private $poll_interval = 100000;
	/**
	 * To interrupt the execution
	 * 
	 * @var bool
	 */
	private $stop = false;
	
	/**
	 * @return int the number of parallel jobs (0 = unlimited)
	 */
	public function get_parallel_count()
	{
		return $this->parallel_count;
	}
	
	/**
	 * Sets the number of parallel jobs
	 * 
	 * @param int $count the new value (0 = unlimited)
	 */
	public function set_parallel_count($count)
	{
		if(!FWS_Helper::is_integer($count) || $count < 0)
			FWS_Helper::def_error('intge0','count',$count);
		$this->parallel_count = $count;
	}
	
	/**
	 * @return int the number of microseconds to wait between polls
	 */
	public function get_poll_interval()
	{
		return $this->poll_interval;
	}
	
	/**
	 * Sets the number of microseconds to wait between polls
	 * 
	 * @param int $intv the new value
	 */
	public function set_poll_interval($intv)
	{
		if(!FWS_Helper::is_integer($intv) || $intv <= 0)
			FWS_Helper::def_error('intgt0','intv',$intv);
		$this->poll_interval = $intv;
	}
	
	/**
	 * Adds the given job to the list
	 * 
	 * @param FWS_Job_Data $job the job
	 */
	public function add_job($job)
	{
		if(!($job instanceof FWS_Job_Data))
			FWS_Helper::def_error('instance','job','FWS_Job_Data',$job);
		$this->jobs[] = $job;
	}
	
	/**
	 * Sets the given job as finalizer, i.e. as a job that will be executed after all others are
	 * finished.
	 * 
	 * @param FWS_Job_Data $job the job
	 */
	public function set_finalizer($job)
	{
		if(!($job instanceof FWS_Job_Data))
			FWS_Helper::def_error('instance','job','FWS_Job_Data',$job);
		$this->finalizer = $job;
	}
	
	/**
	 * Adds the given listener to the list
	 * 
	 * @param FWS_Job_Listener $l the listener
	 */
	public function add_listener($l)
	{
		if(!($l instanceof FWS_Job_Listener))
			FWS_Helper::def_error('instance','l','FWS_Job_Listener',$l);
		$this->listener[] = $l;
	}
	
	/**
	 * @return array the currently waiting jobs
	 */
	public function get_waiting_jobs()
	{
		return $this->jobs;
	}
	
	/**
	 * @return array the currently running jobs
	 */
	public function get_running_jobs()
	{
		$jobs = array();
		foreach($this->running as $info)
			$jobs[] = $info['job'];
		return $jobs;
	}
	
	/**
	 * Starts to execute the jobs. Runs until all jobs are finished and notifies the listeners
	 * for each finished job
	 */
	public function start()
	{
		$ex = null;
		$this->running = array();
		try
		{
			// run until someone ones to stop or we have no finalizer, no waiting jobs and no running jobs
			while(!$this->stop &&
				($this->finalizer !== null || count($this->jobs) > 0 || count($this->running) > 0))
			{
				// determine number of jobs to start
				// if all jobs are done and we've a finalizer, run it now
				if(count($this->running) == 0 && count($this->jobs) == 0 && $this->finalizer !== null)
					$count = 1;
				// run all in parallel?
				else if($this->parallel_count == 0)
					$count = count($this->jobs);
				// add so many jobs as our parallel-count allows
				else
					$count = min($this->parallel_count - count($this->running),count($this->jobs));
				
				// start jobs
				for($i = 0; $i < $count && ($this->finalizer !== null || count($this->jobs) > 0); )
				{
					$pipes = array();
					// take next job or finalizer
					if(count($this->jobs) > 0)
						$job = array_shift($this->jobs);
					else
					{
						$job = $this->finalizer;
						$this->finalizer = null;
					}
					// start and enqueue in running, if successfull
					$proc = proc_open($job->get_command(),array(),$pipes);
					if($proc === false)
						$this->notify_failure($job);
					else
					{
						// success, so add to running and store pid
						$status = proc_get_status($proc);
						$job->set_pid($status['pid']);
						$this->running[] = array('proc' => $proc,'job' => $job);
						$i++;
					}
				}
				
				// check if some processes are done
				foreach($this->running as $k => $info)
				{
					$status = proc_get_status($info['proc']);
					if(!$status['running'])
					{
						// free resources and notify the user
						proc_close($info['proc']);
						$info['job']->set_exitcode($status['exitcode']);
						$this->notify_finish($info['job']);
						unset($this->running[$k]);
					}
				}
				
				$this->notify_sleep();
				// the notify may have changed the flag..
				if($this->stop)
					break;
				usleep($this->poll_interval);
				$this->notify_wakeup();
			}
		}
		catch(Exception $e)
		{
			// the user may throw an exception in the raised events. so catch it to be able to clean up
			$ex = $e;
		}
	
		// close running procs and notify the user
		foreach($this->running as $k => $info)
		{
			proc_close($info['proc']);
			$info['job']->set_exitcode(-1);
			$this->notify_finish($info['job']);
		}
		$this->stop = false;
		
		// rethrow, if necessary
		if($ex !== null)
			throw $ex;
	}
	
	/**
	 * Stops the current job-execution
	 */
	public function stop()
	{
		$this->stop = true;
	}
	
	/**
	 * Notifies all listeners that we're going to sleep
	 */
	private function notify_sleep()
	{
		foreach($this->listener as $l)
			$l->before_sleep();
	}
	
	/**
	 * Notifies all listeners that we waked up
	 */
	private function notify_wakeup()
	{
		foreach($this->listener as $l)
			$l->after_sleep();
	}
	
	/**
	 * Notifies all listeners that the given job could not been started
	 * 
	 * @param FWS_Job_Data $job the job
	 */
	private function notify_failure($job)
	{
		foreach($this->listener as $l)
			$l->start_failed($job);
	}
	
	/**
	 * Notifies all listeners that the given job is finished
	 * 
	 * @param FWS_Job_Data $job the job
	 */
	private function notify_finish($job)
	{
		foreach($this->listener as $l)
			$l->finished($job);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>