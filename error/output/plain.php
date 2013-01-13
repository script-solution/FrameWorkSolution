<?php
/**
 * Contains the plain-error-output-generator-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	error.output
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
	 * @return string
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