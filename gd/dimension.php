<?php
/**
 * Contains the dimension-class
 * 
 * @version			$Id: dimension.php 739 2008-05-24 09:46:09Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a dimension (width and height) for drawing with gd.
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Dimension extends PLIB_FullObject
{
	/**
	 * The height of the dimension
	 *
	 * @var int
	 */
	private $_width;
	
	/**
	 * The width of the dimension
	 *
	 * @var int
	 */
	private $_height;
	
	/**
	 * Constructor
	 * 
	 * @param int $width the width
	 * @param int $height the height
	 */
	public function __construct($width = 0,$height = 0)
	{
		parent::__construct();
		
		$this->set_size($width,$height);
	}
	
	/**
	 * @return int the width of the dimension
	 */
	public function get_width()
	{
		return $this->_width;
	}
	
	/**
	 * @return int the height of the dimension
	 */
	public function get_height()
	{
		return $this->_height;
	}
	
	/**
	 * Returns the size as array
	 *
	 * @return array the size: <code>array(<width>,<height>)</code>
	 */
	public function get()
	{
		return array($this->_width,$this->_height);
	}
	
	/**
	 * Sets the size
	 *
	 * @param int $width the width
	 * @param int $height the height
	 */
	public function set_size($width,$height)
	{
		if(!is_numeric($width) || $width < 0)
			PLIB_Helper::def_error('numge0','width',$width);
		if(!is_numeric($height) || $height < 0)
			PLIB_Helper::def_error('numge0','height',$height);
		
		$this->_width = $width;
		$this->_height = $height;
	}
	
	/**
	 * Derives a new dimension from the current one and adds the given values
	 *
	 * @param int $width the width to add
	 * @param int $height the height to add
	 * @return PLIB_GD_Dimension the new dimension
	 */
	public function derive($width,$height)
	{
		if(!is_numeric($width))
			PLIB_Helper::def_error('numeric','width',$width);
		if(!is_numeric($height))
			PLIB_Helper::def_error('numeric','height',$height);
		
		return new PLIB_GD_Dimension($this->_width + $width,$this->_height + $height);
	}
	
	/**
	 * Increases the size by the give amount
	 *
	 * @param int $w the amount to add to the width
	 * @param int $h the amount to add to the height
	 */
	public function increase($w,$h)
	{
		if(!is_numeric($w) || $w < 0)
			PLIB_Helper::def_error('numge0','w',$w);
		if(!is_numeric($h) || $h < 0)
			PLIB_Helper::def_error('numge0','h',$h);
		
		$this->_width += $w;
		$this->_height += $h;
	}
	
	/**
	 * Decreases the size by the give amount
	 *
	 * @param int $w the amount to substract from the width
	 * @param int $h the amount to substract from the height
	 */
	public function decrease($w,$h)
	{
		if(!is_numeric($w) || $w < 0)
			PLIB_Helper::def_error('numge0','w',$w);
		if(!is_numeric($h) || $h < 0)
			PLIB_Helper::def_error('numge0','h',$h);
		
		$this->_width = max(0,$this->_width - $w);
		$this->_height = max(0,$this->_height - $h);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>