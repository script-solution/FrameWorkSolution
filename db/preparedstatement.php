<?php
/**
 * Contains the prepared-statement-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	db
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for all prepared-statements. Works like the following:
 * <code>
 * $stmt = $mydbcon->get_prepared_statement(
 * 	'SELECT * FROM myTable WHERE field = ? AND otherfield = ?'
 * );
 * $stmt->bind(0,'te"st');
 * $stmt->bind(1,123);
 * $resultset = $mydbcon->execute($stmt->get_statement());
 * // executes the query 'SELECT * FROM myTable WHERE field = "te\"st" AND otherfield = 123'
 * ...
 * </code>
 *
 * @package			FrameWorkSolution
 * @subpackage	db
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_DB_PreparedStatement extends FWS_Object
{
	/**
	 * The SQL-statement
	 *
	 * @var string
	 */
	private $_sql;
	
	/**
	 * The values for the placeholders
	 *
	 * @var array
	 */
	private $_values = array();
	
	/**
	 * The DB-connection
	 *
	 * @var FWS_DB_Connection
	 */
	protected $_con;
	
	/**
	 * Constructor
	 *
	 * @param FWS_DB_Connection $con the connection
	 * @param string $sql the SQL-statement
	 */
	public function __construct($con,$sql)
	{
		parent::__construct();
		
		if(!($con instanceof FWS_DB_Connection))
			FWS_Helper::def_error('instance','con','FWS_DB_Connection',$con);
		if(empty($sql))
			FWS_Helper::def_error('notempty','sql',$sql);
		
		$this->_con = $con;
		$this->_sql = $sql;
	}
	
	/**
	 * Binds the given value to given index
	 *
	 * @param int|string $index the index or a string to replace
	 * @param mixed $value the value
	 */
	public final function bind($index,$value)
	{
		if(!is_string($index) && !FWS_Helper::is_integer($index) || $index < 0)
			FWS_Helper::error('$index should be either a string or an integer >= 0');
		
		$this->_values[$index] = $value;
	}
	
	/**
	 * Builds the SQL-statement with the bound values
	 *
	 * @return string the SQL-statement
	 */
	public function get_statement()
	{
		$sql = $this->_sql;
		$offset = 0;
		foreach($this->_values as $k => $val)
		{
			if(is_numeric($k))
			{
				$p = FWS_String::strpos($sql,'?',$offset);
				if($p === false)
					break;
				$pval = $this->get_value($val);
				$sql = FWS_String::substr($sql,0,$p).$pval.FWS_String::substr($sql,$p + 1);
				$offset = $p + FWS_String::strlen($pval);
			}
			else
			{
				$pval = $this->get_value($val);
				$sql = str_replace($k,$pval,$sql);
			}
		}
		return $sql;
	}
	
	/**
	 * Should generate the value to insert into the SQL-statement from the given one. It has to
	 * escape the value if <code>$this->_con->get_escape_values()</code> is true.
	 *
	 * @param mixed $val the value
	 * @return string the value for the SQL-statement
	 */
	protected abstract function get_value($val);
	
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