<?php
/**
 * Contains the padding-class
 * 
 * @version			$Id: padding.php 739 2008-05-24 09:46:09Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Can be used for all kinds of paddings. Contains a padding-value for all 4 sides
 * (top,right,bottom,left)
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Padding extends PLIB_FullObject
{
	/**
	 * The top-padding
	 *
	 * @var int
	 */
	private $_top;
	
	/**
	 * The right-padding
	 *
	 * @var int
	 */
	private $_right;
	
	/**
	 * The bottom-padding
	 *
	 * @var int
	 */
	private $_bottom;
	
	/**
	 * The left-padding
	 *
	 * @var int
	 */
	private $_left;
	
	/**
	 * There are multiple ways to create an instance:
	 * <ul>
	 * 	<li><var>__construct()</var>: sets all to <var>0</var></li>
	 * 	<li><var>__construct($pad)</var>: sets all to <var>$pad</var></li>
	 * 	<li><var>__construct($hori,$vert)</var>: sets left and right to <var>$hori</var>
	 * 	and top and bottom to <var>$vert</var></li>
	 * 	<li><var>__construct($top,$right,$bottom,$left)</var>: sets all components to
	 * 	the corresponding value</li>
	 * </ul>
	 *
	 * @param int $arg1
	 * @param int $arg2
	 * @param int $arg3
	 * @param int $arg4
	 */
	public function __construct($arg1 = null,$arg2 = null,$arg3 = null,$arg4 = null)
	{
		parent::__construct();
		
		switch(func_num_args())
		{
			case 0:
				$this->_top = 0;
				$this->_right = 0;
				$this->_bottom = 0;
				$this->_left = 0;
				break;
			
			case 1:
				if(!is_numeric($arg1) || $arg1 < 0)
					PLIB_Helper::def_error('numge0','arg1',$arg1);
				
				$this->_top = $arg1;
				$this->_right = $arg1;
				$this->_bottom = $arg1;
				$this->_left = $arg1;
				break;
			
			case 2:
				if(!is_numeric($arg1) || $arg1 < 0)
					PLIB_Helper::def_error('numge0','arg1',$arg1);
				if(!is_numeric($arg2) || $arg2 < 0)
					PLIB_Helper::def_error('numge0','arg2',$arg2);
				
				$this->_top = $arg1;
				$this->_right = $arg2;
				$this->_bottom = $arg1;
				$this->_left = $arg2;
				break;
			
			case 4:
				if(!is_numeric($arg1) || $arg1 < 0)
					PLIB_Helper::def_error('numge0','arg1',$arg1);
				if(!is_numeric($arg2) || $arg2 < 0)
					PLIB_Helper::def_error('numge0','arg2',$arg2);
				if(!is_numeric($arg3) || $arg3 < 0)
					PLIB_Helper::def_error('numge0','arg3',$arg3);
				if(!is_numeric($arg4) || $arg4 < 0)
					PLIB_Helper::def_error('numge0','arg4',$arg4);
				
				$this->_top = $arg1;
				$this->_right = $arg2;
				$this->_bottom = $arg3;
				$this->_left = $arg4;
				break;
			
			default:
				PLIB_Helper::error('Invalid number of arguments. Allowed are 0,1,2 and 4');
				break;
		}
	}

	/**
	 * @return int the bottom-padding
	 */
	public function get_bottom()
	{
		return $this->_bottom;
	}

	/**
	 * @return int the left-padding
	 */
	public function get_left()
	{
		return $this->_left;
	}

	/**
	 * @return int the right-padding
	 */
	public function get_right()
	{
		return $this->_right;
	}

	/**
	 * @return int the top-padding
	 */
	public function get_top()
	{
		return $this->_top;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>