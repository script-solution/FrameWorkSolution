<?php
/**
 * Contains the html-error-backtrace-printer-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The HTML-implementation of the backtrace-printer
 *
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Error_BTPrinter_HTML extends FWS_Object implements FWS_Error_BTPrinter
{
	/**
	 * @see FWS_Error_BTPrinter::print_backtrace()
	 *
	 * @param array $backtrace
	 */
	public function print_backtrace($backtrace)
	{
		$res = '<div><b>Call-trace:</b> <br />'."\n";
		$res .= '<ul>'."\n";
		foreach($backtrace as $item)
		{
			$res .= '<li>';
			if(isset($item['file']) && isset($item['path']))
			{
				$rand_str = FWS_StringHelper::generate_random_key(10);
				$res .= '<a href="javascript:FWS_toggleElement(\'bt_details_'.$rand_str.'\');">';
				$res .= $item['path'].'<b>'.$item['file'].'</b></a>';
			}
			else
				$res .= '<i>Unknown</i>';
			
			if(isset($item['line']))
				$res .= ' in line <b>'.$item['line'].'</b>'."\n";
			
			if(isset($item['method']))
				$res .= ' [ Method: <b>'.$item['method'].'()</b> ]'."\n";
			else if(isset($item['function']))
				$res .= ' [ Function: <b>'.$item['function'].'()</b> ]'."\n";
			
			if(isset($item['filepart']))
			{
				$res .= '<div id="bt_details_'.$rand_str.'" style="padding: 5px; margin-top: 5px;';
				$res .= ' line-height: 15px; border: 1px dotted #AAAAAA; display: none;';
				$res .= ' font-family: courier new; white-space: pre;">';
				foreach($item['filepart'] as $no => $line)
				{
					if($no == $item['line'])
						$res .= '<span style="color: #FF0000;">';
					$res .= '<b>'.$no.'</b>   '.htmlspecialchars($line,ENT_QUOTES)."\n";
					if($no == $item['line'])
						$res .= '</span>';
				}
				$res .= '</div>'."\n";
			}
			
			$res .= '</li>'."\n";
		}
		$res .= '</ul>'."\n";
		$res .= '</div>'."\n";
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