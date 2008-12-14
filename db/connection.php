<?php
/**
 * Contains the base-db-connection-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	db
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-connection-class for all db-connections. By default all values in prepared statements,
 * generated insert- and update-statements will be escaped. You can disable this via
 * <code>set_escape_values(false)</code>.
 * Additionally by default no queries will be saved (for debugging) and transactions are disabled.
 *
 * @package			FrameWorkSolution
 * @subpackage	db
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_DB_Connection extends FWS_Object
{
	/**
	 * The number of queries
	 *
	 * @var int
	 */
	protected $_querycount = 0;
	
	/**
	 * An array with the performed queries
	 *
	 * @var array
	 */
	protected $_queries = array();

	/**
	 * The timer for internal time-measurement
	 *
	 * @var FWS_Profiler
	 */
	protected $_profiler;

	/**
	 * Do you want to save the queries?
	 *
	 * @var boolean
	 */
	private $_save_queries = false;
	
	/**
	 * Wether transaction should be used
	 *
	 * @var boolean
	 */
	private $_transactions = false;
	
	/**
	 * Wether all values should be escaped when building a query
	 *
	 * @var boolean
	 */
	private $_escape_vals = true;

	/**
	 * Contstructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_profiler = new FWS_Profiler();
	}
	
	/**
	 * @return boolean wether the queries will be saved
	 */
	public final function get_save_queries()
	{
		return $this->_save_queries;
	}
	
	/**
	 * Sets wether the queries should be saved
	 *
	 * @param boolean $save the new value
	 */
	public final function set_save_queries($save)
	{
		$this->_save_queries = (bool)$save;
	}
	
	/**
	 * @return boolean wether transactions are used
	 */
	public final function use_transactions()
	{
		return $this->_transactions;
	}
	
	/**
	 * Sets wether transactions should be used
	 *
	 * @param boolean $use the new value
	 */
	public final function set_use_transactions($use)
	{
		$this->_transactions = (bool)$use;
	}
	
	/**
	 * @return boolean wether all values should be escaped when building a query
	 */
	public final function get_escape_values()
	{
		return $this->_escape_vals;
	}
	
	/**
	 * Sets wether all values should be escaped when building a query
	 *
	 * @param boolean $escape the new value
	 */
	public final function set_escape_values($escape)
	{
		$this->_escape_vals = (bool)$escape;
	}
	
	/**
	 * @return array an array with all performed queries
	 */
	public final function get_queries()
	{
		return $this->_queries;
	}

	/**
	 * @return int the number of performed queries
	 */
	public final function get_query_count()
	{
		return $this->_querycount;
	}
	
	/**
	 * Connects to the given database
	 *
	 * @param string $host the hostname. May contain the port
	 * @param string $login the loginname
	 * @param string $password the password
	 * @throws FWS_DB_Exception_ConnectionFailed if the connection fails
	 */
	public abstract function connect($host,$login,$password);
	
	/**
	 * Selects the given database
	 *
	 * @param string $database the database-name
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_DBSelectFailed if it fails
	 */
	public abstract function select_database($database);
	
	/**
	 * @return string the selected database. null = no db selected
	 */
	public abstract function get_selected_db();
	
	/**
	 * @return boolean wether we are connected
	 */
	public abstract function is_connected();

	/**
	 * Closes the db-connection
	 */
	public abstract function disconnect();
	
	/**
	 * Starts a new transaction. Note that you can "nest" transactions. That means the first
	 * transaction-start actually fires a start and every call of this method increases a counter.
	 * If you call commit at first the counter will be decreased and just if all transactions are
	 * "closed" it fires the commit-statement.
	 * 
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 */
	public abstract function start_transaction();
	
	/**
	 * Commits the current transaction. If you "nest" transactions just the last one will fire
	 * the commit-statement.
	 * 
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 */
	public abstract function commit_transaction();
	
	/**
	 * Rolls the current transaction back. Note that this rolls back all transactions if you have
	 * nested transactions.
	 * 
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 */
	public abstract function rollback_transaction();
	
	/**
	 * Returns an instance of FWS_DB_PreparedStatement with given SQL-statement
	 *
	 * @param string $sql the SQL-statement
	 * @return FWS_DB_PreparedStatement the prepared statement
	 */
	public abstract function get_prepared_statement($sql);
	
	/**
	 * Executes the given query and returns a result-iterator
	 *
	 * @param string $sql the SQL-query
	 * @return FWS_DB_ResultSet the result-set
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_QueryFailed if the query fails
	 */
	public abstract function execute($sql);
	
	/**
	 * Executes the given query and returns the first row
	 *
	 * @param string $sql the SQL-query
	 * @return array|boolean an associative array with the first row or false if no row was found
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_QueryFailed if the query fails
	 */
	public function get_row($sql)
	{
		if(strpos($sql,'SELECT') !== false && strpos($sql,'LIMIT') === false)
			$sql .= "\n".'LIMIT 1';
		
		$set = $this->execute($sql);
		return $set->next();
	}
	
	/**
	 * Collects all rows of the given SQL-query and returns them.
	 * 
	 * @param string $sql the SQL-query
	 * @return array a numeric array with all rows or false if an error occurred
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_QueryFailed if the query fails
	 */
	public function get_rows($sql)
	{
		$set = $this->execute($sql);
		return $set->get_rows();
	}

	/**
	 * Executes the query and returns the number of rows
	 * The query will look like:
	 * <code>
	 * 	SELECT COUNT($row) as num FROM $table $where LIMIT 1
	 * </code>
	 *
	 * @param string $table the name of the table
	 * @param string $row the rows you want to select
	 * @param string $where the where statement
	 * @return int the number of found rows
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_QueryFailed if the query fails
	 */
	public function get_row_count($table,$row = '*',$where = '')
	{
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);
		if(empty($row))
			FWS_Helper::def_error('notempty','row',$row);

		$set = $this->execute('SELECT COUNT('.$row.') as num FROM '.$table.' '.$where.' LIMIT 1');
		$row = $set->next();
		return $row['num'];
	}

	/**
	 * Inserts a new row in the given table.
	 * You have to specify the values to insert:
	 * <code>
	 * 	array(
	 * 		<field1> => <value1>,
	 * 		<field2> => <value2>,
	 * 		...
	 * 		<fieldN> => <valueN>
	 * 	)
	 * </code>
	 * You may use an array as element of <var>$values</var> and put the SQL-statement as first
	 * element in the array. In this case it will be integrated directly into the SQL-statement.
	 * That means you can use something like:
	 * <code>array('myfield = myfield + 1');</code>
	 *
	 * @param string $table the table-name
	 * @param array $values an associative array with the fields to update
	 * @return int the inserted id
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_QueryFailed if the query fails
	 */
	public abstract function insert($table,$values);

	/**
	 * Updates rows in the given table. You have to specify the where-clause like
	 * for example:
	 * <code>WHERE id = 2</code>
	 * and the values to update:
	 * <code>
	 * 	array(
	 * 		<field1> => <value1>,
	 * 		<field2> => <value2>,
	 * 		...
	 * 		<fieldN> => <valueN>
	 * 	)
	 * </code>
	 * You may use an array as element of <var>$values</var> and put the SQL-statement as first
	 * element in the array. In this case it will be integrated directly into the SQL-statement.
	 * That means you can use something like:
	 * <code>'myfield' => array('myfield + 1')</code>
	 *
	 * @param string $table the table-name
	 * @param string $where the where-clause
	 * @param array $values an associative array with the fields to update
	 * @return int the number of affected rows
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 * @throws FWS_DB_Exception_QueryFailed if the query fails
	 */
	public abstract function update($table,$where,$values);

	/**
	 * @return int the last inserted id
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 */
	public abstract function get_inserted_id();

	/**
	 * @return the number of affected rows in the last query
	 * @throws FWS_DB_Exception_NotConnected if not connected
	 */
	public abstract function get_affected_rows();

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