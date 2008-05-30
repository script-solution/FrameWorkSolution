<?php
/**
 * Contains the cache-array-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains the content of a cache, loads and stores it.
 *
 * @package			PHPLib
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Cache_Content extends PLIB_Array_2Dim
{
	/**
	 * The name of the cache
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * The source-object that will be used to regenerate the cache
	 *
	 * @var PLIB_Cache_Source
	 */
	private $_source;

	/**
	 * Constructor
	 *
	 * @param string $name the name of the cache
	 * @param PLIB_Cache_Source $source the source-object
	 */
	public function __construct($name,$source)
	{
		if(!preg_match('/^[a-z0-9_]+$/i',$name))
			PLIB_Helper::error('$name is invalid! It may contain a-z, A-Z, 0-9 and _');
		if(!($source instanceof PLIB_Cache_Source))
			PLIB_Helper::def_error('instance','source','PLIB_Cache_Source',$source);
		
		$this->_name = $name;
		$this->_source = $source;
	}
	
	/**
	 * @return string the name of the cache
	 */
	public function get_name()
	{
		return $this->_name;
	}
	
	/**
	 * Reloads the content of the cache
	 */
	public function reload()
	{
		$this->set_elements($this->_source->get_content());
	}
}
?>