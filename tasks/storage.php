<?php
/**
 * Contains the task-storage-interface
 *
 * @version			$Id: storage.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all task-storage types
 *
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Tasks_Storage
{
	/**
	 * Should read all avaiable tasks and return an array with all task-objects
	 *
	 * @return array an array of {@link PLIB_Tasks_Data}
	 */
	public function get_tasks();
	
	/**
	 * This method will be called if multiple tasks may be run. After all tasks have been
	 * executed {@link finish()} will be run.
	 * 
	 * @see finish()
	 */
	public function start();
	
	/**
	 * Should store the given task.
	 *
	 * @param PLIB_Tasks_Data $task the task to store
	 */
	public function store_task($task);
	
	/**
	 * This method will be called if {@link start()} has been called and all required tasks have
	 * been executed. For each task {@link store_task($task)} will be called but if you have to
	 * perform some finishing operation you may do this here.
	 * 
	 * @see start()
	 */
	public function finish();
}
?>