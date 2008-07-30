<?php
/**
 * Contains the testsuite
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

define('FWS_PATH',dirname(dirname(__FILE__)).'/');

// init the framework
include_once(FWS_PATH.'init.php');

/**
 * The document-implementation for the unit-test
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_UnitTest_Document extends FWS_Document
{
	protected function load_db()
	{
		return null;
	}

	protected function load_msgs()
	{
		return null;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}

$doc = new FWS_UnitTest_Document();

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