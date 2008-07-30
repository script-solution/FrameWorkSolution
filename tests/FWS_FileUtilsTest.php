<?php
/**
 * Contains the FWS_FileUtils test
 *
 * @version			$Id: PLIB_FileUtilsTest.php 25 2008-07-30 12:41:15Z nasmussen $
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_FileUtils test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_FileUtilsTest extends PHPUnit_Framework_TestCase
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
	 * Tests FWS_FileUtils::ensure_trailing_slash()
	 */
	public function testEnsure_no_trailing_slash()
	{
		$path = '/home/test/bla';
		$res = FWS_FileUtils::ensure_no_trailing_slash($path);
		self::assertEquals($res,'/home/test/bla');
		
		$path = '/home/test/bla/';
		$res = FWS_FileUtils::ensure_no_trailing_slash($path);
		self::assertEquals($res,'/home/test/bla');
	}

	/**
	 * Tests FWS_FileUtils::ensure_trailing_slash()
	 */
	public function testEnsure_trailing_slash()
	{
		$path = '/home/test/bla';
		$res = FWS_FileUtils::ensure_trailing_slash($path);
		self::assertEquals($res,'/home/test/bla/');
		
		$path = '/home/test/bla/';
		$res = FWS_FileUtils::ensure_trailing_slash($path);
		self::assertEquals($res,'/home/test/bla/');
	}

	/**
	 * Tests FWS_FileUtils::get_extension()
	 */
	public function testGet_extension()
	{
		$res = FWS_FileUtils::get_extension('path/bla/myfile.txt');
		self::assertEquals($res,'txt');
		
		$res = FWS_FileUtils::get_extension('myfile.txt');
		self::assertEquals($res,'txt');
		
		$res = FWS_FileUtils::get_extension('path/bla/myfile');
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_extension('path/.bla/myfile');
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_extension('myfile');
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_extension('path/bla/myfile.foo.bar');
		self::assertEquals($res,'bar');
	}

	/**
	 * Tests FWS_FileUtils::get_name()
	 */
	public function testGet_name()
	{
		// with ext
		$res = FWS_FileUtils::get_name('path/bla/myfile.txt');
		self::assertEquals($res,'myfile.txt');
		
		$res = FWS_FileUtils::get_name('myfile.txt');
		self::assertEquals($res,'myfile.txt');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile');
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/.bla/myfile');
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('myfile');
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile.foo.bar');
		self::assertEquals($res,'myfile.foo.bar');
		
		// without ext
		$res = FWS_FileUtils::get_name('path/bla/myfile.txt',false);
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('myfile.txt',false);
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile',false);
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/.bla/myfile',false);
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('myfile',false);
		self::assertEquals($res,'myfile');
		
		$res = FWS_FileUtils::get_name('path/bla/myfile.foo.bar',false);
		self::assertEquals($res,'myfile.foo');
	}
}
?>