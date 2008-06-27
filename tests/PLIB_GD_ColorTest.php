<?php
/**
 * Contains the PLIB_GD_Color test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_GD_Color test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_ColorTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_GD_Color->get_hex()
	 */
	public function testGet_hex()
	{
		$c = new PLIB_GD_Color(1,2,3);
		self::assertEquals($c->get_hex(),'#01020300');
		
		$c = new PLIB_GD_Color('#00FF00');
		self::assertEquals($c->get_hex(false),'#00FF00');
		
		$c = new PLIB_GD_Color(1,2,3,4);
		self::assertEquals($c->get_hex(),'#01020304');
		
		$c = new PLIB_GD_Color(1,2,3,4);
		self::assertEquals($c->get_hex(false,false),'010203');
	}

	/**
	 * Tests PLIB_GD_Color->__construct()
	 */
	public function test__construct()
	{
		$c = new PLIB_GD_Color(255,255,255);
		self::assertEquals($c->get_hex(),'#FFFFFF00');
		
		$c = new PLIB_GD_Color(0,0,0);
		self::assertEquals($c->get_hex(),'#00000000');
		
		$c = new PLIB_GD_Color();
		self::assertEquals($c->get_hex(),'#00000000');
		
		$c = new PLIB_GD_Color(array(0,255,255));
		self::assertEquals($c->get_hex(),'#00FFFF00');
		
		$c = new PLIB_GD_Color('#ff00CC');
		self::assertEquals($c->get_hex(),'#FF00CC00');
		
		$c = PLIB_GD_Color::$WHITE;
		self::assertEquals($c->get_hex(),'#FFFFFF00');
		
		$c = new PLIB_GD_Color('#ff00007F');
		self::assertEquals($c->get_hex(),'#FF00007F');
		
		$c = new PLIB_GD_Color(255,0,0,0);
		self::assertEquals($c->get_hex(),'#FF000000');
		
		$c = new PLIB_GD_Color(array(255,255,255,0));
		self::assertEquals($c->get_hex(),'#FFFFFF00');
	}
}
?>