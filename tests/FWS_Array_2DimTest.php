<?php
/**
 * Contains the FWS_Array_2Dim test
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_Array_2Dim test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Array_2DimTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var FWS_Array_2Dim
	 */
	private $_cache;
	
	/**
	 * The test-array
	 */
	private $_content = array(
		0 => array('f1' => 1,'f2' => 2,'f3' => 3),
		1 => array('f1' => 1),
		2 => array(),
		3 => array(12 => 'test'),
		4 => 1
	);

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$this->_cache = new FWS_Array_2Dim();
		foreach($this->_content as $k => $v)
			$this->_cache->add_element($v,$k);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->_cache = null;
		parent::tearDown();
	}

	/**
	 * Tests FWS_Array_2Dim->Element_exists_with()
	 */
	public function testElement_exists_with()
	{
		// simple, ex
		$ex = $this->_cache->element_exists_with(array('f1' => 1),FWS_Array_2Dim::LINK_AND);
		self::assertTrue($ex);
		
		// simple, not ex
		$ex = $this->_cache->element_exists_with(array('f2' => 1),FWS_Array_2Dim::LINK_AND);
		self::assertFalse($ex);
		
		// multiple and, ex
		$ex = $this->_cache->element_exists_with(
			array('f1' => 1,'f2' => 2),FWS_Array_2Dim::LINK_AND
		);
		self::assertTrue($ex);
		
		// multiple or, ex
		$ex = $this->_cache->element_exists_with(
			array('f1' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assertTrue($ex);
		
		// multiple or, not ex
		$ex = $this->_cache->element_exists_with(
			array('f5' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assertFalse($ex);
	}

	/**
	 * Tests FWS_Array_2Dim->Get_element_with()
	 */
	public function testGet_element_with()
	{
		$e = $this->_cache->get_element_with(array('f1' => 1),FWS_Array_2Dim::LINK_AND);
		self::assertEquals($e,array('f1' => 1,'f2' => 2,'f3' => 3));
		
		$e = $this->_cache->get_element_with(
			array('f3' => 3,12 => 'test'),FWS_Array_2Dim::LINK_OR
		);
		self::assertEquals($e,array('f1' => 1,'f2' => 2,'f3' => 3));
	}

	/**
	 * Tests FWS_Array_2Dim->Get_elements_with()
	 */
	public function testGet_elements_with()
	{
		// multiple or
		$e = $this->_cache->get_elements_with(
			array('f3' => 3,12 => 'test'),FWS_Array_2Dim::LINK_OR
		);
		self::assertEquals(array_keys($e),array(0,3));
		
		// multiple and
		$e = $this->_cache->get_elements_with(
			array('f3' => 3,12 => 'test'),FWS_Array_2Dim::LINK_AND
		);
		self::assertEquals(array_keys($e),array());
		
		// single
		$e = $this->_cache->get_elements_with(
			array('f1' => 1),FWS_Array_2Dim::LINK_AND
		);
		self::assertEquals(array_keys($e),array(0,1));
	}

	/**
	 * Tests FWS_Array_2Dim->Get_key_with()
	 */
	public function testGet_key_with()
	{
		// simple, ex
		$ex = $this->_cache->get_key_with(array('f1' => 1),FWS_Array_2Dim::LINK_AND);
		self::assertEquals($ex,0);
		
		// simple, not ex
		$ex = $this->_cache->get_key_with(array('f2' => 1),FWS_Array_2Dim::LINK_AND);
		self::assertNull($ex);
		
		// multiple and, ex
		$ex = $this->_cache->get_key_with(
			array('f1' => 1,'f2' => 2),FWS_Array_2Dim::LINK_AND
		);
		self::assertEquals($ex,0);
		
		// multiple or, ex
		$ex = $this->_cache->get_key_with(
			array('f1' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assertEquals($ex,0);
		
		// multiple or, not ex
		$ex = $this->_cache->get_key_with(
			array('f5' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assertNull($ex);
	}

	/**
	 * Tests FWS_Array_2Dim->Set_element_field()
	 */
	public function testSet_element_field()
	{
		$this->_cache->set_element_field(2,'abc','def');
		$this->_content[2]['abc'] = 'def';
		self::assertEquals($this->_cache->get_elements(),$this->_content);
		
		$this->_cache->set_element_field(5,'test','test');
		self::assertEquals($this->_cache->get_elements(),$this->_content);
	}
}
?>