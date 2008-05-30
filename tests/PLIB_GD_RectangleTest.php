<?php
/**
 * Contains the PLIB_GD_Rectangle test
 *
 * @version			$Id: PLIB_GD_RectangleTest.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_GD_Rectangle test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_RectangleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var PLIB_GD_Rectangle
	 */
	private $_rect;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->_rect = new PLIB_GD_Rectangle(1,1,2,3);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->_rect = null;
		parent::tearDown();
	}

	/**
	 * Tests _rect->contains_circle()
	 */
	public function testContains_circle()
	{
		// inside, at the top
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,2),1);
		self::assertTrue($this->_rect->contains_circle($circle));
		
		// inside, at the bottom
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,3),0.5);
		self::assertTrue($this->_rect->contains_circle($circle));
		
		// at the lower-left corner
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(1,1),2);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// top, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,0),1);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// top, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,-1),1);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// left, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(0,1),2);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// left, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(-1,1),1);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// right, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(5,3),2);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// right, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(5,2),1);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// bottom, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,5),1);
		self::assertFalse($this->_rect->contains_circle($circle));
		
		// bottom, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,7),2);
		self::assertFalse($this->_rect->contains_circle($circle));
	}

	/**
	 * Tests _rect->contains_point()
	 */
	public function testContains_point()
	{
		// on the border
		$point = new PLIB_GD_Point(1,1);
		self::assertFalse($this->_rect->contains_point($point));
		
		// inside
		$point = new PLIB_GD_Point(2,2);
		self::assertTrue($this->_rect->contains_point($point));
		
		// inside
		$point = new PLIB_GD_Point(2,3);
		self::assertTrue($this->_rect->contains_point($point));
		
		// top
		$point = new PLIB_GD_Point(0,2);
		self::assertFalse($this->_rect->contains_point($point));
		
		// left
		$point = new PLIB_GD_Point(0,3);
		self::assertFalse($this->_rect->contains_point($point));
		
		// right
		$point = new PLIB_GD_Point(5,1);
		self::assertFalse($this->_rect->contains_point($point));
		
		// bottom
		$point = new PLIB_GD_Point(2,5);
		self::assertFalse($this->_rect->contains_point($point));
	}

	/**
	 * Tests _rect->contains_rect()
	 */
	public function testContains_rect()
	{
		// "real" inside
		$rect = new PLIB_GD_Rectangle(2,1.5,1,1);
		self::assertTrue($this->_rect->contains_rect($rect));
		
		// inside
		$rect = new PLIB_GD_Rectangle(1,1,2,2);
		self::assertTrue($this->_rect->contains_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(0,1,2,2);
		self::assertFalse($this->_rect->contains_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(2,1,2,2);
		self::assertFalse($this->_rect->contains_rect($rect));
		
		// top
		$rect = new PLIB_GD_Rectangle(1,-1,3,1);
		self::assertFalse($this->_rect->contains_rect($rect));
		
		// left
		$rect = new PLIB_GD_Rectangle(0,2,1,1);
		self::assertFalse($this->_rect->contains_rect($rect));
		
		// right
		$rect = new PLIB_GD_Rectangle(4,2,2,3);
		self::assertFalse($this->_rect->contains_rect($rect));
		
		// bottom
		$rect = new PLIB_GD_Rectangle(4,2,2,1);
		self::assertFalse($this->_rect->contains_rect($rect));
	}

	/**
	 * Tests _rect->intersects_circle()
	 */
	public function testIntersects_circle()
	{
		// inside, at the top
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,2),1);
		self::assertTrue($this->_rect->intersects_circle($circle));
		
		// inside, at the bottom
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,3),0.5);
		self::assertTrue($this->_rect->intersects_circle($circle));
		
		// at the lower-left corner
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(1,1),2);
		self::assertTrue($this->_rect->intersects_circle($circle));
		
		// top, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,0),1);
		self::assertFalse($this->_rect->intersects_circle($circle));
		
		// top, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,-1),1);
		self::assertFalse($this->_rect->intersects_circle($circle));
		
		// left, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(0,1),2);
		self::assertTrue($this->_rect->intersects_circle($circle));
		
		// left, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(-1,1),1);
		self::assertFalse($this->_rect->intersects_circle($circle));
		
		// right, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(5,3),2);
		self::assertFalse($this->_rect->intersects_circle($circle));
		
		// right, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(5,2),1);
		self::assertFalse($this->_rect->intersects_circle($circle));
		
		// bottom, touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,5),1);
		self::assertFalse($this->_rect->intersects_circle($circle));
		
		// bottom, no touch
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(2,7),2);
		self::assertFalse($this->_rect->intersects_circle($circle));
	}

	/**
	 * Tests _rect->intersects_rect()
	 */
	public function testIntersects_rect()
	{
		// "real" inside
		$rect = new PLIB_GD_Rectangle(2,1.5,1,1);
		self::assertTrue($this->_rect->intersects_rect($rect));
		
		// inside
		$rect = new PLIB_GD_Rectangle(1,1,2,2);
		self::assertTrue($this->_rect->intersects_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(0,1,2,2);
		self::assertTrue($this->_rect->intersects_rect($rect));
		
		// intersecting
		$rect = new PLIB_GD_Rectangle(2,1,2,2);
		self::assertTrue($this->_rect->intersects_rect($rect));
		
		// top
		$rect = new PLIB_GD_Rectangle(1,-1,3,1);
		self::assertFalse($this->_rect->intersects_rect($rect));
		
		// left
		$rect = new PLIB_GD_Rectangle(0,2,1,1);
		self::assertFalse($this->_rect->intersects_rect($rect));
		
		// right
		$rect = new PLIB_GD_Rectangle(4,2,2,3);
		self::assertFalse($this->_rect->intersects_rect($rect));
		
		// bottom
		$rect = new PLIB_GD_Rectangle(4,2,2,1);
		self::assertFalse($this->_rect->intersects_rect($rect));
	}

	/**
	 * Tests _rect->__construct()
	 */
	public function test__construct()
	{
		$r = new PLIB_GD_Rectangle();
		$o = new PLIB_GD_Point(0,0);
		self::assertEquals($r->get_location()->get(),$o->get());
		$o = new PLIB_GD_Dimension(0,0);
		self::assertEquals($r->get_size()->get(),$o->get());
		
		$r = new PLIB_GD_Rectangle(1,0,1,0);
		$o = new PLIB_GD_Point(1,0);
		self::assertEquals($r->get_location()->get(),$o->get());
		$o = new PLIB_GD_Dimension(1,0);
		self::assertEquals($r->get_size()->get(),$o->get());
		
		$p = new PLIB_GD_Point(1,0);
		$s = new PLIB_GD_Dimension(1,0);
		$r = new PLIB_GD_Rectangle($p,$s);
		self::assertEquals($r->get_location()->get(),$p->get());
		self::assertEquals($r->get_size()->get(),$s->get());
	}
}
?>