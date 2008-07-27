<?php
/**
 * This file contains some general adjustments for the library
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// TODO put this in the init.php

// set error-display-mode
// TODO uncomment this!
@ini_set('display_errors',1);
error_reporting(E_ALL | E_STRICT);

// we don't want to have magic-quotes-runtime enabled
set_magic_quotes_runtime(0);

// set default timezone for the case that PLIB_Date is not used
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
?>