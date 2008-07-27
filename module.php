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
	 * Creates the formular, adds it to the template and allows all methods of it
	 * to be called.
	 *
	 * @return PLIB_HTML_Formular the created formular
	 */
	protected function request_formular()
	{
		$tpl = PLIB_Props::get()->tpl();

		$form = new PLIB_HTML_Formular(false,false);
		$tpl->add_array('form',$form);
		$tpl->add_allowed_method('form','*');
		return $form;
	}
	
	/**
	 * Reports an error and stores that the module has not finished in a correct way.
	 * Note that you have to specify a message if the type is no error and no no-access-msg!
	 *
	 * @param int $type the type. see PLIB_Messages::MSG_TYPE_*
	 * @param string $message you can specify the message to display here, if you like
	 */
	protected function report_error($type = PLIB_Messages::MSG_TYPE_ERROR,$message = '')
	{
		$doc = PLIB_Props::get()->doc();
		$doc->report_error($type,$message);
	}
	
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