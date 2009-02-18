<?php
/**
 * Contains the feed-document-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	feed
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a complete feed-document which contains {@link FWS_Feed_Item}'s.
 *
 * @package			FrameWorkSolution
 * @subpackage	feed
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Feed_Document extends FWS_Object
{
	/**
	 * The title
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * The date of the document
	 *
	 * @var int
	 */
	private $_date;
	
	/**
	 * The encoding of the document
	 *
	 * @var string
	 */
	private $_encoding;
	
	/**
	 * Optionally an id for the feed
	 *
	 * @var mixed
	 */
	private $_id = null;
	
	/**
	 * Optionally a description of the feed
	 *
	 * @var string
	 */
	private $_description = null;
	
	/**
	 * Optionally the author-name
	 *
	 * @var string
	 */
	private $_author = null;
	
	/**
	 * Optionally the link to the website
	 *
	 * @var string
	 */
	private $_link = null;
	
	/**
	 * The items of the feed
	 *
	 * @var array
	 */
	private $_items = array();
	
	/**
	 * Constructor
	 *
	 * @param string $title the title of the document
	 * @param int $date the timestamp for the document
	 */
	public function __construct($title,$date,$encoding = 'UTF-8')
	{
		parent::__construct();

		$this->set_title($title);
		$this->set_date($date);
		$this->set_encoding($encoding);
	}

	/**
	 * @return string the author (null = ignore)
	 */
	public function get_author()
	{
		return $this->_author;
	}

	/**
	 * Sets the author of the document
	 * 
	 * @param string $author the new value (null = ignore)
	 */
	public function set_author($author)
	{
		$this->_author = $author;
	}

	/**
	 * @return int the date of the document
	 */
	public function get_date()
	{
		return $this->_date;
	}

	/**
	 * Sets the date of the document
	 * 
	 * @param int $date the new value
	 */
	public function set_date($date)
	{
		if(!FWS_Helper::is_integer($date) || $date < 0)
			FWS_Helper::def_error('intge0','date',$date);
		
		$this->_date = $date;
	}

	/**
	 * @return string the description of the document (null = ignore)
	 */
	public function get_description()
	{
		return $this->_description;
	}

	/**
	 * Sets the description of the document
	 * 
	 * @param string $description the new value (null = ignore)
	 */
	public function set_description($description)
	{
		$this->_description = $description;
	}

	/**
	 * @return mixed the id of the feed
	 */
	public function get_id()
	{
		return $this->_id;
	}

	/**
	 * Sets the id of the document
	 * 
	 * @param mixed $id the new value (null = ignore)
	 */
	public function set_id($id)
	{
		$this->_id = $id;
	}

	/**
	 * @return array all items in the document (instances of {@link FWS_Feed_Item})
	 */
	public function get_items()
	{
		return $this->_items;
	}

	/**
	 * Adds the given item to the document
	 * 
	 * @param FWS_Feed_Item $item the item to add
	 */
	public function add_item($item)
	{
		$this->_items[] = $item;
	}

	/**
	 * @return string the link to the website (null = ignore)
	 */
	public function get_link()
	{
		return $this->_link;
	}

	/**
	 * Sets the link to the website
	 * 
	 * @param string $link the new value (null = ignore)
	 */
	public function set_link($link)
	{
		$this->_link = $link;
	}

	/**
	 * @return string the document-title
	 */
	public function get_title()
	{
		return $this->_title;
	}

	/**
	 * Sets the document-title
	 * 
	 * @param string $title the new value
	 */
	public function set_title($title)
	{		
		if(empty($title))
			FWS_Helper::def_error('notempty','title',$title);
		
		$this->_title = $title;
	}
	
	/**
	 * @return string the encoding
	 */
	public function get_encoding()
	{
		return $this->_encoding;
	}
	
	/**
	 * Sets the encoding of the document
	 *
	 * @param string $encoding the new value
	 */
	public function set_encoding($encoding)
	{
		if(empty($encoding))
			FWS_Helper::def_error('notempty','encoding',$encoding);
		
		$this->_encoding = $encoding;
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