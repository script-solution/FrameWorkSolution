<?php
/**
 * Contains the PHP-session-storage-implementation for the progress
 *
 * @version			$Id: phpsession.php 736 2008-05-23 18:24:22Z nasmussen $
 * @package			PHPLib
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The PHP-session-based implementation for the progress-storage
 * 
 * @package			PHPLib
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Progress_Storage_PHPSession extends PLIB_FullObject implements PLIB_Progress_Storage
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
	public function __construct($prefix = 'plib_progress_')
	{
		parent::__construct();
		
		if(!is_string($prefix))
			PLIB_Helper::def_error('string','prefix',$prefix);
		
		$this->_prefix = $prefix;
	}
	
	public function clear()
	{
		unset($_SESSION[$this->_prefix.'pos']);
	}

	public function get_position()
	{
		if(isset($_SESSION[$this->_prefix.'pos']))
			return $_SESSION[$this->_prefix.'pos'];
		
		return -1;
	}

	public function store_position($pos)
	{
		if(!PLIB_Helper::is_integer($pos) || $pos < 0)
			PLIB_Helper::def_error('intge0','pos',$pos);
		
		$_SESSION[$this->_prefix.'pos'] = $pos;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>