<?php
/**
 * Contains the cache-storage-file class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	cache.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The file-based implementation of the cache-storage
 *
 * @package			PHPLib
 * @subpackage	cache.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Cache_Storage_File extends PLIB_FullObject implements PLIB_Cache_Storage
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
		
		if(empty($folder) || !is_dir($folder) || !PLIB_FileUtils::is_writable($folder))
			PLIB_Helper::error('$folder is invalid. It has to exist and be writable!');
		
		$this->_folder = PLIB_FileUtils::ensure_trailing_slash($folder);
	}
	
	public function load()
	{
		$res = array();
		$items = PLIB_FileUtils::get_dir_content($this->_folder);
		foreach($items as $item)
		{
			if(PLIB_String::ends_with($item,'.php') && is_file($this->_folder.$item))
				$res[PLIB_FileUtils::get_name($item,false)] = include($this->_folder.$item);
		}
		return $res;
	}

	public function store($name,$content)
	{
		$c = '<?php'."\n";
		$c .= 'return '.$this->_get_content($content).';'."\n";
		$c .= '?>';
		PLIB_FileUtils::write($this->_folder.$name.'.php',$c);
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
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>