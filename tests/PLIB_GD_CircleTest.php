<?php
/**
 * Contains the PLIB_GD_Circle test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_GD_Circle test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_CircleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var PLIB_GD_Circle
	 */
	private $_circle;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->_circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,2),1);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->_circle = null;
		parent::tearDown();
	}

	/**
	 * Tests PLIB_GD_Circle->contains_circle()
	 */
	public function testContains_circle()
	{
		// circle inside
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,2),0.5);
		self::assertTrue($this->_circle->contains_circle($circle));
		
		// circle inside at the border
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,1.5),0.5);
		self::assertTrue($this->_circle->contains_circle($circle));
		
		// circle intersecting
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,1),2);
		self::assertFalse($this->_circle->contains_circle($circle));
		
		// outside
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(0,0),1);
		self::assertFalse($this->_circle->contains_circle($circle));
	}

	/**
	 * Tests PLIB_GD_Circle->contains_point()
	 */
	public function testContains_point()
	{
		// inside
		$p = new PLIB_GD_Point(2,2);
		self::assertTrue($this->_circle->contains_point($p));
		
		// on the border
		$p = new PLIB_GD_Point(2,1);
		self::assertFalse($this->_circle->contains_point($p));
		
		// outside
		$p = new PLIB_GD_Point(0,2);
		self::assertFalse($this->_circle->contains_point($p));
	}

	/**
	 * Tests PLIB_GD_Circle->contains_rect()
	 */
	public function testContains_rect()
	{
		// inside
		$rect = new PLIB_GD_Rectangle(2,2,0.5,0.5);
		self::assertTrue($this->_circle->contains_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(2,1,1,2);
		self::assertFalse($this->_circle->contains_rect($rect));
		
		// outside
		$rect = new PLIB_GD_Rectangle(0,1,1,1);
		self::assertFalse($this->_circle->contains_rect($rect));
	}

	/**
	 * Tests PLIB_GD_Circle->intersects_circle()
	 */
	public function testIntersects_circle()
	{
		// inside
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,2),0.5);
		self::assertTrue($this->_circle->intersects_circle($circle));
		
		// greater
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,2),2);
		self::assertTrue($this->_circle->intersects_circle($circle));
		
		// intersecting
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,1),1);
		self::assertTrue($this->_circle->intersects_circle($circle));
		
		// intersecting
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(0,0),2);
		self::assertTrue($this->_circle->intersects_circle($circle));
		
		// outside
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(0,0),1);
		self::assertFalse($this->_circle->intersects_circle($circle));
	}

	/**
	 * Tests PLIB_GD_Circle->intersects_rect()
	 */
	public function testIntersects_rect()
	{
		// inside
		$rect = new PLIB_GD_Rectangle(1.5,1.5,1,1);
		self::assertTrue($this->_circle->intersects_rect($rect));
		
		// bigger
		$rect = new PLIB_GD_Rectangle(0,0,4,4);
		self::assertTrue($this->_circle->intersects_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(1,2,1,1);
		self::assertTrue($this->_circle->intersects_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(2,1,2,2);
		self::assertTrue($this->_circle->intersects_rect($rect));
		
		// outside
		$rect = new PLIB_GD_Rectangle(4,4,1,1);
		self::assertFalse($this->_circle->intersects_rect($rect));
	}
}
?>