<?php
/**
 * Contains the job-data-class
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
 * A class that represents a job to execute
 *
 * @package			FrameWorkSolution
 * @subpackage	job
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Job_Data extends FWS_Object
{
	/**
	 * The process-id
	 * 
	 * @var int
	 */
	private $pid = -1;
	/**
	 * The exit-code
	 * 
	 * @var int
	 */
	private $exitcode = -1;
	/**
	 * The command to execute
	 * 
	 * @var string
	 */
	private $cmd;
	
	/**
	 * Constructor
	 * 
	 * @param string $cmd the command
	 */
	public function __construct($cmd)
	{
		parent::__construct();
		
		if(empty($cmd))
			FWS_Helper::def_error('notempty','cmd',$cmd);
		
		$this->cmd = $cmd;
	}
	
	/**
	 * @return string the command
	 */
	public function get_command()
	{
		return $this->cmd;
	}
	
	/**
	 * @return int the process-id
	 */
	public function get_pid()
	{
		return $this->pid;
	}
	
	/**
	 * Sets the process-id
	 * 
	 * @param int $pid the process-id
	 */
	public function set_pid($pid)
	{
		if(!FWS_Helper::is_integer($pid))
			FWS_Helper::def_error('int','pid',$pid);
		$this->pid = $pid;
	}
	
	/**
	 * @return int the exit-code
	 */
	public function get_exitcode()
	{
		return $this->exitcode;
	}
	
	/**
	 * Sets the exit-code
	 * 
	 * @param int $code the exit-code
	 */
	public function set_exitcode($code)
	{
		if(!FWS_Helper::is_integer($code))
			FWS_Helper::def_error('int','code',$code);
		$this->exitcode = $code;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>