<?php
/**
 * Contains the action-listener-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	action
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The action-listener-interface. Can be used to do some stuff before or after the execution
 * of an action.
 *
 * @package			FrameWorkSolution
 * @subpackage	action
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Action_Listener
{
	/**
	 * This method will be called before the action has been performed.
	 * 
	 * @param int $id the action-id
	 * @param FWS_Action_Base $action the action-instance
	 */
	public function before_action_performed($id,$action);
	
	/**
	 * This method will be called after the action has been performed.
	 * You may also change the message that should be displayed
	 *
	 * @param int $id the action-id
	 * @param FWS_Action_Base $action the action-instance
	 * @param string $message the message that has been returned from the action
	 */
	public function after_action_performed($id,$action,&$message);
}
?>