<?php
/**
 * Contains the 1-dimensional cache
 * 
 * @package			FrameWorkSolution
 * @subpackage	array
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
 * A container for 1-dimensional-arrays. The array may be associative. You can iterate
 * over the array, add, change and remove elements and search for elements.
 * Additionally you may sort the array by the keys or elements in any direction and
 * may use binary-search if the array is sorted by the elements.
 *
 * @package			FrameWorkSolution
 * @subpackage	array
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Array_1Dim extends FWS_Object implements Iterator
{
	/**
	 * Represents the ascending sort-direction
	 */
	const SORT_DIR_ASC				= 0;
	
/**
	 * Represents the descending sort-direction
	 */
	const SORT_DIR_DESC				= 1;
	
	/**
	 * Will sort by the keys
	 */
	const SORT_MODE_KEYS			= 0;
	
	/**
	 * Will sort by the elements
	 */
	const SORT_MODE_ELEMENTS	= 1;
	
	/**
	 * The content
	 *
	 * @var array
	 */
	protected $_cache_content = array();

	/**
	 * The keys of the cache content
	 *
	 * @var array
	 */
	protected $_cache_content_keys = array();

	/**
	 * The number of rows
	 *
	 * @var integer
	 */
	protected $_length = 0;

	/**
	 * The current position in the array
	 *
	 * @var integer
	 */
	private $_pos = 0;
	
	/**
	 * A temporary variable for the sort-direction
	 *
	 * @var int
	 */
	private $_sort_dir;

	/**
	 * constructor
	 *
	 * @param array $array the content of the cache
	 */
	public function __construct($array = array())
	{
		parent::__construct();
		
		$this->set_elements($array);
	}
	
	/**
	 * Sorts the elements of the array. You can choose if you want to sort by the keys
	 * or elements and in which direction should be sorted.
	 *
	 * @param int $mode the sort-mode. See self::SORT_MODE_*
	 * @param int $dir the sort-direction. See self::SORT_DIR_*
	 * @see binary_search()
	 */
	public final function sort($mode = self::SORT_MODE_ELEMENTS,$dir = self::SORT_DIR_ASC)
	{
		if(!in_array($mode,array(self::SORT_MODE_ELEMENTS,self::SORT_MODE_KEYS)))
		{
			FWS_Helper::def_error(
				'inarray','mode',array(self::SORT_MODE_ELEMENTS,self::SORT_MODE_KEYS),$mode
			);
		}
		
		if(!in_array($dir,array(self::SORT_DIR_ASC,self::SORT_DIR_DESC)))
		{
			FWS_Helper::def_error(
				'inarray','dir',array(self::SORT_DIR_ASC,self::SORT_DIR_DESC),$dir
			);
		}
		
		if($mode == self::SORT_MODE_KEYS)
		{
			if($dir == self::SORT_DIR_ASC)
				sort($this->_cache_content_keys);
			else
				rsort($this->_cache_content_keys);
		}
		else
		{
			$this->_sort_dir = $dir;
			usort($this->_cache_content_keys,array($this,'_sort_elements'));
		}
	}
	
	/**
	 * Searches via binary-search for the given element and returns the key of it.
	 * Of course the elements(!) needs to be sorted. Otherwise the result is undefined!
	 * So please call #sort(self::SORT_MODE_ELEMENTS) before you use this method.
	 *
	 * @param mixed $element the element to search for
	 * @param int $dir the direction in which the elements are sorted
	 * @return mixed the key if found or null if not
	 * @see sort()
	 */
	public final function binary_search($element,$dir = self::SORT_DIR_ASC)
	{
		if(!in_array($dir,array(self::SORT_DIR_ASC,self::SORT_DIR_DESC)))
		{
			FWS_Helper::def_error(
				'inarray','dir',array(self::SORT_DIR_ASC,self::SORT_DIR_DESC),$dir
			);
		}
		
		$from = 0;
		$to = count($this->_cache_content_keys) - 1;
		
		while($from <= $to)
		{
			$m = (int)(($from + $to) / 2);
			$e = $this->_cache_content[$this->_cache_content_keys[$m]];
			
			// compare the elements
			$cmp = $e === $element ? 0 : ($e > $element ? 1 : -1);
			
			// have we found it?
			if($cmp == 0)
				return $this->_cache_content_keys[$m];
			
			// where to continue?
			if(($cmp == 1 && $dir == self::SORT_DIR_ASC) || ($cmp == -1 && $dir == self::SORT_DIR_DESC))
				$to = $m - 1;
			else
				$from = $m + 1;
		}
		
		return null;
	}
	
	/**
	 * Clears the container and resets everything
	 */
	public final function clear()
	{
		$this->_cache_content = array();
		$this->_cache_content_keys = array();
		$this->_length = 0;
		$this->_pos = 0;
	}
	
	/**
	 * @return array the current elements (no copy!)
	 */
	public final function get_elements_quick()
	{
		return $this->_cache_content;
	}
	
	/**
	 * Builds an array with all elements in the current order
	 * 
	 * @return array the elements
	 */
	public final function get_elements()
	{
		$res = array();
		foreach($this->_cache_content_keys as $key)
			$res[$key] = $this->_cache_content[$key];
		return $res;
	}

	/**
	 * Sets the elements of this array
	 *
	 * @param array $elements the elements to set
	 */
	public final function set_elements($elements)
	{
		if(!is_array($elements))
			FWS_Helper::def_error('array','elements',$elements);
		
		if($this->_length > 0)
			$this->clear();
		
		$this->_length = count($elements);
		if($this->_length > 0)
		{
			$this->_cache_content = $elements;
			$this->_cache_content_keys = array_keys($elements);
		}
	}

	/**
	 * Returns the element with given key or index.
	 * 
	 * @param mixed $row the row-index or row-key
	 * @param boolean $is_key is the specified row-index the row-key or the row-index?
	 * @return mixed the element with given index or null if not found
	 */
	public final function get_element($row,$is_key = true)
	{
		if($is_key && isset($this->_cache_content[$row]))
			return $this->_cache_content[$row];

		if(!$is_key && FWS_Helper::is_integer($row) && $row >= 0 && $row < $this->_length)
			return $this->_cache_content[$this->_cache_content_keys[$row]];

		return null;
	}
	
	/**
	 * Returns all elements with the given keys
	 *
	 * @param array $keys an array with the keys
	 * @return array all found elements
	 */
	public final function get_elements_with_keys($keys)
	{
		$elements = array();
		foreach($keys as $key)
		{
			$el = $this->get_element($key);
			if($el !== null)
				$elements[] = $el;
		}
		return $elements;
	}
	
	/**
	 * Determines the key of the first element that is equal to the given one.
	 *
	 * @param mixed $element the element to search for
	 * @return mixed the key of the element or null if not found
	 * @see binary_search()
	 */
	public final function get_key($element)
	{
		$key = array_search($element,$this->_cache_content,true);
		if($key === false)
			return null;
		
		return $key;
	}

	/**
	 * Checks wether the given element exists
	 *
	 * @param mixed $element the element to search for
	 * @return boolean true if the element has been found
	 */
	public final function element_exists($element)
	{
		return in_array($element,$this->_cache_content);
	}

	/**
	 * Checks wether a row with given key exists
	 *
	 * @param mixed $key the key you're looking for
	 * @return boolean true if the key exists
	 */
	public final function key_exists($key)
	{
		return isset($this->_cache_content[$key]);
	}

	/**
	 * Adds the given element to the array. You may specify the key of the element.
	 * <var>$key === false</var> means that the element will be inserted at the end of the array.
	 *
	 * @param mixed $element the element to add
	 * @param mixed $key the key for the row
	 */
	public final function add_element($element,$key = false)
	{
		if($key === false)
			$key = $this->_length;

		$exists = isset($this->_cache_content[$key]);
		$this->_cache_content[$key] = $element;
		if(!$exists)
		{
			$this->_cache_content_keys[] = $key;
			$this->_length++;
		}
	}
	
	/**
	 * Adds the given element at the given index. Note that this works with associative
	 * arrays, too!
	 *
	 * @param mixed $element the element to add
	 * @param int $index
	 * @param mixed $key the key for the row
	 */
	public final function add_element_at($element,$index,$key = false)
	{
		if($index < 0 || $index > $this->_length)
			FWS_Helper::def_error('numbetween','index',0,$this->_length,$index);
		
		if($key === false)
			$key = $this->_length;
		
		// if the key does already exist, we remove it and add the new one
		if(isset($this->_cache_content[$key]))
			$this->remove_element($key);
		
		// insert at the specified position in the key-array
		for($i = $this->_length - 1;$i >= $index;$i--)
			$this->_cache_content_keys[$i + 1] = $this->_cache_content_keys[$i];
		$this->_cache_content_keys[$index] = $key;
		
		// insert into content
		$this->_length++;
		$this->_cache_content[$key] = $element;
	}

	/**
	 * Sets the given key to the given element. If the key doesn't exist the element will
	 * simply be added.
	 *
	 * @param mixed $key the key of the element
	 * @param mixed $element the new value
	 */
	public final function set_element($key,$element)
	{
		// add the element if it doesn't exist
		if(!isset($this->_cache_content[$key]))
		{
			$this->_cache_content_keys[] = $key;
			$this->_length++;
		}

		$this->_cache_content[$key] = $element;
	}

	/**
	 * Removes the element with given key
	 *
	 * @param mixed $key the key of the element (maybe the index)
	 */
	public final function remove_element($key)
	{
		if(isset($this->_cache_content[$key]))
		{
			unset($this->_cache_content[$key]);
			$index = array_search($key,$this->_cache_content_keys,true);
			array_splice($this->_cache_content_keys,$index,1,array());
			$this->_length--;
		}
	}

	/**
	 * @return int the number of elements
	 */
	public final function get_element_count()
	{
		return $this->_length;
	}

	/**
	 * @return int the current position in the array
	 */
	public final function get_position()
	{
		return $this->_pos;
	}
	
	/**
	 * Rewinds the internal position so that you are at the first element
	 */
	public final function rewind()
	{
		$this->_pos = 0;
	}

	/**
	 * Moves the internal position to the last entry
	 */
	public final function to_last()
	{
		$this->_pos = $this->_length - 1;
	}
	
	/**
	 * @return mixed the current element in the array or false
	 */
	public final function current()
	{
		if($this->_length == 0)
			return false;

		if($this->_pos < 0 || $this->_pos >= $this->_length)
			return false;
		
		return $this->_cache_content[$this->_cache_content_keys[$this->_pos]];
	}
	
	/**
	 * @return mixed the current key in the array or false
	 */
	public final function key()
	{
		if($this->_length == 0)
			return false;
		
		if($this->_pos < 0 || $this->_pos >= $this->_length)
			return false;
		
		return $this->_cache_content_keys[$this->_pos];
	}
	
	/**
	 * Moves to the next element in the array
	 */
	public final function next()
	{
		if($this->_pos < $this->_length)
			$this->_pos++;
	}

	/**
	 * Moves to the previous element in the array
	 */
	public final function previous()
	{
		if($this->_pos >= 0)
			$this->_pos--;
	}
	
	/**
	 * @return boolean wether there is an element at the current position
	 */
	public final function valid()
	{
		return $this->current() !== false;
	}
	
	/**
	 * The callback function to sort by the elements
	 *
	 * @param mixed $a the key of the first element
	 * @param mixed $b the key of the second element
	 * @return int -1, if $a < $b, 1 if $a > $b, 0 otherwise
	 */
	private function _sort_elements($a,$b)
	{
		$ae = $this->_cache_content[$a];
		$be = $this->_cache_content[$b];
		if($ae < $be)
			return $this->_sort_dir == self::SORT_DIR_ASC ? -1 : 1;
		if($ae > $be)
			return $this->_sort_dir == self::SORT_DIR_ASC ? 1 : -1;
		return 0;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>