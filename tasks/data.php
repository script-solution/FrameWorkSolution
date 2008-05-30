<?php
/**
 * Contains the task-storage-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The task-data. Contains all data that is required for a task
 * 
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Tasks_Data extends PLIB_FullObject
{
	/**
	 * The id of the task
	 *
	 * @var int
	 */
	private $_id;
	
	/**
	 * The file which contains the task
	 *
	 * @var string
	 */
	private $_file;
	
	/**
	 * The interval in which the task should be executed (in seconds)
	 *
	 * @var int
	 */
	private $_interval;
	
	/**
	 * The time at which the task should be executed. The format is:
	 * <pre><hours>:<minutes>:<seconds></pre>
	 * Note that the time is interpreted as GMT!
	 *
	 * @var string
	 */
	private $_time;
	
	/**
	 * The point of the of the last execution
	 *
	 * @var PLIB_Date
	 */
	private $_last_execution;
	
	/**
	 * Indicates wether the task is enabled (so that it may be executed)
	 *
	 * @var boolean
	 */
	private $_enabled;
	
	/**
	 * Constructor
	 *
	 * @param int $id the id of the task
	 * @param string $file the file of the task
	 * @param int $interval the interval in seconds
	 * @param PLIB_Date $last_execution the last-execution date
	 * @param boolean $enabled is the task enabled?
	 * @param string $time the point of time for the execution (hours:minutes:seconds)
	 */
	public function __construct($id,$file,$interval = 86400,$last_execution = null,
		$enabled = true,$time = '')
	{
		parent::__construct();
		
		if(!PLIB_Helper::is_integer($id))
			PLIB_Helper::def_error('integer','id',$id);
		
		$this->_id = $id;
		$this->set_file($file);
		$this->set_interval($interval);
		$this->set_last_execution($last_execution);
		$this->set_enabled($enabled);
		$this->set_time($time);
	}

	/**
	 * @return int the id of the task
	 */
	public final function get_id()
	{
		return $this->_id;
	}

	/**
	 * @return boolean wether the task is enabled (so that it may be executed)
	 */
	public final function is_enabled()
	{
		return $this->_enabled;
	}

	/**
	 * Sets wether the task should be enabled.
	 * 
	 * @param boolean $enabled the new value
	 */
	public final function set_enabled($enabled)
	{
		$this->_enabled = (boolean)$enabled;
	}

	/**
	 * @return string the file which contains then task.
	 */
	public final function get_file()
	{
		return $this->_file;
	}

	/**
	 * Sets the file which contains the task
	 * 
	 * @param string $file the new value
	 */
	public final function set_file($file)
	{
		if(empty($file))
			PLIB_Helper::def_error('empty','file',$file);
		
		$this->_file = $file;
	}

	/**
	 * @return int the interval in which the task should be executed (in seconds)
	 */
	public final function get_interval()
	{
		return $this->_interval ;
	}

	/**
	 * Sets the interval in which the task should be executed (in seconds)
	 * 
	 * @param int $interval the new value
	 */
	public final function set_interval($interval)
	{
		if(!PLIB_Helper::is_integer($interval))
			PLIB_Helper::def_error('integer','interval',$interval);
		
		$this->_interval = $interval;
	}

	/**
	 * @return PLIB_Date the date of the last execution (may be null!)
	 */
	public final function get_last_execution()
	{
		return $this->_last_execution;
	}

	/**
	 * Sets the last execution time
	 * 
	 * @param PLIB_Date $last_execution the new value (may be null!)
	 */
	public final function set_last_execution($last_execution)
	{
		if($last_execution !== null && !($last_execution instanceof PLIB_Date))
			PLIB_Helper::def_error('instance','last_execution','PLIB_Date',$last_execution);
		
		$this->_last_execution = $last_execution;
	}

	/**
	 * Returns the time at which the task should be executed. The format is:
	 * <pre><hours>:<minutes>:<seconds></pre>
	 * Note that the time is interpreted as GMT!
	 * May be an empty string if the time doesn't matter.
	 * 
	 * @return string the time at which the task should be executed
	 */
	public final function get_time()
	{
		return $this->_time;
	}

	/**
	 * Sets the time at which the task should be executed.
	 * May be an empty string if the time doesn't matter.
	 * 
	 * @param string $time the new value
	 */
	public final function set_time($time)
	{
		if($time && !preg_match('/^\d{2}:\d{2}:\d{2}$/',$time))
			PLIB_Helper::error('$time is invalid!');
		
		$this->_time = $time;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>