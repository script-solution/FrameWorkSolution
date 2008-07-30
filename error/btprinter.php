<?php
/**
 * Contains the backtrace-printer-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all backtrace-printers
 *
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Error_BTPrinter
{
	/**
	 * Should print the given backtrace
	 *
	 * @param array $backtrace the backtrace in the following form:
	 * 	<code>
	 * 	array(
	 * 		'path' => <path>,
	 * 		'file' => <file>,
	 * 		'line' => <line>,
	 * 		'method' => <method>,
	 * 		'function' => <function>,
	 * 		'filepart' => array(
	 * 			<lineNumber> => <line>,
	 * 			...
	 * 		)
	 * 	)
	 * 	</code>
	 * 	Note that all elements are optional!
	 */
	public function print_backtrace($backtrace);
}
?>