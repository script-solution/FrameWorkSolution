<?php
/**
 * Contains the 2dshape-interface
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
 * The interface for all 2 dimensional shapes
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_GD_Shape2D extends FWS_GD_Shape
{
	/**
	 * Determines wether the given point is inside the shape
	 *
	 * @param FWS_GD_Point $point the point
	 * @return boolean true if so
	 */
	public function contains_point($point);
	
	/**
	 * Determines wether the given line is inside the shape
	 *
	 * @param FWS_GD_Line $line the line
	 * @return boolean true if so
	 */
	public function contains_line($line);
	
	/**
	 * Checks wether this shape contains the given circle
	 *
	 * @param FWS_GD_Circle $circle the circle
	 * @return boolean true if so
	 */
	public function contains_circle($circle);
	
	/**
	 * Checks wether this shape contains the given rectangle
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 * @return boolean true if so
	 */
	public function contains_rect($rect);
}
?>