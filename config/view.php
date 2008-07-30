<?php
/**
 * Contains the config-view interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all views for the config-items
 *
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Config_View
{
	/**
	 * Should display the given item
	 *
	 * @param FWS_Config_Item $item the item
	 */
	public function show_item($item);
	
	/**
	 * Should begin a new group with given group-id
	 *
	 * @param FWS_Config_Item $item the current item
	 * @param FWS_Config_Group $group the group
	 */
	public function begin_group($item,$group);
	
	/**
	 * Should end the group with given group-id
	 *
	 * @param FWS_Config_Item $item the current item
	 * @param FWS_Config_Group $group the group
	 */
	public function end_group($item,$group);
}
?>