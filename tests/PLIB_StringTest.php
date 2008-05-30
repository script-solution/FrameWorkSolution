<?php
/**
 * Contains the PLIB_String test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_String test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_StringTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_String::Ends_with()
	 */
	public function testEnds_with()
	{
		self::assertTrue(PLIB_String::ends_with('input','t'));
		self::assertTrue(PLIB_String::ends_with('input','ut'));
		self::assertTrue(PLIB_String::ends_with('input','put'));
		self::assertTrue(PLIB_String::ends_with('input','nput'));
		self::assertTrue(PLIB_String::ends_with('input','input'));
		
		self::assertFalse(PLIB_String::ends_with('input','pu'));
	}

	/**
	 * Tests PLIB_String::Is_alpha()
	 */
	public function testIs_alpha()
	{
		self::assertTrue(PLIB_String::is_alpha('a'));
		self::assertTrue(PLIB_String::is_alpha('h'));
		self::assertTrue(PLIB_String::is_alpha('z'));
		self::assertTrue(PLIB_String::is_alpha('A'));
		self::assertTrue(PLIB_String::is_alpha('N'));
		self::assertTrue(PLIB_String::is_alpha('Z'));
	}

	/**
	 * Tests PLIB_String::Starts_with()
	 */
	public function testStarts_with()
	{
		self::assertTrue(PLIB_String::starts_with('input','i'));
		self::assertTrue(PLIB_String::starts_with('input','in'));
		self::assertTrue(PLIB_String::starts_with('input','inp'));
		self::assertTrue(PLIB_String::starts_with('input','inpu'));
		self::assertTrue(PLIB_String::starts_with('input','input'));
		
		self::assertTrue(PLIB_String::starts_with('input','nput',1));
		self::assertTrue(PLIB_String::starts_with('input','ut',3));
		
		self::assertFalse(PLIB_String::starts_with('input','in',1));
	}
}
?>