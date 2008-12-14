<?php
/**
 * Contains the connection-failed-exception
 *
 * @version			$Id: databaseconnection.php 25 2008-07-30 12:41:15Z nasmussen $
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The connection-failed-exception indicates that an error occurred while trying to
 * connect to the database
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_Exception_ConnectionFailed extends FWS_Exceptions_Critical
{
	/**
	 * Constructor
	 * 
	 * @param string $message the error-message
	 * @param int $number the error-number, if available
	 */
	public function __construct($message,$number)
	{
		$msg = 'Could not establish the MySQL-connection';
		if($message)
		{
			if($number > 0)
				$msg .= $number.': ';
			$msg .= $message;
		}
		$msg .= "\n".'Please verify the following MySQL-connection-settings!';
		parent::__construct($msg,$number);
	}
}
?>