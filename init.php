<?php
/**
 * This file initializes everything which is necessary to use the framework. You should include
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

// no path defined?
if(!defined('FWS_PATH'))
	die('Please define "FWS_PATH" first!');

// check the php-version here to prevent arbitrary errors when an too old version is used
if(version_compare(PHP_VERSION,'5.2.0','<'))
	die('You need at least PHP 5.2.0!');

/**
 * The version of the framework
 */
define('FWS_VERSION','FrameWorkSolution v1.42');

/**
 * The version-id of frameworksolution
 */
define('FWS_VERSION_ID','142');

// include the path-object. we need this for the autoload-function
include_once(FWS_PATH.'utilbase.php');
include_once(FWS_PATH.'path.php');
FWS_Path::set_server_fw(FWS_PATH);
FWS_Path::set_client_fw(FWS_PATH);

// set error-display-mode
@ini_set('display_errors','1');
error_reporting(E_ALL | E_STRICT);

// we don't want to have magic-quotes-runtime enabled; it's deprecated in PHP 5.3 (=> @)
@set_magic_quotes_runtime(false);

// we don't want to escape ' by '' instead of \', either
@ini_set('magic_quotes_sybase','0');

// set default timezone for the case that FWS_Date is not used
date_default_timezone_set('Europe/Berlin');

// if register-globals is activated we delete the registered variables
if(@ini_get('register_globals'))
{
	foreach(@array($_REQUEST,$_SERVER,$_FILES,$_ENV) as $array)
	{
		if(is_array($array))
		{
			foreach($array as $k => $v)
				unset(${$k});
		}
	}
}

// we need some basic stuff
include_once(FWS_Path::server_fw().'helper.php');
include_once(FWS_Path::server_fw().'string.php');
include_once(FWS_Path::server_fw().'autoloader.php');

if(!function_exists('__autoload'))
{
	function __autoload($item)
	{
		// "redirect" the request to our autoloader because we want to support
		// other loaders. For example one for this framework and one for the project that use this fws.
		if(!(FWS_AutoLoader::load_item($item)))
			FWS_Helper::error('The file for item "'.$item.'" could not been found!');
	}
}

// improve the debugging...
$debug = FWS_Error_Handler::get_instance();
// define our own error-handler for easier debugging
set_error_handler(array($debug,'handle_error'));
set_exception_handler(array($debug,'handle_exception'));
?>