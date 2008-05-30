<?php
/**
 * Contains the error-logger-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all error-loggers
 *
 * @package			PHPLib
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Error_Logger
{
	/**
	 * Should log the given error
	 *
	 * @param int $no the error-number
	 * @param string $msg the error-message
	 * @param string $file the file in which the error occurred
	 * @param int $line the line in which the error occurred
	 * @param array $backtrace the result of debug_backtrace()
	 */
	public function log($no,$msg,$file,$line,$backtrace);
}
?>