<?php
/**
 * Contains the prepared-statement-class for MySQL
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	db.mysql
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The prepared-statement for MySQL
 *
 * @package			FrameWorkSolution
 * @subpackage	db.mysql
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_MySQL_PreparedStatement extends FWS_DB_PreparedStatement
{
	/**
	 * @see FWS_DB_PreparedStatement::get_value()
	 *
	 * @param mixed $val
	 * @return string
	 */
	protected function get_value($val)
	{
		if(is_numeric($val))
			return $val;
		
		if($val === null)
			return 'NULL';
		
		if($this->_con->get_escape_values())
			$val = mysql_real_escape_string($val,$this->_con->get_connection());
		return '\''.$val.'\'';
	}
}
?>