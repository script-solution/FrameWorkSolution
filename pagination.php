<?php
/**
 * Contains the pagination-class
 *
 * @version			$Id: pagination.php 736 2008-05-23 18:24:22Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains all information about the pagination and generates the page-numbers
 * with a special algorithm. This algorithm displays a specific number of pages
 * around the current page and increases the distance between the pages with
 * increasing distance to the current page.
 * So the result may look like this: (|x| is the current page)
 * <pre>1 2 |3| 4 5 7 9 ... 15</pre>
 * <pre>1 ... 8 11 13 14 |15|</pre>
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Pagination extends PLIB_FullObject
{
	/**
	 * The number of entries per page
	 * 
	 * @var integer
	 */
	private $_per_page;
	
	/**
	 * The total number of entries
	 * 
	 * @var integer
	 */
	private $_num;
	
	/**
	 * The current page-number
	 * 
	 * @var integer
	 */
	private $_page;
	
	/**
	 * The start-position of the current page (for the database-query)
	 * 
	 * @var integer
	 */
	private $_start;
	
	/**
	 * The number of pages
	 * 
	 * @var integer
	 */
	private $_page_count;
	
	/**
	 * Constructor
	 * 
	 * @param int $per_page the number of entries per page
	 * @param int $num the total number of entries
	 * @param int $page the current page-number
	 */
	public function __construct($per_page,$num,$page = 1)
	{
		parent::__construct();
		
		if(!PLIB_Helper::is_integer($per_page) || $per_page <= 0)
			PLIB_Helper::def_error('intgt0','per_page',$per_page);
		if(!PLIB_Helper::is_integer($num) || $num < 0)
			PLIB_Helper::def_error('intge0','num',$num);

		$this->_per_page = $per_page;
		$this->_num = $num;
		$this->_page = max(1,$page);
		$this->_init();
	}
	
	/**
	 * Init the page-count and the start-position
	 */
	private function _init()
	{
		if(($this->_num % $this->_per_page) == 0)
			$this->_page_count = $this->_num / $this->_per_page;
		else
			$this->_page_count = (int)($this->_num / $this->_per_page) + 1;
		
		if($this->_page_count < 1)
			$this->_page_count = 1;
		
		$this->_start = $this->_per_page * ($this->_page - 1);
	}
	
	/**
	 * @return integer the current page-number
	 */
	public final function get_page()
	{
		return $this->_page;
	}
	
	/**
	 * @return integer the total number of items
	 */
	public final function get_num()
	{
		return $this->_num;
	}
	
	/**
	 * @return integer the number of items per page
	 */
	public final function get_per_page()
	{
		return $this->_per_page;
	}
	
	/**
	 * @return the number of pages
	 */
	public final function get_page_count()
	{
		return $this->_page_count;
	}
	
	/**
	 * @return the start-position of the current page (for the database-query)
	 */
	public final function get_start()
	{
		return $this->_start;
	}

	/**
	 * generates the numbers for the page-split
	 *
	 * @param int $percent the percent-value to skip numbers based on the distance to the
	 * 	current page
	 * @param int $untouched how many numbers in front and behind the current page should
	 * 	stay untouched?
	 * @param int $max_dist the maximum distance (behind / in front of this "..." is used)
	 * @return array a numeric array with the numbers. This can also contain "..."
	 */
	public final function get_page_numbers($percent = 30,$untouched = 2,$max_dist = 6)
	{
		if(!PLIB_Helper::is_integer($percent) || $percent < 0 || $percent > 100)
			PLIB_Helper::def_error('numbetween','percent',0,100,$percent);
		if(!PLIB_Helper::is_integer($untouched) || $untouched < 0)
			PLIB_Helper::def_error('intge0','untouched',$untouched);
		if(!PLIB_Helper::is_integer($max_dist) || $max_dist < 0)
			PLIB_Helper::def_error('intge0','max_dist',$max_dist);

		$numbers = array();
		$numbers[] = 1;
		for($i = 2;$i <= $this->_page_count;$i++)
		{
			// reached max-dist in front?
			if($i < $this->_page - $max_dist)
			{
				$numbers[] = '...';
				$i = $this->_page - $max_dist - 1;
				continue;
			}

			// reached max-dist and end?
			if($i > $this->_page + $max_dist)
			{
				$numbers[] = '...';
				$numbers[] = $this->_page_count;
				break;
			}

			// add the number
			$numbers[] = $i;

			// do we have to skip some numbers?
			if($i < $this->_page_count && !($i >= $this->_page - $untouched - 1 &&
				$i < $this->_page + $untouched))
			{
				// determine the next number to show, based on the distance to the current page
				// and the given percentage
				$i += round(abs($this->_page - $i) / (100 / $percent),0);

				// have we reached the end?
				if($i >= $this->_page_count - 1)
				{
					$numbers[] = $this->_page_count;
					break;
				}
			}
		}

		return $numbers;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>