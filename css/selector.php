<?php
/**
 * Contains the css-selector-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	css
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
 * The interface for all selector-types
 *
 * @package			FrameWorkSolution
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_CSS_Selector
{
	/**
	 * Should create the CSS-code for this selector
	 * 
	 * @return string the CSS-code
	 */
	public function to_css();
}
?>