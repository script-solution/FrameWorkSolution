<?php
/**
 * This file initializes everything which is necessary to use the framework. You should include
 * this file at first! Note that you have to define the constant 'FWS_PATH' which is the path
 * to the folder of the framework before including this file.
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// no path defined?
if(!defined('FWS_PATH'))
	die('Please define "FWS_PATH" first!');

/**
 * The version of the framework
 */
define('FWS_VERSION','FrameWorkSolution v1.00 Beta2');

/**
 * The version-id of frameworksolution
 */
define('FWS_VERSION_ID','100b2');

// include the path-object. we need this for the autoload-function
include_once(FWS_PATH.'utilbase.php');
include_once(FWS_PATH.'path.php');
FWS_Path::set_server_fw(FWS_PATH);
FWS_Path::set_client_fw(FWS_PATH);

// set error-display-mode
@ini_set('display_errors',1);
error_reporting(E_ALL | E_STRICT);

// we don't want to have magic-quotes-runtime enabled
set_magic_quotes_runtime(0);

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