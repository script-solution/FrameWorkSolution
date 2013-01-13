<?php
/**
 * Contains the session-storage-implementation for the progress
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress.storage
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
 * The session-based implementation for the progress-storage
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Progress_Storage_Session extends FWS_Object implements FWS_Progress_Storage
{
	/**
	 * The prefix of the session-variable
	 *
	 * @var string
	 */
	private $_prefix;
	
	/**
	 * Constructor
	 *
	 * @param string $prefix the prefix for the session-variable
	 */
	public function __construct($prefix = 'fws_progress_')
	{
		parent::__construct();
		
		if(!is_string($prefix))
			FWS_Helper::def_error('string','prefix',$prefix);
		
		$this->_prefix = $prefix;
	}
	
	public function clear()
	{
		$user = FWS_Props::get()->user();

		$user->delete_session_data($this->_prefix.'pos');
	}

	public function get_position()
	{
		$user = FWS_Props::get()->user();

		$pos = $user->get_session_data($this->_prefix.'pos');
		if($pos === false)
			return -1;
		
		return $pos;
	}

	public function store_position($pos)
	{
		$user = FWS_Props::get()->user();

		if(!FWS_Helper::is_integer($pos) || $pos < 0)
			FWS_Helper::def_error('intge0','pos',$pos);
		
		$user->set_session_data($this->_prefix.'pos',$pos);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>