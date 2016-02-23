<?php
/**
 * Contains the FWS_KeywordHighlighter test
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
 * FWS_KeywordHighlighter test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_KeywordHighlighter extends FWS_Test_Case
{
	/**
	 * Tests FWS_KeywordHighlighter->highlight()
	 */
	public function testHighlight()
	{
		$hl = new FWS_KeywordHighlighter(array(
			'abc','test','bla','amp'
		),'<b>','</b>');
		$res = $hl->highlight('das ist mein text');
		self::assert_equals($res,'das ist mein text');
		
		$res = $hl->highlight('abc;def;ghi');
		self::assert_equals($res,'<b>abc</b>;def;ghi');
		
		$res = $hl->highlight('abctestbla');
		self::assert_equals($res,'<b>abc</b><b>test</b><b>bla</b>');
		
		$res = $hl->highlight('&amp;abc, das geht aber nicht ;)');
		self::assert_equals($res,'&amp;<b>abc</b>, das geht aber nicht ;)');
	}
}
