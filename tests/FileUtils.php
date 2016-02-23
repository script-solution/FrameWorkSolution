<?php
/**
 * Contains the FWS_FileUtils test
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
 * FWS_FileUtils test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_FileUtils extends FWS_Test_Case
{
	/**
	 * Tests FWS_FileUtils::ensure_trailing_slash()
	 */
	public function testEnsure_no_trailing_slash()
	{
		$path = '/home/test/bla';
		$res = FWS_FileUtils::ensure_no_trailing_slash($path);
		self::assert_equals($res,'/home/test/bla');
		
		$path = '/home/test/bla/';
		$res = FWS_FileUtils::ensure_no_trailing_slash($path);
		self::assert_equals($res,'/home/test/bla');
	}

	/**
	 * Tests FWS_FileUtils::ensure_trailing_slash()
	 */
	public function testEnsure_trailing_slash()
	{
		$path = '/home/test/bla';
		$res = FWS_FileUtils::ensure_trailing_slash($path);
		self::assert_equals($res,'/home/test/bla/');
		
		$path = '/home/test/bla/';
		$res = FWS_FileUtils::ensure_trailing_slash($path);
		self::assert_equals($res,'/home/test/bla/');
	}

	/**
	 * Tests FWS_FileUtils::get_extension()
	 */
	public function testGet_extension()
	{
		$res = FWS_FileUtils::get_extension('path/bla/myfile.txt');
		self::assert_equals($res,'txt');
		
		$res = FWS_FileUtils::get_extension('myfile.txt');
		self::assert_equals($res,'txt');
		
		$res = FWS_FileUtils::get_extension('path/bla/myfile');
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_extension('path/.bla/myfile');
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_extension('myfile');
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_extension('path/bla/myfile.foo.bar');
		self::assert_equals($res,'bar');
	}

	/**
	 * Tests FWS_FileUtils::get_name()
	 */
	public function testGet_name()
	{
		// with ext
		$res = FWS_FileUtils::get_name('path/bla/myfile.txt');
		self::assert_equals($res,'myfile.txt');
		
		$res = FWS_FileUtils::get_name('myfile.txt');
		self::assert_equals($res,'myfile.txt');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile');
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/.bla/myfile');
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('myfile');
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile.foo.bar');
		self::assert_equals($res,'myfile.foo.bar');
		
		// without ext
		$res = FWS_FileUtils::get_name('path/bla/myfile.txt',false);
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('myfile.txt',false);
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile',false);
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/.bla/myfile',false);
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('myfile',false);
		self::assert_equals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile.foo.bar',false);
		self::assert_equals($res,'myfile.foo');
	}
}
