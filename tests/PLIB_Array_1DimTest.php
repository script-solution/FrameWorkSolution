<?php
/**
 * Contains the PLIB_Array_1Dim test
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * PLIB_Array_1Dim test case.
 * 
 * @package			PHPLib
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Array_1DimTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var PLIB_Array_1Dim
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
	protected function setUp()
	{
		parent::setUp();
		
		$this->_cache = new PLIB_Array_1Dim();
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
	 * Tests PLIB_Array_1Dim->Add_element()
	 */
	public function testAdd_element()
	{
		$this->_cache->clear();
		
		$this->_cache->add_element(1);
		self::assertEquals($this->_cache->get_element_count(),1);
		
		$this->_cache->add_element(2,'a');
		self::assertEquals($this->_cache->get_element_count(),2);
		self::assertEquals($this->_cache->get_element('a'),2);
		
		$this->_cache->add_element(3,'a');
		self::assertEquals($this->_cache->get_element_count(),2);
		self::assertEquals($this->_cache->get_element('a'),3);
	}

	/**
	 * Tests PLIB_Array_1Dim->Add_element_at()
	 */
	public function testAdd_element_at()
	{
		$this->_cache->clear();
		
		// insert at the beginning
		$this->_cache->add_element_at(4,0);
		self::assertEquals($this->_cache->get_element_count(),1);
		self::assertEquals($this->_cache->get_element(0,false),4);
		
		// insert at 1
		$this->_cache->add_element_at(5,1);
		self::assertEquals($this->_cache->get_element_count(),2);
		self::assertEquals($this->_cache->get_element(1,false),5);
		
		// insert at the end
		$this->_cache->add_element_at(6,$this->_cache->get_element_count());
		self::assertEquals($this->_cache->get_element_count(),3);
		self::assertEquals($this->_cache->get_element(2,false),6);
		
		// insert existing key
		$this->_cache->add_element_at(7,0,2);
		self::assertEquals($this->_cache->get_element_count(),3);
		self::assertEquals($this->_cache->get_element(0,false),7);
	}

	/**
	 * Tests PLIB_Array_1Dim->Binary_search()
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
		$this->_cache->sort(PLIB_Array_1Dim::SORT_MODE_ELEMENTS,PLIB_Array_1Dim::SORT_DIR_ASC);
		
		$key = $this->_cache->binary_search('abc',PLIB_Array_1Dim::SORT_DIR_ASC);
		self::assertEquals($key,3);
		
		// desc
		$this->_cache->sort(PLIB_Array_1Dim::SORT_MODE_ELEMENTS,PLIB_Array_1Dim::SORT_DIR_DESC);
		
		$key = $this->_cache->binary_search(3,PLIB_Array_1Dim::SORT_DIR_DESC);
		self::assertEquals($key,1);
		
		// test not existing
		$key = $this->_cache->binary_search('notexisting',PLIB_Array_1Dim::SORT_DIR_DESC);
		self::assertNull($key);
	}
	
	/**
	 * Tests PLIB_Array_1Dim->Clear()
	 */
	public function testClear()
	{
		$this->_cache->clear();
		
		self::assertEquals($this->_cache->get_elements(),array());
		self::assertEquals($this->_cache->get_element_count(),0);
		self::assertEquals($this->_cache->get_position(),0);
	}
	
	/**
	 * Tests PLIB_Array_1Dim->Current()
	 */
	public function testCurrent()
	{
		// first
		self::assertEquals($this->_cache->current(),$this->_content_values[0]);
		
		// third
		$this->_cache->next();
		$this->_cache->next();
		self::assertEquals($this->_cache->current(),$this->_content_values[2]);
		
		// last
		$this->_cache->to_last();
		self::assertEquals(
			$this->_cache->current(),$this->_content_values[count($this->_content_values) - 1]
		);
	}

	/**
	 * Tests PLIB_Array_1Dim->Element_exists()
	 */
	public function testElement_exists()
	{
		// existing
		self::assertEquals($this->_cache->element_exists($this->_content_values[0]),true);
		self::assertEquals($this->_cache->element_exists($this->_content_values[2]),true);
		
		// not existing
		self::assertEquals($this->_cache->element_exists(4711),false);
	}

	/**
	 * Tests PLIB_Array_1Dim->Get_element()
	 */
	public function testGet_element()
	{
		// existing
		foreach($this->_content_keys as $k => $key)
			self::assertEquals($this->_cache->get_element($key),$this->_content_values[$k]);
		
		// not existing
		self::assertNull($this->_cache->get_element(count($this->_content_values) + 1,false));
		self::assertNull($this->_cache->get_element('notexisting'));
	}

	/**
	 * Tests PLIB_Array_1Dim->Get_element_count()
	 */
	public function testGet_element_count()
	{
		// full
		self::assertEquals($this->_cache->get_element_count(),count($this->_content_values));
		
		// remove
		$this->_cache->remove_element('a');
		self::assertEquals($this->_cache->get_element_count(),count($this->_content_values) - 1);
		
		// empty
		$this->_cache->clear();
		self::assertEquals($this->_cache->get_element_count(),0);
	}

	/**
	 * Tests PLIB_Array_1Dim->Get_elements()
	 */
	public function testGet_elements()
	{
		self::assertEquals($this->_cache->get_elements(),$this->_content);
		
		$this->_cache->clear();
		self::assertEquals($this->_cache->get_elements(),array());
	}

	/**
	 * Tests PLIB_Array_1Dim->Get_key()
	 */
	public function testGet_key()
	{
		self::assertEquals(
			$this->_cache->get_key($this->_content_values[0]),$this->_content_keys[0]
		);
		self::assertEquals(
			$this->_cache->get_key($this->_content_values[2]),$this->_content_keys[2]
		);
		
		self::assertNull($this->_cache->get_key(4711));
	}

	/**
	 * Tests PLIB_Array_1Dim->Get_position()
	 */
	public function testGet_position()
	{
		// first
		$this->_cache->rewind();
		self::assertEquals($this->_cache->get_position(),0);
		
		// second
		$this->_cache->next();
		self::assertEquals($this->_cache->get_position(),1);
		
		// first
		$this->_cache->previous();
		self::assertEquals($this->_cache->get_position(),0);
		
		// last
		$this->_cache->to_last();
		self::assertEquals($this->_cache->get_position(),count($this->_content_values) - 1);
	}

	/**
	 * Tests PLIB_Array_1Dim->Key()
	 */
	public function testKey()
	{
		// third
		$this->_cache->rewind();
		$this->_cache->next();
		$this->_cache->next();
		self::assertEquals($this->_cache->key(),$this->_content_keys[2]);
		
		// second
		$this->_cache->previous();
		self::assertEquals($this->_cache->key(),$this->_content_keys[1]);
		
		// first
		$this->_cache->rewind();
		self::assertEquals($this->_cache->key(),$this->_content_keys[0]);
	}

	/**
	 * Tests PLIB_Array_1Dim->Key_exists()
	 */
	public function testKey_exists()
	{
		// existing
		self::assertTrue($this->_cache->key_exists($this->_content_keys[2]));
		self::assertTrue($this->_cache->key_exists($this->_content_keys[0]));
		
		// not existing
		self::assertFalse($this->_cache->key_exists('notexisting'));
		self::assertFalse($this->_cache->key_exists(4711));
	}

	/**
	 * Tests PLIB_Array_1Dim->Next()
	 */
	public function testNext()
	{
		foreach($this->_content_values as $v)
			self::assertEquals($this->_cache->next(),$v);
		
		self::assertFalse($this->_cache->next());
	}

	/**
	 * Tests PLIB_Array_1Dim->Previous()
	 */
	public function testPrevious()
	{
		$this->_cache->to_last();
		
		for($i = count($this->_content_values) - 1;$i >= 0;$i--)
			self::assertEquals($this->_cache->previous(),$this->_content_values[$i]);
		
		self::assertFalse($this->_cache->previous());
	}

	/**
	 * Tests PLIB_Array_1Dim->Remove_element()
	 */
	public function testRemove_element()
	{
		$ar = $this->_content;
		
		$this->_cache->remove_element($this->_content_keys[0]);
		unset($ar[$this->_content_keys[0]]);
		self::assertEquals($this->_cache->get_elements(),$ar);
		
		$this->_cache->remove_element($this->_content_keys[3]);
		unset($ar[$this->_content_keys[3]]);
		self::assertEquals($this->_cache->get_elements(),$ar);
		
		$this->_cache->remove_element('notexisting');
		self::assertEquals($this->_cache->get_elements(),$ar);
	}

	/**
	 * Tests PLIB_Array_1Dim->Rewind()
	 */
	public function testRewind()
	{
		$this->_cache->next();
		$this->_cache->next();
		$this->_cache->rewind();
		self::assertEquals($this->_cache->get_position(),0);
	}

	/**
	 * Tests PLIB_Array_1Dim->Set_element()
	 */
	public function testSet_element()
	{
		$ar = $this->_content;
		
		// existing
		$this->_cache->set_element($this->_content_keys[2],5);
		$ar[$this->_content_keys[2]] = 5;
		self::assertEquals($this->_cache->get_elements(),$ar);
		
		// not existing
		$this->_cache->set_element('notexisting',6);
		$ar['notexisting'] = 6;
		self::assertEquals($this->_cache->get_elements(),$ar);
	}

	/**
	 * Tests PLIB_Array_1Dim->Sort()
	 */
	public function testSort()
	{
		$this->_cache->clear();
		$ar = array(0 => 1,2 => 4,'wuff' => 3,-1 => -2,'abc');
		foreach($ar as $k => $v)
			$this->_cache->add_element($v,$k);
		
		// elements ascending
		asort($ar);
		$this->_cache->sort(PLIB_Array_1Dim::SORT_MODE_ELEMENTS,PLIB_Array_1Dim::SORT_DIR_ASC);
		self::assertEquals($this->_cache->get_elements(),$ar);
		
		// elements descending
		arsort($ar);
		$this->_cache->sort(PLIB_Array_1Dim::SORT_MODE_ELEMENTS,PLIB_Array_1Dim::SORT_DIR_DESC);
		self::assertEquals($this->_cache->get_elements(),$ar);
		
		// keys ascending
		ksort($ar);
		$this->_cache->sort(PLIB_Array_1Dim::SORT_MODE_KEYS,PLIB_Array_1Dim::SORT_DIR_ASC);
		self::assertEquals($this->_cache->get_elements(),$ar);
		
		// keys descending
		krsort($ar);
		$this->_cache->sort(PLIB_Array_1Dim::SORT_MODE_KEYS,PLIB_Array_1Dim::SORT_DIR_DESC);
		self::assertEquals($this->_cache->get_elements(),$ar);
	}

	/**
	 * Tests PLIB_Array_1Dim->To_last()
	 */
	public function testTo_last()
	{
		$this->_cache->to_last();
		self::assertEquals($this->_cache->get_position(),count($this->_content_keys) - 1);
	}

	/**
	 * Tests PLIB_Array_1Dim->Valid()
	 */
	public function testValid()
	{
		$this->_cache->to_last();
		self::assertTrue($this->_cache->valid());
		
		$this->_cache->rewind();
		self::assertTrue($this->_cache->valid());
		
		$this->_cache->next();
		self::assertTrue($this->_cache->valid());
		
		$this->_cache->to_last();
		$this->_cache->next();
		self::assertFalse($this->_cache->valid());
	}
}
?>