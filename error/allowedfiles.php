<?php
/**
 * Contains the allowed-files-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface to allow applications to deny some files from being displayed from the
 * error-backtrace-printer.
 * 
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Error_AllowedFiles
{
	/**
	 * Should return wether the given file can be display (contains no sensitive information)
	 *
	 * @param string $file the file to display
	 * @return boolean true if it can be displayed
	 */
	public function can_display_file($file);
}
?>