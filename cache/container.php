<?php
/**
 * Contains the cache-container-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	cache
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
 * The container for all caches
 *
 * @package			FrameWorkSolution
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Cache_Container extends FWS_Object
{
	/**
	 * All cache-objects
	 *
	 * @var array
	 */
	private $_caches;
	
	/**
	 * The cache-contents (will just exist until the corresponding entry in
	 * <var>$this->_caches</var> is loaded)
	 *
	 * @var array
	 */
	private $_cache_contents;
	
	/**
	 * The storage-object that will be used to load and store the cache-objects
	 *
	 * @var FWS_Cache_Storage
	 */
	private $_storage;
	
	/**
	 * Constructor. Note that you have to call #init_content() for each
	 * cache-object before you can use it!
	 *
	 * @param FWS_Cache_Storage $storage the storage-object
	 */
	public function __construct($storage)
	{
		parent::__construct();
		
		if(!($storage instanceof FWS_Cache_Storage))
			FWS_Helper::def_error('instance','storage','FWS_Cache_Storage',$storage);
	
		$this->_storage = $storage;
		
		// load content
		$this->_cache_contents = array();
		foreach($this->_storage->load() as $name => $content)
			$this->_cache_contents[$name] = $content;
	}
	
	/**
	 * Inits the content with given name and source. This method
	 * will create the {@link FWS_Cache_Content} instance which will be usable
	 * afterwards
	 *
	 * @param string $name the name of the cache
	 * @param FWS_Cache_Source $source the source-implementation
	 */
	public function init_content($name,$source)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		if(!($source instanceof FWS_Cache_Source))
			FWS_Helper::def_error('instance','source',FWS_Cache_Source,$source);
		
		if(isset($this->_cache_contents[$name]))
		{
			$this->_caches[$name] = new FWS_Cache_Content($name,$source);
			
			$val = $this->_cache_contents[$name];
			// if the cache isn't valid, we have to reload and store it here
			if($val === false)
			{
				$this->_caches[$name]->reload();
				$this->store($name);
			}
			else
			{
				if(!is_array($val))
					$val = array();
				$this->_caches[$name]->set_elements($val);
			}
			
			unset($this->_cache_contents[$name]);
		}
	}
	
	/**
	 * Sets the content of the given name
	 *
	 * @param string $name the name of the cache
	 * @param object $object the object to set
	 */
	public function set_content($name,$object)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		if(!is_object($object))
			FWS_Helper::def_error('object','object',$object);
		
		$this->_caches[$name] = $object;
		if(isset($this->_cache_contents[$name]))
			unset($this->_cache_contents[$name]);
	}
	
	/**
	 * @return array an associative array with all cache-entries:
	 * 	<code>array(<name> => <FWS_Cache_Content>,...)</code>
	 */
	public function get_caches()
	{
		return $this->_caches;
	}
	
	/**
	 * Return the cache-object with given name. Note that it has to be inited
	 * before you can do that!
	 *
	 * @param string $name the name
	 * @return FWS_Cache_Content the cache-object or null if not found
	 * @see init_content()
	 */
	public function get_cache($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(isset($this->_caches[$name]))
			return $this->_caches[$name];
		
		return null;
	}
	
	/**
	 * Refreshes the cache with given name. "Refresh" means that the content will be reloaded
	 * from the source and will afterwards be written to the storage.
	 *
	 * @param string $name the name of the cache
	 * @see refresh_all()
	 */
	public function refresh($name)
	{
		$this->reload($name);
		$this->store($name);
	}
	
	/**
	 * Refreshes all caches. "Refresh" means that the content will be reloaded
	 * from the source and will afterwards be written to the storage.
	 * 
	 * @see refresh()
	 */
	public function refresh_all()
	{
		foreach(array_keys($this->_caches) as $name)
			$this->refresh($name);
	}
	
	/**
	 * Stores the cache with given name to the storage. That means that the current cache-content
	 * will be saved!
	 *
	 * @param string $name the name of the cache
	 * @see store_all()
	 */
	public function store($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		if(isset($this->_caches[$name]))
			$this->_storage->store($name,$this->_caches[$name]->get_elements());
	}
	
	/**
	 * Stores all caches to the storage. That means that the current cache-content
	 * will be saved!
	 * 
	 * @see store()
	 */
	public function store_all()
	{
		foreach($this->_caches as $name => $cache)
			$this->_storage->store($name,$cache->get_elements());
	}
	
	/**
	 * Reloads the cache with given name. This means that the content will be loaded from
	 * the source. It will NOT store the content to the storage!
	 *
	 * @param string $name the name
	 * @see reload_all()
	 */
	public function reload($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		$cache = $this->get_cache($name);
		if($cache !== null)
		{
			if($cache instanceof FWS_Cache_Content)
				$cache->reload();
		}
	}
	
	/**
	 * Reloads all cache-objects. This means that the content will be loaded from
	 * the source. It will NOT store the content to the storage!
	 * 
	 * @see reload()
	 */
	public function reload_all()
	{
		foreach($this->_caches as $cache)
		{
			if($cache instanceof FWS_Cache_Content)
				$cache->reload();
		}
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>