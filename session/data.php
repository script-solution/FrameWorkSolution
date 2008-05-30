<?php
/**
 * Contains the session-data-class
 *
 * @version			$Id: data.php 736 2008-05-23 18:24:22Z nasmussen $
 * @package			PHPLib
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class represents a user who is currently online. It provides method
 * to read and write the properties of the user and stores wether something
 * has changed in this object.
 *
 * @package			PHPLib
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Session_Data extends PLIB_FullObject
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
    if(!PLIB_Helper::is_integer($value) || $value < 0)
    	PLIB_Helper::def_error('intge0','value',$value);

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
    	PLIB_Helper::def_error('string','value',$value);

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
    	PLIB_Helper::def_error('string','value',$value);

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
    	PLIB_Helper::def_error('string','value',$value);

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
		if(!PLIB_Helper::is_integer($value) || $value < 0)
			PLIB_Helper::def_error('intge0','value',$value);

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
    	PLIB_Helper::def_error('string','value',$value);

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
			PLIB_Helper::def_error('string','value',$value);

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
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>