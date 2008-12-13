<?php
/**
 * Contains the autoloader-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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