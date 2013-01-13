<?php
/**
 * Contains the addfield-source-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	addfield
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
 * The source-interface for the additional-fields. This allows you to load the additional
 * fields from different sources.
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_AddField_Source
{
	/**
	 * Should load all additional fields and return them. This fields have to be sorted
	 * by the sort-key!
	 *
	 * @return array an array of {@link FWS_AddField_Data} objects
	 */
	public function get_fields();
	
	/**
	 * Should return the corresponding field-object for the given data
	 *
	 * @param FWS_AddField_Data $data the data-object
	 * @return FWS_AddField_Field the field-object for that data depending on the type
	 */
	public function get_field($data);
}
?>