<?php
/**
 * Contains the PLIB_GD_ColorFade test
 *
 * @version			$Id: PLIB_GD_ColorFadeTest.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_GD_ColorFade test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_ColorFadeTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var PLIB_GD_ColorFade
	 */
	private $_cf;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$colors = array(
			array(255,0,0),
			array(0,0,255),
			array(0,255,0),
			array(255,0,255)
		);
		$this->_cf = new PLIB_GD_ColorFade(360,120,$colors);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->_cf = null;
		parent::tearDown();
	}

	/**
	 * Tests PLIB_GD_ColorFade->get_color_at()
	 */
	public function testGet_color_at()
	{
		$c = $this->_cf->get_color_at(0);
		$c = $this->_cf->get_color_at(1);
		$c = $this->_cf->get_color_at(2);
		$c = $this->_cf->get_color_at(41);
	}

	/**
	 * Tests PLIB_GD_ColorFade->get_colors()
	 */
	public function testGet_colors()
	{
		// TODO Auto-generated PLIB_GD_ColorFadeTest->testGet_colors()
		$this->markTestIncomplete("get_colors test not implemented");
		$this->_cf->get_colors(/* parameters */);
	}

	/**
	 * Tests PLIB_GD_ColorFade->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated PLIB_GD_ColorFadeTest->test__construct()
		$this->markTestIncomplete("__construct test not implemented");
		$this->_cf->__construct(/* parameters */);
	}

	/**
	 * Tests PLIB_GD_ColorFade->__toString()
	 */
	public function test__toString()
	{
		// TODO Auto-generated PLIB_GD_ColorFadeTest->test__toString()
		$this->markTestIncomplete("__toString test not implemented");
		$this->_cf->__toString(/* parameters */);
	}
}
?>