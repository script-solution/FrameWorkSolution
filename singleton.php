<?php
/**
 * Contains the singleton-base-class
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
 * This should be the base-class of all singletons. It prevents that an instance can be
 * created and that the object of the class can be cloned.
 * <br>
 * Please provide a static method named get_instance() which looks like the following:
 * <code>
 * 	public static function get_instance()
 * 	{
 * 		return parent::_get_instance(get_class());
 * 	}
 * </code>
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Singleton extends FWS_Object
{
	/**
	 * The instance of this class
	 *
	 * @var FWS_Singleton
	 */
	private static $_instances = array();
	
	/**
	 * Indicates whether the constructor is locked
	 *
	 * @var boolean
	 */
	private static $_locked = true;
	
	/**
	 * Creates the object of the given class (once) and returns it
	 *
	 * @param string $name the class-name
	 * @return FWS_Singleton the object of the given class
	 */
	protected static function _get_instance($name)
	{
		if(!isset(self::$_instances[$name]))
		{
			self::$_locked = false;
			self::$_instances[$name] = new $name();
			self::$_locked = true;
		}
		
		return self::$_instances[$name];
	}
	
	/**
	 * Constructor
	 * 
	 * @throws FWS_Exception_UnsupportedMethod if _get_instance() is not used
	 */
	public function __construct()
	{
		if(self::$_locked)
			throw new FWS_Exception_UnsupportedMethod('Since '.get_class($this).' is a singleton'
				.' you can\'t instantiate it but have to use the static get-instance-method!');
		
		parent::__construct();
	}
	
	/**
	 * @throws FWS_Exception_UnsupportedMethod in all cases
	 */
	public function __clone()
	{
		throw new FWS_Exception_UnsupportedMethod(
			'Since '.get_class($this).' is a singleton you can\'t clone it!'
		);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>