<?php
/**
 * Contains the highlighting-decorator-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	highlighting
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
 * The interface for the highlighting-decorators. That means the classes that use HTML, BBCode
 * or something similar to actually highlight the text.
 *
 * @package			FrameWorkSolution
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Highlighting_Decorator
{
	/**
	 * Should open all given attributes and return the result
	 *
	 * @param FWS_Highlighting_Attributes $attr the attributes
	 * @param string $text the text for which the attributes should be applied
	 * @return string the result
	 */
	public function open_attributes($attr,$text);
	
	/**
	 * Should close all given attributes and return the result
	 *
	 * @param FWS_Highlighting_Attributes $attr the attributes
	 * @return string the result
	 */
	public function close_attributes($attr);
	
	/**
	 * Gets a text-part from the code (may be highlighted or not) and should return the string
	 * that should be added to the highlighted text. So you may for example apply htmlspecialchars()
	 * on it or something like that.
	 *
	 * @param string $text the text
	 * @return string the resulting text
	 */
	public function get_text($text);
}
?>