<?php
/**
 * Contains the mysql-connection-class
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
 * The implementation of the db-connection for MySQL
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysql
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_MySQL_Connection extends FWS_DB_Connection
{
	/**
	 * The connection
	 *
	 * @var resource
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
		// already connected?
		if($this->_con !== null)
			return;
		
		if(!$this->_con = @mysql_connect($host,$login,$password,true))
			throw new FWS_DB_Exception_ConnectionFailed(mysql_error(),mysql_errno());
	}
	
	/**
	 * @return string the client-version
	 */
	public function get_client_version()
	{
		return @mysql_get_client_info();
	}
	
	/**
	 * @return string the server-version
	 */
	public function get_server_version()
	{
		if($this->_con !== NULL)
			return @mysql_get_server_info($this->_con);
		else
			return @mysql_get_server_info();
	}
	
	/**
	 * @return resource the connection-resource
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
		
		if(!@mysql_select_db($database,$this->_con))
		{
			throw new FWS_DB_Exception_DBSelectFailed(
				$database,mysql_error($this->_con),mysql_errno($this->_con)
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
			@mysql_close($this->_con);
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
		return new FWS_DB_MySQL_PreparedStatement($this,$sql);
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

		$res = @mysql_query($sql,$this->_con);
		if(!$res)
		{
			$err = mysql_error($this->_con);
			$errno = mysql_errno($this->_con);
			$this->rollback_transaction();
			throw new FWS_DB_Exception_QueryFailed($err,$sql,$errno);
		}

		$this->_querycount++;
		if($this->get_save_queries())
		{
			$time = $this->_profiler->get_time();
			$this->_queries[] = array($sql,$time);
		}
		
		return new FWS_DB_MySQL_ResultSet($res);
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
				@mysql_query('START TRANSACTION',$this->_con);
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
				@mysql_query('COMMIT',$this->_con);
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
			@mysql_query('ROLLBACK',$this->_con);
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
		
		return @mysql_affected_rows($this->_con);
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
		
		return @mysql_insert_id($this->_con);
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