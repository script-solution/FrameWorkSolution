<?php
/**
 * Contains some file-utility-functions
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Utility functions for files
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_FileUtils extends FWS_UtilBase
{
	/**
	 * Cleans the given path. Forces the use of '/' as separator and removes '..'.
	 *
	 * @param string $path the path (will be changed)
	 * @return string the cleaned path
	 */
	public static function clean_path(&$path)
	{
		if($path)
		{
			// ensure that we use just '/'
			$path = preg_replace('/[\/\\\\]+/','/',$path);
			// '..' is not allowed
			$path = str_replace('..','',$path);
		}
		return $path;
	}
	
	/**
	 * "Clears" the given filename. That means german umlaute will be replaced to the corresponding
	 * chars (ae,oe,...) and special-chars will be replaced to '_'.
	 *
	 * @param string $name your file-name (will be changed)
	 * @return string the clean filename
	 */
	public static function clean_filename(&$name)
	{
		// replace german umlaute
		$name = str_replace(
			array('ä','ö','ü','Ä','Ö','Ü','ß'),
			array('ae','oe','ue','Ae','Oe','Ue','ss'),
			$name
		);
		// replace all other special-chars with '_'
		$name = preg_replace('/[^a-z0-9_\-\.]+/i','_',$name);
		return $name;
	}
	
	/**
	 * Ensures that the given path has a trailing slash
	 * 
	 * @param string $path the path (will be changed!)
	 * @return string the corrected path
	 */
	public static function ensure_trailing_slash(&$path)
	{
		if(FWS_String::substr($path,-1) != '/')
			$path .= '/';
		
		return $path;
	}
	
	/**
	 * Ensures that the given path has <b>no</b> trailing slash
	 *
	 * @param string $path the path (will be changed!)
	 * @return string the corrected path
	 */
	public static function ensure_no_trailing_slash(&$path)
	{
		if(FWS_String::substr($path,-1) == '/')
			$path = FWS_String::substr($path,0,-1);
		
		return $path;
	}
	
	/**
	 * Returns the name of the given file or folder. You can choose if you
	 * want to include the file-extension or not.
	 * 
	 * @param string $path the path from which to get the name
	 * @param boolean $with_ext do you want to get the extension?
	 * @return string the name with or without extension
	 */
	public static function get_name($path,$with_ext = true)
	{
		if(empty($path))
			return '';

		// we're not interested in the path
		$path = basename($path);
		if($with_ext)
			return $path;
		
		$pos = FWS_String::strrpos($path,'.');
		if($pos !== false)
			return FWS_String::substr($path,0,$pos);
		
		return $path;
	}
	
	/**
	 * returns the file-extension of the given filename
	 *
	 * @param string $filename the name of the file
	 * @param boolean $lower wether you want to get it in lowercase
	 * @return string the extension of the given file without the dot
	 */
	public static function get_extension($filename,$lower = true)
	{
		if(empty($filename))
			FWS_Helper::def_error('notempty','filename',$filename);
	
		// we're not interested in the path
		$filename = basename($filename);
		
		$pos = FWS_String::strrpos($filename,'.');
		if($pos !== false)
		{
			$extension = FWS_String::substr($filename,$pos + 1);
			return $lower ? FWS_String::strtolower($extension) : $extension;
		}
		
		if($lower)
			return FWS_String::strtolower($filename);
		
		return $filename;
	}
	
	/**
	 * Checks wether the given directory / file is writable
	 * Will try to set the CHMOD! Uses 0666 for files and 0777 for folders.
	 * 
	 * @param string $path the file or directory to check
	 * @return boolean true if the path is writable
	 */
	public static function is_writable($path)
	{
		if(empty($path))
			FWS_Helper::def_error('notempty','path',$path);
		
		$end = FWS_String::substr($path,-1);
		if($end == '/')
			$path = FWS_String::substr($path,0,-1);
		
		// if the file / dir does not exist it can't have a valid chmod
		if(!file_exists($path))
			return false;
		
		if(is_dir($path))
		{
			$tmp = uniqid(mt_rand()).'.tmp';
			$fp = @fopen($path.'/'.$tmp,'a');
			if($fp === false)
			{
				// attempt to change the chmod
				@chmod($path,0777);
				// now check again if we create a file in the directory
				$fp = @fopen($path.'/'.$tmp,'a');
				if($fp === false)
					return false;
			}
			
			// file has been created, so clean up
			fclose($fp);
			@unlink($path.'/'.$tmp);
			return true;
		}
		
		// try to open the file for writing
		$fp = @fopen($path,'a');
		if($fp === false)
		{
			@chmod($path,0666);
			$fp = @fopen($path,'a');
			if($fp === false)
				return false;
		}
		
		// file is writable so cleanup
		fclose($fp);
		
		return true;
	}
	
	/**
	 * Determines the size of the given directory. If you like you can do that recursivly.
	 *
	 * @param string $directory the directory
	 * @param boolean $recursive count recursivly?
	 * @return int the total number of bytes in this directory
	 */
	public static function get_dir_size($directory,$recursive = false)
	{
		if(!is_dir($directory))
			FWS_Helper::error('"'.$directory.'" is no directory!');
		
		$directory = self::ensure_trailing_slash($directory);
		return self::_get_dir_size($directory,(bool)$recursive);
	}
	
	/**
	 * The implementation to get the size of a directory
	 *
	 * @param string $directory the directory
	 * @param boolean $recursive count recursivly?
	 * @return int the total number of bytes in this directory
	 */
	private static function _get_dir_size($directory,$recursive = false)
	{
		$total = 0;
		$handle = opendir($directory);
		while($item = readdir($handle))
		{
			if($item != '.' && $item != '..')
			{
				if(is_dir($directory.$item) && $recursive)
					$total += self::_get_dir_size($directory.$item,$recursive);
				else
					$total += @filesize($directory.$item);
			}
		}
		closedir($handle);
		
		return $total;
	}
	
	/**
	 * Collects all items in the given directory and returns the item-list.
	 * This may be recursivly or not. Note that "." and ".." will be skipped!
	 *
	 * @param string $directory the directory
	 * @param boolean $recursive collect recursivly?
	 * @param boolean $abs use absolute paths that means beginning with <var>$directory</var>?
	 * @return array an array with all items in the directory
	 */
	public static function get_list($directory,$recursive = false,$abs = false)
	{
		if(!is_dir($directory))
			FWS_Helper::error('"'.$directory.'" is no directory!');
		
		$directory = self::ensure_trailing_slash($directory);
		$res = array();
		self::_get_list($res,$directory,$recursive,$abs);
		return $res;
	}
	
	/**
	 * Collects the items of the given directory recursivly, if required
	 *
	 * @param array $res the result-array
	 * @param string $directory the directory
	 * @param boolean $recursive collect recursivly?
	 * @param boolean $abs use absolute paths that means beginning with <var>$directory</var>?
	 */
	private static function _get_list(&$res,$directory,$recursive,$abs)
	{
		$handle = opendir($directory);
		while($item = readdir($handle))
		{
			if($item != '.' && $item != '..')
			{
				if(is_dir($directory.$item))
				{
					$res[] = $abs ? $directory.$item : $item;
					if($recursive)
						self::_get_list($res,$directory.$item.'/',$recursive,$abs);
				}
				else
					$res[] = $abs ? $directory.$item : $item;
			}
		}
		closedir($handle);
	}
	
	/**
	 * Deletes the folder recursively
	 *
	 * @param string $folder the path to the folder
	 * @return true if the folder has been deleted successfully
	 */
	public static function delete_folder($folder)
	{
		if(empty($folder))
			FWS_Helper::def_error('notempty','folder',$folder);
		if(!is_dir($folder))
			FWS_Helper::error('"'.$folder.'" is no folder!');
		
		self::_delete_folder($folder);
	}
	
	/**
	 * The recursive implementation for {@link delete_folder}.
	 *
	 * @param string $folder the path to the folder
	 * @return true if the folder has been deleted successfully
	 */
	private static function _delete_folder($folder)
	{
		$dir = @opendir($folder);
		if($dir)
		{
			while($file = @readdir($dir))
			{
				if($file != '.' && $file != '..')
				{
					if(is_dir($folder.'/'.$file))
					{
						$res = self::_delete_folder($folder.'/'.$file);
						if(!$res)
							return false;
					}
					else
						@unlink($folder.'/'.$file);
				}
			}
			@closedir($dir);
		}

		return @rmdir($folder);
	}
	
	/**
	 * Copies the content of the given folder to the given target-directory
	 * 
	 * @param string $source the source-folder
	 * @param string $target the target-folder
	 * @return boolean true if successfull
	 */
	public static function copy_folder($source,$target)
	{
		// create target-directory
		if(!is_dir($target))
		{
			if(!mkdir($target))
				return false;
		}
		
		$source = self::ensure_trailing_slash($source);
		$target = self::ensure_trailing_slash($target);
		foreach(self::get_list($source,true,true) as $item)
		{
			$relitem = str_replace($source,'',$item);
			if(is_dir($item) && !is_dir($target.$relitem))
			{
				if(!mkdir($target.$relitem))
					return false;
			}
			else if(is_file($item))
			{
				if(!self::copy($item,$target.$relitem))
					return false;
			}
		}
		return true;
	}
	
	/**
	 * Copies the given source-file(!) to the given target-file. That means it reads the content
	 * of the source-file, creates the target-file and writes the content into it.
	 * The target-file will be replaced if it exists!
	 *
	 * @param string $source the source-file
	 * @param string $target the target-file
	 * @return boolean true if it was successfull
	 */
	public static function copy($source,$target)
	{
		if(empty($source))
			FWS_Helper::def_error('notempty','source',$source);
		if(empty($target))
			FWS_Helper::def_error('notempty','target',$target);
		if(!is_file($source))
			FWS_Helper::error('"'.$source.'" is no file!');

		if(is_file($source))
		{
			$content = self::read($source);
			if($content === false)
				return false;
			
			return self::write($target,$content) !== false;
		}
		
		return false;
	}

	/**
	 * Stores the content of the given folder into a zip-file and stores it at <var>$target</var>
	 *
	 * @param string $folder the source-folder
	 * @param string $target the zip-file that should be created
	 * @return boolean true if it was successfull
	 */
	public static function zip_folder($folder,$target)
	{
		$folder = self::ensure_trailing_slash($folder);
		
		$a = new ZipArchive();
		if(!$a->open($target,ZIPARCHIVE::CREATE))
			return false;
		$paths = FWS_FileUtils::get_list($folder,true,true);
		foreach($paths as $path)
		{
			$relitem = str_replace($folder,'',$path);
			if(is_dir($path))
			{
				if(!$a->addEmptyDir($relitem))
				{
					$a->close();
					return false;
				}
			}
			else
			{
				if(!$a->addFile($path,$relitem))
				{
					$a->close();
					return false;
				}
			}
		}
		$a->close();
		return true;
	}
	
	/**
	 * Reads the content of the given file
	 * 
	 * @param string $source the source-file
	 * @return string the content of the file or false if it failed
	 */
	public static function read($source)
	{
		return @file_get_contents($source);
	}
	
	/**
	 * Writes <var>$content</var> to <var>$target</var>
	 * 
	 * @param string $target the target-file
	 * @param string $content the content to store
	 * @return int the number of written bytes or false if it failed
	 */
	public static function write($target,$content)
	{
		return @file_put_contents($target,$content);
	}
	
	/**
	 * Appends <var>$content</var> to <var>$target</var>
	 *
	 * @param string $target the target-file
	 * @param string $content the content to store
	 * @return int the number of written bytes or false if it failed
	 */
	public static function append($target,$content)
	{
		return @file_put_contents($target,$content,FILE_APPEND);
	}
}
?>