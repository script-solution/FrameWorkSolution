<?php
/**
 * Contains the PHP-session-storage-implementation for the progress
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The PHP-session-based implementation for the progress-storage
 * 
 * @package			FrameWorkSolution
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Progress_Storage_PHPSession extends FWS_Object implements FWS_Progress_Storage
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
		if(!FWS_Helper::is_integer($pos) || $pos < 0)
			FWS_Helper::def_error('intge0','pos',$pos);
		
		$_SESSION[$this->_prefix.'pos'] = $pos;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>