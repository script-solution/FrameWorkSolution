<?php
/**
 * Contains the FWS_KeywordHighlighter test
 *
 * @version			$Id: PLIB_KeywordHighlighterTest.php 25 2008-07-30 12:41:15Z nasmussen $
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_KeywordHighlighter test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_KeywordHighlighterTest extends PHPUnit_Framework_TestCase
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
	 * Tests FWS_KeywordHighlighter->highlight()
	 */
	public function testHighlight()
	{
		$hl = new FWS_KeywordHighlighter(array(
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