<?php
/**
 * Contains the progress-listener-interface
 *
 * @version			$Id: listener.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The listener-interface for the progress. Contains methods to react of events like
 * the progress is finished or a cycle is finished.
 * 
 * @package			PHPLib
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Progress_Listener
{
	/**
	 * Will be called if the whole progress has been finished
	 */
	public function progress_finished();
	
	/**
	 * Will be called if the current cycle has been finished
	 *
	 * @param int $pos the position (the number of already executed operations)
	 * @param int $total the total number of operations
	 */
	public function cycle_finished($pos,$total);
}
?>