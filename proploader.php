<?php
/**
 * Contains the property-loader-class
 * 
 * @package			FrameWorkSolution
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
	 * @throws FWS_Exception_UnsupportedMethod always, since we can't establish a connection here
	 */
	protected function db()
	{
		// We don't want to provide a default here because we wouldn't know where to connect to
		// anyway and therefore this makes no sense
		throw new FWS_Exception_UnsupportedMethod('Please provide your own db-loader');
	}
	
	/**
	 * @return FWS_Document_Messages the messages-container
	 */
	protected function msgs()
	{
		return new FWS_Document_Messages();
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
		$storage = new FWS_User_Storage_Empty();
	  return new FWS_User_Current($storage);
	}

	/**
	 * @return FWS_Cookies the cookies-object
	 */
	protected function cookies()
	{
		return new FWS_Cookies('fws_');
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
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>