<?php
/**
 * Contains the job-data-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	job
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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