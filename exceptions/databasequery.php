<?php
/**
 * Contains the DatabaseQuery-exception
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The DatabaseQueryException indicates that an error has occurred while requesting something
 * from the database
 * 
 * @package			FrameWorkSolution
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Exceptions_DatabaseQuery extends FWS_Exceptions_Critical
{
	/**
	 * The query which should be performed
	 *
	 * @var string
	 */
	private $_query = '';
	
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
		
		$this->_query = $query;
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