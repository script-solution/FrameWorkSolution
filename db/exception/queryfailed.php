<?php
/**
 * Contains the query-failed-exception
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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