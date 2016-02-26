<?php
/**
 * Contains the mysqli-connection-class
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
 * The implementation of the db-connection for MySQLi
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysqli
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_MySQLi_Connection extends FWS_DB_Connection
{
	/**
	 * The connection
	 *
	 * @var mysqli
	 */
	private $_con = null;

	/**
	 * The selected database
	 *
	 * @var string
	 */
	private $_database = null;

	/**
	 * The current number of "open" transactions
	 *
	 * @var int
	 */
	private $_transcount = 0;

	/**
	 * @see FWS_DB_Connection::connect()
	 *
	 * @param string $host
	 * @param string $login
	 * @param string $password
	 */
	public function connect($host,$login,$password)
	{
		$this->_con = new mysqli($host,$login,$password);
		if($this->_con->connect_error)
		{
			throw new FWS_DB_Exception_ConnectionFailed(
				$this->_con->connect_error,$this->_con->connect_errno);
		}
	}

	/**
	 * @return string the client-version
	 */
	public function get_client_version()
	{
		return @mysqli_get_client_version($this->_con);
	}

	/**
	 * @return string the server-version
	 */
	public function get_server_version()
	{
		return @mysqli_get_server_info($this->_con);
	}

	/**
	 * @return mysqli the connection-resource
	 */
	public function get_connection()
	{
		return $this->_con;
	}

	/**
	 * @see FWS_DB_Connection::is_connected()
	 *
	 * @return boolean
	 */
	public function is_connected()
	{
		return $this->_con !== null;
	}

	/**
	 * @see FWS_DB_Connection::select_database()
	 *
	 * @param string $database
	 */
	public function select_database($database)
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		if(!@$this->_con->select_db($database))
		{
			throw new FWS_DB_Exception_DBSelectFailed(
				$database,$this->_con->error,$this->_con->errno
			);
		}
		$this->_database = $database;
	}

	/**
	 * @see FWS_DB_Connection::get_selected_db()
	 *
	 * @return string
	 */
	public function get_selected_db()
	{
		return $this->_database;
	}

	/**
	 * @see FWS_DB_Connection::disconnect()
	 */
	public function disconnect()
	{
		if($this->_con !== null)
			$this->_con->close();
		$this->_con = null;
	}

	/**
	 * @see FWS_DB_Connection::get_prepared_statement()
	 *
	 * @param string $sql
	 * @return FWS_DB_PreparedStatement
	 */
	public function get_prepared_statement($sql)
	{
		return new FWS_DB_MySQLi_PreparedStatement($this,$sql);
	}

	/**
	 * @see FWS_DB_Connection::execute()
	 *
	 * @param string $sql
	 * @return FWS_DB_ResultSet
	 */
	public function execute($sql)
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		if($this->get_save_queries())
			$this->_profiler->start();

		$res = @$this->_con->query($sql);
		if($res === false)
		{
			$err = $this->_con->error;
			$errno = $this->_con->errno;
			$this->rollback_transaction();
			throw new FWS_DB_Exception_QueryFailed($err,$sql,$errno);
		}

		$this->_querycount++;
		if($this->get_save_queries())
		{
			$time = $this->_profiler->get_time();
			$this->_queries[] = array($sql,$time);
		}

		return new FWS_DB_MySQLi_ResultSet($res);
	}

	/**
	 * @see FWS_DB_Connection::start_transaction()
	 */
	public function start_transaction()
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		if($this->use_transactions())
		{
			if($this->_transcount == 0)
				$this->_con->begin_transaction();
			$this->_transcount++;
		}
	}

	/**
	 * @see FWS_DB_Connection::commit_transaction()
	 */
	public function commit_transaction()
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		if($this->use_transactions())
		{
			if($this->_transcount == 1)
				$this->_con->commit();
			$this->_transcount--;
		}
	}

	/**
	 * @see FWS_DB_Connection::rollback_transaction()
	 */
	public function rollback_transaction()
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		if($this->use_transactions())
		{
			$this->_con->rollback();
			$this->_transcount = 0;
		}
	}

	/**
	 * @see FWS_DB_Connection::get_affected_rows()
	 *
	 * @return int
	 */
	public function get_affected_rows()
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		return @$this->_con->affected_rows;
	}

	/**
	 * @see FWS_DB_Connection::get_inserted_id()
	 *
	 * @return int
	 */
	public function get_inserted_id()
	{
		if($this->_con === null)
			throw new FWS_DB_Exception_NotConnected();

		return @$this->_con->insert_id;
	}

	/**
	 * @see FWS_DB_Connection::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>