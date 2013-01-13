<?php
/**
 * Contains the diagram-data-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
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
 * The interface for all diagram-datas. The diagrams use this interface to get the data itself,
 * the text for the data-elements, the fill-color and so on.
 *
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_GD_DiagramData
{
	/**
	 * @return array the data that should be displayed: <code>array(<key> => <numericValue>,...)</code>
	 */
	public function get_data();
	
	/**
	 * Should return the title to display for the given key
	 *
	 * @param int $no the number of the item
	 * @param mixed $key the key from the array of {@link get_data()}
	 * @param mixed $value the value from the array of {@link get_data()}
	 * @param float $percent the percentage
	 * @return string the title to display
	 */
	public function get_title_of($no,$key,$value,$percent);
	
	/**
	 * Should return the attributes that should be used for the given key
	 *
	 * @param int $no the number of the item
	 * @param mixed $key the key from the array of {@link get_data()}
	 * @param mixed $value the value from the array of {@link get_data()}
	 * @param float $percent the percentage
	 * @return FWS_GD_TextAttributes the attributes
	 */
	public function get_attributes_of($no,$key,$value,$percent);
	
	/**
	 * Should return the fill-color for the given key
	 *
	 * @param int $no the number of the item
	 * @param mixed $key the key from the array of {@link get_data()}
	 * @param mixed $value the value from the array of {@link get_data()}
	 * @param float $percent the percentage
	 * @return FWS_GD_Color the color
	 */
	public function get_color_of($no,$key,$value,$percent);
	
	/**
	 * @return FWS_GD_Color the background-color for the diagram
	 */
	public function get_diagram_bg();
	
	/**
	 * @return int the padding for the whole diagram
	 */
	public function get_diagram_pad();
}
?>