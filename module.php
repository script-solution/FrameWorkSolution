<?php
/**
 * Contains the module-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The module-class which is the base-class for all modules.
 * A module displays the part between the header and footer. Additionally
 * the module may contain actions that can be performed at specific conditions,
 * can specify the location in the page and other things.
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @see PLIB_Helper::get_module_name()
 */
abstract class PLIB_Module extends PLIB_Component
{
	/**
	 * Should return the template you want to use. An empty value indicates that no template
	 * should be displayed.
	 * By default it returns the suffix of the class-name followed by ".htm".
	 * 
	 * @return string the template to use
	 */
	public function get_template()
	{
		$classname = get_class($this);
		$pos = PLIB_String::strpos($classname,'Module_');
		if($pos == -1)
		{
			PLIB_Helper::error('Invalid classname "'.$classname
				.'". Please name it like <prefix>Module_<suffix>!');
		}
		
		$prefixlen = $pos + 7;
		return PLIB_String::strtolower(PLIB_String::substr($classname,$prefixlen)).'.htm';
	}
	
	/**
	 * this method will be used to determine the location
	 * it should return an array with the location parts.
	 *
	 * For example if you have a location like:
	 * 'Home' => 'YourPage' => 'YourSubPage'
	 * you should return something like:
	 * <code>
	 * 	array(
	 * 		'YourPage' => 'index.php?action=yourpage',
	 * 		'YourSubPage' => 'index.php?action=yourpage&amp;mode=subpage'
	 * 	)
	 * </code>
	 * Note that an empty URL will lead to a text instead of a link!
	 *
	 * @see PLIB_Helper::generate_location()
	 * @return array an array of the following form: <code>array(<name> => <url>[, ...])</code>
	 */
	public abstract function get_location();

	/**
	 * You may use this method to define some actions for your module.
	 * Please return an associative array of the following form:
	 * <code>
	 * 	array(
	 * 		<actionID> => <actionName>,
	 * 		...
	 *  )
	 * </code>
	 * Note that <actionName> has to be in the filename:
	 * <code><prefix><actionName>.php</code>
	 * You can specify the prefix in the action-performer.
	 * Additionally the file(-path) has to be:
	 * <code>modules/<moduleName>/action_<actionName>.php</code>
	 *
	 * @return array the actions
	 */
	public function get_actions()
	{
		return array();
	}

	/**
	 * checks the user has access to this module
	 *
	 * @return boolean true if the user is allowed to use this module
	 */
	public function has_access()
	{
		return true;
	}
}
?>