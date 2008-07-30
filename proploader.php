<?php
/**
 * Contains the property-loader-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The property-loader of the library. The {@link PLIB_PropAccessor} will use the load method
 * of this class if a property does not exist to load it.
 * <br>
 * You can inherit from this class if you want to change the predefined properties. If you
 * do that the properties should be compatible to the default ones. That means you should inherit
 * from the default properties if you want to change them instead of providing your own one.
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_PropLoader extends PLIB_Object
{
	/**
	 * Loads the property with given name
	 *
	 * @param string $name the name of the property
	 * @return mixed the property
	 */
	public final function load($name)
	{
		if(!method_exists($this,$name))
			PLIB_Helper::error('The method '.$name.'() does not exist.
				Does the property you requested exist?');
		
		return $this->$name();
	}
	
	/**
	 * @return PLIB_Document the document
	 */
	protected function doc()
	{
		return new PLIB_Document();
	}
	
	/**
	 * @return PLIB_Document_Messages the messages-container
	 */
	protected function msgs()
	{
		return new PLIB_Document_Messages();
	}
	
	/**
	 * @return PLIB_Profiler the profiler-instance
	 */
	protected function profiler()
	{
		return new PLIB_Profiler();
	}
	
	/**
	 * @return PLIB_Session_Manager the property
	 */
	protected function sessions()
	{
		$storage = new PLIB_Session_Storage_Empty();
	  return new PLIB_Session_Manager($storage,false);
	}
	
	/**
	 * @return PLIB_User_Current the current-user-object
	 */
	protected function user()
	{
	  return new PLIB_User_Current(null,false);
	}

	/**
	 * @return PLIB_Cookies the cookies-object
	 */
	protected function cookies()
	{
		return new PLIB_Cookies('plib_');
	}

	/**
	 * @return PLIB_URL the URL-object
	 */
	protected function url()
	{
		return new PLIB_URL();
	}

	/**
	 * @return PLIB_Template_Handler the template-object
	 */
	protected function tpl()
	{
		return new PLIB_Template_Handler();
	}

	/**
	 * @return PLIB_Locale the property
	 */
	protected function locale()
	{
		return new PLIB_Locale_EN();
	}
	
	/**
	 * @return PLIB_Input the property
	 */
	protected function input()
	{
		return PLIB_Input::get_instance();
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>