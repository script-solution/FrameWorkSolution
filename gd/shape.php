<?php
/**
 * Contains the shape-interface
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
 * The interface for all 1 dimensional shapes
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_GD_Shape
{
	/**
	 * Checks whether this shape intersects the given line
	 *
	 * @param FWS_GD_Line $line the line
	 * @return boolean true if so
	 */
	public function intersects_line($line);
	
	/**
	 * Checks whether this shape intersects the given circle
	 *
	 * @param FWS_GD_Circle $circle the circle
	 * @return boolean true if so
	 */
	public function intersects_circle($circle);
	
	/**
	 * Checks whether this shape intersects the given rectangle
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 * @return boolean true if so
	 */
	public function intersects_rect($rect);
}
?>