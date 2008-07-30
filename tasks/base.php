<?php
/**
 * Contains the task-base-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for all tasks
 * 
 * @package			FrameWorkSolution
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Tasks_Base extends FWS_Object
{
	/**
	 * Should run the task
	 */
	public abstract function run();
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>