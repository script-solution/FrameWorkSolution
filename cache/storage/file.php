<?php
/**
 * Contains the cache-storage-file class
 * 
 * @package			FrameWorkSolution
 * @subpackage	cache.storage
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
 * The file-based implementation of the cache-storage
 *
 * @package			FrameWorkSolution
 * @subpackage	cache.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Cache_Storage_File extends FWS_Object implements FWS_Cache_Storage
{
	/**
	 * The folder which contains the cache-files
	 *
	 * @var string
	 */
	private $_folder;
	
	/**
	 * Constructor
	 *
	 * @param string $folder the folder which contains the cache-files
	 */
	public function __construct($folder)
	{
		parent::__construct();
		
		if(empty($folder) || !is_dir($folder) || !FWS_FileUtils::is_writable($folder))
			FWS_Helper::error('$folder is invalid. It has to exist and be writable!');
		
		$this->_folder = FWS_FileUtils::ensure_trailing_slash($folder);
	}
	
	public function load()
	{
		$res = array();
		$items = FWS_FileUtils::get_list($this->_folder);
		foreach($items as $item)
		{
			if(FWS_String::ends_with($item,'.php') && is_file($this->_folder.$item))
				$res[FWS_FileUtils::get_name($item,false)] = include($this->_folder.$item);
		}
		return $res;
	}

	public function store($name,$content)
	{
		$c = '<?php'."\n";
		$c .= 'return '.$this->_get_content($content).';'."\n";
		$c .= '?>';
		FWS_FileUtils::write($this->_folder.$name.'.php',$c);
	}
	
	/**
	 * Builds the content recursivly
	 *
	 * @param array $content the content
	 * @param int $layer the current layer
	 * @return string the string-representation to store
	 */
	private function _get_content($content,$layer = 1)
	{
		$indent = '';
		for($i = 0;$i < $layer;$i++)
			$indent .= "\t";
		
		$c = '';
		if(is_array($content))
		{
			$c .= $indent.'array('."\n";
			foreach($content as $k => $v)
				$c .= $indent."\t".$k.' => '.$this->_get_content($v,$layer + 1).','."\n";
			$c .= $indent.')'."\n";
		}
		else if(is_string($content))
			$c .= '"'.str_replace('"','\\"',$content).'"';
		else if(is_scalar($content))
			$c .= $content;
		
		return $c;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>