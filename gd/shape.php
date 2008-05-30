<?php
/**
 * Contains the shape-interface
 * 
 * @version			$Id: shape.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all 1 dimensional shapes
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_GD_Shape
{
	/**
	 * Checks wether this shape intersects the given line
	 *
	 * @param PLIB_GD_Line $line the line
	 * @return boolean true if so
	 */
	public function intersects_line($line);
	
	/**
	 * Checks wether this shape intersects the given circle
	 *
	 * @param PLIB_GD_Circle $circle the circle
	 * @return boolean true if so
	 */
	public function intersects_circle($circle);
	
	/**
	 * Checks wether this shape intersects the given rectangle
	 *
	 * @param PLIB_GD_Rectangle $rect the rectangle
	 * @return boolean true if so
	 */
	public function intersects_rect($rect);
}
?>