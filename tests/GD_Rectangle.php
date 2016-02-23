<?php
/**
 * Contains the FWS_GD_Rectangle test
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
 * FWS_GD_Rectangle test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_GD_Rectangle extends FWS_Test_Case
{
	/**
	 * @var FWS_GD_Rectangle
	 */
	private $_rect;

	/**
	 * Prepares the environment before running a test.
	 */
	public function set_up()
	{
		$this->_rect = new FWS_GD_Rectangle(1,1,2,3);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	public function tear_down()
	{
		$this->_rect = null;
	}

	/**
	 * Tests _rect->contains_circle()
	 */
	public function testContains_circle()
	{
		// inside, at the top
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,2),1);
		self::assert_true($this->_rect->contains_circle($circle));
		
		// inside, at the bottom
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,3),0.5);
		self::assert_true($this->_rect->contains_circle($circle));
		
		// at the lower-left corner
		$circle = new FWS_GD_Circle(new FWS_GD_Point(1,1),2);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// top, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,0),1);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// top, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,-1),1);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// left, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(0,1),2);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// left, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(-1,1),1);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// right, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(5,3),2);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// right, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(5,2),1);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// bottom, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,5),1);
		self::assert_false($this->_rect->contains_circle($circle));
		
		// bottom, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,7),2);
		self::assert_false($this->_rect->contains_circle($circle));
	}

	/**
	 * Tests _rect->contains_point()
	 */
	public function testContains_point()
	{
		// on the border
		$point = new FWS_GD_Point(1,1);
		self::assert_false($this->_rect->contains_point($point));
		
		// inside
		$point = new FWS_GD_Point(2,2);
		self::assert_true($this->_rect->contains_point($point));
		
		// inside
		$point = new FWS_GD_Point(2,3);
		self::assert_true($this->_rect->contains_point($point));
		
		// top
		$point = new FWS_GD_Point(0,2);
		self::assert_false($this->_rect->contains_point($point));
		
		// left
		$point = new FWS_GD_Point(0,3);
		self::assert_false($this->_rect->contains_point($point));
		
		// right
		$point = new FWS_GD_Point(5,1);
		self::assert_false($this->_rect->contains_point($point));
		
		// bottom
		$point = new FWS_GD_Point(2,5);
		self::assert_false($this->_rect->contains_point($point));
	}

	/**
	 * Tests _rect->contains_rect()
	 */
	public function testContains_rect()
	{
		// "real" inside
		$rect = new FWS_GD_Rectangle(2,1.5,1,1);
		self::assert_true($this->_rect->contains_rect($rect));
		
		// inside
		$rect = new FWS_GD_Rectangle(1,1,2,2);
		self::assert_true($this->_rect->contains_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(0,1,2,2);
		self::assert_false($this->_rect->contains_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(2,1,2,2);
		self::assert_false($this->_rect->contains_rect($rect));
		
		// top
		$rect = new FWS_GD_Rectangle(1,-1,3,1);
		self::assert_false($this->_rect->contains_rect($rect));
		
		// left
		$rect = new FWS_GD_Rectangle(0,2,1,1);
		self::assert_false($this->_rect->contains_rect($rect));
		
		// right
		$rect = new FWS_GD_Rectangle(4,2,2,3);
		self::assert_false($this->_rect->contains_rect($rect));
		
		// bottom
		$rect = new FWS_GD_Rectangle(4,2,2,1);
		self::assert_false($this->_rect->contains_rect($rect));
	}

	/**
	 * Tests _rect->intersects_circle()
	 */
	public function testIntersects_circle()
	{
		// inside, at the top
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,2),1);
		self::assert_true($this->_rect->intersects_circle($circle));
		
		// inside, at the bottom
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,3),0.5);
		self::assert_true($this->_rect->intersects_circle($circle));
		
		// at the lower-left corner
		$circle = new FWS_GD_Circle(new FWS_GD_Point(1,1),2);
		self::assert_true($this->_rect->intersects_circle($circle));
		
		// top, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,0),1);
		self::assert_false($this->_rect->intersects_circle($circle));
		
		// top, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,-1),1);
		self::assert_false($this->_rect->intersects_circle($circle));
		
		// left, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(0,1),2);
		self::assert_true($this->_rect->intersects_circle($circle));
		
		// left, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(-1,1),1);
		self::assert_false($this->_rect->intersects_circle($circle));
		
		// right, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(5,3),2);
		self::assert_false($this->_rect->intersects_circle($circle));
		
		// right, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(5,2),1);
		self::assert_false($this->_rect->intersects_circle($circle));
		
		// bottom, touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,5),1);
		self::assert_false($this->_rect->intersects_circle($circle));
		
		// bottom, no touch
		$circle = new FWS_GD_Circle(new FWS_GD_Point(2,7),2);
		self::assert_false($this->_rect->intersects_circle($circle));
	}

	/**
	 * Tests _rect->intersects_rect()
	 */
	public function testIntersects_rect()
	{
		// "real" inside
		$rect = new FWS_GD_Rectangle(2,1.5,1,1);
		self::assert_true($this->_rect->intersects_rect($rect));
		
		// inside
		$rect = new FWS_GD_Rectangle(1,1,2,2);
		self::assert_true($this->_rect->intersects_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(0,1,2,2);
		self::assert_true($this->_rect->intersects_rect($rect));
		
		// intersecting
		$rect = new FWS_GD_Rectangle(2,1,2,2);
		self::assert_true($this->_rect->intersects_rect($rect));
		
		// top
		$rect = new FWS_GD_Rectangle(1,-1,3,1);
		self::assert_false($this->_rect->intersects_rect($rect));
		
		// left
		$rect = new FWS_GD_Rectangle(0,2,1,1);
		self::assert_false($this->_rect->intersects_rect($rect));
		
		// right
		$rect = new FWS_GD_Rectangle(4,2,2,3);
		self::assert_false($this->_rect->intersects_rect($rect));
		
		// bottom
		$rect = new FWS_GD_Rectangle(4,2,2,1);
		self::assert_false($this->_rect->intersects_rect($rect));
	}

	/**
	 * Tests _rect->__construct()
	 */
	public function test__construct()
	{
		$r = new FWS_GD_Rectangle();
		$o = new FWS_GD_Point(0,0);
		self::assert_equals($r->get_location()->get(),$o->get());
		$o = new FWS_GD_Dimension(0,0);
		self::assert_equals($r->get_size()->get(),$o->get());
		
		$r = new FWS_GD_Rectangle(1,0,1,0);
		$o = new FWS_GD_Point(1,0);
		self::assert_equals($r->get_location()->get(),$o->get());
		$o = new FWS_GD_Dimension(1,0);
		self::assert_equals($r->get_size()->get(),$o->get());
		
		$p = new FWS_GD_Point(1,0);
		$s = new FWS_GD_Dimension(1,0);
		$r = new FWS_GD_Rectangle($p,$s);
		self::assert_equals($r->get_location()->get(),$p->get());
		self::assert_equals($r->get_size()->get(),$s->get());
	}
}
