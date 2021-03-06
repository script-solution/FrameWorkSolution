<?php
/**
 * Contains the FWS_GD_Circle test
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
 * FWS_GD_Circle test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_GD_Circle extends FWS_Test_Case
{
	/**
	 * @var FWS_GD_Circle
	 */
	private $_circle;

	/**
	 * Prepares the environment before running a test.
	 */
	public function set_up()
	{
		$this->_circle = new FWS_GD_Circle(new FWS_GD_Point(2,2),1);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	public function tear_down()
	{
		$this->_circle = null;
	}

	/**
	 * Tests FWS_GD_Circle->contains_circle()
	 */
	public function testContains_circle()
	{
		// circle inside
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,2),0.5);
		self::assert_true($this->_circle->contains_circle($circle));
		
		// circle inside at the border
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,1.5),0.5);
		self::assert_true($this->_circle->contains_circle($circle));
		
		// circle intersecting
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,1),2);
		self::assert_false($this->_circle->contains_circle($circle));
		
		// outside
		$circle = new FWS_GD_Circle(new FWS_GD_Point(0,0),1);
		self::assert_false($this->_circle->contains_circle($circle));
	}

	/**
	 * Tests FWS_GD_Circle->contains_point()
	 */
	public function testContains_point()
	{
		// inside
		$p = new FWS_GD_Point(2,2);
		self::assert_true($this->_circle->contains_point($p));
		
		// on the border
		$p = new FWS_GD_Point(2,1);
		self::assert_false($this->_circle->contains_point($p));
		
		// outside
		$p = new FWS_GD_Point(0,2);
		self::assert_false($this->_circle->contains_point($p));
	}

	/**
	 * Tests FWS_GD_Circle->contains_rect()
	 */
	public function testContains_rect()
	{
		// inside
		$rect = new FWS_GD_Rectangle(2,2,0.5,0.5);
		self::assert_true($this->_circle->contains_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(2,1,1,2);
		self::assert_false($this->_circle->contains_rect($rect));
		
		// outside
		$rect = new FWS_GD_Rectangle(0,1,1,1);
		self::assert_false($this->_circle->contains_rect($rect));
	}

	/**
	 * Tests FWS_GD_Circle->intersects_circle()
	 */
	public function testIntersects_circle()
	{
		// inside
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,2),0.5);
		self::assert_true($this->_circle->intersects_circle($circle));
		
		// greater
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,2),2);
		self::assert_true($this->_circle->intersects_circle($circle));
		
		// intersecting
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,1),1);
		self::assert_true($this->_circle->intersects_circle($circle));
		
		// intersecting
		$circle = new FWS_GD_Circle(new FWS_GD_Point(0,0),2);
		self::assert_true($this->_circle->intersects_circle($circle));
		
		// outside
		$circle = new FWS_GD_Circle(new FWS_GD_Point(0,0),1);
		self::assert_false($this->_circle->intersects_circle($circle));
	}

	/**
	 * Tests FWS_GD_Circle->intersects_rect()
	 */
	public function testIntersects_rect()
	{
		// inside
		$rect = new FWS_GD_Rectangle(1.5,1.5,1,1);
		self::assert_true($this->_circle->intersects_rect($rect));
		
		// bigger
		$rect = new FWS_GD_Rectangle(0,0,4,4);
		self::assert_true($this->_circle->intersects_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(1,2,1,1);
		self::assert_true($this->_circle->intersects_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(2,1,2,2);
		self::assert_true($this->_circle->intersects_rect($rect));
		
		// outside
		$rect = new FWS_GD_Rectangle(4,4,1,1);
		self::assert_false($this->_circle->intersects_rect($rect));
	}
}
