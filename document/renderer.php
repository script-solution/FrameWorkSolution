<?php
/**
 * Contains the document-renderer-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	document
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all document-renderer
 *
 * @package			FrameWorkSolution
 * @subpackage	document
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Document_Renderer
{
	/**
	 * Should render the given document and return the result
	 *
	 * @param FWS_Document $doc the document
	 * @return string the result
	 */
	public function render($doc);
}
?>