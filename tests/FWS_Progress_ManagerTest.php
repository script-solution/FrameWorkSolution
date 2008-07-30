<?php
/**
 * Contains the FWS_Progress_Manager test
 *
 * @version			$Id: PLIB_Progress_ManagerTest.php 25 2008-07-30 12:41:15Z nasmussen $
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_Progress_Manager test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Progress_ManagerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown();
	}

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
			self::assertEquals($task->get_x(),$x * 2);
			self::assertEquals($storage->get_position(),$pm->is_finished() ? -1 : $x * 2);
		}
		
		self::assertEquals($x,10);
		self::assertEquals($task->get_x(),20);
		self::assertEquals($storage->get_position(),-1);
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
			$this->_x++;
	}
}
?>