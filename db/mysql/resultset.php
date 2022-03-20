<?php
/**
 * Contains the mysql-result-set-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.mysql
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
 * The result-set for MySQL. Note that a change of the fetch-mode has no effect as soon as
 * you have accessed the rows the first time (by get_rows(), next(), current() or __toString()).
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysql
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_MySQL_ResultSet extends FWS_DB_ResultSet
{
	/**
	 * The query-result
	 *
	 * @var resource
	 */
	private $_res;
	
	/**
	 * The current index
	 *
	 * @var int
	 */
	private $_index = 0;
	
	/**
	 * All rows from the result
	 *
	 * @var array
	 */
	private $_rows = null;
	
	/**
	 * The total number of rows
	 *
	 * @var int
	 */
	private $_rowcount;
	
	/**
	 * Constructor
	 *
	 * @param resource $result the result-resource
	 */
	public function __construct($result)
	{
		parent::__construct();
		
		$this->_res = $result;
		$this->_rowcount = @mysql_num_rows($this->_res);
	}
	
	/**
	 * Destructor: free's the result
	 */
	public function __destruct()
	{
		@mysql_free_result($this->_res);
	}
	
	/**
	 * @see FWS_DB_ResultIterator::get_rows()
	 *
	 * @return array
	 */
	public function get_rows()
	{
		if($this->_rows === null)
			$this->_load_rows();
		return $this->_rows;
	}
	
	/**
	 * @see FWS_DB_ResultIterator::get_row_count()
	 *
	 * @return int
	 */
	public function get_row_count()
	{
		return $this->_rowcount;
	}
	
	/**
	 * @see FWS_DB_ResultIterator::get_field_count()
	 *
	 * @return int
	 */
	public function get_field_count()
	{
		return @mysql_num_fields($this->_res);
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_name()
	 *
	 * @param int $col
	 * @return string
	 */
	public function get_field_name($col)
	{
		return @mysql_field_name($this->_res,$col);
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_type()
	 *
	 * @param int $col
	 * @return string
	 */
	public function get_field_type($col)
	{
		return @mysql_field_type($this->_res,$col);
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_len()
	 *
	 * @param int $col
	 * @return int
	 */
	public function get_field_len($col)
	{
		return @mysql_field_len($this->_res,$col);
	}

	#[ReturnTypeWillChange]
	public function current()
	{
		// already finished?
		if($this->_index >= $this->_rowcount)
			return false;
		
		if($this->_rows === null)
			$this->_load_rows();
		return $this->_rows[$this->_index];
	}
	
	#[ReturnTypeWillChange]
	public function key()
	{
		return $this->_index;
	}
	
	#[ReturnTypeWillChange]
	public function next()
	{
		if($this->_index + 1 < $this->_rowcount)
			$this->_index++;
	}
	
	#[ReturnTypeWillChange]
	public function rewind()
	{
		$this->_index = 0;
	}
	
	#[ReturnTypeWillChange]
	public function valid()
	{
		return $this->_index < $this->_rowcount;
	}
	
	/**
	 * Loads all rows
	 */
	private function _load_rows()
	{
		$this->_rows = array();
		if($this->get_fetch_mode() == self::ASSOC)
		{
			while($row = @mysql_fetch_assoc($this->_res))
				$this->_rows[] = $row;
		}
		else
		{
			while($row = @mysql_fetch_array($this->_res))
				$this->_rows[] = $row;
		}
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>