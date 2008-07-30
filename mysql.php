<?php
/**
 * Contains the MySQL-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The MySQL-interface. This makes it easier to perform DB-actions.
 * It raises errors automaticly and performs debugging-operations if enabled
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_MySQL extends FWS_Singleton
{
	/**
	 * @return FWS_MySQL the instance of this class (only one is allowed!)
	 */
	public static function get_instance()
	{
		return parent::_get_instance(get_class());
	}
	
	/**
	 * The mysql-connection
	 *
	 * @var resource
	 */
	private $_con = null;

	/**
	 * The total number of queries
	 *
	 * @var integer
	 */
	private $_total_queries = 0;

	/**
	 * An array with the performed queries
	 *
	 * @var array
	 */
	private $_saved_queries = array();

	/**
	 * The timer for internal time-measurement
	 *
	 * @var FWS_Profiler
	 */
	private $_profiler;

	/**
	 * Do you want to enable debugging?
	 *
	 * @var boolean
	 */
	private $_enable_debug = false;
	
	/**
	 * Wether transaction should be used
	 *
	 * @var boolean
	 */
	private $_transactions = false;
	
	/**
	 * The current number of "open" transactions
	 *
	 * @var int
	 */
	private $_transcount = 0;
	
	/**
	 * Contstructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_profiler = new FWS_Profiler();
	}

	/**
	 * Connects to the database
	 *
	 * @param string $mysql_host the hostname
	 * @param string $mysql_login the login to the MySQL-DB
	 * @param string $mysql_password the password to the MySQL-DB
	 * @param string $mysql_database the database with which you want to work
	 * @param boolean $die_on_no_db do you want to exit the script if the database-selection failed?
	 * @throws FWS_Exceptions_DatabaseConnection if the connection fails or the database can't be
	 * 	selected
	 */
	public function connect($mysql_host,$mysql_login,$mysql_password,$mysql_database,
		$die_on_no_db = true)
	{
		// already connected?
		if($this->_con !== null)
			return;
		
		if(!$this->_con = @mysql_connect($mysql_host,$mysql_login,$mysql_password,true))
			throw new FWS_Exceptions_DatabaseConnection(mysql_error(),mysql_errno());

		if(!@mysql_select_db($mysql_database,$this->_con) && $die_on_no_db)
		{
			throw new FWS_Exceptions_DatabaseConnection(
				mysql_error($this->_con),mysql_errno($this->_con)
			);
		}
	}

	/**
	 * Closes the mysql-connection
	 */
	public function disconnect()
	{
		if($this->_con !== null)
			@mysql_close($this->_con);
		$this->_con = null;
	}
	
	/**
	 * @return boolean wether transactions are used
	 */
	public function use_transactions()
	{
		return $this->_transactions;
	}
	
	/**
	 * Sets wether transactions should be used
	 *
	 * @param boolean $use the new value
	 */
	public function set_use_transactions($use)
	{
		$this->_transactions = (bool)$use;
	}

	/**
	 * Inits the database-connection. The charset will be set, if MySQL-Version >= 4.1
	 * and the SQL-Mode will be set to an empty string.
	 *
	 * @param string $charset the charset to use
	 */
	public function init($charset)
	{
		if(empty($charset))
			FWS_Helper::def_error('notempty','charset',$charset);

		$version = $this->get_server_version();
		if($version >= '4.1')
		{
			$this->sql_qry('SET CHARACTER SET '.$charset.';');
			
			// we don't want to have any sql-modes
			$this->sql_qry('SET SESSION sql_mode="";');
			
			// reset query-counter
			$this->_total_queries = 0;
		}
	}
	
	/**
	 * Sets wether debugging is enabled
	 *
	 * @param boolean $enabled the new value
	 */
	public function set_debugging_enabled($enabled)
	{
		$this->_enable_debug = $enabled;
	}
	
	/**
	 * @return string the MySQL-server-version
	 */
	public function get_server_version()
	{
		return mysql_get_server_info($this->_con);
	}
	
	/**
	 * Starts a new transaction. Note that you can "nest" transactions. That means the first
	 * transaction-start actually fires a start and every call of this method increases a counter.
	 * If you call commit at first the counter will be decreased and just if all transactions are
	 * "closed" it fires the commit-statement.
	 */
	public function start_transaction()
	{
		if($this->_transactions)
		{
			if($this->_transcount == 0)
				@mysql_query('START TRANSACTION',$this->_con);
			$this->_transcount++;
		}
	}
	
	/**
	 * Commits the current transaction. If you "nest" transactions just the last one will fire
	 * the commit-statement.
	 */
	public function commit_transaction()
	{
		if($this->_transactions)
		{
			if($this->_transcount == 1)
				@mysql_query('COMMIT',$this->_con);
			$this->_transcount--;
		}
	}
	
	/**
	 * Rolls the current transaction back. Note that this rolls back all transactions if you have
	 * nested transactions.
	 */
	public function rollback_transaction()
	{
		if($this->_transactions)
		{
			@mysql_query('ROLLBACK',$this->_con);
			$this->_transcount = 0;
		}
	}

	/**
	 * performs the given query and returns the first row
	 *
	 * @param string $sql the sql-statement
	 * @param boolean $error do you want to stop if an error occurred? (default is true)
	 * @return array an associative array with the first row
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function sql_fetch($sql,$error = true)
	{
		if($this->_enable_debug)
			$this->_profiler->start();

		if(strpos($sql,'LIMIT') === false)
			$sql .= "\n".'LIMIT 1';
		
		$query = @mysql_query($sql,$this->_con);
		if(!$query && $error)
		{
			$err = mysql_error($this->_con);
			$errno = mysql_errno($this->_con);
			$this->rollback_transaction();
			throw new FWS_Exceptions_DatabaseQuery($err,$sql,$errno);
		}

		$this->_total_queries++;
		if($this->_enable_debug)
		{
			$sql_time = $this->_profiler->get_time();
			$result = array(array(),array(''));
			@preg_match_all('/(?:FROM|UPDATE|INSERT\s+INTO)\s+([a-z0-9_]+)/',$sql,$result);
			if(isset($result[1][0]))
				$this->_saved_queries[$result[1][0]][] = array('qry' => $sql,'time' => $sql_time);
			else
				$this->_saved_queries[] = array('qry' => $sql,'time' => $sql_time);
		}

		$result = mysql_fetch_array($query);
		mysql_free_result($query);

		return $result;
	}

	/**
	 * performs the given query and returns the result
	 *
	 * @param string $sql the SQL-query
	 * @param boolean $error do you want to stop if an error occurred? (default is true)
	 * @return resource the result-resource
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function sql_qry($sql,$error = true)
	{
		if($this->_enable_debug)
			$this->_profiler->start();

		$query = @mysql_query($sql,$this->_con);
		if(!$query && $error)
		{
			$err = mysql_error($this->_con);
			$errno = mysql_errno($this->_con);
			$this->rollback_transaction();
			throw new FWS_Exceptions_DatabaseQuery($err,$sql,$errno);
		}

		$this->_total_queries++;
		if($this->_enable_debug)
		{
			$sql_time = $this->_profiler->get_time();
			$result = array(array(),array(''));
			@preg_match_all('/(?:FROM|UPDATE|INSERT\s+INTO)\s+([a-z0-9_]+)/',$sql,$result);
			if(isset($result[1][0]))
				$this->_saved_queries[$result[1][0]][] = array('qry' => $sql,'time' => $sql_time);
			else
				$this->_saved_queries[] = array('qry' => $sql,'time' => $sql_time);
		}
		return $query;
	}

	/**
	 * performs the query and returns the number of rows
	 * The query will look like:
	 * <code>
	 * 	SELECT COUNT($row) as num FROM $tabelle $where LIMIT 1
	 * </code>
	 *
	 * @param string $table the name of the table
	 * @param string $row the rows you want to select
	 * @param string $where the where statement
	 * @param boolean $error do you want to stop if an error occurred? (default is true)
	 * @return int the number of found rows
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function sql_num($table,$row,$where,$error = true)
	{
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);

		if(empty($row))
			FWS_Helper::def_error('notempty','row',$row);

		$sql = 'SELECT COUNT('.$row.') as num FROM '.$table.' '.$where.' LIMIT 1';
		$qry = $this->sql_qry($sql,$error);
		$res = $this->sql_fetch_assoc($qry);
		$this->sql_free($qry);
		return $res['num'];
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
	 * @param boolean $error raise an error if the query fails?
	 * @return resource the result-resource of mysql_query()
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function sql_insert($table,$values,$error = true)
	{
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);

		if(!is_array($values) || count($values) == 0)
			FWS_Helper::def_error('array>0','values',$values);

		$sql = 'INSERT INTO '.$table.' SET '.$this->get_update_fields($values);
		return $this->sql_qry($sql,$error);
	}

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
	 * <code>array('myfield = myfield + 1');</code>
	 *
	 * @param string $table the table-name
	 * @param string $where the where-clause
	 * @param array $values an associative array with the fields to update
	 * @param boolean $error raise an error if the query fails?
	 * @return resource the result-resource of mysql_query()
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function sql_update($table,$where,$values,$error = true)
	{
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);

		if(!is_array($values) || count($values) == 0)
			FWS_Helper::def_error('array>0','values',$values);

		$sql = 'UPDATE '.$table.' SET '.$this->get_update_fields($values);
		$sql .= ' '.$where;
		
		return $this->sql_qry($sql,$error);
	}
	
	/**
	 * Collects all rows of the given SQL-query and returns them.
	 * 
	 * @param string $sql the SQL-query
	 * @param boolean $error die on error?
	 * @return array a numeric array with all rows or false if an error occurred
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function sql_rows($sql,$error = true)
	{
		$rows = array();
		$qry = $this->sql_qry($sql,$error);
		if(!$qry)
			return false;
		
		while($row = $this->sql_fetch_assoc($qry))
			$rows[] = $row;
		$this->sql_free($qry);
		
		return $rows;
	}
	
	/**
	 * Prints or returns the result-set of the given query. This is just intended
	 * for debugging!
	 *
	 * @param string $sql the query to execute
	 * @param boolean $return do you want to get the result or print it?
	 * @return mixed the result-table if <var>$return</var> == true or an empty
	 * 	string
	 * @throws FWS_Exceptions_DatabaseQuery if the query fails (and $error is true)
	 */
	public function print_result_from_sql($sql,$return = false)
	{
		// execute the query
	  $qry = $this->sql_qry($sql);
	  if(!$qry)
			return '';
		 
		return $this->print_result($qry,$return);
	}
	
	/**
	 * Prints or returns the result-set of the given query-result. This is just intended
	 * for debugging!
	 *
	 * @param resource $qry the query-resource
	 * @param boolean $return do you want to get the result or print it?
	 * @return mixed the result-table if <var>$return</var> == true or an empty
	 * 	string
	 */
	public function print_result($qry,$return = false)
	{
		// define some stylesheets
		$res = '<style type="text/css">'."\n";
		$res .= ' table.sql_debug {font-family: verdana, helvetica, arial, sans-serif;';
		$res .= ' font-size: 12px; border-collapse: collapse; border-spacing: 2px;}'."\n";
		$res .= ' table.sql_debug td {border: 1px solid #bbb; padding: 3px;}'."\n";
		$res .= ' table.sql_debug thead td {background-color: #008; color: #fff;}'."\n";
		$res .= '</style>'."\n";
		$res .= '<script type="text/javascript" src="'.FWS_Path::client_fw().'js/basic.js"></script>'."\n";
		$res .= '<script type="text/javascript" src="'.FWS_Path::client_fw().'js/table_sorter.js">';
		$res .= '</script>'."\n";
		$res .= '<table id="sql_debug" class="sql_debug" width="100%">'."\n";
		
		// table-columns
		$field_num = $this->sql_num_fields($qry);
		$res .= ' <thead>'."\n";
		$res .= ' 	<tr>'."\n";
		for($a = 0;$a < $field_num;$a++)
		{
			$res .= '			<td onmouseover="this.style.cursor = \'pointer\';"';
			$res .= ' onmouseout="this.style.cursor = \'default\';"';
			$res .= ' onclick="sorter.sortTable(this.cellIndex);" title="Sort by column">';
			$res .= $this->sql_field_name($qry,$a).'</td>'."\n";
		}
		$res .= ' 	</tr>'."\n";
		$res .= ' </thead>'."\n";

		// entries
		$res .= ' <tbody>'."\n";
		for($i = 0;$data = $this->sql_fetch_array($qry);$i++)
		{
			$res .= ' 	<tr>'."\n";
			for($a = 0;$a < $field_num;$a++)
				$res .= '			<td>'.$data[$a].'</td>'."\n";
			$res .= ' 	</tr>'."\n";
		}
		$res .= ' </tbody>'."\n";
		$res .= '</table>'."\n";

		$res .= '<script type="text/javascript">var sorter = new FWS_TableSorter("sql_debug");';
		$res .= '</script>'."\n";

		$this->sql_free($qry);
		
		// print / return result
		if($return)
		  return $res;

		echo $res;
		return '';
	}
	
	/**
	 * Generates a query-clause from the given parts
	 * 
	 * @param array $parts all parts to connect with $link
	 * @param string $link the link of the parts: AND,OR,...
	 * @return string the where-clause:
	 * 	<code> WHERE (<part1>)[ <link> (<part2>) ...]</code>
	 */
	public function generate_where_clause($parts,$link = 'AND')
	{
		$where = '';
		foreach($parts as $part)
			$where .= $link.' ('.$part.')';
		
		if($where != '')
			$where = ' WHERE '.FWS_String::substr($where,FWS_String::strlen($link));
		return $where;
	}

	/**
	 * @param resource $qry the query-resource-id
	 * @return array an associative array of the next result-row in the given query
	 */
	public function sql_fetch_assoc($qry)
	{
		return mysql_fetch_assoc($qry);
	}

	/**
	 * @param resource $qry the query-resource-id
	 * @return array an associative and numeric array of the next result-row in the given query
	 */
	public function sql_fetch_array($qry)
	{
		return mysql_fetch_array($qry);
	}

	/**
	 * @param resource $qry the query-resource-id
	 * @return int the number of rows in the given query
	 */
	public function sql_num_rows($qry)
	{
		return mysql_num_rows($qry);
	}

	/**
	 * @param resource $qry the query-resource-id
	 * @return int the number of fields in the given query
	 */
	public function sql_num_fields($qry)
	{
		return mysql_num_fields($qry);
	}

	/**
	 * @param resource $qry the query-resource-id
	 * @param int $index the index of the field
	 * @return string the name of the field with given index
	 */
	public function sql_field_name($qry,$index)
	{
		return mysql_field_name($qry,$index);
	}

	/**
	 * @return int the last inserted id (mysql_insert_id())
	 */
	public function get_last_insert_id()
	{
		return mysql_insert_id($this->_con);
	}

	/**
	 * @return the number of affected rows in the last query (mysql_affected_rows())
	 */
	public function get_affected_rows()
	{
		return mysql_affected_rows($this->_con);
	}

	/**
	 * @return string the last error-message
	 */
	public function last_error()
	{
		return mysql_error($this->_con);
	}

	/**
	 * deletes the given query-resource (mysql_free_result())
	 *
	 * @param resource $res the query-resource
	 */
	public function sql_free($res)
	{
		mysql_free_result($res);
	}
	
	/**
	 * @return array an array with all performed queries
	 */
	public function get_performed_queries()
	{
		return $this->_saved_queries;
	}

	/**
	 * prints the performed queries
	 *
	 */
	public function print_performed_queries()
	{
		echo FWS_PrintUtils::to_string($this->_saved_queries);
	}

	/**
	 * @return int the number of performed queries
	 */
	public function get_performed_query_num()
	{
		return $this->_total_queries;
	}

	/**
	 * Builds the SQL-string to update/insert the given fields.
	 * This method assumes that addslashes has already been done!
	 *
	 * @param array $values an associative array with all fields to set
	 * @return string the SQL-string
	 */
	public function get_update_fields($values)
	{
		$sql = '';
		foreach($values as $k => $v)
			$sql .= '`'.$k.'` = '.$this->get_sql_value($v).',';
		return FWS_String::substr($sql,0,-1);
	}
	
	/**
	 * Determines the value for the SQL-statement for the given value. That means
	 * either just <var>$value</var>, or <var>NULL</var> or <var>'$value'</var>.
	 * You may use an array and put the SQL-statement as first element in the array. In this case
	 * it will be integrated directly into the SQL-statement. That means you can use something like:
	 * <code>array('myfield = myfield + 1');</code>
	 *
	 * @param mixed $value the value to store
	 * @return string the value for the sql-statement
	 */
	public function get_sql_value($value)
	{
		// for "field = field + 1" and similar stuff
		if(is_array($value))
			return $value[0];
		
		if(is_int($value) || is_float($value))
			return $value;
		
		if($value === null)
			return 'NULL';
		
		return '\''.$value.'\'';
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>