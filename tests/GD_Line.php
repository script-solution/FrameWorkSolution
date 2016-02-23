<?php
/**
 * Contains the FWS_GD_Line test
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
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
 * FWS_GD_Line test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_GD_Line extends FWS_Test_Case
{
	/**
	 * Tests FWS_GD_Line->intersects_circle()
	 */
	public function testIntersects_circle()
	{
		// line in the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(5,5),2);
		$line = new FWS_GD_Line(3,3,4,4);
		self::assert_true($line->intersects_circle($circle));
		
		// line from upperleft to lowerright through the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(27,27,143,129);
		self::assert_true($line->intersects_circle($circle));
		
		// line from lowerleft to upperright through the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(19,127,143,22);
		self::assert_true($line->intersects_circle($circle));
		
		// line from left to right through the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(12,99,143,99);
		self::assert_true($line->intersects_circle($circle));
		
		// line from top to bottom through the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(80,15,80,147);
		self::assert_true($line->intersects_circle($circle));
		
		// line on the left of the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(20,15,20,147);
		self::assert_false($line->intersects_circle($circle));
		
		// line on the right of the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(145,15,145,147);
		self::assert_false($line->intersects_circle($circle));
		
		// line at the top of the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(20,15,145,15);
		self::assert_false($line->intersects_circle($circle));
		
		// line at the bottom of the circle
		$circle = new FWS_GD_Circle(new FWS_GD_Point(84,75),50);
		$line = new FWS_GD_Line(20,140,145,140);
		self::assert_false($line->intersects_circle($circle));
	}

	/**
	 * Tests FWS_GD_Line->intersects_rect()
	 */
	public function testIntersects_rect()
	{
		$rect = new FWS_GD_Rectangle(new FWS_GD_Point(50,50),new FWS_GD_Dimension(50,50));
		
		// line in the rectangle
		$line = new FWS_GD_Line(60,60,70,70);
		self::assert_true($line->intersects_rect($rect));
		
		$line = new FWS_GD_Line(70,70,60,60);
		self::assert_true($line->intersects_rect($rect));
		
		// line from the upperleft to lowerright
		$line = new FWS_GD_Line(40,40,110,110);
		self::assert_true($line->intersects_rect($rect));
		
		// line from lowerright to upperleft
		$line = new FWS_GD_Line(120,120,30,30);
		self::assert_true($line->intersects_rect($rect));
		
		// line from left to right
		$line = new FWS_GD_Line(40,60,120,60);
		self::assert_true($line->intersects_rect($rect));
		
		// line from top to bottom
		$line = new FWS_GD_Line(70,20,70,120);
		self::assert_true($line->intersects_rect($rect));
		
		// line on the left of the rectangle
		$line = new FWS_GD_Line(30,30,30,70);
		self::assert_false($line->intersects_rect($rect));
		
		// line on the right of the rectangle
		$line = new FWS_GD_Line(120,30,120,70);
		self::assert_false($line->intersects_rect($rect));
		
		// line at the top of the rectangle
		$line = new FWS_GD_Line(20,30,120,30);
		self::assert_false($line->intersects_rect($rect));
		
		// line at the bottom of the rectangle
		$line = new FWS_GD_Line(20,130,120,130);
		self::assert_false($line->intersects_rect($rect));
	}
}
