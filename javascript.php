<?php
/**
 * Contains the javascript-class
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
 * A class to cache and use javascript-files.
 * Example:
 * <code>
 * $js = FWS_Javascript::get_instance();
 * $js->set_cache_folder(FWS_Path::server_app().'cache');
 * // will return the name of the cache-file
 * echo $js->get_file('myfile.js');
 * </code>
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Javascript extends FWS_Singleton
{
	/**
	 * @return FWS_Javascript the instance of this class
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
	 * @return string the cache-folder or null if not set (starting at FWS_Path::server_app()!)
	 */
	public function get_cache_folder()
	{
		return $this->_cache;
	}
	
	/**
	 * Sets the cache folder for shrinked javascript-files. This method assumes that the folder
	 * is writable!
	 * 
	 * @param string $folder the new value (starting at FWS_Path::server_app()!)
	 */
	public function set_cache_folder($folder)
	{
		if(empty($folder))
			FWS_Helper::def_error('notempty','folder',$folder);
		$rfolder = FWS_Path::server_app().$folder;
		if(!file_exists($rfolder) || !is_dir($rfolder))
			FWS_Helper::error('"'.$rfolder.'" is no valid folder!');
		
		$this->_cache = FWS_FileUtils::ensure_trailing_slash($folder);
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
	 * If $source is 'fws' the fill be assumed at:
	 * <code><fwspath><file></code>
	 * Otherwise:
	 * <code><path><file></code>
	 * 
	 * @param string $file the js-file
	 * @param string $source if $source = 'fws' the FrameWorkSolution will be used as root
	 */
	public function get_file($file,$source = 'def')
	{
		if(empty($file))
			FWS_Helper::def_error('notempty','file',$file);
		
		if($this->_cache === null)
		{
			FWS_Helper::error(
				'Please specify the cache-folder first via FWS_Javascript::set_cache_folder()!'
			);
		}
		
		$prefix = '';
		if($source == 'fws')
			$prefix = 'fws_';
		
		// init some vars
		$filepath = $source == 'fws' ? FWS_Path::server_fw().$file : FWS_Path::server_app().$file;
		$shrinked = $this->_shrink;
		if($this->_shrink)
		{
			$filename = basename($file);
			$modtime = @filemtime($filepath);
			$ext = FWS_FileUtils::get_extension($filename);
			$suffix = '_shrinked';
			$cache_file = preg_replace('/[^a-z0-9_]/','_',$file);
			$output_file = $this->_cache.$prefix.$cache_file.$suffix.'.'.$ext;
			$server_output = FWS_Path::server_app().$output_file;
			
			// determine if we have to recache the file
			$recache = !is_file($server_output);
			if(!$recache)
			{
				$outputmod = @filemtime($server_output);
				$recache = $modtime > $outputmod;
			}
			
			if($recache)
			{
				$shrinker = new FWS_JS_FileShrinker($filepath);
				$output = $shrinker->get_shrinked_content();
				if(!FWS_FileUtils::write($server_output,$output))
				{
					$output_file = $file;
					$shrinked = false;
				}
			}
		}
		else
			$output_file = $file;
		
		if($shrinked)
			return FWS_Path::client_app().$output_file;
		
		if($source == 'fws')
			return FWS_Path::client_fw().$file;
		return FWS_Path::client_app().$file;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>