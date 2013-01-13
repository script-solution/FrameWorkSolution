<?php
/**
 * Contains the feed-item-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	feed
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
 * Represents a feed-item
 *
 * @package			FrameWorkSolution
 * @subpackage	feed
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Feed_Item extends FWS_Object
{
	/**
	 * The id of the item
	 *
	 * @var mixed
	 */
	private $_id;
	
	/**
	 * The title of the item
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * The content
	 *
	 * @var string
	 */
	private $_content;
	
	/**
	 * The link to view the full content
	 *
	 * @var string
	 */
	private $_link;
	
	/**
	 * The author
	 *
	 * @var string
	 */
	private $_author;
	
	/**
	 * The timestamp of the item
	 *
	 * @var int
	 */
	private $_date;
	
	/**
	 * Constructor
	 *
	 * @param mixed $id the item-id
	 * @param string $title the title
	 * @param string $content the content
	 * @param string $link the link to the full version
	 * @param string $author the author
	 * @param int $date the timestamp
	 */
	public function __construct($id,$title,$content,$link,$author,$date)
	{
		parent::__construct();
		
		if(empty($id))
			FWS_Helper::def_error('notemtpy','id',$id);
		if(!FWS_Helper::is_integer($date) || $date < 0)
			FWS_Helper::def_error('intge0','date',$date);
		
		$this->_id = $id;
		$this->_title = $title;
		$this->_content = $content;
		$this->_link = $link;
		$this->_author = $author;
		$this->_date = $date;
	}

	/**
	 * @return string the author
	 */
	public function get_author()
	{
		return $this->_author;
	}

	/**
	 * @return string the content
	 */
	public function get_content()
	{
		return $this->_content;
	}

	/**
	 * @return int the timestamp of the item
	 */
	public function get_date()
	{
		return $this->_date;
	}

	/**
	 * @return mixed the id of the item
	 */
	public function get_id()
	{
		return $this->_id;
	}

	/**
	 * @return string the link to view the full content
	 */
	public function get_link()
	{
		return $this->_link;
	}

	/**
	 * @return string the title
	 */
	public function get_title()
	{
		return $this->_title;
	}
	
	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>