<?php
/**
 * Contains the email-base-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for sending emails. Contains the attributes for the emails (subject, body,
 * receiver, ...) and provides methods to change them
 *
 * @package			FrameWorkSolution
 * @subpackage	email
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Email_Base extends FWS_Object
{
	/**
	 * the subject of the email
	 *
	 * @var string
	 */
	private $_subject = '';

	/**
	 * the recipient of the email
	 *
	 * @var string
	 */
	private $_recipient = '';

	/**
	 * the message
	 *
	 * @var string
	 */
	private $_message = '';

	/**
	 * the sender-address
	 *
	 * @var string
	 */
	private $_from;

	/**
	 * an array with the bcc-recipients
	 *
	 * @var array
	 */
	private $_bcc_recipients = array();

	/**
	 * the content-type
	 *
	 * @var string
	 */
	private $_content_type = 'text/plain';

	/**
	 * The x-mailer name
	 *
	 * @var string
	 */
	private $_xmailer = 'FrameWorkSolution';
	
	/**
	 * The charset for the email
	 *
	 * @var string
	 */
	private $_charset = 'UTF-8';

	/**
	 * The error message that occurred, if any
	 *
	 * @var string
	 */
	protected $_error_message = '';

	/**
	 * Constructor
	 *
	 * @param string $recipient the email-address of the recipient
	 * @param string $subject the subject of the email
	 * @param string $message the message
	 */
	public function __construct($recipient = '',$subject = '',$message = '')
	{
		parent::__construct();
		
		if($recipient)
			$this->set_recipient($recipient);
		if($subject)
			$this->set_subject($subject);
		if($message)
			$this->set_message($message);
	}

	/**
	 * Retrieves the error message which has been set.
	 * Will be empty if everything is ok
	 *
	 * @return string the error-message
	 */
	public final function get_error_message()
	{
		return $this->_error_message;
	}

	/**
	 * @return string the subject of the email
	 */
	public final function get_subject()
	{
		return $this->_subject;
	}

	/**
	 * Sets the subject to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_subject($value)
	{
		if(empty($value))
			FWS_Helper::def_error('notempty','value',$value);
		
		$this->_subject = $value;
	}

	/**
	 * @return string the recipient of the email
	 */
	public final function get_recipient()
	{
		return $this->_recipient;
	}

	/**
	 * sets the recipient to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_recipient($value)
	{
		$this->_recipient = '';
		$recipients = explode(',',$value);
		foreach($recipients as $recipient)
		{
			$recipient = trim($recipient);
			if(!FWS_StringHelper::is_valid_email($recipient))
				FWS_Helper::error('The email-address "'.$recipient.'" is invalid!');
		
			$this->_recipient .= $recipient;
		}
	}

	/**
	 * @return string the message of the email
	 */
	public final function get_message()
	{
		return $this->_message;
	}

	/**
	 * sets the message to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_message($value)
	{
		if(empty($value))
			FWS_Helper::def_error('notempty','value',$value);
		
		$this->_message = $value;
	}

	/**
	 * @return string the sender-address of the email
	 */
	public final function get_from()
	{
		return $this->_from;
	}

	/**
	 * sets the sender-address to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_from($value)
	{
		if(!FWS_StringHelper::is_valid_email($value))
			FWS_Helper::error('The email-address "'.$value.'" is invalid!');
		
		$this->_from = $value;
	}

	/**
	 * @return array a reference to the numeric array with the bcc-recipients
	 */
	public final function &get_bcc_recipients()
	{
		return $this->_bcc_recipients;
	}

	/**
	 * Removes all bcc-recipients from the list
	 */
	public final function clear_bcc_recipients()
	{
		$this->_bcc_recipients = array();
	}

	/**
	 * adds the given recipient to the bcc-list
	 *
	 * @param string $email the email-address of the recipient
	 */
	public final function add_bcc_recipient($email = '')
	{
		if(empty($email))
			FWS_Helper::def_error('notempty','email',$email);

		$this->_bcc_recipients[] = $email;
	}

	/**
	 * sets the bcc-recipients
	 *
	 * @param array $recipients an numeric array with the recipients
	 */
	public final function set_bcc_recipients(&$recipients)
	{
		if(!is_array($recipients))
			FWS_Helper::def_error('array','recipients',$recipients);

		$this->_bcc_recipients = &$recipients;
	}

	/**
	 * @return string the content-type of the email
	 */
	public final function get_content_type()
	{
		return $this->_content_type;
	}

	/**
	 * sets the content-type to given value
	 *
	 * @param string $content_type the content-type of the email
	 */
	public final function set_content_type($content_type)
	{
		if(empty($content_type))
			FWS_Helper::def_error('notempty','content_type',$content_type);
		
		$this->_content_type = $content_type;
	}
	
	/**
	 * @return string the charset for the email
	 */
	public final function get_charset()
	{
		return $this->_charset;
	}
	
	/**
	 * Sets the charset for the email
	 *
	 * @param string $charset the new value
	 */
	public final function set_charset($charset)
	{
		if(empty($charset))
			FWS_Helper::def_error('notempty','charset',$charset);
		
		$this->_charset = $charset;
	}

	/**
	 * @return string the xmailer (the name of the mailer)
	 */
	public final function get_xmailer()
	{
		return $this->_xmailer;
	}

	/**
	 * Sets the x-mailer to given value (the name of the mailer)
	 *
	 * @param string $xmailer the new value
	 */
	public final function set_xmailer($xmailer)
	{
		if(empty($xmailer))
			FWS_Helper::def_error('notempty','xmailer',$xmailer);
		
		$this->_xmailer = $xmailer;
	}

	/**
	 * Checks if all required attributes are set
	 *
	 * @return boolean true if so
	 */
	protected function check_attributes()
	{
		// check if all required attributes are set
		if($this->_subject == '')
			return false;

		if($this->_recipient == '' && count($this->_bcc_recipients) == 0)
			return false;

		if($this->_message == '')
			return false;

		if($this->_content_type == '')
			return false;

		if($this->_from == '')
			return false;

		return true;
	}

	/**
	 * Builds the header
	 *
	 * @param string $method mail or smtp
	 * @return string the header
	 */
	protected function build_header($method = 'mail')
	{
		$headers = 'From: '.$this->_from."\n";

		if($method == 'smtp' && $this->_recipient != '')
			$headers .= 'To: <'.$this->_recipient.'>'."\n";

		if(($len = count($this->_bcc_recipients)) > 0)
		{
			$headers .= 'Bcc: ';
			for($i = 0;$i < $len;$i++)
			{
				$headers .= $this->_bcc_recipients[$i];
				if($i < $len - 1)
					$headers .= ', ';
			}
			$headers .= "\n";
		}

		if($method == 'smtp')
			$headers .= 'Subject: '.$this->_subject."\n";

		$headers .= 'Return-Path: '.$this->_from."\n";
		$headers .= 'MIME-Version: 1.0'."\n";
		$headers .= 'Date: '.gmdate('D, d M Y H:i:s O',time())."\n";
		$headers .= 'Content-type: '.$this->_content_type.'; charset='.$this->_charset."\n";
		$headers .= 'Content-transfer-encoding: 8bit'."\n";
		$headers .= 'X-Priority: 3 (Normal)'."\n";
		$headers .= 'X-MSMail-Priority: Normal'."\n";
		$headers .= 'X-Mailer: '.$this->_xmailer."\n";
		$headers .= 'Importance: Normal';

		return $headers;
	}

	/**
	 * Wraps the html-body around the text
	 *
	 * @param string $text your text
	 * @param string $charset the charset to use
	 * @return string the text to send
	 */
	protected function prepare_html_message($text)
	{
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"';
		$html .= ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$html .= '<html>'."\n";
		$html .= '<head>'."\n";
		$html .= '	<meta http-equiv="Content-Type" content="text/html; charset='
			.$this->_charset.'" />'."\n";
		$html .= '	<meta http-equiv="Content-Style-Type" content="text/css" />'."\n";
		$html .= '</head>'."\n";
		$html .= '<body>'."\n";
		$html .= $text;
		$html .= '</body>'."\n";
		$html .= '</html>';
		return $html;
	}

	/**
	 * Reports an error with given message
	 *
	 * @param string $msg the message
	 */
	protected function report_error($msg)
	{
		$this->_error_message = htmlspecialchars($msg,ENT_QUOTES);
	}

	/**
	 * sends the email
	 *
	 * @return boolean true if the message has been send
	 */
	public abstract function send_mail();
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>