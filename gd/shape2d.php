<?php
/**
 * Contains the 2dshape-interface
 * 
 * @version			$Id: shape2d.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all 2 dimensional shapes
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_GD_Shape2D extends PLIB_GD_Shape
{
	/**
	 * Determines wether the given point is inside the shape
	 *
	 * @param PLIB_GD_Point $point the point
	 * @return boolean true if so
	 */
	public function contains_point($point);
	
	/**
	 * Determines wether the given line is inside the shape
	 *
	 * @param PLIB_GD_Line $line the line
	 * @return boolean true if so
	 */
	public function contains_line($line);
	
	/**
	 * Checks wether this shape contains the given circle
	 *
	 * @param PLIB_GD_Circle $circle the circle
	 * @return boolean true if so
	 */
	public function contains_circle($circle);
	
	/**
	 * Checks wether this shape contains the given rectangle
	 *
	 * @param PLIB_GD_Rectangle $rect the rectangle
	 * @return boolean true if so
	 */
	public function contains_rect($rect);
}
?>