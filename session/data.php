<?php
/**
 * Contains the session-data-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	session
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
 * This class represents a user who is currently online. It provides method
 * to read and write the properties of the user and stores wether something
 * has changed in this object.
 *
 * @package			FrameWorkSolution
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Session_Data extends FWS_Object
{
	/**
	 * Stores wether this entry has changed
	 *
	 * @var boolean
	 */
	protected $_has_changed = false;

	/**
	 * The id of the user (0 if it is a guest)
	 *
	 * @var integer
	 */
	private $_user_id = 0;

	/**
	 * The name of the user (empty if it is a guest)
	 *
	 * @var string
	 */
	private $_user_name = '';

	/**
	 * The ip of the user
	 *
	 * @var string
	 */
	private $_user_ip = '';

	/**
	 * The session-id
	 *
	 * @var string
	 */
	private $_session_id = null;

	/**
	 * The date of the last access
	 *
	 * @var integer
	 */
	private $_date = 0;

	/**
	 * The user-agent of this user
	 *
	 * @var string
	 */
	private $_user_agent = '';
	
	/**
	 * Wether the user uses a mobile device
	 *
	 * @var boolean
	 */
	private $_uses_mobile_device = null;

	/**
	 * The session-data of this user
	 *
	 * @var string
	 */
	private $_session_data = '';

	/**
	 * Constructor
	 * 
	 * @param string $session_id the session-id
	 * @param int $user_id the user-id
	 * @param string $user_ip the user-ip
	 * @param string $user_name the user-name
	 * @param int $date the date
	 * @param string $user_agent the user-agent
	 * @param string $session_data the session-data
	 */
	public function __construct($session_id = null,$user_id = 0,$user_ip = '',
		$user_name = '',$date = 0,$user_agent = '',$session_data = '')
	{
		parent::__construct();
		
		$this->_session_id = $session_id;
		$this->_user_id = $user_id;
		$this->_user_ip = $user_ip;
		$this->_user_name = $user_name;
		$this->_date = $date;
		$this->_user_agent = $user_agent;
		$this->_session_data = $session_data;
	}
	
	/**
	 * @return boolean wether this user is logged in
	 */
	public final function is_loggedin()
	{
		return $this->_user_id > 0;
	}
	
	/**
	 * @return boolean wether this user is a guest
	 */
	public final function is_guest()
	{
		return $this->_user_id == 0;
	}
	
	/**
	 * Indicates wether the user uses a mobile device
	 *
	 * @return boolean true if so
	 */
	public function uses_mobile_device()
	{
		if($this->_uses_mobile_device === null)
		{
			$mobile = array(
				'AvantGo',
				'DoCoMo/',
				'UP.Browser/',
				'Vodafone/',
				'J-PHONE/',
				'DDIPOCKET',
				'PDXGW/',
				'ASTEL/',
				'PalmOS',
				'Windows CE',
				'PalmSource',
				'Mobile Content Viewer',
				'PlayStation BB Navigator',
				'PDA',
				'Xiino/',
				'BlackBerry',
				'Plucker/',
				'MMP/',
				'MIB/',
				'portalmmm/',
				'MIDP-1.0',
				'MIDP-2.0',
				'SymbianOS/',
				'Symbian OS',
				'SmartPhone',
				'Blazer',
				'RegKing',
				'EPOC',
				'Opera Mini/',
				'ReqwirelessWeb/',
				'PlayStation Portable',
				'Elaine/',
				'iPhone;',
				'Android',
			);
		
			// is it a mobile device?
			$this->_uses_mobile_device = false;
			$useragent = $this->get_user_agent();
			foreach($mobile as $m)
			{
				if(strstr($useragent,$m) !== false)
				{
					$this->_uses_mobile_device = true;
					break;
				}
			}
		}
		
		return $this->_uses_mobile_device;
	}

	/**
	 * @return boolean wether something has changed in this object
	 */
	public final function has_changed()
	{
		return $this->_has_changed;
	}

	/**
	 * @return int the user-id
	 */
	public final function get_user_id()
	{
		return $this->_user_id;
	}

	/**
	 * Sets the user-id to given value
	 *
	 * @param int $value the new value
	 */
	public final function set_user_id($value)
	{
		if(!FWS_Helper::is_integer($value) || $value < 0)
			FWS_Helper::def_error('intge0','value',$value);

		$this->_has_changed |= $value != $this->_user_id;
		$this->_user_id = $value;
	}

	/**
	 * @return string the user-name
	 */
	public final function get_user_name()
	{
		return $this->_user_name;
	}

	/**
	 * Sets the user-name to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_user_name($value)
	{
		if(!is_string($value))
			FWS_Helper::def_error('string','value',$value);

		$this->_has_changed |= $value != $this->_user_name;
		$this->_user_name = $value;
	}

	/**
	 * @return string the user-ip
	 */
	public final function get_user_ip()
	{
		return $this->_user_ip;
	}

	/**
	 * Sets the user-ip to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_user_ip($value)
	{
		if(!is_string($value))
			FWS_Helper::def_error('string','value',$value);

		$this->_has_changed |= $value != $this->_user_ip;
		$this->_user_ip = $value;
	}

	/**
	 * @return string the session-id
	 */
	public final function get_session_id()
	{
		return $this->_session_id;
	}

	/**
	 * Sets the session-id to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_session_id($value)
	{
		if(!is_string($value))
			FWS_Helper::def_error('string','value',$value);

		$this->_has_changed |= $value != $this->_session_id;
		$this->_session_id = $value;
	}

	/**
	 * @return int the date
	 */
	public final function get_date()
	{
		return $this->_date;
	}

	/**
	 * Sets the date to given value
	 *
	 * @param int $value the new value
	 */
	public final function set_date($value)
	{
		if(!FWS_Helper::is_integer($value) || $value < 0)
			FWS_Helper::def_error('intge0','value',$value);

		$this->_has_changed |= $value != $this->_date;
		$this->_date = $value;
	}

	/**
	 * @return string the user-agent
	 */
	public final function get_user_agent()
	{
		return $this->_user_agent;
	}

	/**
	 * Sets the user-agent to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_user_agent($value)
	{
		if(!is_string($value))
			FWS_Helper::def_error('string','value',$value);

		$this->_has_changed |= $value != $this->_user_agent;
		$this->_user_agent = $value;
	}

	/**
	 * @return string the session-data
	 */
	public final function get_session_data()
	{
		return $this->_session_data;
	}

	/**
	 * Sets the session-data to given value
	 *
	 * @param string $value the new value
	 */
	public final function set_session_data($value)
	{
		if(!is_string($value))
			FWS_Helper::def_error('string','value',$value);

		$this->_has_changed |= $value != $this->_session_data;
		$this->_session_data = $value;
	}

	/**
	 * Makes this user to a guest
	 */
	public final function make_guest()
	{
		$this->set_user_id(0);
		$this->set_user_name('');
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>