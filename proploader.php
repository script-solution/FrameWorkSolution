<?php
/**
 * Contains the property-loader-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The property-loader of the framework. The {@link FWS_PropAccessor} will use the load method
 * of this class if a property does not exist to load it.
 * <br>
 * You can inherit from this class if you want to change the predefined properties. If you
 * do that the properties should be compatible to the default ones. That means you should inherit
 * from the default properties if you want to change them instead of providing your own one.
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_PropLoader extends FWS_Object
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
			FWS_Helper::error('The method '.$name.'() does not exist.
				Does the property you requested exist?');
		
		return $this->$name();
	}
	
	/**
	 * @return FWS_Document the document
	 */
	protected function doc()
	{
		return new FWS_Document();
	}
	
	/**
	 * @return FWS_Document_Messages the messages-container
	 */
	protected function msgs()
	{
		return new FWS_Document_Messages();
	}
	
	/**
	 * @return FWS_Profiler the profiler-instance
	 */
	protected function profiler()
	{
		return new FWS_Profiler();
	}
	
	/**
	 * @return FWS_Session_Manager the property
	 */
	protected function sessions()
	{
		$storage = new FWS_Session_Storage_Empty();
	  return new FWS_Session_Manager($storage,false);
	}
	
	/**
	 * @return FWS_User_Current the current-user-object
	 */
	protected function user()
	{
	  return new FWS_User_Current(null,false);
	}

	/**
	 * @return FWS_Cookies the cookies-object
	 */
	protected function cookies()
	{
		return new FWS_Cookies('fws_');
	}

	/**
	 * @return FWS_URL the URL-object
	 */
	protected function url()
	{
		return new FWS_URL();
	}

	/**
	 * @return FWS_Template_Handler the template-object
	 */
	protected function tpl()
	{
		return new FWS_Template_Handler();
	}

	/**
	 * @return FWS_Locale the property
	 */
	protected function locale()
	{
		return new FWS_Locale_EN();
	}
	
	/**
	 * @return FWS_Input the property
	 */
	protected function input()
	{
		return FWS_Input::get_instance();
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>