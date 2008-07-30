<?php
/**
 * Contains the missing-data-exception
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The MissingDataException indicates that some data is missing to continue
 * 
 * @package			FrameWorkSolution
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Exceptions_MissingData extends FWS_Exceptions_Critical
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