<?php
/**
 * Contains the bbcode-error-backtrace-printer-class
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
	 * @return string
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