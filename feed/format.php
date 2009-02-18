<?php
/**
 * Contains the feed-format-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	feed
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all feed-formats
 *
 * @package			FrameWorkSolution
 * @subpackage	feed
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Feed_Format
{
	/**
	 * Renders the given feed-document in the corresponding feed-format
	 * 
	 * @param FWS_Feed_Document $doc the document to render
	 * @return string the XML-string
	 */
	public function render($doc);
}
?>