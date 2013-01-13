<?php
/**
 * Contains the boxposition-class
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

// init the static fields
FWS_GD_BoxPosition::$TOP_LEFT = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::FIRST,FWS_GD_BoxPosition::FIRST
);
FWS_GD_BoxPosition::$TOP_CENTER = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::MIDDLE,FWS_GD_BoxPosition::FIRST
);
FWS_GD_BoxPosition::$TOP_RIGHT = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::LAST,FWS_GD_BoxPosition::FIRST
);
FWS_GD_BoxPosition::$CENTER_LEFT = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::FIRST,FWS_GD_BoxPosition::MIDDLE
);
FWS_GD_BoxPosition::$CENTER_CENTER = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::MIDDLE,FWS_GD_BoxPosition::MIDDLE
);
FWS_GD_BoxPosition::$CENTER_RIGHT = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::LAST,FWS_GD_BoxPosition::MIDDLE
);
FWS_GD_BoxPosition::$BOTTOM_LEFT = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::FIRST,FWS_GD_BoxPosition::LAST
);
FWS_GD_BoxPosition::$BOTTOM_CENTER = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::MIDDLE,FWS_GD_BoxPosition::LAST
);
FWS_GD_BoxPosition::$BOTTOM_RIGHT = new FWS_GD_BoxPosition(
	FWS_GD_BoxPosition::LAST,FWS_GD_BoxPosition::LAST
);

/**
 * Represents a position in a box.
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_BoxPosition extends FWS_Object
{
	/**
	 * The box-position top-left
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $TOP_LEFT;
	
	/**
	 * The box-position top-center
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $TOP_CENTER;
	
	/**
	 * The box-position top-right
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $TOP_RIGHT;
	
	/**
	 * The box-position center-left
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $CENTER_LEFT;
	
	/**
	 * The box-position center-center
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $CENTER_CENTER;
	
	/**
	 * The box-position center-right
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $CENTER_RIGHT;
	
	/**
	 * The box-position bottom-left
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $BOTTOM_LEFT;
	
	/**
	 * The box-position bottom-center
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $BOTTOM_CENTER;
	
	/**
	 * The box-position bottom-right
	 *
	 * @var FWS_GD_BoxPosition
	 */
	public static $BOTTOM_RIGHT;
	
	
	/**
	 * Represents left or top
	 */
	const FIRST					= 0;
	
	/**
	 * Represents right or bottom
	 */
	const LAST					= 1;
	
	/**
	 * Represents horizontal- or vertical-center
	 */
	const MIDDLE				= 2;
	
	
	/**
	 * The vertical position
	 *
	 * @var int
	 */
	private $_vpos;
	
	/**
	 * The horizontal position
	 *
	 * @var int
	 */
	private $_hpos;
	
	/**
	 * Constructor
	 *
	 * @param int $hpos the horizontal position: FIRST,MIDDLE or LAST
	 * @param int $vpos the vertical position: FIRST,MIDDLE or LAST
	 * @see FIRST
	 * @see LAST
	 * @see MIDDLE
	 */
	public function __construct($hpos = self::MIDDLE,$vpos = self::MIDDLE)
	{
		parent::__construct();
		
		if(!in_array($hpos,array(self::FIRST,self::MIDDLE,self::LAST)))
			FWS_Helper::def_error('inarray','hpos',array(self::FIRST,self::MIDDLE,self::LAST),$hpos);
		if(!in_array($vpos,array(self::FIRST,self::MIDDLE,self::LAST)))
			FWS_Helper::def_error('inarray','vpos',array(self::FIRST,self::MIDDLE,self::LAST),$vpos);
		
		$this->_hpos = $hpos;
		$this->_vpos = $vpos;
	}

	/**
	 * @return int the horizontal position
	 */
	public function get_hpos()
	{
		return $this->_hpos;
	}

	/**
	 * @return int the vertical position
	 */
	public function get_vpos()
	{
		return $this->_vpos;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>