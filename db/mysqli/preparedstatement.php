<?php
/**
 * Contains the prepared-statement-class for MySQLi
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
 * The prepared-statement for MySQLi
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysqli
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_MySQLi_PreparedStatement extends FWS_DB_PreparedStatement
{
	/**
	 * @see FWS_DB_PreparedStatement::get_value()
	 *
	 * @param mixed $val
	 * @return string
	 */
	protected function get_value($val)
	{
		// ensure that we don't change something (e.g. '01' => 1)
		if(is_int($val))
			return $val;
		if(is_float($val))
			return $val;

		if($val === null)
			return 'NULL';

		if($this->_con->get_escape_values())
			$val = $this->_con->get_connection()->real_escape_string($val);
		return '\''.$val.'\'';
	}
}
?>