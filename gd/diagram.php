<?php
/**
 * Contains the diagram-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The diagram-interface
 *
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_GD_Diagram
{
	/**
	 * Draws the data in the given rectangle
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 */
	public function draw_diagram($rect);
}
?>