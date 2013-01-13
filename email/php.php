<?php
/**
 * Contains the php-mail-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	email
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
 * The mail()-implementation for sending emails
 *
 * @package			FrameWorkSolution
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Email_PHP extends FWS_Email_Base
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
		// the additional_parameters-parameter is disabled in safe-mode
		if(FWS_PHPConfig::is_safemode_enabled())
			$res = @mail($this->get_recipient(),$this->get_subject(),$message,$headers);
		else {
			$res = @mail(
				$this->get_recipient(),$this->get_subject(),$message,$headers,'-f '.$this->get_from()
			);
		}

		if(!$res)
		{
			$this->report_error('Mail could not been sent');
			return false;
		}

		return true;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>