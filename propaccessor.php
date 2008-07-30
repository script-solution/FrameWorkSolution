<?php
/**
 * Contains the property-accessor-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The property-accessor for the framework. Provides methods to access the properties. If a property
 * does not exist, the loader will be used to load the property.
 * <br>
 * You can set your own property-loader if you want to change the predefined properties. If you
 * do that the properties should be compatible to the default ones. That means you should inherit
 * from the default properties if you want to change them instead of providing your own one.
 * <br>
 * You can inherit from this class, too, if you want to provide additional properties
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_PropAccessor extends FWS_Object
{
	/**
	 * The instance of the property-loader
	 *
	 * @var FWS_PropLoader
	 */
	private $_loader;
	
	/**
	 * The loaded properties
	 *
	 * @var array
	 */
	private $_instances = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		// use the default loader
		$this->_loader = new FWS_PropLoader();
	}
	
	/**
	 * Sets the loader that should be used for the properties
	 *
	 * @param FWS_PropLoader $loader the loader
	 */
	public final function set_loader($loader)
	{
		if(!($loader instanceof FWS_PropLoader))
			FWS_Helper::def_error('instance','loader','FWS_PropLoader',$loader);
		
		$this->_loader = $loader;
	}
	
	/**
	 * Reloads the property with given name
	 *
	 * @param string $name the name of the property
	 */
	public final function reload($name)
	{
		$this->_instances[$name] = $this->_loader->load($name);
	}
	
	/**
	 * @return array all properties: <code>array(<name> => <value>)</code>
	 */
	public final function get_all()
	{
		return $this->_instances;
	}
	
	/**
	 * Returns the property with given name. If it does not exists the property will be loaded
	 * by the specified loader.
	 *
	 * @param string $name the property-name
	 * @return mixed the property
	 */
	protected final function get($name)
	{
		if(!isset($this->_instances[$name]))
			$this->_instances[$name] = $this->_loader->load($name);
		return $this->_instances[$name];
	}
	
	/**
	 * @return FWS_Document the document-instance
	 */
	public function doc()
	{
		return $this->get('doc');
	}
	
	/**
	 * @return FWS_Document_Messages the messages-container
	 */
	public function msgs()
	{
		return $this->get('msgs');
	}
	
	/**
	 * @return FWS_Profiler the profiler instance
	 */
	public function profiler()
	{
		return $this->get('profiler');
	}
	
	/**
	 * @return FWS_Session_Manager the session-manager
	 */
	public function sessions()
	{
	  return $this->get('sessions');
	}
	
	/**
	 * @return FWS_User_Current the current-user-object
	 */
	public function user()
	{
	  return $this->get('user');
	}

	/**
	 * @return FWS_Cookies the cookies-object
	 */
	public function cookies()
	{
		return $this->get('cookies');
	}

	/**
	 * @return FWS_Template_Handler the template-object
	 */
	public function tpl()
	{
		return $this->get('tpl');
	}

	/**
	 * @return FWS_Locale the locale
	 */
	public function locale()
	{
		return $this->get('locale');
	}
	
	/**
	 * @return FWS_Input the input-instance
	 */
	public function input()
	{
		return $this->get('input');
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>