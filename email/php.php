<?php
/**
 * Contains the php-mail-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The mail()-implementation for sending emails
 *
 * @package			PHPLib
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Email_PHP extends PLIB_Email_Base
{
	/**
	 * Constructor
	 *
	 * @param string $recipient the email-address of the recipient
	 * @param string $subject the subject of the email
	 * @param string $message the message
	 */
	public function __construct($recipient = '',$subject = '',$message = '')
	{
		parent::__construct($recipient,$subject,$message);
	}

	public function send_mail()
	{
		if(!$this->check_attributes())
			return false;

		$headers = $this->build_header('mail');
		if($this->get_content_type() == 'text/html')
			$message = $this->prepare_html_message($this->get_message());
		else
			$message = $this->get_message();

		// send the email...
		if(PLIB_PHPConfig::is_safemode_enabled())
		{
			$res = @mail(
				$this->get_recipient(),$this->get_subject(),$message,$headers,'-f '.$this->get_from()
			);
		}
		else
			$res = @mail($this->get_recipient(),$this->get_subject(),$message,$headers);

		if(!$res)
		{
			$this->report_error('Mail could not been sent');
			return false;
		}

		return true;
	}
	
	protected function get_print_vars()
	{
		return array_merge(parent::get_print_vars(),get_object_vars($this));
	}
}
?>