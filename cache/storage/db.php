<?php
/**
 * Contains the cache-storage-db class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	cache.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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

		$qry = $db->sql_qry(
			'SELECT '.$this->_name_column.','.$this->_content_column.' FROM '.$this->_table
		);
		$res = array();
		while($row = $db->sql_fetch_assoc($qry))
			$res[$row[$this->_name_column]] = @unserialize($row[$this->_content_column]);
		$db->sql_free($qry);
		return $res;
	}

	public function store($name,$content)
	{
		$db = FWS_Props::get()->db();

		$values =	array(
			$this->_name_column => $name,
			$this->_content_column => addslashes(serialize($content))
		);
		
		// At first we try an update. That should work most of the time
		$db->sql_update(
			$this->_table,' WHERE '.$this->_name_column.' = "'.$name.'"',$values
		);
		
		// does it not exist? so create it
		// TODO how to do that? affected rows is just > 0 if something has been changed :/
		//if($db->get_affected_rows() == 0)
		//	$db->sql_insert($this->_table,$values);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>