<?php
/**
 * Contains the cache-array-class
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
 * Contains the content of a cache, loads and stores it.
 *
 * @package			FrameWorkSolution
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Cache_Content extends FWS_Array_2Dim
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
	 * @var FWS_Cache_Source
	 */
	private $_source;

	/**
	 * Constructor
	 *
	 * @param string $name the name of the cache
	 * @param FWS_Cache_Source $source the source-object
	 */
	public function __construct($name,$source)
	{
		if(!preg_match('/^[a-z0-9_]+$/i',$name))
			FWS_Helper::error('$name is invalid! It may contain a-z, A-Z, 0-9 and _');
		if(!($source instanceof FWS_Cache_Source))
			FWS_Helper::def_error('instance','source','FWS_Cache_Source',$source);
		
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