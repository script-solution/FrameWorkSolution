<?php
/**
 * Contains the util-base-class
 * 
 * @package			FrameWorkSolution
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
 * This should be the base-class for all classes that contain just static methods.
 * It prevents the instantiation and cloning of the class.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_UtilBase
{
	/**
	 * Constructor
	 * 
	 * @throws FWS_Exception_UnsupportedMethod in all cases
	 */
	public function __construct()
	{
		throw new FWS_Exception_UnsupportedMethod('Since '.get_class($this).' contains just static'
			.' methods you can\'t instantiate the class!');
	}
	
	/**
	 * @throws FWS_Exception_UnsupportedMethod in all cases
	 */
	public function __clone()
	{
		throw new FWS_Exception_UnsupportedMethod(
			'Since '.get_class($this).' contains just static methods you can\'t clone the class!'
		);
	}
}
?>