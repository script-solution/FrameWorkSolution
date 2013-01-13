<?php
/**
 * Contains the storage-interface for the progress
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress
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
 * The storage-interface for the progress. This allows multiple locations
 * to store the position.
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Progress_Storage
{
	/**
	 * Should read the current position from the corresponding location. If it does not exist
	 * (= the progress is not yet running) it should return -1
	 * 
	 * @return int the position or -1 if it is not yet running
	 */
	public function get_position();
	
	/**
	 * Should store the given position at the corresponding location
	 *
	 * @param int $pos the current position
	 */
	public function store_position($pos);
	
	/**
	 * Should clear the stored data. Will be called if we are finished.
	 */
	public function clear();
}
?>