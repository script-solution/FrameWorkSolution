<?php
/**
 * Contains the property-accessor-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The property-accessor for the library. Provides methods to access the properties. If a property
 * does not exist, the loader will be used to load the property.
 * <br>
 * You can set your own property-loader if you want to change the predefined properties. If you
 * do that the properties should be compatible to the default ones. That means you should inherit
 * from the default properties if you want to change them instead of providing your own one.
 * <br>
 * You can inherit from this class, too, if you want to provide additional properties
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_PropAccessor extends PLIB_Object
{
	/**
	 * The instance of the property-loader
	 *
	 * @var PLIB_PropLoader
	 */
	private $_loader;
	
	/**
	 * The loaded properties
	 *
	 * @var array
	 */
	private $_instances = array();
	
	/**
	 * The document
	 *
	 * @var PLIB_Document
	 */
	protected $_doc = null;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		// use the default loader
		$this->_loader = new PLIB_PropLoader();
	}
	
	/**
	 * Sets the loader that should be used for the properties
	 *
	 * @param PLIB_PropLoader $loader the loader
	 */
	public final function set_loader($loader)
	{
		if(!($loader instanceof PLIB_PropLoader))
			PLIB_Helper::def_error('instance','loader','PLIB_PropLoader',$loader);
		
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
		if($this->_doc !== null)
			return array_merge($this->_instances,array('doc' => $this->_doc));
		
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
	 * @return PLIB_Document the document-instance (has to be set manually!)
	 */
	public function doc()
	{
		return $this->_doc;
	}
	
	/**
	 * Sets the document instance for this request
	 *
	 * @param PLIB_Document $doc the document
	 */
	public function set_doc($doc)
	{
		if(!($doc instanceof PLIB_Document))
			PLIB_Helper::def_error('instance','doc','PLIB_Document',$doc);
		
		$this->_doc = $doc;
	}
	
	/**
	 * @return PLIB_Profiler the profiler instance
	 */
	public function profiler()
	{
		return $this->get('profiler');
	}
	
	/**
	 * @return PLIB_Session_Manager the session-manager
	 */
	public function sessions()
	{
	  return $this->get('sessions');
	}
	
	/**
	 * @return PLIB_User_Current the current-user-object
	 */
	public function user()
	{
	  return $this->get('user');
	}

	/**
	 * @return PLIB_Cookies the cookies-object
	 */
	public function cookies()
	{
		return $this->get('cookies');
	}

	/**
	 * @return PLIB_URL the URL-object
	 */
	public function url()
	{
		return $this->get('url');
	}

	/**
	 * @return PLIB_Template_Handler the template-object
	 */
	public function tpl()
	{
		return $this->get('tpl');
	}

	/**
	 * @return PLIB_Locale the locale
	 */
	public function locale()
	{
		return $this->get('locale');
	}
	
	/**
	 * @return PLIB_Input the input-instance
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