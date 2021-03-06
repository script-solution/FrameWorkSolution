<?php
/**
 * Contains the FWS_Array_2Dim test
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
 * FWS_Array_2Dim test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_Array_2Dim extends FWS_Test_Case
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
	public function set_up()
	{
		$this->_cache = new FWS_Array_2Dim();
		foreach($this->_content as $k => $v)
			$this->_cache->add_element($v,$k);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	public function tear_down()
	{
		$this->_cache = null;
	}

	/**
	 * Tests FWS_Array_2Dim->Element_exists_with()
	 */
	public function testElement_exists_with()
	{
		// simple, ex
		$ex = $this->_cache->element_exists_with(array('f1' => 1),FWS_Array_2Dim::LINK_AND);
		self::assert_true($ex);
		
		// simple, not ex
		$ex = $this->_cache->element_exists_with(array('f2' => 1),FWS_Array_2Dim::LINK_AND);
		self::assert_false($ex);
		
		// multiple and, ex
		$ex = $this->_cache->element_exists_with(
			array('f1' => 1,'f2' => 2),FWS_Array_2Dim::LINK_AND
		);
		self::assert_true($ex);
		
		// multiple or, ex
		$ex = $this->_cache->element_exists_with(
			array('f1' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assert_true($ex);
		
		// multiple or, not ex
		$ex = $this->_cache->element_exists_with(
			array('f5' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assert_false($ex);
	}

	/**
	 * Tests FWS_Array_2Dim->Get_element_with()
	 */
	public function testGet_element_with()
	{
		$e = $this->_cache->get_element_with(array('f1' => 1),FWS_Array_2Dim::LINK_AND);
		self::assert_equals($e,array('f1' => 1,'f2' => 2,'f3' => 3));
		
		$e = $this->_cache->get_element_with(
			array('f3' => 3,12 => 'test'),FWS_Array_2Dim::LINK_OR
		);
		self::assert_equals($e,array('f1' => 1,'f2' => 2,'f3' => 3));
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
		self::assert_equals(array_keys($e),array(0,3));
		
		// multiple and
		$e = $this->_cache->get_elements_with(
			array('f3' => 3,12 => 'test'),FWS_Array_2Dim::LINK_AND
		);
		self::assert_equals(array_keys($e),array());
		
		// single
		$e = $this->_cache->get_elements_with(
			array('f1' => 1),FWS_Array_2Dim::LINK_AND
		);
		self::assert_equals(array_keys($e),array(0,1));
	}

	/**
	 * Tests FWS_Array_2Dim->Get_key_with()
	 */
	public function testGet_key_with()
	{
		// simple, ex
		$ex = $this->_cache->get_key_with(array('f1' => 1),FWS_Array_2Dim::LINK_AND);
		self::assert_equals($ex,0);
		
		// simple, not ex
		$ex = $this->_cache->get_key_with(array('f2' => 1),FWS_Array_2Dim::LINK_AND);
		self::assert_null($ex);
		
		// multiple and, ex
		$ex = $this->_cache->get_key_with(
			array('f1' => 1,'f2' => 2),FWS_Array_2Dim::LINK_AND
		);
		self::assert_equals($ex,0);
		
		// multiple or, ex
		$ex = $this->_cache->get_key_with(
			array('f1' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assert_equals($ex,0);
		
		// multiple or, not ex
		$ex = $this->_cache->get_key_with(
			array('f5' => 1,'f3' => 4),FWS_Array_2Dim::LINK_OR
		);
		self::assert_null($ex);
	}

	/**
	 * Tests FWS_Array_2Dim->Set_element_field()
	 */
	public function testSet_element_field()
	{
		$this->_cache->set_element_field(2,'abc','def');
		$this->_content[2]['abc'] = 'def';
		self::assert_equals($this->_cache->get_elements(),$this->_content);
		
		$this->_cache->set_element_field(5,'test','test');
		self::assert_equals($this->_cache->get_elements(),$this->_content);
	}
}
