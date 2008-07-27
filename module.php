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
abstract class PLIB_Module extends PLIB_Object
{
	/**
	 * The init-method for this module. Will be called at the very beginning and is intended
	 * for preparing the document. For example setting the content-type, adding bread-crumbs and so
	 * on.
	 *
	 * @param PLIB_Document $doc the document
	 */
	public function init($doc)
	{
		// by default we do nothing
	}
	
	/**
	 * The method which should start everything.
	 *
	 * If any kind of error appears (for example: a parameter is invalid), please call
	 * report_error() to let the module know that something went wrong.
	 *
	 * @see report_error()
	 */
	public abstract function run();
	
	/**
	 * @see PLIB_Object::get_print_vars()
	 *
	 * @return array
	 */
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>