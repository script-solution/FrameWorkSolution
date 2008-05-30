<?php
/**
 * Contains the diagram-interface
 *
 * @version			$Id: diagram.php 652 2008-05-03 10:37:24Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The diagram-interface
 *
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_GD_Diagram
{
	/**
	 * Draws the data in the given rectangle
	 *
	 * @param PLIB_GD_Rectangle $rect the rectangle
	 */
	public function draw_diagram($rect);
}
?>