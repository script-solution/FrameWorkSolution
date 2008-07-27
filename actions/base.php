<?php
/**
 * Contains the base-action-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	actions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-action-class. Every action which may be performed should
 * inherit from this class.
 *
 * @package			PHPLib
 * @subpackage	actions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Actions_Base extends PLIB_Object
{
	/**
	 * The id of this action
	 *
	 * @var int
	 */
	private $_action_id;
	
	/**
	 * Indicates wether something has been done
	 *
	 * @var boolean
	 */
	private $_action_performed = false;
	
	/**
	 * The success-message to display. This may be a custom message or:
	 * <pre>PLIB_Props::get()->locale()->lang('success_&lt;actionID&gt;)</pre>
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
	 * The value to return in {@link PLIB_Actions_Performer::perform_actions()} if an
	 * error has occurred
	 *
	 * @var int
	 */
	private $_error_return_val = -1;
	
	/**
	 * Wether the user should be redirected.
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
	 * @var string
	 */
	private $_redirect_url = '';
	
	/**
	 * The time to wait before the redirect
	 *
	 * @var int
	 */
	private $_redirect_time = 3;
	
	/**
	 * Wether we want to display a 'status-page'
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
		
		if(!PLIB_Helper::is_integer($id) || $id < 0)
			PLIB_Helper::def_error('intge0','id',$id);
		
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
	 * @return boolean wether something has been done
	 */
	public final function get_action_performed()
	{
		return $this->_action_performed;
	}

	/**
	 * Sets wether something has been done
	 * 
	 * @param boolean $performed has something been done?
	 */
	protected final function set_action_performed($performed)
	{
		$this->_action_performed = $performed;
	}

	/**
	 * @return int the value to return in {@link PLIB_Actions_Performer::perform_actions()} if an
	 * 	error has occurred
	 */
	public final function get_error_return_val()
	{
		return $this->_error_return_val;
	}

	/**
	 * Sets the value to return in {@link PLIB_Actions_Performer::perform_actions()} if an
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
	 * @param string $links the name of the link
	 * @param string $url the URL of the link
	 */
	protected final function add_link($name,$url)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
		if(empty($url))
			PLIB_Helper::def_error('notempty','url',$url);
		
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
	 * <pre>PLIB_Props::get()->locale()->lang('success_&lt;actionID&gt;)</pre>
	 * 
	 * @param string $msg the new value
	 */
	protected final function set_success_msg($msg)
	{
		if(empty($msg))
			PLIB_Helper::def_error('notempty','msg',$msg);
		
		$this->_success_msg = $msg;
	}

	/**
	 * @return boolean wether we should redirect the user
	 */
	public final function get_redirect()
	{
		return $this->_redirect;
	}

	/**
	 * Sets wether the user should be redirected
	 * 
	 * @param boolean $redirect the new value
	 * @param string $url the URL to redirect to (may be empty to use the first link)
	 * @see add_link($name,$url)
	 */
	protected final function set_redirect($redirect,$url = '',$time = 3)
	{
		if(!PLIB_Helper::is_integer($time) || $time < 0)
			PLIB_Helper::def_error('intge0','time',$time);
		
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
	 * @return string the URL to redirect to
	 */
	public final function get_redirect_url()
	{
		if(empty($this->_redirect_url))
		{
			if(count($this->_links) == 0)
			{
				PLIB_Helper::error(
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
	 * @return boolean wether a 'status-page' should be displayed
	 */
	public final function show_status_page()
	{
		return $this->_show_status_page;
	}
	
	/**
	 * Sets wether a 'status-page' should be displayed
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
	 * @return mixed the error-message or an empty string. May be an array, too
	 */
	public abstract function perform_action();
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>