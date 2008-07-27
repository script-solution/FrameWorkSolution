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
	 * The relative path to the application from the servers point of view
	 *
	 * @var string
	 */
	private static $_server_app = '';
	
	/**
	 * The relative path to the application from the clients point of view
	 *
	 * @var string
	 */
	private static $_client_app = '';
	
	/**
	 * The relative path to the library from the servers point of view
	 *
	 * @var unknown_type
	 */
	private static $_server_lib = 'lib/';
	
	/**
	 * The relative path to the library from the clients point of view
	 *
	 * @var string
	 */
	private static $_client_lib = 'lib/';
	
	/**
	 * The outer-path
	 * 
	 * @var string
	 */
	private static $_outer = null;
	
	/**
	 * Sets the value of the path to the application (relative with trailing slash!) from the
	 * servers point of view.
	 * 
	 * @param string $path the new value
	 */
	public static function set_server_app($path)
	{
		self::$_server_app = $path;
	}
	
	/**
	 * Sets the value of the path to the application (relative with trailing slash!) from the
	 * clients point of view.
	 * 
	 * @param string $path the new value
	 */
	public static function set_client_app($path)
	{
		self::$_client_app = $path;
	}
	
	/**
	 * Sets the value of the path to the library (relative with trailing slash!) from the
	 * servers point of view.
	 * 
	 * @param string $path the new value
	 */
	public static function set_server_lib($path)
	{
		self::$_server_lib = $path;
	}
	
	/**
	 * Sets the value of the path to the library (relative with trailing slash!) from the
	 * clients point of view.
	 * 
	 * @param string $path the new value
	 */
	public static function set_client_lib($path)
	{
		self::$_client_lib = $path;
	}
	
	/**
	 * Sets the value of the inner path (absolute with trailing slash!)
	 * 
	 * @param string $url the new value
	 */
	public static function set_outer($url)
	{
		self::$_outer = $url;
	}
	
	/**
	 * @return string the relative path to the application from the servers point of view
	 */
	public static function server_app()
	{
		return self::$_server_app;
	}
	
	/**
	 * @return string the relative path to the application from the clients point of view
	 */
	public static function client_app()
	{
		return self::$_client_app;
	}
	
	/**
	 * @return string the relative path to the library from the servers point of view
	 */
	public static function server_lib()
	{
		return self::$_server_lib;
	}
	
	/**
	 * @return string the relative path to the library from the clients point of view
	 */
	public static function client_lib()
	{
		return self::$_client_lib;
	}
	
	/**
	 * Returns the absolute outer-path to the root-folder of the project with a trailing slash.
	 * "outer" means that this should be used for paths in the HTML-document.
	 * 
	 * @return string the absolute outer-path to the project-folder
	 */
	public static function outer()
	{
		if(self::$_outer === null)
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
			
			self::$_outer = $url;
		}
		
		return self::$_outer;
	}
}
?>