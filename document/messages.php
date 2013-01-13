<?php
/**
 * Contains the message-container-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	document
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
 * The message-container. Collects messages (errors, warnings and notices) which
 * may be displayed at some place and time in the document.
 * 
 * @package			FrameWorkSolution
 * @subpackage	document
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Document_Messages extends FWS_Object
{
	/**
	 * Represents an error.
	 * This is intended for failures. For example if the user hasn't filled
	 * a required field in a formular.
	 */
	const ERROR			= 0;
	
	/**
	 * Represents a warning.
	 * This may be used for something which is not wrong but may cause trouble.
	 * So it should work if you ignore it but you should consider changing something.
	 */
	const WARNING		= 1;
	
	/**
	 * Represents a notice.
	 * Notices may be success-messages after an action or similar.
	 */
	const NOTICE			= 2;
	
	/**
	 * Represents a no-access problem.
	 * This may be used if a user has no access to a module for example.
	 */
	const NO_ACCESS	= 3;
	
	/**
	 * Contains all messages
	 *
	 * @var array
	 */
	private $_messages = array(
		self::ERROR => array(),
		self::WARNING => array(),
		self::NOTICE => array(),
		self::NO_ACCESS => array()
	);
	
	/**
	 * An array of links that may be displayed somewhere
	 *
	 * @var array
	 */
	private $_links = array();
	
	/**
	 * A list of listeners
	 *
	 * @var array
	 */
	private $_listener = array();
	
	/**
	 * Adds the given listener to the list
	 *
	 * @param FWS_Document_MsgListener $listener your listener
	 */
	public function add_listener($listener)
	{
		if(!($listener instanceof FWS_Document_MsgListener))
			FWS_Helper::def_error('instance','listener','FWS_Document_MsgListener',$listener);
		
		$this->_listener[] = $listener;
	}
	
	/**
	 * Removes the given listener from the list
	 *
	 * @param FWS_Document_MsgListener $listener your listener
	 */
	public function remove_listener($listener)
	{
		if(!($listener instanceof FWS_Document_MsgListener))
			FWS_Helper::def_error('instance','listener','FWS_Document_MsgListener',$listener);
		
		$index = array_search($listener,$this->_listener,true);
		if($index !== false)
			unset($this->_listener[$index]);
	}
	
	/**
	 * Clears the messages and links
	 */
	public function clear()
	{
		$this->_messages[self::ERROR] = array();
		$this->_messages[self::WARNING] = array();
		$this->_messages[self::NOTICE] = array();
		$this->_messages[self::NO_ACCESS] = array();
		$this->_links = array();
	}
	
	/**
	 * Clears all messages of given type
	 *
	 * @param int $type the type
	 */
	public function clear_type($type)
	{
		if(!$this->_is_valid_type($type))
			FWS_Helper::error('Invalid type: '.$type.'!');
		
		$this->_messages[$type] = array();
	}
	
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
	public function add_link($name,$url)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(empty($url))
			FWS_Helper::def_error('notempty','url',$url);
		
		$this->_links[$name] = $url;
	}
	
	/**
	 * Adds the given error-message to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_error($msg)
	{
		$this->add_message($msg,self::ERROR);
	}
	
	/**
	 * Adds the given warning to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_warning($msg)
	{
		$this->add_message($msg,self::WARNING);
	}
	
	/**
	 * Adds the given notice to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_notice($msg)
	{
		$this->add_message($msg,self::NOTICE);
	}
	
	/**
	 * Adds the given no-access-message to the container
	 *
	 * @param string $msg the message
	 */
	public final function add_noaccess($msg)
	{
		$this->add_message($msg,self::NO_ACCESS);
	}
	
	/**
	 * Adds the given message of given type to the container
	 *
	 * @param string $msg the message
	 * @param int $type the message-type; see FWS_Messages::*
	 */
	public function add_message($msg,$type = self::NOTICE)
	{
		if(!$this->_is_valid_type($type))
			FWS_Helper::error('Invalid type: '.$type.'!');
		
		if(empty($msg))
			FWS_Helper::def_error('notempty','msg',$msg);
		
		$this->_messages[$type][] = $msg;
		
		// notify listeners
		foreach($this->_listener as $l)
			$l->received_msg($type,$msg);
	}
	
	/**
	 * @return boolean true if the container contains any message
	 */
	public function contains_msg()
	{
		return $this->contains(self::ERROR) || $this->contains(self::WARNING) ||
			$this->contains(self::NO_ACCESS) || $this->contains(self::NOTICE);
	}
	
	/**
	 * @return boolean wether an error has been added
	 * @see contains($type)
	 */
	public final function contains_error()
	{
		return $this->contains(self::ERROR);
	}
	
	/**
	 * @return boolean wether a warning has been added
	 * @see contains($type)
	 */
	public final function contains_warning()
	{
		return $this->contains(self::WARNING);
	}
	
	/**
	 * @return boolean wether a notice has been added
	 * @see contains($type)
	 */
	public final function contains_notice()
	{
		return $this->contains(self::NOTICE);
	}
	
	/**
	 * @return boolean wether a no-access message has been added
	 * @see contains($type)
	 */
	public final function contains_no_access()
	{
		return $this->contains(self::NO_ACCESS);
	}
	
	/**
	 * Checks wether a message of given type exists
	 *
	 * @param int $type the message-type; see FWS_Messages::*
	 * @return boolean true if there has been added a message of given type
	 */
	public function contains($type)
	{
		if(!$this->_is_valid_type($type))
			FWS_Helper::error('Invalid type: '.$type.'!');
		
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
	 * @param int $type the message-type; see FWS_Messages::*
	 * @return array a numeric array with all messages of the type
	 * @see get_all_messages()
	 */
	public final function get_messages($type)
	{
		if(!$this->_is_valid_type($type))
			FWS_Helper::error('Invalid type: '.$type.'!');
		
		return $this->_messages[$type];
	}
	
	/**
	 * Determines the name of the given type
	 *
	 * @param int $type the type
	 * @return string the name
	 */
	public function get_type_name($type)
	{
		switch($type)
		{
			case FWS_Document_Messages::ERROR:
				return 'Error';
			case FWS_Document_Messages::NOTICE:
				return 'Notice';
			case FWS_Document_Messages::WARNING:
				return 'Warning';
			case FWS_Document_Messages::NO_ACCESS:
				return 'No-Access';
		}
		
		return '';
	}
	
	/**
	 * Checks wether the given type is valid
	 *
	 * @param int $type the type
	 * @return boolean true if the type is valid
	 */
	private function _is_valid_type($type)
	{
		$valid = array(
			self::ERROR,
			self::WARNING,
			self::NOTICE,
			self::NO_ACCESS
		);
		return in_array($type,$valid);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>