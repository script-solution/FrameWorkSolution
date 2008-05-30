<?php
/**
 * This file initializes everything which is necessary to use the library. You should include
 * this file at first! Note that you have to define the constant 'PLIB_PATH' which is the path
 * to the folder of the library before including this file.
 *
 * @version			$Id: init.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// no path defined?
if(!defined('PLIB_PATH'))
	die('Please define "PLIB_PATH" first!');

// include the path-object. we need this for the autoload-function
include_once(PLIB_PATH.'utilbase.php');
include_once(PLIB_PATH.'path.php');
PLIB_Path::set_lib(PLIB_PATH);

// we need some basic stuff
include_once(PLIB_Path::lib().'general.php');
include_once(PLIB_Path::lib().'helper.php');
include_once(PLIB_Path::lib().'string.php');
include_once(PLIB_Path::lib().'autoloader.php');

function __autoload($item)
{
	// "redirect" the request to our autoloader because we want to support
	// other loaders. For example one for this library and one for the project that use this lib.
	if(!(PLIB_AutoLoader::load_item($item)))
		PLIB_Helper::error('The file for item "'.$item.'" could not been found!');
}

// improve the debugging...
$debug = PLIB_Error_Handler::get_instance();
// define our own error-handler for easier debugging
set_error_handler(array($debug,'handle_error'));
set_exception_handler(array($debug,'handle_exception'));
?>