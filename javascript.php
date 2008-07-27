<?php
/**
 * Contains the javascript-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A class to cache and use javascript-files.
 * Example:
 * <code>
 * $js = PLIB_Javascript::get_instance();
 * $js->set_cache_folder(PLIB_Path::server_app().'cache');
 * // will return the name of the cache-file
 * echo $js->get_file('myfile.js');
 * </code>
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Javascript extends PLIB_Singleton
{
	/**
	 * @return PLIB_Javascript the instance of this class
	 */
	public static function get_instance()
	{
		return parent::_get_instance(get_class());
	}
	
	/**
	 * The path to the cache-folder
	 *
	 * @var string
	 */
	private $_cache = null;
	
	/**
	 * Wether the files should be shrinked
	 *
	 * @var boolean
	 */
	private $_shrink = true;
	
	/**
	 * @return string the cache-folder or null if not set (starting at PLIB_Path::server_app()!)
	 */
	public function get_cache_folder()
	{
		return $this->_cache;
	}
	
	/**
	 * Sets the cache folder for shrinked javascript-files. This method assumes that the folder
	 * is writable!
	 * 
	 * @param string $folder the new value (starting at PLIB_Path::server_app()!)
	 */
	public function set_cache_folder($folder)
	{
		if(empty($folder))
			PLIB_Helper::def_error('notempty','folder',$folder);
		$rfolder = PLIB_Path::server_app().$folder;
		if(!file_exists($rfolder) || !is_dir($rfolder))
			PLIB_Helper::error('"'.$rfolder.'" is no valid folder!');
		
		$this->_cache = PLIB_FileUtils::ensure_trailing_slash($folder);
	}
	
	/**
	 * Sets wether the files should be shrinked. By default this is enabled.
	 * For example you may disable this for debugging javascript.
	 *
	 * @param boolean $shrink the new value
	 */
	public function set_shrink($shrink)
	{
		$this->_shrink = (bool)$shrink;
	}
	
	/**
	 * Returns the javascript-file to use for the given file. This file will be cached and a shrinked
	 * version will be used.
	 * If $source is "lib" the fill be assumed at:
	 * <code><libpath><file></code>
	 * Otherwise:
	 * <code><path><file></code>
	 * 
	 * @param string $file the js-file
	 * @param string $source if $source = "lib" the PHPLib will be used as root
	 */
	public function get_file($file,$source = 'def')
	{
		if(empty($file))
			PLIB_Helper::def_error('notempty','file',$file);
		
		if($this->_cache === null)
		{
			PLIB_Helper::error(
				'Please specify the cache-folder first via PLIB_Javascript::set_cache_folder()!'
			);
		}
		
		$prefix = '';
		if($source == 'lib')
			$prefix = 'plib_';
		
		// init some vars
		$filepath = $source == 'lib' ? PLIB_Path::server_lib().$file : PLIB_Path::server_app().$file;
		$shrinked = $this->_shrink;
		if($this->_shrink)
		{
			$filename = basename($file);
			$modtime = @filemtime($filepath);
			$ext = PLIB_FileUtils::get_extension($filename);
			$suffix = '_shrinked';
			$cache_file = preg_replace('/[^a-z0-9_]/','_',$file);
			$output_file = $this->_cache.$prefix.$cache_file.$suffix.'.'.$ext;
			$server_output = PLIB_Path::server_app().$output_file;
			
			// determine if we have to recache the file
			$recache = !is_file($server_output);
			if(!$recache)
			{
				$outputmod = @filemtime($server_output);
				$recache = $modtime > $outputmod;
			}
			
			if($recache)
			{
				$shrinker = new PLIB_JS_FileShrinker($filepath);
				$output = $shrinker->get_shrinked_content();
				if(!PLIB_FileUtils::write($server_output,$output))
				{
					$output_file = $file;
					$shrinked = false;
				}
			}
		}
		else
			$output_file = $file;
		
		if($shrinked)
			return PLIB_Path::client_app().$output_file;
		
		if($source == 'lib')
			return PLIB_Path::client_lib().$file;
		return PLIB_Path::client_app().$file;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>