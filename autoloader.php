<?php
/**
 * Contains the autoloader-class
 *
 * @version			$Id: autoloader.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The autoloader for the library. It can load items and you may also register other load-
 * functions to search at different locations for the item or something like that.
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AutoLoader extends PLIB_UtilBase
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
	 * @param mixed $loader the loader-function / -method
	 */
	public static function register_loader($loader)
	{
		if(!is_callable($loader))
			PLIB_Helper::error('The given loader is not callable!');
		
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
		if(PLIB_String::starts_with($item,'PLIB_'))
		{
			$myitem = PLIB_String::substr($item,5);
			$myitem = str_replace('_','/',$myitem);
			$myitem = PLIB_String::strtolower($myitem);
			$myitem .= '.php';
			$path = PLIB_Path::lib().$myitem;
			
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