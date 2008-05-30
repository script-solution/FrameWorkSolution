<?php
/**
 * Contains the PLIB_Progress_Manager test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_Progress_Manager test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Progress_ManagerTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_Progress_Manager->__construct()
	 */
	public function test__construct()
	{
		$task = new PLIB_Progress_ManagerTestTask();
		$storage = new PLIB_Progress_ManagerTestStorage();
		$pm = new PLIB_Progress_Manager($storage);
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

class PLIB_Progress_ManagerTestStorage implements PLIB_Progress_Storage
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

class PLIB_Progress_ManagerTestTask implements PLIB_Progress_Task
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