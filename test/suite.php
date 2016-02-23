<?php
/**
 * Contains the testsuite class
 * 
 * @package			FrameWorkSolution
 * @subpackage	test
 *
 * Copyright (C) 2003 - 2016 Nils Asmussen
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
 * The testsuite that runs all testcases
 * 
 * @package			FrameWorkSolution
 * @subpackage	test
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Test_Suite extends FWS_Object
{
	/**
	 * The line wrap
	 *
	 * @var string
	 */
	const LINE_WRAP = PHP_SAPI == 'cli' ? "\n" : '<br />';
	
	/**
	 * The test cases in form of class names
	 *
	 * @var array
	 */
	private $_classes = array();
	
	/**
	 * Adds the given test case
	 *
	 * @param string $classname the name of the class
	 */
	public function add($classname)
	{
		$this->_classes[] = $classname;
	}
	
	/**
	 * Runs all tests by instantiating the added classes and calling all methods starting with 'test'.
	 *
	 * @return array an array with 2 elements: the # of succeeded tests and # of failed tests.
	 */
	public function run()
	{
		$succ = 0;
		$fail = 0;
		
		foreach($this->_classes as $class)
		{
			$this->testcase_starting($class);
			
			$t = new $class();
			foreach(get_class_methods($t) as $m)
			{
				if(FWS_String::starts_with($m,'test'))
				{
					$this->test_starting($class,$m);
					$t->set_up();
					
					try
					{
						$t->$m();
						$succ++;
					}
					catch(Exception $e)
					{
						$this->test_failed($class,$m,$e);
						$fail++;
					}
					
					$t->tear_down();
				}
			}
		}
		
		$this->finished($succ,$fail);
		
		return array($succ,$fail);
	}
	
	/**
	 * Is called whenever a testcase is about to be started.
	 *
	 * @param string $class the class name
	 */
	protected function testcase_starting($class)
	{
		echo "-- ".$class.":".self::LINE_WRAP;
	}
	
	/**
	 * Is called whenever a test within a testcase is about to be started.
	 *
	 * @param string $class the class name
	 * @param string $method the method name
	 */
	protected function test_starting($class,$method)
	{
		echo "   - Testing method ".$method."...".self::LINE_WRAP;
	}
	
	/**
	 * Is called whenever a test failed.
	 *
	 * @param string $class the class name
	 * @param string $method the method name
	 * @param Exception $ex the exception
	 */
	protected function test_failed($class,$method,$ex)
	{
		echo $ex.self::LINE_WRAP;
	}
	
	/**
	 * Is called whenever a testcase is finished.
	 *
	 * @param string $class the class name
	 */
	protected function testcase_finished($class)
	{
		echo self::LINE_WRAP;
	}
	
	/**
	 * Is called after all testcases are finished
	 * 
	 * @param int $succ the number of succeeded tests
	 * @param int $fail the number of failed tests
	 */
	protected function finished($succ,$fail)
	{
		echo self::LINE_WRAP;
		echo "------------------------".self::LINE_WRAP;
		echo "Total: ".$succ." / ".($succ + $fail)." succeeded".self::LINE_WRAP;
		echo "------------------------".self::LINE_WRAP;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>