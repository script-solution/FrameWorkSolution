<?php
/**
 * Contains the storage-interface for the progress
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The storage-interface for the progress. This allows multiple locations
 * to store the position.
 * 
 * @package			PHPLib
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Progress_Storage
{
	/**
	 * Should read the current position from the corresponding location. If it does not exist
	 * (= the progress is not yet running) it should return -1
	 * 
	 * @return int the position or -1 if it is not yet running
	 */
	public function get_position();
	
	/**
	 * Should store the given position at the corresponding location
	 *
	 * @param int $pos the current position
	 */
	public function store_position($pos);
	
	/**
	 * Should clear the stored data. Will be called if we are finished.
	 */
	public function clear();
}
?>