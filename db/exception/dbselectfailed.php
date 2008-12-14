<?php
/**
 * Contains the db-select-failed-exception
 *
 * @version			$Id: databaseconnection.php 25 2008-07-30 12:41:15Z nasmussen $
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The db-select-failed-exception indicates that an error occurred while trying to
 * select a database
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_Exception_DBSelectFailed extends FWS_Exceptions_Critical
{
	/**
	 * Constructor
	 * 
	 * @param string $database the database that should be selected
	 * @param string $message the error-message
	 * @param int $number the error-number, if available
	 */
	public function __construct($database,$message,$number)
	{
		$msg = 'Could not select the desired database "'.$database.'": ';
		if($message)
		{
			if($number > 0)
				$msg .= '('.$number.') ';
			$msg .= $message;
		}
		parent::__construct($msg,$number);
	}
}
?>