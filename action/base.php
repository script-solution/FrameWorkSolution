<?php
/**
 * Contains the base-action-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	action
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
 * The base-action-class. Every action which may be performed should
 * inherit from this class.
 *
 * @package			FrameWorkSolution
 * @subpackage	action
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Action_Base extends FWS_Object
{
	/**
	 * The id of this action
	 *
	 * @var int
	 */
	private $_action_id;
	
	/**
	 * Indicates whether something has been done
	 *
	 * @var boolean
	 */
	private $_action_performed = false;
	
	/**
	 * The success-message to display. This may be a custom message or:
	 * <pre>FWS_Props::get()->locale()->lang('success_&lt;actionID&gt;)</pre>
	 *
	 * @var boolean
	 */
	private $_success_msg = '';
	
	/**
	 * An array of links to display:
	 * <pre>
	 * 	array(
	 * 		&lt;name&gt; => &lt;URL&gt;,
	 * 		...
	 * 	)
	 * </pre>
	 *
	 * @var array
	 */
	private $_links = array();
	
	/**
	 * The value to return in {@link FWS_Action_Performer::perform_action()} if an
	 * error has occurred
	 *
	 * @var int
	 */
	private $_error_return_val = -1;
	
	/**
	 * Whether the user should be redirected.
	 * If <var>$this->get_redirect_url()</var> has not been specified the first
	 * URL in <var>$this->get_links()</var> will be used.
	 *
	 * @var boolean
	 */
	private $_redirect = true;
	
	/**
	 * The URL to redirect to.
	 * If it has not been specified the first URL in <var>$this->get_links()</var> will be used.
	 *
	 * @var FWS_URL
	 */
	private $_redirect_url = null;
	
	/**
	 * The time to wait before the redirect
	 *
	 * @var int
	 */
	private $_redirect_time = 3;
	
	/**
	 * Whether we want to display a 'status-page'
	 *
	 * @var boolean
	 */
	private $_show_status_page = true;
	
	/**
	 * Constructor
	 *
	 * @param int $id the id of this action
	 */
	public function __construct($id)
	{
		parent::__construct();
		
		if(!FWS_Helper::is_integer($id) || $id < 0)
			FWS_Helper::def_error('intge0','id',$id);
		
		$this->_action_id = $id;
	}
	
	/**
	 * @return int the id of this action
	 */
	public final function get_action_id()
	{
		return $this->_action_id;
	}

	/**
	 * @return boolean whether something has been done
	 */
	public final function get_action_performed()
	{
		return $this->_action_performed;
	}

	/**
	 * Sets whether something has been done
	 * 
	 * @param boolean $performed has something been done?
	 */
	protected final function set_action_performed($performed)
	{
		$this->_action_performed = $performed;
	}

	/**
	 * @return int the value to return in {@link FWS_Action_Performer::perform_action()} if an
	 * 	error has occurred
	 */
	public final function get_error_return_val()
	{
		return $this->_error_return_val;
	}

	/**
	 * Sets the value to return in {@link FWS_Action_Performer::perform_action()} if an
	 * error has occurred
	 * 
	 * @param int $val the value to return
	 */
	protected final function set_error_return_val($val)
	{
		$this->_error_return_val = $val;
	}

	/**
	 * @return array the links to display:
	 * <pre>
	 * 	array(
	 * 		&lt;name&gt; => &lt;URL&gt;,
	 * 		...
	 * 	)
	 * </pre>
	 */
	public final function get_links()
	{
		return $this->_links;
	}

	/**
	 * Adds the given link to the action
	 * 
	 * @param string $name the name of the link
	 * @param FWS_URL|string $url the URL of the link (may also be a string, if you want to specify the URL
	 * 	manually)
	 */
	protected final function add_link($name,$url)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(!is_string($url) && !($url instanceof FWS_URL))
			FWS_Helper::def_error('instance','url','FWS_URL',$url);
		
		$this->_links[$name] = $url;
	}

	/**
	 * @return string the success-message
	 */
	public final function get_success_msg()
	{
		return $this->_success_msg;
	}

	/**
	 * Sets the success-message to display. This may be a custom message or:
	 * <pre>FWS_Props::get()->locale()->lang('success_&lt;actionID&gt;)</pre>
	 * 
	 * @param string $msg the new value
	 */
	protected final function set_success_msg($msg)
	{
		if(empty($msg))
			FWS_Helper::def_error('notempty','msg',$msg);
		
		$this->_success_msg = $msg;
	}

	/**
	 * @return boolean whether we should redirect the user
	 */
	public final function get_redirect()
	{
		return $this->_redirect;
	}

	/**
	 * Sets whether the user should be redirected
	 * 
	 * @param boolean $redirect the new value
	 * @param FWS_URL|string $url the URL to redirect to (may be null to use the first link). May also be
	 * 	a string, if you want to specify the URL manually.
	 * @see add_link($name,$url)
	 */
	protected final function set_redirect($redirect,$url = null,$time = 3)
	{
		if(!FWS_Helper::is_integer($time) || $time < 0)
			FWS_Helper::def_error('intge0','time',$time);
		if(!is_string($url) && $url !== null && !($url instanceof FWS_URL))
			FWS_Helper::def_error('instance','url','FWS_URL',$url);
		
		$this->_redirect = $redirect;
		$this->_redirect_url = $url;
		$this->_redirect_time = $time;
	}

	/**
	 * @return int the time to wait before redirect
	 */
	public final function get_redirect_time()
	{
		return $this->_redirect_time;
	}

	/**
	 * @return FWS_URL|string the URL to redirect to
	 */
	public final function get_redirect_url()
	{
		if($this->_redirect_url === null)
		{
			if(count($this->_links) == 0)
			{
				FWS_Helper::error(
					'You have no links and no redirect-url specified but want to redirect! ('
					.get_class($this).')'
				);
			}
			
			reset($this->_links);
			list(,$first) = each($this->_links);
			return $first;
		}
		
		return $this->_redirect_url;
	}

	/**
	 * @return boolean whether a 'status-page' should be displayed
	 */
	public final function show_status_page()
	{
		return $this->_show_status_page;
	}
	
	/**
	 * Sets whether a 'status-page' should be displayed
	 *
	 * @param boolean $show the new value
	 */
	protected final function set_show_status_page($show)
	{
		$this->_show_status_page = (bool)$show;
	}

	/**
	 * Performs the action and returns the error-message if any or an empty
	 * string
	 *
	 * @return string|array the error-message or an empty string. May be an array, too
	 */
	public abstract function perform_action();
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>