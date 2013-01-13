<?php
/**
 * Contains the error-logger-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	error
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
 * The interface for all error-loggers
 *
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Error_Logger
{
	/**
	 * Should log the given error
	 *
	 * @param int $no the error-number
	 * @param string $msg the error-message
	 * @param string $file the file in which the error occurred
	 * @param int $line the line in which the error occurred
	 * @param array $backtrace the result of debug_backtrace()
	 */
	public function log($no,$msg,$file,$line,$backtrace);
}
?>