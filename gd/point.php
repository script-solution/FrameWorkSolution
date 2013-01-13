<?php
/**
 * Contains the point-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Represents a point for drawing with gd.
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Point extends FWS_Object
{
	/**
	 * The x-coordinate of the point
	 *
	 * @var float
	 */
	private $_x;
	
	/**
	 * The y-coordinate of the point
	 *
	 * @var float
	 */
	private $_y;
	
	/**
	 * Constructor
	 * 
	 * @param int|float $x the x-coordinate
	 * @param int|float $y the y-coordinate
	 */
	public function __construct($x = 0,$y = 0)
	{
		parent::__construct();
		
		$this->set_position($x,$y);
	}
	
	/**
	 * @return float the x-coordinate
	 */
	public function get_x()
	{
		return $this->_x;
	}
	
	/**
	 * @return float the y-coordinate
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
	 * @param int|float $x the x-coordinate
	 * @param int|float $y the y-coordinate
	 */
	public function set_position($x,$y)
	{
		if(!is_numeric($x))
			FWS_Helper::def_error('numeric','x',$x);
		if(!is_numeric($y))
			FWS_Helper::def_error('numeric','y',$y);
		
		$this->_x = $x;
		$this->_y = $y;
	}
	
	/**
	 * Derives a point from this one and translates the position by the given amounts.
	 *
	 * @param int|float $x the amount in x-direction
	 * @param int|float $y the amount in y-direction
	 * @return FWS_GD_Point the derived point
	 */
	public function derive($x,$y)
	{
		if(!is_numeric($x))
			FWS_Helper::def_error('numeric','x',$x);
		if(!is_numeric($y))
			FWS_Helper::def_error('numeric','y',$y);
		
		return new FWS_GD_Point($this->_x + $x,$this->_y + $y);
	}
	
	/**
	 * Tramslates the point by the given amount
	 *
	 * @param int|float $x the amount in x-direction
	 * @param int|float $y the amount in y-direction
	 */
	public function translate($x,$y)
	{
		if(!is_numeric($x))
			FWS_Helper::def_error('numeric','x',$x);
		if(!is_numeric($y))
			FWS_Helper::def_error('numeric','y',$y);
		
		$this->_x += $x;
		$this->_y += $y;
	}
	
	/**
	 * Determines the distance to the given point
	 *
	 * @param FWS_GD_Point $point the other point
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
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>