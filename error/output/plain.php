<?php
/**
 * Contains the plain-error-output-generator-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The plain implementation of the output-generator-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Error_Output_Plain extends FWS_Object implements FWS_Error_Output
{
	/**
	 * @see FWS_Error_Output::print_error()
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
			$htmlbt = new FWS_Error_BTPrinter_Plain();
			$res .= "\nBacktrace:\n".$htmlbt->print_backtrace($backtrace);
		}
		else
		{
			$realfile = str_replace(realpath(FWS_Path::server_app()),'',$file);
			$realpath = str_replace($realfile,'',$file);
			$res .= ' in '.$realpath.$realfile.', line '.$line."\n";
		}

		return $res;
	}

	/**
	 * @see FWS_Object::get_print_vars()
	 *
	 * @return array
	 */
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>