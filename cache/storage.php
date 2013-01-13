<?php
/**
 * Contains the cache-storage-interface
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
 * The interface for all storage-implementations.
 *
 * @package			FrameWorkSolution
 * @subpackage	cache
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Cache_Storage
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