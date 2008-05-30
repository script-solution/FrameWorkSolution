<?php
/**
 * Contains SMTP-email-class
 *
 * @version			$Id: smtp.php 804 2008-05-30 07:19:41Z nasmussen $
 * @package			PHPLib
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The SMTP-implementation for sending emails
 *
 * @package			PHPLib
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Email_SMTP extends PLIB_Email_Base
{
	/**
	 * The host of the SMTP-server
	 *
	 * @var string
	 */
	private $_smtp_host = '';

	/**
	 * The port of the SMTP-server
	 *
	 * @var integer
	 */
	private $_smtp_port = 25;

	/**
	 * The login for the SMTP-server
	 *
	 * @var string
	 */
	private $_smtp_login = '';

	/**
	 * The password for the SMTP-server
	 *
	 * @var string
	 */
	private $_smtp_pw = '';

	/**
	 * Use &lt; and &gt; for the email-addresses?
	 *
	 * @var boolean
	 */
	private $_use_ltgt = true;

	/**
	 * The socket
	 *
	 * @var resource
	 */
	private $_sock = null;
	
	/**
	 * The last response
	 *
	 * @var string
	 */
	private $_response = null;

	/**
	 * The last response-code
	 *
	 * @var integer
	 */
	private $_code = -1;

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

	/**
	 * Sets the SMTP-host to connect to
	 *
	 * @param string $host the host
	 */
	public function set_smtp_host($host)
	{
		if(empty($host))
			PLIB_Helper::def_error('notempty','host',$host);

		$this->_smtp_host = $host;
	}

	/**
	 * Sets the SMTP-login you want to use
	 *
	 * @param string $login the login for the SMTP-server
	 */
	public function set_smtp_login($login)
	{
		$this->_smtp_login = $login;
	}

	/**
	 * Sets the SMTP-password you want to use
	 *
	 * @param string $password the password for the SMTP-server
	 */
	public function set_smtp_password($password)
	{
		$this->_smtp_pw = $password;
	}

	/**
	 * Sets the SMTP-port you want to use
	 *
	 * @param int $port the port of the SMTP-server
	 */
	public function set_smtp_port($port)
	{
		if(!PLIB_Helper::is_integer($port) || $port <= 0)
			PLIB_Helper::def_error('intgt0','port',$port);

		$this->_smtp_port = $port;
	}

	/**
	 * Do you want to use &lt; and &gt; for the email-addresses?
	 *
	 * @param boolean $use the new value
	 */
	public function set_use_ltgt($use)
	{
		$this->_use_ltgt = $use;
	}

	public function send_mail()
	{
		if(!$this->_check_attributes())
			return false;

		// open socket to host
		$this->_sock = @fsockopen($this->_smtp_host,$this->_smtp_port);
		if(!$this->_sock)
		{
			$this->_report_error(
				'Unable to open socket to "'.$this->_smtp_host.':'.$this->_smtp_port.'"!'
			);
			return false;
		}

		$this->_read_reply();

		// is the server ready?
		if($this->_code != 220)
		{
			$this->_report_error('Server "'.$this->_smtp_host.'" is not ready!');
			return false;
		}

		// greet the server :)
		// we try EHLO at first
		$this->_send_command('EHLO '.$this->_smtp_host);
		if($this->_code != 250)
		{
			// ok, that didn't work. Try HELO
			$this->_send_command('HELO '.$this->_smtp_host);
			
			if($this->_code != 250)
			{
				// give up
				$this->_report_error('"HELO" failed!');
				return false;
			}
		}

		// do we have to auth?
		if($this->_smtp_login != '')
		{
			// try to login
			$this->_send_command('AUTH LOGIN');

			// can we auth?
			if($this->_code != 334)
			{
				$this->_report_error('Unable to auth!');
				return false;
			}

			// send the login
			$this->_send_command(base64_encode($this->_smtp_login));
			if($this->_code != 334)
			{
				$this->_report_error('Invalid username!');
				return false;
			}
			
			// send the pw
			$this->_send_command(base64_encode($this->_smtp_pw));
			if($this->_code != 235)
			{
				$this->_report_error('Invalid password!');
				return false;
			}
		}

		// set sender
		if($this->_use_ltgt)
			$this->_send_command('MAIL FROM:<'.$this->get_from().'>');
		else
			$this->_send_command('MAIL FROM:'.$this->get_from());

		if($this->_code != 250)
		{
			$this->_report_error('Unable to set "from"!');
			return false;
		}

		// collect receiver
		$receiver = array();
		if($this->get_recipient() != '')
			$receiver[] = $this->get_recipient();
		foreach($this->get_bcc_recipients() as $email)
			$receiver[] = $email;

		// no receivers?
		if(count($receiver) == 0)
		{
			$this->_report_error('No receiver set!');
			return false;
		}

		// set receiver
		foreach($receiver as $r)
		{
			$r = $this->_use_ltgt ? '<'.$r.'>' : $r;
			$this->_send_command('RCPT TO:'.$r);

			if($this->_code != 250)
			{
				$this->_report_error('Unable to set receiver "'.$r.'"!');
				return false;
			}
		}

		// we want to send the data
		$this->_send_command('DATA');
		if($this->_code != 354)
		{
			$this->_report_error('Unable to send data!');
			return false;
		}

		// build headers
		$headers = $this->_build_header('smtp');
		if($this->get_content_type() == 'text/html')
			$message = $this->_prepare_html_message($this->get_message());
		else
			$message = $this->get_message();
		$message = $headers."\n\n".$message."\n.";

		// now send the message-body
		$this->_send_command($message);
		if($this->_code != 250)
		{
			$this->_report_error('Unable to send data!');
			return false;
		}

		// say good bye :)
		$this->_send_command('QUIT');
		fclose($this->_sock);

		return true;
	}

	protected function _report_error($msg)
	{
		$this->_error_message = htmlspecialchars($msg,ENT_QUOTES);
		$this->_error_message .= '<br />Server replied: "'.$this->_response.'"';
		if($this->_sock)
			fclose($this->_sock);
	}

	/**
	 * Sends the given command to the SMTP-server
	 *
	 * @param string $cmd the command
	 */
	private function _send_command($cmd)
	{
		fwrite($this->_sock,$cmd."\r\n");

		// read the reply of the server
		return $this->_read_reply();
	}

	/**
	 * Reads an reply from the server.
	 * Will set <var>$this->_code</var> to the received response-code
	 *
	 * @return string the received message
	 */
	private function _read_reply()
	{
		$str = '';
		while(!feof($this->_sock))
		{
			$l = fgets($this->_sock,128);
			$str .= $l;
			if(PLIB_String::substr($l,3,1) == " ")
				break;
		}

		// set response-code
		$this->_response = $str;
		$this->_code = intval(PLIB_String::substr($str,0,3));

		return $str;
	}
	
	protected function _get_print_vars()
	{
		return array_merge(parent::_get_print_vars(),get_object_vars($this));
	}
}
?>