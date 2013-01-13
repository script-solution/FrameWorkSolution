<?php
/**
 * Contains the query-failed-exception
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
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
 * The query-failed-exception indicates that an error has occurred while requesting something
 * from the database
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_Exception_QueryFailed extends FWS_Exception_Critical
{
	/**
	 * The query which should be performed
	 *
	 * @var string
	 */
	private $_query;
	
	/**
	 * The error-message
	 *
	 * @var string
	 */
	private $_message;
	
	/**
	 * Constructor
	 * 
	 * @param string $message the exception-message
	 * @param string $query the query, if available
	 * @param int $number the MySQL-error-number, if available
	 */
	public function __construct($message,$query,$number = 0)
	{
		$msg = 'MySQL-Query failed: '.$number.': '.$message."\n";
		$msg .= 'MySQL-Query: '."\n".$query;
		parent::__construct($msg,$number);
		
		$this->_message = $message;
		$this->_query = $query;
	}
	
	/**
	 * @return string the mysql-error-message
	 */
	public function get_mysql_error()
	{
		return $this->_message;
	}
	
	/**
	 * @return string the query which should be performed
	 */
	public function get_query()
	{
		return $this->_query;
	}
}
?>