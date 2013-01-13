<?php
/**
 * Contains the task-storage-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	tasks
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
 * The interface for all task-storage types
 *
 * @package			FrameWorkSolution
 * @subpackage	tasks
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Tasks_Storage
{
	/**
	 * Should read all avaiable tasks and return an array with all task-objects
	 *
	 * @return array an array of {@link FWS_Tasks_Data}
	 */
	public function get_tasks();
	
	/**
	 * This method will be called if multiple tasks may be run. After all tasks have been
	 * executed {@link finish()} will be run.
	 * 
	 * @see finish()
	 */
	public function start();
	
	/**
	 * Should store the given task.
	 *
	 * @param FWS_Tasks_Data $task the task to store
	 */
	public function store_task($task);
	
	/**
	 * This method will be called if {@link start()} has been called and all required tasks have
	 * been executed. For each task {@link store_task($task)} will be called but if you have to
	 * perform some finishing operation you may do this here.
	 * 
	 * @see start()
	 */
	public function finish();
}
?>