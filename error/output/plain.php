<?php
/**
 * Contains the plain-error-output-generator-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The plain implementation of the output-generator-interface
 * 
 * @package			PHPLib
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Error_Output_Plain extends PLIB_FullObject implements PLIB_Error_Output
{
	/**
	 * @see PLIB_Error_Output::print_error()
	 *
	 * @param int $no
	 * @param string $msg
	 * @param string $file
	 * @param int $line
	 * @param array $backtrace
	 */
	public function print_error($no,$msg,$file,$line,$backtrace)
	{
		$res = $msg;

		// add html-backtrace
		if($backtrace !== null)
		{
			$htmlbt = new PLIB_Error_BTPrinter_Plain();
			$res .= "\nBacktrace:\n".$htmlbt->print_backtrace($backtrace);
		}
		else
		{
			$realfile = str_replace(realpath(PLIB_Path::inner()),'',$file);
			$realpath = str_replace($realfile,'',$file);
			$res .= ' in '.$realpath.$realfile.', line '.$line."\n";
		}

		return $res;
	}

	/**
	 * @see PLIB_Object::_get_print_vars()
	 *
	 * @return array
	 */
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>