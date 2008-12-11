<?php
/**
 * Contains the FWS_GD_ColorFade test
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_GD_ColorFade test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_ColorFadeTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var FWS_GD_ColorFade
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
		$this->_cf = new FWS_GD_ColorFade(360,120,$colors);
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
	 * Tests FWS_GD_ColorFade->get_color_at()
	 */
	public function testGet_color_at()
	{
		$c = $this->_cf->get_color_at(0);
		$c = $this->_cf->get_color_at(1);
		$c = $this->_cf->get_color_at(2);
		$c = $this->_cf->get_color_at(41);
	}
}
?>