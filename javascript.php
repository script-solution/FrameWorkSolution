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
 * $js->set_cache_folder(PLIB_Path::inner().'cache');
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
	 * @return string the cache-folder or null if not set
	 */
	public function get_cache_folder()
	{
		return $this->_cache;
	}
	
	/**
	 * Sets the cache folder for shrinked javascript-files. This method assumes that the folder
	 * is writable!
	 * 
	 * @param string $folder the new value
	 */
	public function set_cache_folder($folder)
	{
		if(empty($folder))
			PLIB_Helper::def_error('notempty','folder',$folder);
		if(!file_exists($folder) || !is_dir($folder))
			PLIB_Helper::error('"'.$folder.'" is no valid folder!');
		
		$this->_cache = PLIB_FileUtils::ensure_trailing_slash($folder);
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
	 * @param boolean $shrink do you want to shrink the file? (default=true)
	 */
	public function get_file($file,$source = 'def',$shrink = true)
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
		$filepath = $source == 'lib' ? PLIB_Path::lib().$file : PLIB_Path::inner().$file;
		if($shrink)
		{
			$filename = basename($file);
			$modtime = @filemtime($filepath);
			$ext = PLIB_FileUtils::get_extension($filename);
			$suffix = '_shrinked';
			$cache_file = preg_replace('/[^a-z0-9_]/','_',$file);
			$output_file = $this->_cache.$prefix.$cache_file.$suffix.'.'.$ext;
			
			// determine if we have to recache the file
			$recache = !is_file($output_file);
			if(!$recache)
			{
				$outputmod = @filemtime($output_file);
				$recache = $modtime > $outputmod;
			}
			
			if($recache)
			{
				$shrinker = new PLIB_JS_FileShrinker($filepath);
				$output = $shrinker->get_shrinked_content();
				if(!PLIB_FileUtils::write($output_file,$output))
					$output_file = $filepath;
			}
		}
		else
			$output_file = $filepath;
		
		return $output_file;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>