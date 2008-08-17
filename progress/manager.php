<?php
/**
 * Contains the progress-manager
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This is the central class in the progress-package. It controls the progress.
 * By "progress" is meant a long task that can't be executed all in one and will
 * therefore be splitted into multiple parts which are each executable in a reasonable
 * time.
 * <br>
 * The manager takes a storage-object which is responsible for storing the current position.
 * Additionally you may (or should if this should make sense ;)) add listeners to get informed
 * about the current state of the operation.
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Progress_Manager extends FWS_Object
{
	/**
	 * The storage-object for the position
	 *
	 * @var FWS_Progress_Storage
	 */
	private $_storage;
	
	/**
	 * An array of listeners for this progress-manager
	 *
	 * @var array
	 */
	private $_listener = array();
	
	/**
	 * The number of operations per cycle
	 *
	 * @var integer
	 */
	private $_ops_per_cycle = 100;
	
	/**
	 * The current position of the task
	 *
	 * @var int
	 */
	private $_position = -1;
	
	/**
	 * The total number of operations
	 *
	 * @var int
	 */
	private $_total = 0;
	
	/**
	 * Indicates wether the task has been finished
	 *
	 * @var boolean
	 */
	private $_finished = false;
	
	/**
	 * Constructor
	 *
	 * @param FWS_Progress_Storage $storage the storage-implementation
	 */
	public function __construct($storage)
	{
		parent::__construct();
		
		if(!($storage instanceof FWS_Progress_Storage))
			FWS_Helper::def_error('instance','storage','FWS_Progress_Storage',$storage);
		
		$this->_storage = $storage;
	}
	
	/**
	 * Adds the given listener to the list so that it will be informed if something happened.
	 *
	 * @param FWS_Progress_Listener $l the listener
	 */
	public function add_listener($l)
	{
		if(!($l instanceof FWS_Progress_Listener))
			FWS_Helper::def_error('instance','l','FWS_Progress_Listener',$l);
		
		$this->_listener[] = $l;
	}

	/**
	 * @return integer the number of operations per cycle
	 */
	public function get_ops_per_cycle()
	{
		return $this->_ops_per_cycle;
	}

	/**
	 * Sets the number of operations per cycle.
	 * 
	 * @param integer $value the new value
	 */
	public function set_ops_per_cycle($value)
	{
		if(!FWS_Helper::is_integer($value) || $value <= 0)
			FWS_Helper::def_error('intgt0','value',$value);
		
		$this->_ops_per_cycle = $value;
	}
	
	/**
	 * Returns the percentage of completion. This method should be called <b>after</b>
	 * #run_task()!
	 * 
	 * @return float the percentage of completion
	 */
	public function get_percentage()
	{
		if($this->_finished)
			return 100;
		if($this->_position == -1)
			return 0;
		
		return min(100,$this->_total > 0 ? (100 / ($this->_total / $this->_position)) : 100);
	}
	
	/**
	 * @return int the current position (the number of already executed operations)
	 */
	public function get_position()
	{
		return $this->_position;
	}
	
	/**
	 * Tests wether the progress is currently running, that means it has been started but is
	 * not finished.
	 *
	 * @return boolean true if it is running
	 */
	public function is_running()
	{
		return !$this->_finished && $this->_storage->get_position() != -1;
	}
	
	/**
	 * @return boolean wether the task is finished
	 */
	public function is_finished()
	{
		return $this->_finished;
	}
	
	/**
	 * Runs the given task.
	 *
	 * @param FWS_Progress_Task $task the task to run
	 */
	public function run_task($task)
	{
		if(!($task instanceof FWS_Progress_Task))
			FWS_Helper::def_error('instance','task','FWS_Progress_Task',$task);
		
		$this->_total = $task->get_total_operations();
		$position = $this->_storage->get_position();
		if($position == -1)
			$position = 0;
		
		$task->run($position,$this->_ops_per_cycle);
		
		// are we finished?
		if($position + $this->_ops_per_cycle >= $this->_total)
		{
			// clear up and inform the listeners
			$this->_position = -1;
			$this->_finished = true;
			$this->_storage->clear();
			foreach($this->_listener as $l)
				$l->progress_finished();
		}
		else
		{
			// store the position
			$this->_position = $position + $this->_ops_per_cycle;
			$this->_storage->store_position($this->_position);
			foreach($this->_listener as $l)
				$l->cycle_finished($position,$this->_total);
		}
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>