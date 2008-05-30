<?php
/**
 * Contains the PLIB_Exceptions_Critical-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The critical exception should not be used as an exception itself. You may subclass
 * this class to indicate that an error is critical and therefore the script-execution should
 * be stopped
 * 
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Exceptions_Critical extends Exception
{
	/**
	 * Constructor
	 * 
	 * @param string $message the error-message
	 * @param int $code the error-code, if available
	 */
	public function __construct($message,$code = 0)
	{
		parent::__construct($message,$code);
	}
	
	public function __toString()
	{
		$msg = '<span style="color: #f00; font-size: 16px;">';
		$msg .= '<b>Critical error! Stopping script-execution...</b></span><br />';
		$msg .= PLIB_Error_Handler::get_instance()->get_error_message(
			$this->getCode(),$this->getMessage(),$this->getFile(),$this->getLine(),$this->getTrace()
		);
		return $msg;
	}
}
?>