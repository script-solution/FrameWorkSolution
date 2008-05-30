<?php
/**
 * Contains the PLIB_KeywordHighlighter test
 *
 * @version			$Id: PLIB_KeywordHighlighterTest.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_KeywordHighlighter test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_KeywordHighlighterTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_KeywordHighlighter->highlight()
	 */
	public function testHighlight()
	{
		$hl = new PLIB_KeywordHighlighter(array(
			'abc','test','bla','amp'
		),'<b>','</b>');
		$res = $hl->highlight('das ist mein text');
		self::assertEquals($res,'das ist mein text');
		
		$res = $hl->highlight('abc;def;ghi');
		self::assertEquals($res,'<b>abc</b>;def;ghi');
		
		$res = $hl->highlight('abctestbla');
		self::assertEquals($res,'<b>abc</b><b>test</b><b>bla</b>');
		
		$res = $hl->highlight('&amp;abc, das geht aber nicht ;)');
		self::assertEquals($res,'&amp;<b>abc</b>, das geht aber nicht ;)');
	}
}
?>