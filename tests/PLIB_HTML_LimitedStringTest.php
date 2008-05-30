<?php
/**
 * Contains the PLIB_HTML_LimitedString test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_HTML_LimitedString test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_HTML_LimitedStringTest extends PHPUnit_Framework_TestCase
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
	 * Tests PLIB_HTML_LimitedString->Get()
	 */
	public function testGet()
	{
		$lstr = new PLIB_HTML_LimitedString('test1',3);
		$res = $lstr->get();
		self::assertEquals($res,'...');
		self::assertTrue($lstr->has_cut());
		
		$lstr = new PLIB_HTML_LimitedString('test1',4);
		$res = $lstr->get();
		self::assertEquals($res,'t...');
		self::assertTrue($lstr->has_cut());
		
		$lstr = new PLIB_HTML_LimitedString('test1',5);
		$res = $lstr->get();
		self::assertEquals($res,'test1');
		self::assertFalse($lstr->has_cut());
		
		$lstr = new PLIB_HTML_LimitedString('<b>abc</b> test',5);
		$res = $lstr->get();
		self::assertEquals($res,'<b>ab...</b>');
		self::assertTrue($lstr->has_cut());
		
		$lstr = new PLIB_HTML_LimitedString('<b>abc</b> test',6);
		$res = $lstr->get();
		self::assertEquals($res,'<b>abc</b>...');
		self::assertTrue($lstr->has_cut());
		
		$lstr = new PLIB_HTML_LimitedString('<b>abc</b><ul><li>123</li><li>4567</li></ul>',7);
		$res = $lstr->get();
		self::assertEquals($res,'<b>abc</b><ul><li>1...</li></ul>');
		self::assertTrue($lstr->has_cut());
		
		$html = <<<EOF
<TABLE border="1">
	<TR>
		<TD COLSPAN="2">123</td>
		<td>456</td>
	</tr>
	<tr>
		<td>789</td>
		<td>abc</td>
		<td>def</td>
	</tr>
	<tr>
		<td>ghi</td>
		<td colspan="2">jkl</td>
	</tr>
</table>
EOF;
		$lstr = new PLIB_HTML_LimitedString($html,18);
		$res = $lstr->get();
		echo $html;
		echo $res;
		//self::assertEquals($res,$html);
		//self::assertTrue($lstr->has_cut());
	}
}
?>