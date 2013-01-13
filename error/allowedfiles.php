<?php
/**
 * Contains the allowed-files-interface
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
 * The interface to allow applications to deny some files from being displayed from the
 * error-backtrace-printer.
 * 
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Error_AllowedFiles
{
	/**
	 * Should return wether the given file can be display (contains no sensitive information)
	 *
	 * @param string $file the file to display
	 * @return boolean true if it can be displayed
	 */
	public function can_display_file($file);
}
?>