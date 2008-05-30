<?php
/**
 * Contains the message-container-class
 *
 * @version			$Id: messages.php 672 2008-05-05 21:58:06Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The message-container. Collects messages (errors, warnings and notices) which
 * may be displayed at some place and time in the document.
 * This class is abstract because print_messages() has to be implemented!
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Messages extends PLIB_FullObject
{
	/**
	 * Represents an error.
	 * This is intended for failures. For example if the user hasn't filled
	 * a required field in a formular.
	 */
	const MSG_TYPE_ERROR			= 0;
	
	/**
	 * Represents a warning.
	 * This may be used for something which is not wrong but may cause trouble.
	 * So it should work if you ignore it but you should consider changing something.
	 */
	const MSG_TYPE_WARNING		= 1;
	
	/**
	 * Represents a notice.
	 * Notices may be success-messages after an action or similar.
	 */
	const MSG_TYPE_NOTICE			= 2;
	
	/**
	 * Represents a no-access problem.
	 * This may be used if a user has no access to a module for example.
	 */
	const MSG_TYPE_NO_ACCESS	= 3;
	
	/**
	 * Contains all messages
	 *
	 * @var array
	 */
	private $_messages = array(
		self::MSG_TYPE_ERROR => array(),
		self::MSG_TYPE_WARNING => array(),
		self::MSG_TYPE_NOTICE => array(),
		self::MSG_TYPE_NO_ACCESS => array()
	);
	
	/**
	 * An array of links that may be displayed somewhere
	 *
	 * @var array
	 */
	private $_links = array();
	
	/**
	 * @return array an array of links that may be displayed somewhere
	 */
	public final function get_links()
	{
		return $this->_links;
	}
	
	/**
	 * Adds the given link to the printer
	 *
	 * @param string $name the name or title of the link
	 * @param string $url the URL of the link
	 */
	public final function add_link($name,$url)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
		if(empty($url))
			PLIB_Helper::def_error('notempty','url',$url);
		
		$this->_links[$name] = $url;
	}
	
	/**
	 * Adds the given error-message to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_error($msg)
	{
		$this->add_message($msg,self::MSG_TYPE_ERROR);
	}
	
	/**
	 * Adds the given warning to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_warning($msg)
	{
		$this->add_message($msg,self::MSG_TYPE_WARNING);
	}
	
	/**
	 * Adds the given notice to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_notice($msg)
	{
		$this->add_message($msg,self::MSG_TYPE_NOTICE);
	}
	
	/**
	 * Adds the given no-access-message to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_noaccess($msg)
	{
		$this->add_message($msg,self::MSG_TYPE_NO_ACCESS);
	}
	
	/**
	 * Adds the given message of given type to the container
	 *
	 * @param string $msg the message
	 * @param int $type the message-type; see PLIB_Messages::MSG_TYPE_*
	 */
	public final function add_message($msg,$type = self::MSG_TYPE_NOTICE)
	{
		if(!$this->_is_valid_type($type))
			PLIB_Helper::error('Invalid type: '.$type.'!');
		
		if(empty($msg))
			PLIB_Helper::def_error('notempty','msg',$msg);
		
		$this->_messages[$type][] = $msg;
	}
	
	/**
	 * @return boolean wether an error has been added
	 * @see contains($type)
	 */
	public final function containsError()
	{
		return $this->contains(self::MSG_TYPE_ERROR);
	}
	
	/**
	 * @return boolean wether a warning has been added
	 * @see contains($type)
	 */
	public final function containsWarning()
	{
		return $this->contains(self::MSG_TYPE_WARNING);
	}
	
	/**
	 * @return boolean wether a notice has been added
	 * @see contains($type)
	 */
	public final function containsNotice()
	{
		return $this->contains(self::MSG_TYPE_NOTICE);
	}
	
	/**
	 * @return boolean wether a no-access message has been added
	 * @see contains($type)
	 */
	public final function containsNoAccess()
	{
		return $this->contains(self::MSG_TYPE_NO_ACCESS);
	}
	
	/**
	 * Checks wether a message of given type exists
	 *
	 * @param int $type the message-type; see PLIB_Messages::MSG_TYPE_*
	 * @return boolean true if there has been added a message of given type
	 */
	public final function contains($type)
	{
		if(!$this->_is_valid_type($type))
			PLIB_Helper::error('Invalid type: '.$type.'!');
		
		return count($this->_messages[$type]) > 0;
	}
	
	/**
	 * Returns all added messages
	 *
	 * @return array the messages:
	 * 	<pre>
	 * 	array(
	 * 		&lt;type&gt; => array(&lt;msg1&gt;,...,&lt;msgN&gt;),
	 * 		...
	 * 	)
	 * 	</pre>
	 * @see get_messages($type)
	 */
	public final function get_all_messages()
	{
		return $this->_messages;
	}
	
	/**
	 * Returns all messages of given type
	 *
	 * @param int $type the message-type; see PLIB_Messages::MSG_TYPE_*
	 * @return array a numeric array with all messages of the type
	 * @see get_all_messages()
	 */
	public final function get_messages($type)
	{
		if(!$this->_is_valid_type($type))
			PLIB_Helper::error('Invalid type: '.$type.'!');
		
		return $this->_messages[$type];
	}
	
	/**
	 * The method which should "print" the messages.
	 */
	public abstract function print_messages();
	
	/**
	 * Checks wether the given type is valid
	 *
	 * @param int $type the type
	 * @return boolean true if the type is valid
	 */
	private function _is_valid_type($type)
	{
		$valid = array(
			self::MSG_TYPE_ERROR,
			self::MSG_TYPE_WARNING,
			self::MSG_TYPE_NOTICE,
			self::MSG_TYPE_NO_ACCESS
		);
		return in_array($type,$valid);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>