<?php
/**
 * Contains the DatabaseConnection-exception
 *
 * @version			$Id: databaseconnection.php 672 2008-05-05 21:58:06Z nasmussen $
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The DatabaseConnectionException indicates that an error occurred while trying to
 * connect to the database
 * 
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Exceptions_DatabaseConnection extends PLIB_Exceptions_Critical
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