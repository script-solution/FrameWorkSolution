<?php
/**
 * Contains the css-selector-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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