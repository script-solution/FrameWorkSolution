<?php
/**
 * Contains the FWS_StringHelper test
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
 * FWS_StringHelper test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_StringHelper extends FWS_Test_Case
{
	/**
	 * Tests FWS_StringHelper::Correct_homepage()
	 */
	public function testCorrect_homepage()
	{
		$res = FWS_StringHelper::correct_homepage('http://mypage.de');
		self::assert_equals($res,'http://mypage.de');
		
		$res = FWS_StringHelper::correct_homepage('http://www.mypage.de');
		self::assert_equals($res,'http://www.mypage.de');
		
		$res = FWS_StringHelper::correct_homepage('www.mypage.de');
		self::assert_equals($res,'http://www.mypage.de');
		
		$res = FWS_StringHelper::correct_homepage('mypage.de');
		self::assert_equals($res,'http://mypage.de');
		
		$res = FWS_StringHelper::correct_homepage('http://');
		self::assert_false($res);
		
		$res = FWS_StringHelper::correct_homepage('abc');
		self::assert_false($res);
	}

	/**
	 * Tests FWS_StringHelper::Generate_random_key()
	 */
	public function testGenerate_random_key()
	{
		$res = FWS_StringHelper::generate_random_key(10);
		self::assert_true(preg_match('/^[a-z0-9]{10}$/i',$res) ? true : false);
		
		$res = FWS_StringHelper::generate_random_key(1);
		self::assert_true(preg_match('/^[a-z0-9]{1}$/i',$res) ? true : false);
	}

	/**
	 * Tests FWS_StringHelper::Get_formated_data_size()
	 */
	public function testGet_formated_data_size()
	{
		$res = FWS_StringHelper::get_formated_data_size(1024);
		self::assert_equals($res,'1.000 KiB');
		
		$res = FWS_StringHelper::get_formated_data_size(1024,'.',',');
		self::assert_equals($res,'1,000 KiB');
		
		$res = FWS_StringHelper::get_formated_data_size(10);
		self::assert_equals($res,'10.000 Byte');
		
		$res = FWS_StringHelper::get_formated_data_size(1024 * 1024 * 4);
		self::assert_equals($res,'4.000 MiB');
	}

	/**
	 * Tests FWS_StringHelper::Get_words()
	 */
	public function testGet_words()
	{
		$res = FWS_StringHelper::get_words('abc foo bar');
		self::assert_equals($res,array('abc' => true,'foo' => true,'bar' => true));
		
		$res = FWS_StringHelper::get_words('!abc, foo; bar??');
		self::assert_equals($res,array('abc' => true,'foo' => true,'bar' => true));
		
		$res = FWS_StringHelper::get_words('a,b,c');
		self::assert_equals($res,array('a' => true,'b' => true,'c' => true));
		
		$res = FWS_StringHelper::get_words("a\n\rb\t;;c");
		self::assert_equals($res,array('a' => true,'b' => true,'c' => true));
	}

	/**
	 * Tests FWS_StringHelper::Is_valid_email()
	 */
	public function testIs_valid_email()
	{
		$res = FWS_StringHelper::is_valid_email('nils@script-solution.de');
		self::assert_true($res ? true : false);
		
		$res = FWS_StringHelper::is_valid_email('nils@script-solution.de.com');
		self::assert_true($res ? true : false);
		
		$res = FWS_StringHelper::is_valid_email('');
		self::assert_false($res ? true : false);
		
		$res = FWS_StringHelper::is_valid_email('a@b.de');
		self::assert_true($res ? true : false);
		
		$res = FWS_StringHelper::is_valid_email('a@b@c.de');
		self::assert_false($res ? true : false);
	}
}
