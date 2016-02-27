<?php
/**
 * Contains some PHP-config-functions
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
	 * Determines whether the php.ini-setting is activated
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
	 * Checks whether the gd-library is installed
	 *
	 * @return boolean true if the gd-lib is installed
	 */
	public static function is_gd_installed()
	{
		return function_exists('imagecreate');
	}
	
	/**
	 * Checks whether at least the gd-library version 2 is installed
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