<?php
/**
 * Contains the simple-db-implementation for the source
 * 
 * @package			FrameWorkSolution
 * @subpackage	cache.source
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
 * A simple db-based implementation for the source. You can specify the table-name, a key
 * of the table and the order of the result.
 *
 * @package			FrameWorkSolution
 * @subpackage	cache.source
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Cache_Source_SimpleDB extends FWS_Object implements FWS_Cache_Source
{
	/**
	 * The table-name to use for the query
	 *
	 * @var string
	 */
	private $_table;
	
	/**
	 * The key of the table (null = none)
	 *
	 * @var string
	 */
	private $_key;
	
	/**
	 * The column for the order-clause (null = none)
	 *
	 * @var string
	 */
	private $_order;
	
	/**
	 * The direction for the order-clause
	 *
	 * @var string
	 */
	private $_direction;
	
	/**
	 * Constructor
	 * 
	 * @param string $table the table-name to use for the query
	 * @param string $key the key of the table (null = none)
	 * @param string $order the column for the order-clause (null = none)
	 * @param string $dir the direction for the order-clause
	 */
	public function __construct($table,$key = 'id',$order = 'id',$dir = 'ASC')
	{
		parent::__construct();
		
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);
		if($key !== null && empty($key))
			FWS_Helper::error('$key is not null but empty!');
		if($order !== null && empty($order))
			FWS_Helper::error('$order is not null but empty!');
		if(!in_array($dir,array('ASC','DESC')))
			FWS_Helper::def_error('inarray','dir',array('ASC','DESC'),$dir);
		
		$this->_table = $table;
		$this->_key = $key;
		$this->_order = $order;
		$this->_direction = $dir;
	}
	
	public function get_content()
	{
		$db = FWS_Props::get()->db();

		// perform query
		$sql = 'SELECT * FROM '.$this->_table;
		if($this->_order !== null)
			$sql .= ' ORDER BY '.$this->_order.' '.$this->_direction;
		
		// collect rows
		$rows = array();
		foreach($db->get_rows($sql) as $row)
		{
			if($this->_key !== null)
				$rows[$row[$this->_key]] = $row;
			else
				$rows[] = $row;
		}
		
		return $rows;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>