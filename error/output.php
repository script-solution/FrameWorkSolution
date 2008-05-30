<?php
/**
 * Contains the error-output-interface
 *
 * @version			$Id: output.php 548 2008-04-10 10:15:35Z nasmussen $
 * @package			PHPLib
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The error-output-interface for the different output-generators
 *
 * @package			PHPLib
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Error_Output
{
	/**
	 * Should print the given error
	 *
	 * @param int $no the error-number
	 * @param string $msg the error-message
	 * @param string $file the file in which the error occurred
	 * @param int $line the line in which the error occurred
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
	 * 	Note that all elements are optional! (may also be completely null)
	 */
	public function print_error($no,$msg,$file,$line,$backtrace);
}
?>