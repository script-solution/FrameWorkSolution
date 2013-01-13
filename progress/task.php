<?php
/**
 * Contains the task-interface for progresses
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
 * The interface for a task that takes a long time to complete and should therefore
 * be splitted into multiple small parts.
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Progress_Task
{
	/**
	 * Should return the total number of necessary operations
	 * 
	 * @return int the total number of operations
	 */
	public function get_total_operations();
	
	/**
	 * Runs the next operations of this task
	 * 
	 * @param int $pos the current position (the number of already executed operations)
	 * @param int $ops the number of steps to perform
	 */
	public function run($pos,$ops);
}
?>