<?php
/**
 * Contains the cache-source-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all source-implementations.
 *
 * @package			PHPLib
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Cache_Source
{
	/**
	 * Should generate the cache-content from the corresponding source and return it.
	 * 
	 * @return array the content of the cache
	 */
	public function get_content();
}
?>