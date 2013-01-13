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
	if(FWS_String::ends_with($item,'Test'))
	{
		$path = FWS_Path::server_fw().'tests/'.$item.'.php';
		if(is_file($path))
		{
			include($path);
			return true;
		}
	}
	
	return false;
}

FWS_AutoLoader::register_loader('FWS_UnitTest_autoloader');

/**
 * Static test suite.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class testsSuite extends PHPUnit_Framework_TestSuite
{
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName('testsSuite');
		$this->addTestSuite('FWS_Array_1DimTest');
		$this->addTestSuite('FWS_Array_2DimTest');
		$this->addTestSuite('FWS_StringHelperTest');
		$this->addTestSuite('FWS_StringTest');
		$this->addTestSuite('FWS_HTML_LimitedStringTest');
		$this->addTestSuite('FWS_KeywordHighlighterTest');
		$this->addTestSuite('FWS_InputTest');
		$this->addTestSuite('FWS_FileUtilsTest');
		$this->addTestSuite('FWS_GD_ColorTest');
		$this->addTestSuite('FWS_GD_RectangleTest');
		$this->addTestSuite('FWS_GD_CircleTest');
		$this->addTestSuite('FWS_GD_ColorFadeTest');
		$this->addTestSuite('FWS_DateTest');
		$this->addTestSuite('FWS_Array_UtilsTest');
		$this->addTestSuite('FWS_Progress_ManagerTest');
		$this->addTestSuite('FWS_AddField_FieldTest');
		$this->addTestSuite('FWS_GD_LineTest');
		$this->addTestSuite('FWS_CSS_StyleSheetTest');
	}
	
	/**
	 * We overwrite this method to autoload the class
	 */
	public function addTestSuite($name)
	{
		new $name();
		parent::addTestSuite($name);
	}

	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		$suite = new self();
		return $suite;
	}
}
?>