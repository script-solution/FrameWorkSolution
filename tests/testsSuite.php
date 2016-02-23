<?php
/**
 * Contains the testsuite
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

define('FWS_PATH',dirname(dirname(__FILE__)).'/');

// init the framework
include_once(FWS_PATH.'init.php');

/**
 * The autoloader for the test-cases
 * 
 * @param string $item the item to load
 * @return boolean wether the file has been loaded
 */
function FWS_UnitTest_autoloader($item)
{
	if(FWS_String::starts_with($item,'FWS_Tests_'))
	{
		$path = FWS_Path::server_fw().'tests/'.substr($item,10).'.php';
		if(is_file($path))
		{
			include($path);
			return true;
		}
	}
	
	return false;
}

FWS_AutoLoader::register_loader('FWS_UnitTest_autoloader');

$tests = array(
	'FWS_Tests_Array_1Dim',
	'FWS_Tests_Array_2Dim',
	'FWS_Tests_StringHelper',
	'FWS_Tests_String',
	'FWS_Tests_HTML_LimitedString',
	'FWS_Tests_KeywordHighlighter',
	'FWS_Tests_Input',
	'FWS_Tests_FileUtils',
	'FWS_Tests_GD_Color',
	'FWS_Tests_GD_Rectangle',
	'FWS_Tests_GD_Circle',
	'FWS_Tests_GD_ColorFade',
	'FWS_Tests_Date',
	'FWS_Tests_Array_Utils',
	'FWS_Tests_Progress_Manager',
	'FWS_Tests_AddField',
	'FWS_Tests_GD_Line',
	'FWS_Tests_CSS_StyleSheet',
);

$suite = new FWS_Test_Suite();
foreach($tests as $test)
	$suite->add($test);
$suite->run();
?>