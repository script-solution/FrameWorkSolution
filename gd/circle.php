<?php
/**
 * Contains the circle-class
 * 
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a circle for drawing with GD
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_Circle extends PLIB_GD_Ellipse implements PLIB_GD_Shape2D
{
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Point $center the center-point
	 * @param int $radius the radius
	 */
	public function __construct($center,$radius)
	{
		parent::__construct($center,new PLIB_GD_Dimension($radius * 2,$radius * 2));
	}
	
	/**
	 * @return int the radius of the circle
	 */
	public function get_radius()
	{
		return $this->_size->get_width() / 2;
	}
	
	public final function contains_point($point)
	{
		if(!($point instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','point','PLIB_GD_Point',$point);
		
		// simple check if the distance to the center is < radius
		$radius = $this->get_radius();
		return $radius > 0 && $this->_center->distance($point) < $radius;
	}
	
	public final function contains_line($line)
	{
		if(!($line instanceof PLIB_GD_Line))
			PLIB_Helper::def_error('instance','line','PLIB_GD_Line',$line);
		
		// the start- and end-point of the line have to be in the circle
		$radius = $this->get_radius();
		return $line->get_from()->distance($this->_center) <= $radius &&
			$line->get_to()->distance($this->_center) <= $radius;
	}
	
	public final function contains_circle($circle)
	{
		if(!($circle instanceof PLIB_GD_Circle))
			PLIB_Helper::def_error('instance','circle','PLIB_GD_Circle',$circle);
		
		$radius = $this->get_radius();
		// if the radius of this circle is smaller than the given one
		// this one can't contain the given one :)
		if($radius < $circle->get_radius())
			return false;
		
		// check if the distance plus the radius of the small circle
		// is smaller than the radius of the big one
		$dist = $circle->get_center()->distance($this->_center);
		return $circle->get_radius() + $dist <= $radius;
	}
	
	public final function contains_rect($rect)
	{
		if(!($rect instanceof PLIB_GD_Rectangle))
			PLIB_Helper::def_error('instance','rect','PLIB_GD_Rectangle',$rect);
		
		// cache vars
		$radius = $this->get_radius();
		list($x,$y) = $rect->get_location()->get();
		list($w,$h) = $rect->get_size()->get();
		
		// check for each corner of the rectangle if the distance to the circle-border
		// is greater 0
		// or in other words: if (radius - distance) is <= 0
		$points = array(
			array($x,$y),
			array($x + $w,$y),
			array($x,$y + $h),
			array($x + $w,$y + $h)
		);
		foreach($points as $p)
		{
			if($radius - $this->_center->distance(new PLIB_GD_Point($p[0],$p[1])) <= 0)
				return false;
		}
		
		return true;
	}

	public final function intersects_line($line)
	{
		if(!($line instanceof PLIB_GD_Line))
			PLIB_Helper::def_error('instance','line','PLIB_GD_Line',$line);
		
		// use the implementation of the line
		return $line->intersects_circle($this);
	}
	
	public final function intersects_circle($circle)
	{
		if(!($circle instanceof PLIB_GD_Circle))
			PLIB_Helper::def_error('instance','circle','PLIB_GD_Circle',$circle);
		
		// check if the distance between the center-points is smaller than both radius's
		$dist = $circle->get_center()->distance($this->_center);
		return $dist < ($this->get_radius() + $circle->get_radius());
	}
	
	public final function intersects_rect($rect)
	{
		if(!($rect instanceof PLIB_GD_Rectangle))
			PLIB_Helper::def_error('instance','rect','PLIB_GD_Rectangle',$rect);
		
		// we use the implementation of the rectangle here
		return $rect->intersects_circle($this);
	}
	
	/**
	 * @return float the area-size of the circle
	 */
	public function get_area()
	{
		$radius = $this->get_radius();
		return pi() * $radius * $radius;
	}
	
	/**
	 * @return float the circumference of the circle
	 */
	public function get_circumference()
	{
		return 2 * pi() * $this->get_radius();
	}
	
	public function grow($w,$h)
	{
		// ensure that it remains a circle
		if($w != $h)
			PLIB_Helper::error('Since this is a circle the width has to be equal to the height ;)');
		
		parent::grow($w,$h);
	}

	public function shrink($w,$h)
	{
		// ensure that it remains a circle
		if($w != $h)
			PLIB_Helper::error('Since this is a circle the width has to be equal to the height ;)');
		
		parent::shrink($w,$h);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>