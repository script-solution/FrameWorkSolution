<?php
/**
 * Contains the FWS_Exception_Critical-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	exceptions
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
 * The critical exception should not be used as an exception itself. You may subclass
 * this class to indicate that an error is critical and therefore the script-execution should
 * be stopped
 * 
 * @package			FrameWorkSolution
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Exception_Critical extends Exception
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
		$msg .= FWS_Error_Handler::get_instance()->get_error_message(
			$this->getCode(),$this->getMessage(),$this->getFile(),$this->getLine(),$this->getTrace()
		);
		return $msg;
	}
}
?>