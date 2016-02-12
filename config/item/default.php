<?php
/**
 * Contains the default-item-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	config.item
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
 * The default-implementation for the config-item
 * 
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Config_Item_Default extends FWS_Object implements FWS_Config_Item
{
	/**
	 * The data of the item
	 *
	 * @var FWS_Config_Data
	 */
	protected $_data;
	
	/**
	 * Constructor
	 *
	 * @param FWS_Config_Data $data the data of the item
	 */
	public function __construct($data)
	{
		parent::__construct();
		
		if(!($data instanceof FWS_Config_Data))
			FWS_Helper::def_error('instance','data','FWS_Config_Data',$data);
		
		$this->_data = $data;
	}
	
	public function get_data()
	{
		return $this->_data;
	}
	
	public function has_changed()
	{
		return $this->_data->get_value() != $this->get_value();
	}
	
	/**
	 * @return string the suffix or an empty string for no suffix
	 */
	protected function get_suffix()
	{
		if(($suffix = $this->_data->get_suffix()))
		{
			return ' '.preg_replace_callback(
				'/%([a-z0-9_]+)/i',
				function($match)
				{
					return FWS_Props::get()->locale()->lang($match[1]);
				},
				$suffix
			);
		}
		return '';
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>