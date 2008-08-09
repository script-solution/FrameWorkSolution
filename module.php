<?php
/**
 * Contains the module-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The module-class which is the base-class for all modules. Modules are used by the class
 * {@link FWS_Document} and are intended to provide a modular and more independend architecture.
 * A module can control the complete result that will be sent to the browser by switching and
 * manipulating the renderer of the document and the changing properties of the document itself.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @see FWS_Helper::get_module_name()
 */
abstract class FWS_Module extends FWS_Object
{
	/**
	 * Stores wether the module has been shown successfully or something unexpected
	 * has happened (missing parameter, no access, ...).
	 *
	 * @var boolean
	 */
	private $_error = false;
	
	/**
	 * Reports an error in this module
	 * 
	 * @see error_occurred()
	 */
	public final function set_error()
	{
		$this->_error = true;
	}

	/**
	 * Returns wether the module has been shown successfully or something unexpected
	 * has happened (missing parameter, no access, ...).
	 *
	 * @return boolean wether an error has been occurred
	 * @see set_error()
	 */
	public function error_occurred()
	{
		return $this->_error;
	}
	
	/**
	 * The init-method for this module. Will be called at the very beginning and is intended
	 * for preparing the document. For example setting the content-type, adding bread-crumbs and so
	 * on.
	 *
	 * @param FWS_Document $doc the document
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
	 * @return FWS_HTML_Formular the created formular
	 */
	protected function request_formular()
	{
		$tpl = FWS_Props::get()->tpl();

		$form = new FWS_HTML_Formular(false,false);
		$tpl->add_array('form',$form);
		$tpl->add_allowed_method('form','*');
		return $form;
	}
	
	/**
	 * Reports an error and stores that the module has not finished in a correct way.
	 * Note that you have to specify a message if the type is no error and no no-access-msg!
	 *
	 * @param int $type the type. see FWS_Document_Messages::*
	 * @param string $message you can specify the message to display here, if you like
	 */
	protected function report_error($type = FWS_Document_Messages::ERROR,$message = '')
	{
		$locale = FWS_Props::get()->locale();
		$msgs = FWS_Props::get()->msgs();

		// determine message to report
		$msg = '';
		if($message !== '')
			$msg = $message;
		else
		{
			switch($type)
			{
				case FWS_Document_Messages::NO_ACCESS:
					$msg = $locale->lang('permission_denied');
					break;
				
				case FWS_Document_Messages::ERROR:
					$msg = $locale->lang('invalid_page');
					break;
					
				default:
					FWS_Helper::error('Missing message or invalid type: '.$type);
			}
		}
		
		// report error
		$this->set_error();
		$msgs->add_message($msg,$type);
	}
	
	/**
	 * @see FWS_Object::get_print_vars()
	 *
	 * @return array
	 */
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>