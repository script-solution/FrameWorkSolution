<?php
/**
 * Contains the cookie-handling functions
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Makes the cookie-handling easier. Can get, set and delete cookies.
 * You can specify the domain and path for all cookies.
 * Additionally you have the opportunity to give all cookies a prefix to prevent
 * conflicts.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Cookies extends FWS_Object
{
	/**
	 * The prefix for all cookies
	 *
	 * @var string
	 */
	private $_prefix;

	/**
	 * The default lifetime for cookies
	 *
	 * @var integer
	 */
	private $_lifetime;

	/**
	 * The cookie-domain to use
	 *
	 * @var string
	 */
	private $_domain;

	/**
	 * The cookie-path to use
	 *
	 * @var string
	 */
	private $_path;

	/**
	 * Constructor
	 *
	 * @param string $prefix the cookie-prefix
	 * @param string $path the cookie-path
	 * @param string $domain the cookie-domain
	 * @param int $lifetime the default lifetime
	 */
	public function __construct($prefix,$path = '/',$domain = '',$lifetime = 0)
	{
		parent::__construct();
		
		if(!FWS_Helper::is_integer($lifetime) || $lifetime < 0)
			FWS_Helper::def_error('intge0','lifetime',$lifetime);
		
		$this->_prefix = (string)$prefix;
		$this->_path = (string)$path;
		$this->_domain = (string)$domain;
		$this->_lifetime = $lifetime;
	}

	/**
	 * @return string the cookie-prefix
	 */
	public function get_prefix()
	{
		return $this->_prefix;
	}

	/**
	 * Sets the cookie-prefix to given value
	 *
	 * @param string $prefix the new value
	 */
	public function set_prefix($prefix)
	{
		$this->_prefix = (string)$prefix;
	}

	/**
	 * @return string the cookie-path
	 */
	public function get_path()
	{
		return $this->_path;
	}

	/**
	 * Sets the cookie-path to given value
	 *
	 * @param string $path the new value
	 */
	public function set_path($path)
	{
		$this->_path = (string)$path;
	}

	/**
	 * @return string the cookie-domain
	 */
	public function get_domain()
	{
		return $this->_domain;
	}

	/**
	 * Sets the cookie-domain to given value
	 *
	 * @param string $domain the new value
	 */
	public function set_domain($domain)
	{
		$this->_domain = (string)$domain;
	}

	/**
	 * @return int the default cookie-lifetime
	 */
	public function get_lifetime()
	{
		return $this->_lifetime;
	}

	/**
	 * Sets the default cookie-lifetime to given value.
	 * 0 means that the cookie will be deleted after this session
	 *
	 * @param int $lifetime the new value
	 */
	public function set_lifetime($lifetime)
	{
		if(!FWS_Helper::is_integer($lifetime) || $lifetime < 0)
			FWS_Helper::def_error('intge0','lifetime',$lifetime);

		$this->_lifetime = $lifetime;
	}

	/**
	 * Checks wether a cookie with given name exists
	 *
	 * @param string $name the cookie-name (without prefix)
	 * @see FWS_Input::isset_var()
	 * @return boolean true if it exists
	 */
	public function isset_cookie($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		return FWS_Input::get_instance()->isset_var($this->_prefix.$name,'cookie');
	}

	/**
	 * Returns the value of the cookie with given value. Uses
	 * the method get_var() of {@link FWS_Input}.
	 *
	 * @param string $name the name of the cookie (without prefix)
	 * @param int $type the datatype you expect. See FWS_*
	 * @see FWS_Input::get_var()
	 * @return mixed the value of the cookie (null if not existing or invalid)
	 */
	public function get_cookie($name,$type = FWS_Input::STRING)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		return FWS_Input::get_instance()->get_var($this->_prefix.$name,'cookie',$type);
	}

	/**
	 * Sets a cookie with given name and value for the specified periode of time
	 * NOTE: Please don't use this function to delete a cookie,
	 * but delete_cookie().
	 *
	 * @param string $name the name of the cookie (without the prefix)
	 * @param mixed $value the value of the cookie
	 * @param int $lifetime the time the cookie should exist (-1 = default)
	 */
	public function set_cookie($name,$value,$lifetime = -1)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		if(!FWS_Helper::is_integer($lifetime))
			FWS_Helper::def_error('integer','lifetime',$lifetime);

		FWS_Input::get_instance()->set_var($this->_prefix.$name,'cookie',$value);

		$lf = $lifetime === -1 ? $this->_lifetime : $lifetime;
		$end = $lf === 0 ? 0 : time() + $lf;
		setcookie($this->_prefix.$name,$value,$end,$this->_path,$this->_domain);
	}

	/**
	 * Deletes the cookie with given name
	 *
	 * @param string $name the name of the cookie (without the prefix)
	 */
	public function delete_cookie($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		FWS_Input::get_instance()->unset_var($this->_prefix.$name,'cookie');
		setcookie($this->_prefix.$name,'',time() - 3600,$this->_path,$this->_domain);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>