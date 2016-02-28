<?php
/**
 * Contains the action-listener-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	action
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
	 * @param string &$message the message that has been returned from the action
	 */
	public function after_action_performed($id,$action,&$message);
}
?>