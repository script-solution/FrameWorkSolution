<?php
/**
 * Contains the line-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a line for drawing with GD.
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Line extends PLIB_Object implements PLIB_GD_Shape
{
	/**
	 * The start-point of the line
	 * 
	 * @var PLIB_GD_Point
	 */
	private $_from;
	
	/**
	 * The end-point of the line
	 * 
	 * @var PLIB_GD_Point
	 */
	private $_to;
	
	/**
	 * There are multiple ways to create a line:
	 * <ul>
	 * 	<li><var>__construct($from,$to)</var>: with $from and $to as {@link PLIB_GD_Point}</li>
	 * 	<li><var>__construct($x1,$y1,$x2,$y2)</var></li>
	 * </ul>
	 */
	public function __construct($arg1,$arg2,$arg3 = null,$arg4 = null)
	{
		parent::__construct();
		
		switch(func_num_args())
		{
			// __construct($from,$to)
			case 2:
				if(!($arg1 instanceof PLIB_GD_Point))
					PLIB_Helper::def_error('instance','arg1','PLIB_GD_Point',$arg1);
				if(!($arg2 instanceof PLIB_GD_Point))
					PLIB_Helper::def_error('instance','arg2','PLIB_GD_Point',$arg2);
				
				$this->_from = $arg1;
				$this->_to = $arg2;
				break;
			
			// __construct($x1,$y1,$x2,$y2)
			case 4:
				$this->_from = new PLIB_GD_Point($arg1,$arg2);
				$this->_to = new PLIB_GD_Point($arg3,$arg4);
				break;
			
			default:
				PLIB_Helper::error('Invalid number of arguments!');
				break;
		}
	}
	
	/**
	 * @return int the first x-position
	 */
	public function get_x1()
	{
		return $this->_from->get_x();
	}
	
	/**
	 * @return int the first y-position
	 */
	public function get_y1()
	{
		return $this->_from->get_y();
	}
	
	/**
	 * @return int the second x-position
	 */
	public function get_x2()
	{
		return $this->_to->get_x();
	}
	
	/**
	 * @return int the second y-position
	 */
	public function get_y2()
	{
		return $this->_to->get_y();
	}
	
	/**
	 * @return PLIB_GD_Point the start-point of the line
	 */
	public function get_from()
	{
		return $this->_from;
	}
	
	/**
	 * @return PLIB_GD_Point the end-point of the line
	 */
	public function get_to()
	{
		return $this->_to;
	}
	
	/**
	 * @return double the length of this line
	 */
	public function get_length()
	{
		return $this->_from->distance($this->_to);
	}

	public function intersects_line($line)
	{
		if(!($line instanceof PLIB_GD_Line))
			PLIB_Helper::def_error('instance','line','PLIB_GD_Line',$line);
		
		return $this->_lines_intersect(
			$line->get_x1(),$line->get_y1(),$line->get_x2(),$line->get_y2(),
			$this->get_x1(),$this->get_y1(),$this->get_x2(),$this->get_y2()
		);
	}

	public function intersects_circle($circle)
	{
		if(!($circle instanceof PLIB_GD_Circle))
			PLIB_Helper::def_error('instance','circle','PLIB_GD_Circle',$circle);
		
		list($x,$y) = $circle->get_center()->get();
		$r = $circle->get_radius();
		if($r <= 0)
 			return false;
 		
 		$x1 = $this->get_x1();
 		$y1 = $this->get_y1();
 		$x2 = $this->get_x2();
 		$y2 = $this->get_y2();
 		
 		// start or end point in the circle?
 		$s2cdist = $this->_from->distance($circle->get_center());
 		$e2cdist = $this->_to->distance($circle->get_center());
 		if($s2cdist <= $r || $e2cdist <= $r)
 			return true;
 		
 		$lxdiff = $x2 - $x1;
 		if($lxdiff == 0)
 		{
 			// if the line is vertical the x-position has to be in the circle (just x)
 			// additionally one y-pos has to be above the center-y and the other one below
 			return $x1 >= $x - $r && $x1 <= $x + $r &&
 				(($y1 <= $y && $y2 >= $y) || ($y1 >= $y && $y2 <= $y));
 		}
 		
 		// is the line nearer to the circle than its radius?
 		$c2l = $this->distance($circle->get_center());
 		if($c2l <= $r)
 		{
 			// If x1,x2 or y1,y2 are on different sides of the circle, the line intersects the circle.
 			// Because the start- and end-point of the line is not in the circle. Therefore the only way
 			// to hit the circle is that one point is on one side and the other on the other side.
 			// This prevents that lines hit the circle which would just hit it if the length is infinitive
 			// (which does the distance() method assume).
	 		if(($x1 < $x && $x2 > $x) || ($x1 > $x && $x2 < $x))
	 			return true;
	 		
	 		if(($y1 < $y && $y2 > $y) || ($y1 > $y && $y2 < $y))
	 			return true;
 		}
 		
 		return false;
	}

	public function intersects_rect($rect)
	{
		if(!($rect instanceof PLIB_GD_Rectangle))
			PLIB_Helper::def_error('instance','rect','PLIB_GD_Rectangle',$rect);
		
		// Borrowed from java.awt.geom.Line2D
		
		list($x,$y) = $rect->get_location()->get();
		list($w,$h) = $rect->get_size()->get();
		if($w <= 0 || $h <= 0)
 			return false;
 		
 		$x1 = $this->get_x1();
 		$y1 = $this->get_y1();
 		$x2 = $this->get_x2();
 		$y2 = $this->get_y2();

 		// start point in the rectangle?
 		if($x1 >= $x && $x1 <= $x + $w && $y1 >= $y && $y1 <= $y + $h)
 			return true;
 		// end point in the rectangle?
 		if($x2 >= $x && $x2 <= $x + $w && $y2 >= $y && $y2 <= $y + $h)
 			return true;
 
 		$x3 = $x + $w;
 		$y3 = $y + $h;
 		
		return $this->_lines_intersect($x1,$y1,$x2,$y2,$x,$y,$x,$y3) ||
			$this->_lines_intersect($x1,$y1,$x2,$y2,$x,$y3,$x3,$y3) ||
			$this->_lines_intersect($x1,$y1,$x2,$y2,$x3,$y3,$x3,$y) ||
			$this->_lines_intersect($x1,$y1,$x2,$y2,$x3,$y,$x,$y);
	}
	
	/**
	 * Calculates the distance to the given point. Note that the method assumes that the line
	 * has an infinive length!
	 *
	 * @param PLIB_GD_Point $point the point
	 * @return double the distance of this line to the point
	 */
	public function distance($point)
	{
		if(!($point instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','point','PLIB_GD_Point',$point);
		
		// a = vector1, b = vector2, alpha = angle between a and b
		// vectorproduct: |c| = |a|*|b|*sin(alpha)
		// angle between a and b: acos(a*b / (|a|*|b|))
		// distance of vector v to a straight line: |c| / |x| where x is the direction vector of the
		// straight line.
		
		// build straight line with va = direction-vector and vx = vector between the line and the given
		// point
		$va = array(
			-$this->get_x2() + $this->get_x1(),
			-$this->get_y2() + $this->get_y1()
		);
		$vx = array(
			-$point->get_x() + $this->get_x1(),
			-$point->get_y() + $this->get_y1()
		);
		// calculate length
		$ava = $this->_abs_value($va);
		$avx = $this->_abs_value($vx);
		
		// calculate the scalarproduct of va and vx
		$scalarprod = 0;
		for($i = 0;$i < 2;$i++)
			$scalarprod += $va[$i] * $vx[$i];
		
		// calculate the angle between va and vx
		$alpha = acos($scalarprod / ($ava * $avx));
		
		// now calculate the distance, that means |c| / |x|
		$avc = $ava * $avx * sin($alpha);
		return $avc / $ava;
	}
	
	/**
	 * Calculates the absolute value of the given vector
	 * 
	 * @param array $vector the vector
	 * @return double the abs-value
	 */
	private function _abs_value($vector)
	{
		$c = 0;
		foreach($vector as $v)
			$c += $v * $v;
		return sqrt($c);
	}
	
	/**
	 * Test if the line segment (x1,y1)->(x2,y2) intersects the line segment 
	 * (x3,y3)->(x4,y4).
	 *
	 * @param int $x1 the first x coordinate of the first segment
	 * @param int $y1 the first y coordinate of the first segment 
	 * @param int $x2 the second x coordinate of the first segment
	 * @param int $y2 the second y coordinate of the first segment
	 * @param int $x3 the first x coordinate of the second segment
	 * @param int $y3 the first y coordinate of the second segment
	 * @param int $x4 the second x coordinate of the second segment
	 * @param int $y4 the second y coordinate of the second segment
	 * @return true if the segments intersect
	 */
	private function _lines_intersect($x1,$y1,$x2,$y2,$x3,$y3,$x4,$y4)
	{
		// y = ax + b
		// ax + b = cx + d
		// => ax - cx = d - b
		// => a - c = (d - b) / x
		// => (a - c) / (d - b) = 1 / x
		// => (d - b) / (a - c) = x

		$l1xdiff = $x2 - $x1;
		$l2xdiff = $x4 - $x3;
		
		// if both are vertical the x-coordinate has to be equal
		if($l1xdiff == 0 && $l2xdiff == 0)
			return $x1 == $x3;
		
		$crossx = null;
		$crossy = null;
		
		// if one of them is vertical we switch x and y
		if($l1xdiff == 0 || $l2xdiff == 0)
		{
			$t = $x1; $x1 = $y1; $y1 = $t;
			$t = $x2; $x2 = $y2; $y2 = $t;
			$t = $x3; $x3 = $y3; $y3 = $t;
			$t = $x4; $x4 = $y4; $y4 = $t;
			$l1xdiff = $x2 - $x1;
			$l2xdiff = $x4 - $x3;
			
			// one line vertical and one line horizontal?
			if($l1xdiff == 0 || $l2xdiff == 0)
			{
				// we know the cross-point..
				if($l1xdiff == 0)
				{
					$crossx = $x1;
					$crossy = $y3;
				}
				else
				{
					$crossx = $x3;
					$crossy = $y1;
				}
			}
		}
		
		// just calculate the cross-point if not already done
		if($crossx === null && $crossy === null)
		{
			$a = ($y2 - $y1) / $l1xdiff;
			$b = $y1 - ($x1 * $a);
			
			$c = ($y4 - $y3) / $l2xdiff;
			$d = $y3 - ($x3 * $c);
			
			// x-coordinates equal?
			if($a - $c == 0)
				return $y1 == $y3;
			
			$crossx = ($d - $b) / ($a - $c);
			$crossy = $a * $crossx + $b;
		}
		
		// create the 4 points and check wether the cross-point is in the first and the second line
		// or in other words: if the distance of p1 <-> cross and p2 <-> cross is less than the length
		// of the line. The same with p3 and p4
		$p1 = new PLIB_GD_Point($x1,$y1);
		$p2 = new PLIB_GD_Point($x2,$y2);
		$p3 = new PLIB_GD_Point($x3,$y3);
		$p4 = new PLIB_GD_Point($x4,$y4);
		
		$l1len = $p1->distance($p2);
		$l2len = $p3->distance($p4);
		$cross = new PLIB_GD_Point($crossx,$crossy);
		
		return $cross->distance($p1) <= $l1len && $cross->distance($p2) <= $l1len &&
			$cross->distance($p3) <= $l2len && $cross->distance($p4) <= $l2len;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>