<?php
/**
 * Contains the feed-item-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	feed
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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