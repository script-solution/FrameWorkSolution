<?php
/**
 * Contains the FWS_HTML_LimitedString test
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
 * FWS_HTML_LimitedString test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_HTML_LimitedString extends FWS_Test_Case
{
	/**
	 * Tests FWS_HTML_LimitedString->Get()
	 */
	public function testGet()
	{
		$lstr = new FWS_HTML_LimitedString('test1',3);
		$res = $lstr->get();
		self::assert_equals($res,'...');
		self::assert_true($lstr->has_cut());
		
		$lstr = new FWS_HTML_LimitedString('test1',4);
		$res = $lstr->get();
		self::assert_equals($res,'t...');
		self::assert_true($lstr->has_cut());
		
		$lstr = new FWS_HTML_LimitedString('test1',5);
		$res = $lstr->get();
		self::assert_equals($res,'test1');
		self::assert_false($lstr->has_cut());
		
		$lstr = new FWS_HTML_LimitedString('<b>abc</b> test',5);
		$res = $lstr->get();
		self::assert_equals($res,'<b>ab...</b>');
		self::assert_true($lstr->has_cut());
		
		$lstr = new FWS_HTML_LimitedString('<b>abc</b> test',6);
		$res = $lstr->get();
		self::assert_equals($res,'<b>abc</b>...');
		self::assert_true($lstr->has_cut());
		
		$lstr = new FWS_HTML_LimitedString('<b>abc</b><ul><li>123</li><li>4567</li></ul>',7);
		$res = $lstr->get();
		self::assert_equals($res,'<b>abc</b><ul><li>1...</li></ul>');
		self::assert_true($lstr->has_cut());
		
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
		$lstr = new FWS_HTML_LimitedString($html,18);
		$res = $lstr->get();
		// echo $html;
		// echo $res;
		// self::assert_equals($res,$html);
		// self::assert_true($lstr->has_cut());
	}
}
