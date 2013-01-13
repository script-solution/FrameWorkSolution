<?php
/**
 * Contains the html-error-backtrace-printer-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	error.btprinter
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
	 * @return string
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
				if(isset($item['filepart']))
					$res .= '<a href="javascript:FWS_toggleElement(\'bt_details_'.$rand_str.'\');">';
				$res .= $item['path'].'<b>'.$item['file'].'</b>';
				if(isset($item['filepart']))
					$res .= '</a>';
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