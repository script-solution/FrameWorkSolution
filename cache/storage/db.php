<?php
/**
 * Contains the cache-storage-db class
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
 * The db-based implementation of the cache-storage
 *
 * @package			FrameWorkSolution
 * @subpackage	cache.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Cache_Storage_DB extends FWS_Object implements FWS_Cache_Storage
{
	/**
	 * The table-name to use for the query
	 *
	 * @var string
	 */
	private $_table;

	/**
	 * The column for the name of the cache
	 *
	 * @var string
	 */
	private $_name_column;

	/**
	 * The column for the cache-content
	 *
	 * @var string
	 */
	private $_content_column;

	/**
	 * Constructor
	 *
	 * @param string $table the table-name to use for the query
	 * @param string $name_col the column for the name of the cache
	 * @param string $content_col the column for the cache-content
	 */
	public function __construct($table,$name_col = 'name',$content_col = 'content')
	{
		parent::__construct();
		
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);
		if(empty($name_col))
			FWS_Helper::def_error('notempty','name_col',$name_col);
		if(empty($content_col))
			FWS_Helper::def_error('notempty','content_col',$content_col);
		
		$this->_table = $table;
		$this->_name_column = $name_col;
		$this->_content_column = $content_col;
	}

	public function load()
	{
		$db = FWS_Props::get()->db();

		$rows = $db->get_rows(
			'SELECT '.$this->_name_column.','.$this->_content_column.' FROM '.$this->_table
		);
		$res = array();
		foreach($rows as $row)
			$res[$row[$this->_name_column]] = @unserialize($row[$this->_content_column]);
		return $res;
	}

	public function store($name,$content)
	{
		$db = FWS_Props::get()->db();

		$c = serialize($content);
		if(!$db->get_escape_values())
			$c = addslashes($c);
		
		$values =	array(
			$this->_name_column => $name,
			$this->_content_column => $c
		);
		
		// At first we try an update. That should work most of the time
		$db->update(
			$this->_table,' WHERE '.$this->_name_column.' = "'.$name.'"',$values
		);
		
		// does it not exist? so create it
		// TODO how to do that? affected rows is just > 0 if something has been changed :/
		//if($db->get_affected_rows() == 0)
		//	$db->insert($this->_table,$values);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>