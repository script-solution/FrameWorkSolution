<?php
/**
 * Contains the FWS_Array_1Dim test
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
 * FWS_Array_1Dim test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_Array_1Dim extends FWS_Test_Case
{
	/**
	 * @var FWS_Array_1Dim
	 */
	private $_cache;
	
	/**
	 * The initial content of the cache
	 *
	 * @var array
	 */
	private $_content = array(
		0 => 4,
		1 => array(12),
		'a' => 3,
		'b' => 4,
		'a+b' => 0x33
	);
	
	/**
	 * The values of our test-array
	 */
	private $_content_values;
	
	/**
	 * The keys of our test-array
	 */
	private $_content_keys;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_content_keys = array_keys($this->_content);
		$this->_content_values = array_values($this->_content);
	}

	/**
	 * Prepares the environment before running a test.
	 */
	public function set_up()
	{
		$this->_cache = new FWS_Array_1Dim();
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
	 * Tests FWS_Array_1Dim->Add_element()
	 */
	public function testAdd_element()
	{
		$this->_cache->clear();
		
		$this->_cache->add_element(1);
		self::assert_equals($this->_cache->get_element_count(),1);
		
		$this->_cache->add_element(2,'a');
		self::assert_equals($this->_cache->get_element_count(),2);
		self::assert_equals($this->_cache->get_element('a'),2);
		
		$this->_cache->add_element(3,'a');
		self::assert_equals($this->_cache->get_element_count(),2);
		self::assert_equals($this->_cache->get_element('a'),3);
	}

	/**
	 * Tests FWS_Array_1Dim->Add_element_at()
	 */
	public function testAdd_element_at()
	{
		$this->_cache->clear();
		
		// insert at the beginning
		$this->_cache->add_element_at(4,0);
		self::assert_equals($this->_cache->get_element_count(),1);
		self::assert_equals($this->_cache->get_element(0,false),4);
		
		// insert at 1
		$this->_cache->add_element_at(5,1);
		self::assert_equals($this->_cache->get_element_count(),2);
		self::assert_equals($this->_cache->get_element(1,false),5);
		
		// insert at the end
		$this->_cache->add_element_at(6,$this->_cache->get_element_count());
		self::assert_equals($this->_cache->get_element_count(),3);
		self::assert_equals($this->_cache->get_element(2,false),6);
		
		// insert existing key
		$this->_cache->add_element_at(7,0,2);
		self::assert_equals($this->_cache->get_element_count(),3);
		self::assert_equals($this->_cache->get_element(0,false),7);
	}

	/**
	 * Tests FWS_Array_1Dim->Binary_search()
	 */
	public function testBinary_search()
	{
		$this->_cache->clear();
		$this->_cache->add_element(10);
		$this->_cache->add_element(3);
		$this->_cache->add_element(8);
		$this->_cache->add_element('abc');
		$this->_cache->add_element(12);
		
		// asc
		$this->_cache->sort(FWS_Array_1Dim::SORT_MODE_ELEMENTS,FWS_Array_1Dim::SORT_DIR_ASC);
		
		$key = $this->_cache->binary_search('abc',FWS_Array_1Dim::SORT_DIR_ASC);
		self::assert_equals($key,3);
		
		// desc
		$this->_cache->sort(FWS_Array_1Dim::SORT_MODE_ELEMENTS,FWS_Array_1Dim::SORT_DIR_DESC);
		
		$key = $this->_cache->binary_search(3,FWS_Array_1Dim::SORT_DIR_DESC);
		self::assert_equals($key,1);
		
		// test not existing
		$key = $this->_cache->binary_search('notexisting',FWS_Array_1Dim::SORT_DIR_DESC);
		self::assert_null($key);
	}
	
	/**
	 * Tests FWS_Array_1Dim->Clear()
	 */
	public function testClear()
	{
		$this->_cache->clear();
		
		self::assert_equals($this->_cache->get_elements(),array());
		self::assert_equals($this->_cache->get_element_count(),0);
		self::assert_equals($this->_cache->get_position(),0);
	}
	
	/**
	 * Tests FWS_Array_1Dim->Current()
	 */
	public function testCurrent()
	{
		// first
		self::assert_equals($this->_cache->current(),$this->_content_values[0]);
		
		// third
		$this->_cache->next();
		$this->_cache->next();
		self::assert_equals($this->_cache->current(),$this->_content_values[2]);
		
		// last
		$this->_cache->to_last();
		self::assert_equals(
			$this->_cache->current(),$this->_content_values[count($this->_content_values) - 1]
		);
	}

	/**
	 * Tests FWS_Array_1Dim->Element_exists()
	 */
	public function testElement_exists()
	{
		// existing
		self::assert_equals($this->_cache->element_exists($this->_content_values[0]),true);
		self::assert_equals($this->_cache->element_exists($this->_content_values[2]),true);
		
		// not existing
		self::assert_equals($this->_cache->element_exists(4711),false);
	}

	/**
	 * Tests FWS_Array_1Dim->Get_element()
	 */
	public function testGet_element()
	{
		// existing
		foreach($this->_content_keys as $k => $key)
			self::assert_equals($this->_cache->get_element($key),$this->_content_values[$k]);
		
		// not existing
		self::assert_null($this->_cache->get_element(count($this->_content_values) + 1,false));
		self::assert_null($this->_cache->get_element('notexisting'));
	}

	/**
	 * Tests FWS_Array_1Dim->Get_element_count()
	 */
	public function testGet_element_count()
	{
		// full
		self::assert_equals($this->_cache->get_element_count(),count($this->_content_values));
		
		// remove
		$this->_cache->remove_element('a');
		self::assert_equals($this->_cache->get_element_count(),count($this->_content_values) - 1);
		
		// empty
		$this->_cache->clear();
		self::assert_equals($this->_cache->get_element_count(),0);
	}

	/**
	 * Tests FWS_Array_1Dim->Get_elements()
	 */
	public function testGet_elements()
	{
		self::assert_equals($this->_cache->get_elements(),$this->_content);
		
		$this->_cache->clear();
		self::assert_equals($this->_cache->get_elements(),array());
	}

	/**
	 * Tests FWS_Array_1Dim->Get_key()
	 */
	public function testGet_key()
	{
		self::assert_equals(
			$this->_cache->get_key($this->_content_values[0]),$this->_content_keys[0]
		);
		self::assert_equals(
			$this->_cache->get_key($this->_content_values[2]),$this->_content_keys[2]
		);
		
		self::assert_null($this->_cache->get_key(4711));
	}

	/**
	 * Tests FWS_Array_1Dim->Get_position()
	 */
	public function testGet_position()
	{
		// first
		$this->_cache->rewind();
		self::assert_equals($this->_cache->get_position(),0);
		
		// second
		$this->_cache->next();
		self::assert_equals($this->_cache->get_position(),1);
		
		// first
		$this->_cache->previous();
		self::assert_equals($this->_cache->get_position(),0);
		
		// last
		$this->_cache->to_last();
		self::assert_equals($this->_cache->get_position(),count($this->_content_values) - 1);
	}

	/**
	 * Tests FWS_Array_1Dim->Key()
	 */
	public function testKey()
	{
		// third
		$this->_cache->rewind();
		$this->_cache->next();
		$this->_cache->next();
		self::assert_equals($this->_cache->key(),$this->_content_keys[2]);
		
		// second
		$this->_cache->previous();
		self::assert_equals($this->_cache->key(),$this->_content_keys[1]);
		
		// first
		$this->_cache->rewind();
		self::assert_equals($this->_cache->key(),$this->_content_keys[0]);
	}

	/**
	 * Tests FWS_Array_1Dim->Key_exists()
	 */
	public function testKey_exists()
	{
		// existing
		self::assert_true($this->_cache->key_exists($this->_content_keys[2]));
		self::assert_true($this->_cache->key_exists($this->_content_keys[0]));
		
		// not existing
		self::assert_false($this->_cache->key_exists('notexisting'));
		self::assert_false($this->_cache->key_exists(4711));
	}

	/**
	 * Tests FWS_Array_1Dim->Next()
	 */
	public function testNext()
	{
		foreach($this->_content_values as $v)
		{
			self::assert_equals($this->_cache->current(),$v);
			$this->_cache->next();
		}
		
		self::assert_false($this->_cache->valid());
	}

	/**
	 * Tests FWS_Array_1Dim->Previous()
	 */
	public function testPrevious()
	{
		$this->_cache->to_last();
		
		for($i = count($this->_content_values) - 1;$i >= 0;$i--)
		{
			self::assert_equals($this->_cache->current(),$this->_content_values[$i]);
			$this->_cache->previous();
		}
		
		self::assert_false($this->_cache->valid());
	}

	/**
	 * Tests FWS_Array_1Dim->Remove_element()
	 */
	public function testRemove_element()
	{
		$ar = $this->_content;
		
		$this->_cache->remove_element($this->_content_keys[0]);
		unset($ar[$this->_content_keys[0]]);
		self::assert_equals($this->_cache->get_elements(),$ar);
		
		$this->_cache->remove_element($this->_content_keys[3]);
		unset($ar[$this->_content_keys[3]]);
		self::assert_equals($this->_cache->get_elements(),$ar);
		
		$this->_cache->remove_element('notexisting');
		self::assert_equals($this->_cache->get_elements(),$ar);
	}

	/**
	 * Tests FWS_Array_1Dim->Rewind()
	 */
	public function testRewind()
	{
		$this->_cache->next();
		$this->_cache->next();
		$this->_cache->rewind();
		self::assert_equals($this->_cache->get_position(),0);
	}

	/**
	 * Tests FWS_Array_1Dim->Set_element()
	 */
	public function testSet_element()
	{
		$ar = $this->_content;
		
		// existing
		$this->_cache->set_element($this->_content_keys[2],5);
		$ar[$this->_content_keys[2]] = 5;
		self::assert_equals($this->_cache->get_elements(),$ar);
		
		// not existing
		$this->_cache->set_element('notexisting',6);
		$ar['notexisting'] = 6;
		self::assert_equals($this->_cache->get_elements(),$ar);
	}

	/**
	 * Tests FWS_Array_1Dim->Sort()
	 */
	public function testSort()
	{
		$this->_cache->clear();
		$ar = array(0 => 1,2 => 4,'wuff' => 3,-1 => -2,'abc');
		foreach($ar as $k => $v)
			$this->_cache->add_element($v,$k);
		
		// elements ascending
		asort($ar);
		$this->_cache->sort(FWS_Array_1Dim::SORT_MODE_ELEMENTS,FWS_Array_1Dim::SORT_DIR_ASC);
		self::assert_equals($this->_cache->get_elements(),$ar);
		
		// elements descending
		arsort($ar);
		$this->_cache->sort(FWS_Array_1Dim::SORT_MODE_ELEMENTS,FWS_Array_1Dim::SORT_DIR_DESC);
		self::assert_equals($this->_cache->get_elements(),$ar);
		
		// keys ascending
		ksort($ar);
		$this->_cache->sort(FWS_Array_1Dim::SORT_MODE_KEYS,FWS_Array_1Dim::SORT_DIR_ASC);
		self::assert_equals($this->_cache->get_elements(),$ar);
		
		// keys descending
		krsort($ar);
		$this->_cache->sort(FWS_Array_1Dim::SORT_MODE_KEYS,FWS_Array_1Dim::SORT_DIR_DESC);
		self::assert_equals($this->_cache->get_elements(),$ar);
	}

	/**
	 * Tests FWS_Array_1Dim->To_last()
	 */
	public function testTo_last()
	{
		$this->_cache->to_last();
		self::assert_equals($this->_cache->get_position(),count($this->_content_keys) - 1);
	}

	/**
	 * Tests FWS_Array_1Dim->Valid()
	 */
	public function testValid()
	{
		$this->_cache->to_last();
		self::assert_true($this->_cache->valid());
		
		$this->_cache->rewind();
		self::assert_true($this->_cache->valid());
		
		$this->_cache->next();
		self::assert_true($this->_cache->valid());
		
		$this->_cache->to_last();
		$this->_cache->next();
		self::assert_false($this->_cache->valid());
	}
}
