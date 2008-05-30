<?php
/**
 * Contains the PLIB_GD_Line test
 *
 * @version			$Id: PLIB_GD_LineTest.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_GD_Line test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_LineTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_GD_Line->intersects_circle()
	 */
	public function testIntersects_circle()
	{
		// line in the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(5,5),2);
		$line = new PLIB_GD_Line(3,3,4,4);
		self::assertTrue($line->intersects_circle($circle));
		
		// line from upperleft to lowerright through the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(27,27,143,129);
		self::assertTrue($line->intersects_circle($circle));
		
		// line from lowerleft to upperright through the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(19,127,143,22);
		self::assertTrue($line->intersects_circle($circle));
		
		// line from left to right through the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(12,99,143,99);
		self::assertTrue($line->intersects_circle($circle));
		
		// line from top to bottom through the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(80,15,80,147);
		self::assertTrue($line->intersects_circle($circle));
		
		// line on the left of the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(20,15,20,147);
		self::assertFalse($line->intersects_circle($circle));
		
		// line on the right of the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(145,15,145,147);
		self::assertFalse($line->intersects_circle($circle));
		
		// line at the top of the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(20,15,145,15);
		self::assertFalse($line->intersects_circle($circle));
		
		// line at the bottom of the circle
		$circle = new PLIB_GD_Circle(new PLIB_GD_Point(84,75),50);
		$line = new PLIB_GD_Line(20,140,145,140);
		self::assertFalse($line->intersects_circle($circle));
	}

	/**
	 * Tests PLIB_GD_Line->intersects_rect()
	 */
	public function testIntersects_rect()
	{
		$rect = new PLIB_GD_Rectangle(new PLIB_GD_Point(50,50),new PLIB_GD_Dimension(50,50));
		
		// line in the rectangle
		$line = new PLIB_GD_Line(60,60,70,70);
		self::assertTrue($line->intersects_rect($rect));
		
		$line = new PLIB_GD_Line(70,70,60,60);
		self::assertTrue($line->intersects_rect($rect));
		
		// line from the upperleft to lowerright
		$line = new PLIB_GD_Line(40,40,110,110);
		self::assertTrue($line->intersects_rect($rect));
		
		// line from lowerright to upperleft
		$line = new PLIB_GD_Line(120,120,30,30);
		self::assertTrue($line->intersects_rect($rect));
		
		// line from left to right
		$line = new PLIB_GD_Line(40,60,120,60);
		self::assertTrue($line->intersects_rect($rect));
		
		// line from top to bottom
		$line = new PLIB_GD_Line(70,20,70,120);
		self::assertTrue($line->intersects_rect($rect));
		
		// line on the left of the rectangle
		$line = new PLIB_GD_Line(30,30,30,70);
		self::assertFalse($line->intersects_rect($rect));
		
		// line on the right of the rectangle
		$line = new PLIB_GD_Line(120,30,120,70);
		self::assertFalse($line->intersects_rect($rect));
		
		// line at the top of the rectangle
		$line = new PLIB_GD_Line(20,30,120,30);
		self::assertFalse($line->intersects_rect($rect));
		
		// line at the bottom of the rectangle
		$line = new PLIB_GD_Line(20,130,120,130);
		self::assertFalse($line->intersects_rect($rect));
	}
}
?>