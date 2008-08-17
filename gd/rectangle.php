<?php
/**
 * Contains the rectangle-class
 * 
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a rectangle for drawing with gd.
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_Rectangle extends FWS_Object implements FWS_GD_Shape2D
{
	/**
	 * The position of the rectangle
	 *
	 * @var FWS_GD_Point
	 */
	private $_pos;
	
	/**
	 * The size of the rectangle
	 *
	 * @var FWS_GD_Dimension
	 */
	private $_size;
	
	/**
	 * There are multiple ways to create a rectangle:
	 * <ul>
	 * 	<li><var>__construct()</var>: creates an empty rectangle at (0,0)</li>
	 * 	<li><var>__construct($pos,$size)</var>: with $pos as {@link FWS_GD_Point} and $size as
	 * 	{@link FWS_GD_Dimension}</li>
	 * 	<li><var>__construct($pos1,$pos2)</var>: with $pos1 and $pos2 as {@link FWS_GD_Point}</li>
	 * 	<li><var>__construct($x,$y,$width,$height)</var></li>
	 * </ul>
	 */
	public function __construct($arg1 = null,$arg2 = null,$arg3 = null,$arg4 = null)
	{
		parent::__construct();
		
		$pos = new FWS_GD_Point(0,0);
		$size = new FWS_GD_Dimension(0,0);
		switch(func_num_args())
		{
			// __construct()
			case 0:
				// nothing to do
				break;
			
			// __construct($pos,$size)
			// __construct($pos1,$pos2)
			case 2:
				if(!($arg1 instanceof FWS_GD_Point))
					FWS_Helper::def_error('instance','arg1','FWS_GD_Point',$arg1);
				
				$pos->set_position($arg1->get_x(),$arg1->get_y());
				
				if($arg2 instanceof FWS_GD_Point)
				{
					$size->set_size(
						abs($arg2->get_x() - $arg1->get_x()),
						abs($arg2->get_y() - $arg1->get_y())
					);
				}
				else
				{
					if(!($arg2 instanceof FWS_GD_Dimension))
						FWS_Helper::def_error('instance','arg2','FWS_GD_Dimension',$arg2);
					
					$size->set_size($arg2->get_width(),$arg2->get_height());
				}
				break;
			
			// __construct($x,$y,$width,$height)
			case 4:
				$pos->set_position($arg1,$arg2);
				$size->set_size($arg3,$arg4);
				break;
			
			default:
				FWS_Helper::error('Invalid number of arguments!');
				break;
		}
		
		$this->_pos = $pos;
		$this->_size = $size;
	}
	
	/**
	 * @return FWS_GD_Point the location of the rectangle
	 */
	public function get_location()
	{
		return $this->_pos;
	}
	
	/**
	 * Sets the location of the rectangle
	 *
	 * @param FWS_GD_Point $loc the new value
	 */
	public function set_location($loc)
	{
		if(!($loc instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','loc','FWS_GD_Point',$loc);
		
		$this->_pos = $loc;
	}
	
	/**
	 * @return FWS_GD_Dimension the size of the rectangle
	 */
	public function get_size()
	{
		return $this->_size;
	}
	
	/**
	 * Sets the size of the rectangle
	 *
	 * @param FWS_GD_Dimension $size the new value
	 */
	public function set_size($size)
	{
		if(!($size instanceof FWS_GD_Dimension))
			FWS_Helper::def_error('instance','size','FWS_GD_Dimension',$size);
		
		$this->_size = $size;
	}
	
	/**
	 * Appends the given rectangle to this one. That means the resulting rectangle contains both.
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 */
	public function append($rect)
	{
		list($x1,$y1) = $this->_pos->get();
		list($x2,$y2) = $rect->get_location()->get();
		list($w1,$h1) = $this->_size->get();
		list($w2,$h2) = $rect->get_size()->get();
		$x1 = min($x1,$x2);
		$y1 = min($y1,$y2);
		$this->_pos->set_position($x1,$y1);
		$ex = max($x1 + $w1,$x2 + $w2);
		$ey = max($y1 + $h1,$y2 + $h2);
		$this->_size->set_size($ex - $x1,$ey - $y1);
	}
	
	/**
	 * Moves the rectangle by the given amount
	 *
	 * @param int $x the amount in x-direction
	 * @param int $y the amount in y-direction
	 */
	public function translate($x,$y)
	{
		$this->_pos->translate($x,$y);
	}
	
	/**
	 * Increases the size of the rectangle by the given amounts
	 *
	 * @param int $w the amount to add to the width
	 * @param int $h the amount to add to the height
	 */
	public function grow($w,$h)
	{
		$this->_size->increase($w,$h);
	}
	
	/**
	 * Decreases the size of the rectangle by the given amounts
	 *
	 * @param int $w the amount to substract from the width
	 * @param int $h the amount to substract from the height
	 */
	public function shrink($w,$h)
	{
		$this->_size->decrease($w,$h);
	}
	
	public final function contains_point($point)
	{
		if(!($point instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','point','FWS_GD_Point',$point);
		
		// has to be not-empty
		list($w,$h) = $this->get_size()->get();
		if($w <= 0 || $h <= 0)
			return false;
		
		list($tx,$ty) = $this->get_location()->get();
		list($x,$y) = $point->get();
		return $x > $tx && $x < $tx + $w && $y > $ty && $y < $ty + $h;
	}
	
	public final function contains_line($line)
	{
		if(!($line instanceof FWS_GD_Line))
			FWS_Helper::def_error('instance','line','FWS_GD_Line',$line);
		
		// the start- and end-point of the line have to be in the rectangle
		return $this->contains_point($line->get_from()) && $this->contains_point($line->get_to());
	}
	
	public final function contains_circle($circle)
	{
		if(!($circle instanceof FWS_GD_Circle))
			FWS_Helper::def_error('instance','circle','FWS_GD_Circle',$circle);
		
		// has to be not-empty
		list($w,$h) = $this->get_size()->get();
		if($w == 0 || $h == 0)
			return false;
		
		list($x,$y) = $this->get_location()->get();
		$center = $circle->get_center();
		$radius = $circle->get_radius();
		
		// top
		if(($center->get_y() - $y) < $radius)
			return false;
		
		// left
		if(($center->get_x() - $x) < $radius)
			return false;
		
		// right
		if((($x + $w) - $center->get_x()) < $radius)
			return false;
		
		// bottom
		if((($y + $h) - $center->get_y()) < $radius)
			return false;
		
		return true;
	}
	
	public final function contains_rect($rect)
	{
		if(!($rect instanceof FWS_GD_Rectangle))
			FWS_Helper::def_error('instance','rect','FWS_GD_Rectangle',$rect);
		
		// has to be not-empty
		list($tw,$th) = $this->get_size()->get();
		if($tw <= 0 || $th <= 0)
			return false;
		
		// has to be not-empty
		list($rw,$rh) = $rect->get_size()->get();
		if($rw <= 0 || $rh <= 0)
			return false;
		
		// check if its inside
		list($tx,$ty) = $this->get_location()->get();
		list($rx,$ry) = $rect->get_location()->get();
		return $rx >= $tx && $rx + $rw <= $tx + $tw &&
			$ry >= $ty && $ry + $rh <= $ty + $th;
	}

	public final function intersects_line($line)
	{
		if(!($line instanceof FWS_GD_Line))
			FWS_Helper::def_error('instance','line','FWS_GD_Line',$line);
		
		// use the implementation of the line
		return $line->intersects_rect($this);
	}
	
	public final function intersects_circle($circle)
	{
		if(!($circle instanceof FWS_GD_Circle))
			FWS_Helper::def_error('instance','circle','FWS_GD_Circle',$circle);
		
		$center = $circle->get_center();
		$radius = $circle->get_radius();
		
		// check the distance to the 4 corners because the algo below
		// would return true if the circle is at one corner of the
		// rectangle but doesn't intersect it
		list($tx,$ty) = $this->get_location()->get();
		list($tw,$th) = $this->get_size()->get();
		$points = array(
			array($tx,$ty),
			array($tx + $tw,$ty),
			array($tx,$ty + $th),
			array($tx + $tw,$ty + $th)
		);
		foreach($points as $p)
		{
			if($center->distance(new FWS_GD_Point($p[0],$p[1])) < $radius)
				return true;
		}
		
		// we create a new rectangle, move it by radius to top and left
		// and increase the size by 2 * radius.
		// if this contains the center of the circle this rectangle contains the
		// circle
		$con_rect = new FWS_GD_Rectangle(
			$tx - $radius,$ty - $radius,$tw + $radius * 2,$th + $radius * 2
		);
		return $con_rect->contains_point($center);
	}
	
	public final function intersects_rect($rect)
	{
		if(!($rect instanceof FWS_GD_Rectangle))
			FWS_Helper::def_error('instance','rect','FWS_GD_Rectangle',$rect);
		
		// has to be not-empty
		list($tw,$th) = $this->get_size()->get();
		if($tw <= 0 || $th <= 0)
			return false;
		
		// has to be not-empty
		list($rw,$rh) = $rect->get_size()->get();
		if($rw <= 0 || $rh <= 0)
			return false;
		
		// check if it intersects
		list($tx,$ty) = $this->get_location()->get();
		list($rx,$ry) = $rect->get_location()->get();
		return $rx < $tx + $tw && $rx + $rw > $tx &&
			$ry < $ty + $th && $ry + $rh > $ty;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>