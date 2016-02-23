<?php
/**
 * Contains the FWS_Progress_Manager test
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
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
 * FWS_Progress_Manager test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_Progress_Manager extends FWS_Test_Case
{
	/**
	 * Tests FWS_Progress_Manager->__construct()
	 */
	public function test__construct()
	{
		$task = new FWS_Progress_ManagerTestTask();
		$storage = new FWS_Progress_ManagerTestStorage();
		$pm = new FWS_Progress_Manager($storage);
		$pm->set_ops_per_cycle(2);
		
		$x = 0;
		while(!$pm->is_finished())
		{
			$pm->run_task($task);
			$x++;
			self::assert_equals($task->get_x(),$x * 2);
			self::assert_equals($storage->get_position(),$pm->is_finished() ? -1 : $x * 2);
		}
		
		self::assert_equals($x,10);
		self::assert_equals($task->get_x(),20);
		self::assert_equals($storage->get_position(),-1);
	}
}

class FWS_Progress_ManagerTestStorage implements FWS_Progress_Storage
{
	private $_x = -1;
	
	public function clear()
	{
		$this->_x = -1;
	}

	public function get_position()
	{
		return $this->_x;
	}

	public function store_position($pos)
	{
		$this->_x = $pos;
	}
}

class FWS_Progress_ManagerTestTask implements FWS_Progress_Task
{
	private $_x = 0;
	
	public function get_x()
	{
		return $this->_x;
	}
	
	public function get_total_operations()
	{
		return 20;
	}
	
	public function run($pos,$ops)
	{
		for($i = 0;$i < $ops;$i++)
			++$this->_x;
	}
}
