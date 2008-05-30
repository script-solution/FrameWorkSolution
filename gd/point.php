<?php
/**
 * Contains the point-class
 * 
 * @version			$Id: point.php 739 2008-05-24 09:46:09Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a point for drawing with gd.
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Point extends PLIB_FullObject
{
	/**
	 * The x-coordinate of the point
	 *
	 * @var int
	 */
	private $_x;
	
	/**
	 * The y-coordinate of the point
	 *
	 * @var int
	 */
	private $_y;
	
	/**
	 * Constructor
	 * 
	 * @param int $x the x-coordinate
	 * @param int $y the y-coordinate
	 */
	public function __construct($x = 0,$y = 0)
	{
		parent::__construct();
		
		$this->set_position($x,$y);
	}
	
	/**
	 * @return int the x-coordinate
	 */
	public function get_x()
	{
		return $this->_x;
	}
	
	/**
	 * @return int the y-coordinate
	 */
	public function get_y()
	{
		return $this->_y;
	}
	
	/**
	 * Returns the position as numeric array
	 *
	 * @return array the position: <code>array(<x>,<y>)</code>
	 */
	public function get()
	{
		return array($this->_x,$this->_y);
	}
	
	/**
	 * Sets the position to given value
	 *
	 * @param int $x the x-coordinate
	 * @param int $y the y-coordinate
	 */
	public function set_position($x,$y)
	{
		if(!is_numeric($x))
			PLIB_Helper::def_error('numeric','x',$x);
		if(!is_numeric($y))
			PLIB_Helper::def_error('numeric','y',$y);
		
		$this->_x = $x;
		$this->_y = $y;
	}
	
	/**
	 * Derives a point from this one and translates the position by the given amounts.
	 *
	 * @param int $x the amount in x-direction
	 * @param int $y the amount in y-direction
	 * @return PLIB_GD_Point the derived point
	 */
	public function derive($x,$y)
	{
		if(!is_numeric($x))
			PLIB_Helper::def_error('numeric','x',$x);
		if(!is_numeric($y))
			PLIB_Helper::def_error('numeric','y',$y);
		
		return new PLIB_GD_Point($this->_x + $x,$this->_y + $y);
	}
	
	/**
	 * Tramslates the point by the given amount
	 *
	 * @param int $x the amount in x-direction
	 * @param int $y the amount in y-direction
	 */
	public function translate($x,$y)
	{
		if(!is_numeric($x))
			PLIB_Helper::def_error('numeric','x',$x);
		if(!is_numeric($y))
			PLIB_Helper::def_error('numeric','y',$y);
		
		$this->_x += $x;
		$this->_y += $y;
	}
	
	/**
	 * Determines the distance to the given point
	 *
	 * @param PLIB_GD_Point $point the other point
	 * @return float the distance to the point
	 */
	public function distance($point)
	{
		$x2 = $point->get_x();
		$y2 = $point->get_y();
		$x2 -= $this->_x;
		$y2 -= $this->_y;
		return sqrt($x2 * $x2 + $y2 * $y2);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>