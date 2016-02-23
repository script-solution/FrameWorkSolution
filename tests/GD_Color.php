<?php
/**
 * Contains the FWS_GD_Color test
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
 * FWS_GD_Color test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_GD_Color extends FWS_Test_Case
{
	/**
	 * Tests FWS_GD_Color->get_hex()
	 */
	public function testGet_hex()
	{
		$c = new FWS_GD_Color(1,2,3);
		self::assert_equals($c->get_hex(),'#01020300');
		
		$c = new FWS_GD_Color('#00FF00');
		self::assert_equals($c->get_hex(false),'#00FF00');
		
		$c = new FWS_GD_Color(1,2,3,4);
		self::assert_equals($c->get_hex(),'#01020304');
		
		$c = new FWS_GD_Color(1,2,3,4);
		self::assert_equals($c->get_hex(false,false),'010203');
	}

	/**
	 * Tests FWS_GD_Color->__construct()
	 */
	public function test__construct()
	{
		$c = new FWS_GD_Color(255,255,255);
		self::assert_equals($c->get_hex(),'#FFFFFF00');
		
		$c = new FWS_GD_Color(0,0,0);
		self::assert_equals($c->get_hex(),'#00000000');
		
		$c = new FWS_GD_Color();
		self::assert_equals($c->get_hex(),'#00000000');
		
		$c = new FWS_GD_Color(array(0,255,255));
		self::assert_equals($c->get_hex(),'#00FFFF00');
		
		$c = new FWS_GD_Color('#ff00CC');
		self::assert_equals($c->get_hex(),'#FF00CC00');
		
		$c = FWS_GD_Color::$WHITE;
		self::assert_equals($c->get_hex(),'#FFFFFF00');
		
		$c = new FWS_GD_Color('#ff00007F');
		self::assert_equals($c->get_hex(),'#FF00007F');
		
		$c = new FWS_GD_Color(255,0,0,0);
		self::assert_equals($c->get_hex(),'#FF000000');
		
		$c = new FWS_GD_Color(array(255,255,255,0));
		self::assert_equals($c->get_hex(),'#FFFFFF00');
	}
}
