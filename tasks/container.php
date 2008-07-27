<?php
/**
 * Contains the task-container
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */
 
/**
 * The task-container which contains all tasks, manages the execution and so on
 * 
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Tasks_Container extends PLIB_Object
{
	/**
	 * The storage-object for the tasks
	 *
	 * @var PLIB_Tasks_Storage
	 */
	private $_storage;
	
	/**
	 * The folder which contains the tasks
	 *
	 * @var string
	 */
	private $_folder;
	
	/**
	 * The prefix of all task-classes
	 *
	 * @var string
	 */
	private $_prefix;
	
	/**
	 * All tasks in an array (the data of the tasks)
	 *
	 * @var array
	 */
	private $_tasks = array();
	
	/**
	 * Contains the executable tasks
	 *
	 * @var array
	 */
	private $_task_objs = array();
	
	/**
	 * constructor
	 * 
	 * @param PLIB_Tasks_Storage $storage the storage-object
	 * @param string $folder the folder which contains all task-files
	 * @param string $prefix the prefix for all task-classes
	 */
	public function __construct($storage,$folder,$prefix = 'PLIB_Tasks_')
	{
		parent::__construct();
		
		if(!($storage instanceof PLIB_Tasks_Storage))
			PLIB_Helper::def_error('instance','storage','PLIB_Tasks_Storage',$storage);
		if(!is_dir($folder))
			PLIB_Helper::error($folder.' is no existing(?) directory!');
		
		$this->_storage = $storage;
		$this->_prefix = $prefix;
		$this->_folder = PLIB_FileUtils::ensure_trailing_slash($folder);
		
		// load task-data
		$tasks = $this->_storage->get_tasks();
		foreach($tasks as $task)
		{
			if($task instanceof PLIB_Tasks_Data)
				$this->_tasks[$task->get_id()] = $task;
			else
				PLIB_Helper::def_error('instance','task','PLIB_Tasks_Data',$task);
		}
	}
	
	/**
	 * Runs the task with given id
	 * 
	 * @param int $id the id of the task
	 */
	public final function run_task($id)
	{
		// ensure that it is loaded
		$this->_load_task($this->_tasks[$id]);
		
		// does the task exist?
		if(isset($this->_task_objs[$id]))
		{
			// run the task
			$this->_task_objs[$id]->run();
			
			// store the last-execution time
			$lastexec = $this->_get_last_execution($this->_tasks[$id]);
			$this->_tasks[$id]->set_last_execution($lastexec);
			$this->_storage->store_task($this->_tasks[$id]);
		}
	}
	
	/**
	 * Runs all necessary tasks
	 */
	public final function run_tasks()
	{
		$time = time();
		
		// fire start event
		$this->_storage->start();
		
		foreach($this->_tasks as $id => $task)
		{
			if(!$task->is_enabled())
				continue;
			
			// do we have to run the task?
			$last_exec = $task->get_last_execution();
			$timestamp = $last_exec !== null ? $last_exec->to_timestamp() : 0;
			if(($time - $task->get_interval()) > $timestamp)
				$this->run_task($id);
		}
		
		// do we have to regenerate the cache?
		$this->_storage->finish();
	}
	
	/**
	 * Loads the given task so that <var>$this->_task_objs[<id>]</var> is available.
	 *
	 * @param PLIB_Tasks_data $task the task-data
	 */
	private function _load_task($task)
	{
		if(isset($this->_task_objs[$task->get_id()]))
			return;
		
		if(is_file($this->_folder.$task->get_file()))
		{
			$name = PLIB_FileUtils::get_name($task->get_file(),false);
			include_once($this->_folder.$task->get_file());
			$class = $this->_prefix.$name;
			if(class_exists($class))
			{
				$c = new $class();
				if($c instanceof PLIB_Tasks_Base)
				{
					$this->_task_objs[$task->get_id()] = $c;
					return;
				}
			}
		}
		
		PLIB_Helper::error(
			'The task-file with id '.$task->get_id().' does not exist or is invalid!',false
		);
	}
	
	/**
	 * Determines the last execution time
	 * 
	 * @param PLIB_Tasks_Data $task the task-object
	 * @return PLIB_Date the date to store
	 */
	private function _get_last_execution($task)
	{
		$time = $task->get_time();
		if($time)
		{
			$now = new PLIB_Date('now',PLIB_Date::TZ_GMT,PLIB_Date::TZ_GMT);
			list($y,$m,$d,$h,$i) = explode(',',$now->to_format('Y,m,d,H,i'));
			list($th,$ti,$ts) = explode(':',$time);
			
			// days
			if($task->get_interval() % 86400 == 0)
				$res = PLIB_Date::get_timestamp(array($th,$ti,$ts,$m,$d,$y),PLIB_Date::TZ_GMT);
			// hours
			else if($task->get_interval() % 3600 == 0)
				$res = PLIB_Date::get_timestamp(array($h,$ti,$ts,$m,$d,$y),PLIB_Date::TZ_GMT);
			// minutes
			else
				$res = PLIB_Date::get_timestamp(array($h,$i,$ts,$m,$d,$y),PLIB_Date::TZ_GMT);
		}
		else
			$res = time();
		
		return new PLIB_Date($res);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>