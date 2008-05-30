<?php
/**
 * Contains the missing-data-exception
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The MissingDataException indicates that some data is missing to continue
 * 
 * @package			PHPLib
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Exceptions_MissingData extends PLIB_Exceptions_Critical
{
	/**
	 * Constructor
	 * 
	 * @param string $message the error-message
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
?>