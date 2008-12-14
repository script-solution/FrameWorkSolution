<?php
/**
 * Contains the not-connected-exception
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The not-connected-exception indicates that we are not connected to a database but
 * wanted do use it
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_Exception_NotConnected extends FWS_Exceptions_Critical
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('Not connected to a database',0);
	}
}
?>