<?php
/**
 * Contains some file-utility-functions
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains all paths that we need to know. They are stored statically so that can
 * access them from everywhere.
 * You have to set the inner- and lib-path by yourself!
 * You may also set the outer-path!
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Path extends PLIB_UtilBase
{
	/**
	 * The inner-path
	 * 
	 * @var string
	 */
	private static $_inner = '';
	
	/**
	 * The outer-path
	 * 
	 * @var string
	 */
	private static $_outer = null;
	
	/**
	 * The lib-path
	 * 
	 * @var string
	 */
	private static $_lib = 'lib/';
	
	/**
	 * Sets the value of the inner path (relative with trailing slash!)
	 * 
	 * @param string $path the new value
	 */
	public static function set_inner($path)
	{
		PLIB_Path::$_inner = $path;
	}
	
	/**
	 * Sets the value of the lib path (relative with trailing slash!)
	 * 
	 * @param string $path the new value
	 */
	public static function set_lib($path)
	{
		PLIB_Path::$_lib = $path;
	}
	
	/**
	 * Sets the value of the inner path (absolute with trailing slash!)
	 * 
	 * @param string $url the new value
	 */
	public static function set_outer($url)
	{
		PLIB_Path::$_outer = $url;
	}
	
	/**
	 * Returns the inner-path to the root-folder of the project with a trailing slash.
	 * "inner" means that this should be used to include files or other work on the server.
	 * 
	 * @return string the inner-path to the project-folder
	 */
	public static function inner()
	{
		return PLIB_Path::$_inner;
	}
	
	/**
	 * Returns the absolute outer-path to the root-folder of the project with a trailing slash.
	 * "outer" means that this should be used for paths in the HTML-document.
	 * 
	 * @return string the absolute outer-path to the project-folder
	 */
	public static function outer()
	{
		if(PLIB_Path::$_outer === null)
		{
			$input = PLIB_Input::get_instance();
			$https = $input->get_var('HTTPS','server',PLIB_Input::STRING);
			// protocol
			if($https !== null && !empty($https) && strtolower($https) != 'off')
				$proto = 'https://';
			else
				$proto = 'http://';
			
			// request uri
			$request_uri = $input->get_var('REQUEST_URI','server',PLIB_Input::STRING);
			$url = $proto.$input->get_var('HTTP_HOST','server',PLIB_Input::STRING);
			if(!$request_uri)
				$request_uri = $input->get_var('SCRIPT_NAME','server',PLIB_Input::STRING);
			
			// ensure that we get the directory with a trailing slash
			if(!PLIB_String::ends_with($request_uri,'/'))
				$url .= dirname($request_uri).'/';
			else
				$url .= $request_uri;
			
			PLIB_Path::$_outer = $url;
		}
		
		return PLIB_Path::$_outer;
	}
	
	/**
	 * Returns the (inner-)path to the library-folder with a trailing slash
	 * 
	 * @return string the path to the library
	 */
	public static function lib()
	{
		return PLIB_Path::$_lib;
	}
}
?>