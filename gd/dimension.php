<?php
/**
 * Contains the dimension-class
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
 * Represents a dimension (width and height) for drawing with gd.
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Dimension extends FWS_Object
{
	/**
	 * The height of the dimension
	 *
	 * @var float
	 */
	private $_width;
	
	/**
	 * The width of the dimension
	 *
	 * @var float
	 */
	private $_height;
	
	/**
	 * Constructor
	 * 
	 * @param int|float $width the width
	 * @param int|float $height the height
	 */
	public function __construct($width = 0,$height = 0)
	{
		parent::__construct();
		
		$this->set_size($width,$height);
	}
	
	/**
	 * @return float the width of the dimension
	 */
	public function get_width()
	{
		return $this->_width;
	}
	
	/**
	 * @return float the height of the dimension
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
	 * @param int|float $width the width
	 * @param int|float $height the height
	 */
	public function set_size($width,$height)
	{
		if(!is_numeric($width) || $width < 0)
			FWS_Helper::def_error('numge0','width',$width);
		if(!is_numeric($height) || $height < 0)
			FWS_Helper::def_error('numge0','height',$height);
		
		$this->_width = $width;
		$this->_height = $height;
	}
	
	/**
	 * Derives a new dimension from the current one and adds the given values
	 *
	 * @param int|float $width the width to add
	 * @param int|float $height the height to add
	 * @return FWS_GD_Dimension the new dimension
	 */
	public function derive($width,$height)
	{
		if(!is_numeric($width))
			FWS_Helper::def_error('numeric','width',$width);
		if(!is_numeric($height))
			FWS_Helper::def_error('numeric','height',$height);
		
		return new FWS_GD_Dimension($this->_width + $width,$this->_height + $height);
	}
	
	/**
	 * Increases the size by the give amount
	 *
	 * @param int|float $w the amount to add to the width
	 * @param int|float $h the amount to add to the height
	 */
	public function increase($w,$h)
	{
		if(!is_numeric($w) || $w < 0)
			FWS_Helper::def_error('numge0','w',$w);
		if(!is_numeric($h) || $h < 0)
			FWS_Helper::def_error('numge0','h',$h);
		
		$this->_width += $w;
		$this->_height += $h;
	}
	
	/**
	 * Decreases the size by the give amount
	 *
	 * @param int|float $w the amount to substract from the width
	 * @param int|float $h the amount to substract from the height
	 */
	public function decrease($w,$h)
	{
		if(!is_numeric($w) || $w < 0)
			FWS_Helper::def_error('numge0','w',$w);
		if(!is_numeric($h) || $h < 0)
			FWS_Helper::def_error('numge0','h',$h);
		
		$this->_width = max(0,$this->_width - $w);
		$this->_height = max(0,$this->_height - $h);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>