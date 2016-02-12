<?php
/**
 * Contains the mysqli-result-set-class
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysqli
 *
 * Copyright (C) 2003 - 2016 Nils Asmussen
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
 * The result-set for MySQLi. Note that a change of the fetch-mode has no effect as soon as
 * you have accessed the rows the first time (by get_rows(), next(), current() or __toString()).
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysqli
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_MySQLi_ResultSet extends FWS_DB_ResultSet
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
	 * Constructor
	 *
	 * @param resource $result the result-resource
	 */
	public function __construct($result)
	{
		parent::__construct();

		$this->_res = $result;
	}

	/**
	 * Destructor: free's the result
	 */
	public function __destruct()
	{
		if(is_object($this->_res))
			$this->_res->close();
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
		return $this->_res->num_rows;
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_count()
	 *
	 * @return int
	 */
	public function get_field_count()
	{
		return $this->_res->field_count;
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_name()
	 *
	 * @param int $col
	 * @return string
	 */
	public function get_field_name($col)
	{
		return $this->_res->fetch_fields()[$col]->name;
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_type()
	 *
	 * @param int $col
	 * @return string
	 */
	public function get_field_type($col)
	{
		return $this->_res->fetch_fields()[$col]->type;
	}

	/**
	 * @see FWS_DB_ResultIterator::get_field_len()
	 *
	 * @param int $col
	 * @return int
	 */
	public function get_field_len($col)
	{
		return $this->_res->fetch_fields()[$col]->length;
	}

	public function current()
	{
		// already finished?
		if($this->_index >= $this->get_row_count())
			return false;

		if($this->_rows === null)
			$this->_load_rows();
		return $this->_rows[$this->_index];
	}

	public function key()
	{
		return $this->_index;
	}

	public function next()
	{
		// already finished?
		if($this->_index >= $this->get_row_count())
			return false;

		if($this->_rows === null)
			$this->_load_rows();
		return $this->_rows[$this->_index++];
	}

	public function rewind()
	{
		$this->_index = 0;
	}

	public function valid()
	{
		return $this->_index < $this->get_row_count();
	}

	/**
	 * Loads all rows
	 */
	private function _load_rows()
	{
		$this->_rows = array();
		if($this->get_fetch_mode() == self::ASSOC)
		{
			while($row = $this->_res->fetch_assoc())
				$this->_rows[] = $row;
		}
		else
		{
			while($row = $this->_res->fetch_array())
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