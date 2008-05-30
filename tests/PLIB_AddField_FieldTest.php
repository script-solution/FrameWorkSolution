<?php
/**
 * Contains the PLIB_AddField_Field test
 *
 * @version			$Id: PLIB_AddField_FieldTest.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_AddField_Field test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AddField_FieldTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_AddField_Type_Int
	 */
	public function testType_Int()
	{
		$data = new PLIB_AddField_Data(1,'int',1,'name','title');
		$f = new PLIB_AddField_Type_Int($data);
		self::assertEquals($f->is_valid_value('a'),'value_invalid');
		self::assertEquals($f->is_valid_value(''),'');
		self::assertEquals($f->is_valid_value('123a'),'value_invalid');
		self::assertEquals($f->is_valid_value('123'),'');
		self::assertEquals($f->is_valid_value(0),'');
		self::assertEquals($f->is_valid_value(1),'');
		self::assertEquals($f->is_valid_value(-100),'');
		
		// required field
		$data = new PLIB_AddField_Data(1,'int',1,'name','title',1,true);
		$f = new PLIB_AddField_Type_Int($data);
		self::assertEquals($f->is_valid_value(''),'value_missing');
		self::assertEquals($f->is_valid_value(null),'value_missing');
	}
	
	/**
	 * Tests PLIB_AddField_Type_Line
	 */
	public function testType_Line()
	{
		$data = new PLIB_AddField_Data(1,'line',1,'name','title');
		$f = new PLIB_AddField_Type_Line($data);
		self::assertEquals($f->is_valid_value('abc'),'');
		self::assertEquals($f->is_valid_value(''),'');
		self::assertEquals($f->is_valid_value(123),'');
		self::assertEquals($f->is_valid_value('123'),'');
		
		// with validation
		$data = new PLIB_AddField_Data(
			1,'line',1,'name','title',1,false,'','',false,0,array(),'/^[a-z][A-Z]{3}$/'
		);
		$f = new PLIB_AddField_Type_Line($data);
		self::assertEquals($f->is_valid_value('abc'),'value_invalid');
		self::assertEquals($f->is_valid_value('aBC'),'value_invalid');
		self::assertEquals($f->is_valid_value('123'),'value_invalid');
		
		self::assertEquals($f->is_valid_value('fSFW'),'');
		self::assertEquals($f->is_valid_value('fABC'),'');
		self::assertEquals($f->is_valid_value('gSSD'),'');
		
		// required field
		$data = new PLIB_AddField_Data(1,'line',1,'name','title',1,true);
		$f = new PLIB_AddField_Type_Line($data);
		self::assertEquals($f->is_valid_value(''),'value_missing');
		self::assertEquals($f->is_valid_value(null),'value_missing');
	}
	
	/**
	 * Tests PLIB_AddField_Type_Text
	 */
	public function testType_Text()
	{
		$data = new PLIB_AddField_Data(1,'line',1,'name','title');
		$f = new PLIB_AddField_Type_Text($data);
		self::assertEquals($f->is_valid_value('abc'),'');
		self::assertEquals($f->is_valid_value(''),'');
		self::assertEquals($f->is_valid_value(123),'');
		self::assertEquals($f->is_valid_value('123'),'');
		
		// with validation
		$data = new PLIB_AddField_Data(
			1,'line',1,'name','title',1,false,'','',false,0,array(),'/^[a-z][A-Z]{3}$/'
		);
		$f = new PLIB_AddField_Type_Text($data);
		self::assertEquals($f->is_valid_value('abc'),'value_invalid');
		self::assertEquals($f->is_valid_value('aBC'),'value_invalid');
		self::assertEquals($f->is_valid_value('123'),'value_invalid');
		
		self::assertEquals($f->is_valid_value('fSFW'),'');
		self::assertEquals($f->is_valid_value('fABC'),'');
		self::assertEquals($f->is_valid_value('gSSD'),'');
		
		// required field
		$data = new PLIB_AddField_Data(1,'line',1,'name','title',1,true);
		$f = new PLIB_AddField_Type_Text($data);
		self::assertEquals($f->is_valid_value(''),'value_missing');
		self::assertEquals($f->is_valid_value(null),'value_missing');
	}
	
	/**
	 * Tests PLIB_AddField_Type_Date
	 */
	public function testType_Date()
	{
		$data = new PLIB_AddField_Data(1,'date',1,'name','title');
		$f = new PLIB_AddField_Type_Date($data);
		self::assertEquals($f->is_valid_value('a'),'value_invalid');
		self::assertEquals($f->is_valid_value(''),'value_invalid');
		self::assertEquals($f->is_valid_value('123a'),'value_invalid');
		self::assertEquals($f->is_valid_value(1),'value_invalid');
		self::assertEquals($f->is_valid_value('2008-01-43'),'value_invalid');
		self::assertEquals($f->is_valid_value('2007-02-29'),'value_invalid');
		
		self::assertEquals($f->is_valid_value('2008-01-24'),'');
		self::assertEquals($f->is_valid_value('2008-02-29'),'');
		
		// required field
		$data = new PLIB_AddField_Data(1,'date',1,'name','title',1,true);
		$f = new PLIB_AddField_Type_Date($data);
		self::assertEquals($f->is_valid_value('0000-00-00'),'value_missing');
		self::assertEquals($f->is_valid_value(null),'value_invalid');
	}
	
	/**
	 * Tests PLIB_AddField_Type_Enum
	 */
	public function testType_Enum()
	{
		$values = array('a','b','c');
		$data = new PLIB_AddField_Data(1,'enum',1,'name','title',1,false,'','',false,0,$values);
		$f = new PLIB_AddField_Type_Enum($data);
		self::assertEquals($f->is_valid_value('a'),'value_invalid');
		self::assertEquals($f->is_valid_value('b'),'value_invalid');
		self::assertEquals($f->is_valid_value('c'),'value_invalid');
		self::assertEquals($f->is_valid_value(4),'value_invalid');
		self::assertEquals($f->is_valid_value(-1),'');
		
		self::assertEquals($f->is_valid_value(0),'');
		self::assertEquals($f->is_valid_value(1),'');
		self::assertEquals($f->is_valid_value(2),'');
		
		// required field
		$data = new PLIB_AddField_Data(1,'date',1,'name','title',1,true,'','',false,0,$values);
		$f = new PLIB_AddField_Type_Enum($data);
		self::assertEquals($f->is_valid_value(-1),'value_missing');
		self::assertEquals($f->is_valid_value(0),'');
	}
}
?>