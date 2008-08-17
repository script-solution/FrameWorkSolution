<?php
/**
 * Contains the plain-error-backtrace-printer-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The plain-implementation of the backtrace-printer
 *
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Error_BTPrinter_Plain extends FWS_Object implements FWS_Error_BTPrinter
{
	/**
	 * @see FWS_Error_BTPrinter::print_backtrace()
	 *
	 * @param array $backtrace
	 */
	public function print_backtrace($backtrace)
	{
		$res = '';
		foreach($backtrace as $item)
		{
			$res .= "\t";
			if(isset($item['file']) && isset($item['path']))
				$res .= $item['path'].$item['file'];
			else
				$res .= 'Unknown';
			
			if(isset($item['line']))
				$res .= ', '.$item['line'];
			
			if(isset($item['method']))
				$res .= ' ['.$item['method'].'()]';
			else if(isset($item['function']))
				$res .= ' ['.$item['function'].'()]';
			
			$res .= "\n";
		}
		return $res;
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>