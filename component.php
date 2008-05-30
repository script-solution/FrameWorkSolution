<?php
/**
 * Contains the component-class
 *
 * @version			$Id: component.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A component is a module or standalone-module. That means a custom component of the page
 * that is controlled by the component.
 * <br>
 * This class gives modules and standalone-modules some basic methods that are interesting
 * for both. That is error-handling, requesting a formular for the template and other stuff.
 * <br>
 * The most important method is run(). This method should perform all stuff
 * to display the module. This may be setting the template-variables to
 * corresponding values.
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Component extends PLIB_FullObject
{
	/**
	 * Stores wether the module has been shown successfully or something unexpected
	 * has happened (missing parameter, no access, ...).
	 *
	 * @var boolean
	 */
	private $_error = false;
	
	/**
	 * The method which should start everything.
	 *
	 * If any kind of error appears (for example: a parameter is invalid), please call
	 * _report_error() to let the module know that something went wrong.
	 *
	 * @see _report_error()
	 */
	public abstract function run();
	
	/**
	 * Reports an error in this module
	 * 
	 * @see error_occurred()
	 */
	public function set_error()
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
	 * Instantiates {@link PLIB_HTML_Formular} with the action-result of the document,
	 * adds it as 'form' to the template with all methods allowed and returns
	 * the instance.
	 *
	 * @return PLIB_HTML_Formular the created formular
	 */
	protected function _request_formular()
	{
		$form = new PLIB_HTML_Formular($this->doc->get_action_result() === -1);
		$this->tpl->add_array('form',$form);
		$this->tpl->add_allowed_method('form','*');
		return $form;
	}

	/**
	 * Reports an error and stores that the module has not finished in a correct way.
	 * Note that you have to specify a message if the type is no error and no no-access-msg!
	 *
	 * @param int $type the type. see PLIB_Messages::MSG_TYPE_*
	 * @param string $message you can specify the message to display here, if you like
	 */
	protected function _report_error($type = PLIB_Messages::MSG_TYPE_ERROR,$message = '')
	{
		// determine message to report
		$msg = '';
		if($message !== '')
			$msg = $message;
		else
		{
			switch($type)
			{
				case PLIB_Messages::MSG_TYPE_NO_ACCESS:
					$msg = $this->locale->lang('permission_denied');
					break;
				
				case PLIB_Messages::MSG_TYPE_ERROR:
					$msg = $this->locale->lang('invalid_page');
					break;
					
				default:
					PLIB_Helper::error('Missing message or invalid type: '.$type);
			}
		}
		
		// report error
		$this->set_error();
		$this->msgs->add_message($msg,$type);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>