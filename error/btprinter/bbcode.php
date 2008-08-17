<?php
/**
 * Contains the bbcode-error-backtrace-printer-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The BBCode-implementation of the backtrace-printer
 *
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Error_BTPrinter_BBCode extends FWS_Object implements FWS_Error_BTPrinter
{
	/**
	 * @see FWS_Error_BTPrinter::print_backtrace()
	 *
	 * @param array $backtrace
	 */
	public function print_backtrace($backtrace)
	{
		$res = "\n".'[b]Call-trace:[/b]';
		$res .= '[list]'."\n";
		foreach($backtrace as $item)
		{
			$res .= '[*]';
			if(isset($item['file']) && isset($item['path']))
				$res .= $item['path'].'[b]'.$item['file'].'[/b]';
			else
				$res .= '[i]Unknown[/i]';
			
			if(isset($item['line']))
				$res .= ' in line [b]'.$item['line'].'[/b]'."\n";
			
			if(isset($item['method']))
				$res .= ' [ Method: [b]'.$item['method'].'()[/b] ]';
			else if(isset($item['function']))
				$res .= ' [ Function: [b]'.$item['function'].'()[/b] ]';
			
			if(isset($item['filepart']))
			{
				$res .= '[code]';
				foreach($item['filepart'] as $no => $line)
					$res .= $no."\t".htmlspecialchars($line,ENT_QUOTES)."\n";
				$res .= '[code]';
			}
			
			$res .= "\n";
		}
		$res .= '[/list]';
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