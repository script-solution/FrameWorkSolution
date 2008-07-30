<?php
/**
 * Contains some PHP-config-functions
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Utility functions to get information about PHP
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_PHPConfig extends FWS_UtilBase
{
	/**
	 * Just a short-hand for:
	 * <code>self::is_enabled('safe_mode')</code>
	 * 
	 * @return boolean true if the Safe-Mode is enabled
	 */
	public static function is_safemode_enabled()
	{
		return self::is_enabled('safe_mode');
	}
	
	/**
	 * Determines wether the php.ini-setting is activated
	 *
	 * @param string $flag the php-flag
	 * @return boolean true if the flag is enabled
	 */
	public static function is_enabled($flag)
	{
		$val = @ini_get($flag);
		return $val == 1 || FWS_String::strtolower($val) == 'on';
	}
	
	/**
	 * Checks wether the gd-library is installed
	 *
	 * @return boolean true if the gd-lib is installed
	 */
	public static function is_gd_installed()
	{
		return function_exists('imagecreate');
	}
	
	/**
	 * Checks wether at least the gd-library version 2 is installed
	 *
	 * @return boolean true if gd2 is installed
	 */
	public static function is_gd2_installed()
	{
		return function_exists('imagecreatetruecolor');
	}
	
	/**
	 * Determines the version of the GD-library. If the version-information is not available
	 * (for example because the GD-library is not installed '0' will be returned)
	 *
	 * @return string the gd-version
	 */
	public static function get_gd_version()
	{
		if(function_exists('gd_info'))
			$gd = gd_info();
		else
			$gd = array('GD Version' => '0');

		return $gd['GD Version'];
	}
}
?>