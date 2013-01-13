<?php
/**
 * Contains the autoloader-class
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
 * The autoloader for the framework. It can load items and you may also register other load-
 * functions to search at different locations for the item or something like that.
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_AutoLoader extends FWS_UtilBase
{
	/**
	 * All registered loaders
	 *
	 * @var array
	 */
	private static $_loader = array();
	
	/**
	 * Registers the given loader. This may be a function or a method.
	 * It will be called if the default load-method couldn't find the item.
	 * 
	 * @param string|array $loader the loader-function / -method
	 */
	public static function register_loader($loader)
	{
		if(!is_callable($loader))
			FWS_Helper::error('The given loader is not callable!');
		
		self::$_loader[] = $loader;
	}
	
	/**
	 * Tries to load the given item
	 * 
	 * @param string $item the item to load
	 * @return boolean true if successfull
	 */
	public static function load_item($item)
	{
		// Note that we don't use the MB-functions here for performance issues
		if(substr($item,0,4) == 'FWS_')
		{
			$myitem = substr($item,4);
			$myitem = str_replace('_','/',$myitem);
			$myitem = strtolower($myitem);
			$myitem .= '.php';
			$path = FWS_Path::server_fw().$myitem;
			
			if(is_file($path))
			{
				include($path);
				return true;
			}
		}
		
		foreach(self::$_loader as $loader)
		{
			if(call_user_func($loader,$item))
				return true;
		}
		
		return false;
	}
}
?>