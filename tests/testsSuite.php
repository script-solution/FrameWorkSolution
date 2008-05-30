<?php
/**
 * Contains the testsuite
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

define('PLIB_PATH',dirname(dirname(__FILE__)).'/');

// init the library
include_once(PLIB_PATH.'init.php');

/**
 * The document-implementation for the unit-test
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_UnitTest_Document extends PLIB_Document
{
	protected function _load_db()
	{
		return null;
	}

	protected function _load_msgs()
	{
		return null;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}

$doc = new PLIB_UnitTest_Document();

/**
 * The autoloader for the test-cases
 * 
 * @param string $item the item to load
 * @return boolean wether the file has been loaded
 */
function PLIB_UnitTest_autoloader($item)
{
	if(PLIB_String::ends_with($item,'Test'))
	{
		$path = PLIB_Path::lib().'tests/'.$item.'.php';
		if(is_file($path))
		{
			include($path);
			return true;
		}
	}
	
	return false;
}

PLIB_AutoLoader::register_loader('PLIB_UnitTest_autoloader');

/**
 * Static test suite.
 * 
 * @package			PHPLib
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
		$this->addTestSuite('PLIB_Array_1DimTest');
		$this->addTestSuite('PLIB_Array_2DimTest');
		$this->addTestSuite('PLIB_StringHelperTest');
		$this->addTestSuite('PLIB_StringTest');
		$this->addTestSuite('PLIB_HTML_LimitedStringTest');
		$this->addTestSuite('PLIB_KeywordHighlighterTest');
		$this->addTestSuite('PLIB_InputTest');
		$this->addTestSuite('PLIB_FileUtilsTest');
		$this->addTestSuite('PLIB_GD_ColorTest');
		$this->addTestSuite('PLIB_GD_RectangleTest');
		$this->addTestSuite('PLIB_GD_CircleTest');
		$this->addTestSuite('PLIB_GD_ColorFadeTest');
		$this->addTestSuite('PLIB_DateTest');
		$this->addTestSuite('PLIB_Array_UtilsTest');
		$this->addTestSuite('PLIB_Progress_ManagerTest');
		$this->addTestSuite('PLIB_AddField_FieldTest');
		$this->addTestSuite('PLIB_GD_LineTest');
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