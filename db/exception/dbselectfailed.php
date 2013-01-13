<?php
/**
 * Contains the db-select-failed-exception
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * The db-select-failed-exception indicates that an error occurred while trying to
 * select a database
 * 
 * @package			FrameWorkSolution
 * @subpackage	db.exception
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_DB_Exception_DBSelectFailed extends FWS_Exception_Critical
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