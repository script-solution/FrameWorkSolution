<?php
/**
 * Contains the unsupported-method-exception
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The unsupported-method-exception indicates that a method is not implemented or should not
 * be called.
 * 
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Exceptions_UnsupportedMethod extends PLIB_Exceptions_Critical
{
	/**
	 * Constructor
	 * 
	 * @param string $message the message of the exception
	 */
	public function __construct($message = '')
	{
		$msg = $message ? 'Unsupported Method: '.$message : 'Unsupported Method';
		parent::__construct($msg);
	}
}
?>