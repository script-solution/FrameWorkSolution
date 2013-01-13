<?php
/**
 * Contains the progress-listener-interface
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
 * The listener-interface for the progress. Contains methods to react of events like
 * the progress is finished or a cycle is finished.
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Progress_Listener
{
	/**
	 * Will be called if the whole progress has been finished
	 */
	public function progress_finished();
	
	/**
	 * Will be called if the current cycle has been finished
	 *
	 * @param int $pos the position (the number of already executed operations)
	 * @param int $total the total number of operations
	 */
	public function cycle_finished($pos,$total);
}
?>