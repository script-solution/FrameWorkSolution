<?php
/**
 * Contains the message-listener-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	document
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The listener-interface for messages in the message-container
 * 
 * @package			FrameWorkSolution
 * @subpackage	document
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Document_MsgListener
{
	/**
	 * Will be called as soon as a message has been received
	 *
	 * @param int $type the message-type
	 * @param string $text the text
	 */
	public function received_msg($type,$text);
}
?>