<?php
/**
 * Contains the db-result-set-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	db
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
 * The interface for all result-sets.
 *
 * @package			FrameWorkSolution
 * @subpackage	db
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_DB_ResultSet extends FWS_Object implements Iterator
{
	/**
	 * The associative fetch-mode
	 */
	const ASSOC		= 0;
	/**
	 * The numberic fetch-mode
	 */
	const NUM			= 1;
	
	/**
	 * The fetch-mode
	 *
	 * @var int
	 */
	private $_mode = self::ASSOC;
	
	/**
	 * @return int the fetch-mode
	 */
	public final function get_fetch_mode()
	{
		return $this->_mode;
	}
	
	/**
	 * Sets the fetch-mode
	 *
	 * @param int $mode the mode: self::ASSOC or self::NUM
	 */
	public final function set_fetch_mode($mode)
	{
		if($mode !== self::ASSOC && $mode != self::NUM)
			FWS_Helper::error('Mode has to be either '.self::ASSOC.' or '.self::NUM.'!');
		
		$this->_mode = $mode;
	}
	
	/**
	 * @return array an numeric array with all rows
	 */
	public abstract function get_rows();
	
	/**
	 * @return int the number of rows
	 */
	public abstract function get_row_count();
	
	/**
	 * @return int the number of fields per row
	 */
	public abstract function get_field_count();
	
	/**
	 * Returns the field-name of the given column
	 *
	 * @param int $col the column
	 * @return string the field-name
	 */
	public abstract function get_field_name($col);
	
	/**
	 * Returns the field-type of the given column
	 *
	 * @param int $col the column
	 * @return string the field-type
	 */
	public abstract function get_field_type($col);
	
	/**
	 * Returns the field-len of the given column
	 *
	 * @param int $col the column
	 * @return int the field-len
	 */
	public abstract function get_field_len($col);

	/**
	 * Builds a HTML-table that shows the complete result-set
	 *
	 * @return string the HTML-table
	 */
	public function __toString()
	{
		$rows = $this->get_rows();
		$res = '<table border="1">'."\n";
		$field_num = $this->get_field_count();
		$res .= '	<tr>'."\n";
		for($a = 0;$a < $field_num;$a++)
			$res .= '		<th>'.$this->get_field_name($a).'</th>'."\n";
		$res .= '	</tr>'."\n";
		foreach($rows as $row)
		{
			$res .= '	<tr>'."\n";
			foreach($row as $v)
				$res .= '		<td>'.$v.'</td>'."\n";
			$res .= '	</tr>'."\n";
		}
		$res .= '</table>'."\n";
		return $res;
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>