<?php
/**
 * Contains the PLIB_Array_Utils test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_Array_Utils test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Array_UtilsTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_Array_Utils::convert_to_2d()
	 */
	public function testConvert_to_2d()
	{
		// default
		$input = array(1,2,3,4,5,6);
		$perline = 4;
		$res = PLIB_Array_Utils::convert_to_2d($input,$perline);
		self::assertEquals($res,array(array(1,2,3,4),array(5,6)));
		
		// perline = 1
		$input = array(1,2,3,4,5,6);
		$perline = 1;
		$res = PLIB_Array_Utils::convert_to_2d($input,$perline);
		self::assertEquals($res,array(array(1),array(2),array(3),array(4),array(5),array(6)));
		
		// empty
		$input = array();
		$perline = 4;
		$res = PLIB_Array_Utils::convert_to_2d($input,$perline);
		self::assertEquals($res,array());
		
		// just one row
		$input = array(1,2,3,4,5,6);
		$perline = 6;
		$res = PLIB_Array_Utils::convert_to_2d($input,$perline);
		self::assertEquals($res,array(array(1,2,3,4,5,6)));
	}

	/**
	 * Tests PLIB_Array_Utils::advanced_explode()
	 */
	public function testAdvanced_explode()
	{
		$res = PLIB_Array_Utils::advanced_explode(',','a,b,c');
		self::assertEquals($res,array('a','b','c'));
		
		$res = PLIB_Array_Utils::advanced_explode(',','a,b,c,');
		self::assertEquals($res,array('a','b','c'));
		
		$res = PLIB_Array_Utils::advanced_explode(',',',a,b,c,');
		self::assertEquals($res,array('a','b','c'));
		
		$res = PLIB_Array_Utils::advanced_explode('@','@@a');
		self::assertEquals($res,array('','a'));
		
		$res = PLIB_Array_Utils::advanced_explode('@','@@a@');
		self::assertEquals($res,array('','a'));
		
		$res = PLIB_Array_Utils::advanced_explode('abc','abcabc');
		self::assertEquals($res,array(''));
		
		$res = PLIB_Array_Utils::advanced_explode('abcabc','abc');
		self::assertEquals($res,array('abc'));
	}

	/**
	 * Tests PLIB_Array_Utils::advanced_implode()
	 */
	public function testAdvanced_implode()
	{
		$res = PLIB_Array_Utils::advanced_implode(',',array('a','b','c'));
		self::assertEquals($res,'a,b,c');
		
		$res = PLIB_Array_Utils::advanced_implode(',',array('a','b','c',''));
		self::assertEquals($res,'a,b,c');
		
		$res = PLIB_Array_Utils::advanced_implode(',',array('','a','b','c'));
		self::assertEquals($res,'a,b,c');
		
		$res = PLIB_Array_Utils::advanced_implode(',',array('','a','','b','','c',''));
		self::assertEquals($res,'a,b,c');
		
		$res = PLIB_Array_Utils::advanced_implode('aa',array('abc','def'));
		self::assertEquals($res,'abcaadef');
		
		$res = PLIB_Array_Utils::advanced_implode('aa',array());
		self::assertEquals($res,'');
	}

	/**
	 * Tests PLIB_Array_Utils::filter_2dim()
	 */
	public function testFilter_2dim()
	{
		$test1 = array(
			array('a' => 1,'b' => 2,'c' => 3),
			array('a' => 1,'b' => 2)
		);
		$test2 = array(
			array('a' => 1,'c' => 3)
		);
		$test3 = array(
			array(1,2,3),
			array('c' => 3)
		);
		
		$res = PLIB_Array_Utils::filter_2dim($test1,array('a','b'));
		self::assertEquals($res,array(array('a' => 1,'b' => 2),array('a' => 1,'b' => 2)));
		
		$res = PLIB_Array_Utils::filter_2dim($test2,array('a'));
		self::assertEquals($res,array(array('a' => 1)));
		
		$res = PLIB_Array_Utils::filter_2dim($test3,array(0,'c'));
		self::assertEquals($res,array(array(1),array('c' => 3)));
		
		$res = PLIB_Array_Utils::filter_2dim($test3,array());
		self::assertEquals($res,array(array(),array()));
		
		$res = PLIB_Array_Utils::filter_2dim(array(),array(1,2,3));
		self::assertEquals($res,array());
	}

	/**
	 * Tests PLIB_Array_Utils::get_fast_access()
	 */
	public function testGet_fast_access()
	{
		$test1 = array(1,2,3);
		$test2 = array('a','b','c');
		$test3 = array(0.5,1.2,'b');
		$test4 = array('a','b','a');
		
		$res = PLIB_Array_Utils::get_fast_access($test1);
		self::assertEquals($res,array(1 => true,2 => true,3 => true));
		
		$res = PLIB_Array_Utils::get_fast_access($test2);
		self::assertEquals($res,array('a' => true,'b' => true,'c' => true));
		
		$res = PLIB_Array_Utils::get_fast_access($test3);
		self::assertEquals($res,array(0.5 => true,1.2 => true,'b' => true));
		
		$res = PLIB_Array_Utils::get_fast_access(array());
		self::assertEquals($res,array());
		
		$res = PLIB_Array_Utils::get_fast_access($test4);
		self::assertEquals($res,array('a' => true,'b' => true));
	}

	/**
	 * Tests PLIB_Array_Utils::is_numeric()
	 */
	public function testIs_numeric()
	{
		$test1 = array(1,2,3);
		$test2 = array(-1,0.5,'a' => 4);
		$test3 = array('a',1,2,3);
		$test4 = array(1,2,'a',4);
		
		$res = PLIB_Array_Utils::is_numeric($test1);
		self::assertTrue($res);
		
		$res = PLIB_Array_Utils::is_numeric($test2);
		self::assertTrue($res);
		
		$res = PLIB_Array_Utils::is_numeric($test3);
		self::assertFalse($res);
		
		$res = PLIB_Array_Utils::is_numeric($test4);
		self::assertFalse($res);
	}
}