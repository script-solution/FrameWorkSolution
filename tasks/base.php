<?php
/**
 * Contains the task-base-class
 *
 * @version			$Id: base.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for all tasks
 * 
 * @package			PHPLib
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Tasks_Base extends PLIB_FullObject
{
	/**
	 * Should run the task
	 */
	public abstract function run();
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>