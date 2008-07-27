<?php
/**
 * Contains the session-storage-implementation for the progress
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The session-based implementation for the progress-storage
 * 
 * @package			PHPLib
 * @subpackage	progress.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Progress_Storage_Session extends PLIB_Object implements PLIB_Progress_Storage
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
		$user = PLIB_Props::get()->user();

		$user->delete_session_data($this->_prefix.'pos');
	}

	public function get_position()
	{
		$user = PLIB_Props::get()->user();

		$pos = $user->get_session_data($this->_prefix.'pos');
		if($pos === false)
			return -1;
		
		return $pos;
	}

	public function store_position($pos)
	{
		$user = PLIB_Props::get()->user();

		if(!PLIB_Helper::is_integer($pos) || $pos < 0)
			PLIB_Helper::def_error('intge0','pos',$pos);
		
		$user->set_session_data($this->_prefix.'pos',$pos);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>