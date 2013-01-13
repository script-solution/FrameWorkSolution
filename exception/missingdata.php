<?php
/**
 * Contains the missing-data-exception
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
 * The MissingDataException indicates that some data is missing to continue
 * 
 * @package			FrameWorkSolution
 * @subpackage	exceptions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Exception_MissingData extends FWS_Exception_Critical
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