<?php
/**
 * Contains the FWS_String test
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
 * FWS_String test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_String extends FWS_Test_Case
{
	/**
	 * Tests FWS_String::Ends_with()
	 */
	public function testEnds_with()
	{
		self::assert_true(FWS_String::ends_with('input','t'));
		self::assert_true(FWS_String::ends_with('input','ut'));
		self::assert_true(FWS_String::ends_with('input','put'));
		self::assert_true(FWS_String::ends_with('input','nput'));
		self::assert_true(FWS_String::ends_with('input','input'));
		
		self::assert_false(FWS_String::ends_with('input','pu'));
	}

	/**
	 * Tests FWS_String::Is_alpha()
	 */
	public function testIs_alpha()
	{
		self::assert_true(FWS_String::is_alpha('a'));
		self::assert_true(FWS_String::is_alpha('h'));
		self::assert_true(FWS_String::is_alpha('z'));
		self::assert_true(FWS_String::is_alpha('A'));
		self::assert_true(FWS_String::is_alpha('N'));
		self::assert_true(FWS_String::is_alpha('Z'));
	}

	/**
	 * Tests FWS_String::Starts_with()
	 */
	public function testStarts_with()
	{
		self::assert_true(FWS_String::starts_with('input','i'));
		self::assert_true(FWS_String::starts_with('input','in'));
		self::assert_true(FWS_String::starts_with('input','inp'));
		self::assert_true(FWS_String::starts_with('input','inpu'));
		self::assert_true(FWS_String::starts_with('input','input'));
		
		self::assert_true(FWS_String::starts_with('input','nput',1));
		self::assert_true(FWS_String::starts_with('input','ut',3));
		
		self::assert_false(FWS_String::starts_with('input','in',1));
	}
}
