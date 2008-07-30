<?php
/**
 * Contains the task-interface for progresses
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for a task that takes a long time to complete and should therefore
 * be splitted into multiple small parts.
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Progress_Task
{
	/**
	 * Should return the total number of necessary operations
	 * 
	 * @return int the total number of operations
	 */
	public function get_total_operations();
	
	/**
	 * Runs the next operations of this task
	 * 
	 * @param int $pos the current position (the number of already executed operations)
	 * @param int $ops the number of steps to perform
	 */
	public function run($pos,$ops);
}
?>