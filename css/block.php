<?php
/**
 * Contains the css-block-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all blocks
 * 
 * @package			FrameWorkSolution
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_CSS_Block
{
	/**
	 * Represents a comment
	 */
	const COMMENT = 0;
	/**
	 * Represents a ruleset
	 */
	const RULESET = 1;
	/**
	 * Represents an import (at-rule)
	 */
	const IMPORT	= 2;
	/**
	 * Represents a charset (at-rule)
	 */
	const CHARSET	= 3;
	
	/**
	 * @return int the type of this block: self::COMMENT or self::RULESET
	 */
	public function get_type();
}
?>