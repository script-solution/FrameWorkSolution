<?php
/**
 * Contains the cache-storage-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all storage-implementations.
 *
 * @package			PHPLib
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Cache_Storage
{
	/**
	 * Should load the cache-objects from the corresponding source and return it
	 * 
	 * @return array an associative array with all cache-contents:
	 * 	<code>array(<name> => <content>, ...)</code>
	 */
	public function load();
	
	/**
	 * Should store the given content to the corresponding destination
	 *
	 * @param string $name the name of the cache
	 * @param array $content the content to store
	 */
	public function store($name,$content);
}
?>