<?php
/**
 * Contains the plain-error-backtrace-printer-class
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
	 * @return string
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