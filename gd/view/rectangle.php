<?php
/**
 * Contains the rectangle-view-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The view for a rectangle which allows the painting of it
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_View_Rectangle extends FWS_GD_View
{
	/**
	 * The rectangle
	 *
	 * @var FWS_GD_Rectangle
	 */
	protected $_rect;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $img the image
	 * @param FWS_GD_Rectangle $rect the rectangle
	 */
	public function __construct($img,$rect)
	{
		parent::__construct($img);
		
		if(!($rect instanceof FWS_GD_Rectangle))
			FWS_Helper::def_error('instance','rect','FWS_GD_Rectangle',$rect);
		
		$this->_rect = $rect;
	}
	
	/**
	 * Fills the rectangle with rounded corners with given radius
	 *
	 * @param FWS_GD_Color $color the color of the rectangle
	 * @param int $radius the radius of the corners
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 */
	public final function fill_rounded($color,$radius = 3,$angle = 0,$pos = null)
	{
		$this->_paint_rounded($color,$radius,$angle,$pos,'filled');
	}
	
	/**
	 * Draws the rectangle with rounded corners with given radius
	 *
	 * @param FWS_GD_Color $color the color of the rectangle
	 * @param int $radius the radius of the corners
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 */
	public final function draw_rounded($color,$radius = 3,$angle = 0,$pos = null)
	{
		$this->_paint_rounded($color,$radius,$angle,$pos,'');
	}
	
	/**
	 * Draws the rectangle with the given color
	 *
	 * @param FWS_GD_Color $color
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 * @return the result of imagerectangle()
	 */
	public final function draw($color,$angle = 0,$pos = null)
	{
		return $this->_paint($color,$angle,$pos,'');
	}
	
	/**
	 * Fills the rectangle with the given color
	 *
	 * @param FWS_GD_Color $color
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 * @return the result of imagefilledrectangle()
	 */
	public final function fill($color,$angle = 0,$pos = null)
	{
		return $this->_paint($color,$angle,$pos,'filled');
	}
	
	/**
	 * Fills the rectangle with a color-fade with the given colors and given step-width.
	 *
	 * @param array $colors an array with all colors that should be used. These may be instances of
	 * 	FWS_GD_Color or arrays with the 3 or 4 color-components
	 * @param int $step the step-size
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 */
	public final function fill_colorfade($colors,$step = 1,$angle = 0,$pos = null)
	{
		if(!FWS_Helper::is_integer($step) || $step <= 0)
			FWS_Helper::def_error('intgt0','step',$step);
		
		$distance = $this->_rect->get_size()->get_width();
		$cf = new FWS_GD_ColorFade($distance,$distance / $step,$colors);
		$cfcolors = $cf->get_colors();
		$img = $this->get_image_res();
		
		// determine paint-points for the rotated rectangle
		$points = $this->get_paint_points($angle,$pos);
		list($ltx,$lty,$lbx,$lby,,,$rtx,$rty) = $points;
		
		// calculate step-widths
		$ccolors = count($cfcolors);
		$x_step = (($rtx - $ltx) / $ccolors);
		$y_step = ($rty - $lty) / $ccolors;
		
		// adjust right-bottom and right-top for the first step
		$points[4] = $lbx + $x_step;
		$points[5] = $lby + $y_step;
		$points[6] = $ltx + $x_step;
		$points[7] = $lty + $y_step;
		
		foreach($cfcolors as $color)
		{
			// copy the array (bug in imagefilledpolygon?)
			$p = array();
			foreach($points as $myp)
				$p[] = $myp;
			
			// draw the rectangle-part
			imagefilledpolygon(
				$img,
				$p,4,
				$color->get_color($img)
			);
			
			// move all points to the next step
			for($i = 0;$i < 8;$i += 2)
			{
				$points[$i] += $x_step;
				$points[$i + 1] += $y_step;
			}
		}
	}
	
	/**
	 * Filles the rectangle with the given color. Additional lines will be draw on the top and
	 * right with a slightly (<var>$color_diff</var>) different color so that it looks 3 dimensional.
	 * Note that this method fills the rectangle, too. So you shouldn't call fill(), too.
	 *
	 * @param FWS_GD_Color $color the color of the rectangle
	 * @param int $color_diff the difference of the color on the top and right to the given color
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 * @return boolean the result of imagefilledrectangle() or imagefilledpolygon()
	 */
	public final function fill_3d($color,$color_diff = 80,$angle = 0,$pos = null)
	{
		if(!FWS_Helper::is_integer($color_diff) || $color_diff <= 0)
			FWS_Helper::def_error('intgt0','color_diff',$color_diff);
		
		$res = $this->_paint($color,$angle,$pos,'filled');
		
		$mycolor = clone $color;
		$mycolor->brighter($color_diff);
		$paint_points = $this->get_paint_points($angle,$pos);
		$lview = $this->get_graphics()->get_line_view(
			new FWS_GD_Line(
				new FWS_GD_Point($paint_points[0],$paint_points[1]),
				new FWS_GD_Point($paint_points[6],$paint_points[7])
			)
		);
		$lview->draw($mycolor);
		
		$mycolor->darker($color_diff * 2);
		$lview = $this->get_graphics()->get_line_view(
			new FWS_GD_Line(
				new FWS_GD_Point($paint_points[4],$paint_points[5]),
				new FWS_GD_Point($paint_points[6],$paint_points[7])
			)
		);
		$lview->draw($mycolor);
		
		return $res;
	}
	
	/**
	 * Does the actual painting
	 *
	 * @param FWS_GD_Color $color the color
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 * @param string $func an empty string for draw() and 'filled' for fill()
	 * @return the result of image*rectangle()
	 */
	private function _paint($color,$angle,$pos,$func)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
		$img = $this->get_image_res();
		
		// use the simpler way if the angle is 0,360,...
		if(($angle % 360) == 0)
		{
			list($x,$y) = $this->_rect->get_location()->get();
			list($w,$h) = $this->_rect->get_size()->get();
			$funcname = 'image'.$func.'rectangle';
			return $funcname($img,$x,$y,$x + $w,$y + $h,$color->get_color($img));
		}
		
		$paint_points = $this->get_paint_points($angle,$pos);
		$funcname = 'image'.$func.'polygon';
		return $funcname($img,$paint_points,4,$color->get_color($img));
	}
	
	/**
	 * Does the actual painting for the rounded-methods
	 *
	 * @param FWS_GD_Color $color the color of the rectangle
	 * @param int $radius the radius of the corners
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 * @param string $func the function: 'filled' or ''
	 */
	private function _paint_rounded($color,$radius,$angle,$pos,$func)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		if(!FWS_Helper::is_integer($radius) || $radius <= 0)
			FWS_Helper::def_error('intgt0','radius',$radius);
		if(!FWS_Helper::is_integer($angle))
			FWS_Helper::def_error('integer','angle',$angle);
		
		list($w,$h) = $this->_rect->get_size()->get();
		$img = $this->_img->get_image();
		$dradius = $radius * 2;
		$col = $color->get_color($img);
		
		// rotate the center-points of the ellipse-parts around the specified position
		$centers = array(
			array($w - $radius,$radius),
			array($w - $radius,$h - $radius),
			array($radius,$h - $radius),
			array($radius,$radius),
		);
		$this->rotate_points($centers,$angle,$pos);
		
		// draw the ellipse-parts
		$i = 0;
		$map90 = 90 - $angle;
		foreach(range(0,270,90) as $a)
		{
			if($func == 'filled')
			{
				imagefilledarc(
					$img,
					$centers[$i],$centers[$i + 1],
					$dradius,$dradius,
					$a - $map90,($a + 90) - $map90,
					$col,
					IMG_ARC_PIE
				);
			}
			else
			{
				imagearc(
					$img,
					$centers[$i],$centers[$i + 1],
					$dradius,$dradius,
					$a - $map90,($a + 90) - $map90,
					$col
				);
			}
			$i += 2;
		}
		
		// rotate the line-starts and -ends around the specified position
		$points = array(
			// first
			array($radius,0),
			array($w - $radius,0),
			// second
			array($w,$radius),
			array($w,$h - $radius),
			// third
			array($w - $radius,$h),
			array($radius,$h),
			// fourth
			array(0,$h - $radius),
			array(0,$radius)
		);
		$this->rotate_points($points,$angle,$pos);
		
		// draw the lines
		if($func == 'filled')
			imagefilledpolygon($img,$points,count($points) / 2,$col);
		else
		{
			for($i = 0;$i < count($points);$i += 4)
			{
				imageline(
					$img,
					$points[$i],$points[$i + 1],
					$points[$i + 2],$points[$i + 3],
					$col
				);
			}
		}
	}
	
	/**
	 * Determines the paint-points for the rotation of the rectangle
	 * 
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 */
	protected final function get_paint_points($angle,$pos)
	{
		$size = $this->_rect->get_size();
		$points = array(
			array(0,0),
			array(0,$size->get_height()),
			array($size->get_width(),$size->get_height()),
			array($size->get_width(),0)
		);
		$this->rotate_points($points,$angle,$pos);
		return $points;
	}
	
	/**
	 * Determines the paint-points for the rotation of the rectangle
	 * 
	 * @param array $points rotates all points by the specified angle around the specified position
	 * @param int $angle the angle to use (in degree)
	 * @param FWS_GD_BoxPosition $pos the point to use for the rotation (null = middle,middle)
	 */
	protected final function rotate_points(&$points,$angle,$pos)
	{
		if(!FWS_Helper::is_integer($angle))
			FWS_Helper::def_error('integer','angle',$angle);
		if($pos !== null && !($pos instanceof FWS_GD_BoxPosition))
			FWS_Helper::def_error('instance','pos','FWS_GD_BoxPosition',$pos);
		
		if($pos === null)
			$pos = new FWS_GD_BoxPosition();
		
		$size = $this->_rect->get_size();
		
		// determine horizontal position
		switch($pos->get_hpos())
		{
			case FWS_GD_BoxPosition::FIRST:
				$ptx = 0;
				break;
			
			case FWS_GD_BoxPosition::LAST:
				$ptx = $size->get_width();
				break;
			
			case FWS_GD_BoxPosition::MIDDLE:
				$ptx = $size->get_width() / 2;
				break;
		}
		
		// determine vertical position
		switch($pos->get_vpos())
		{
			case FWS_GD_BoxPosition::FIRST:
				$pty = 0;
				break;
			
			case FWS_GD_BoxPosition::LAST:
				$pty = $size->get_height();
				break;
			
			case FWS_GD_BoxPosition::MIDDLE:
				$pty = $size->get_height() / 2;
				break;
		}
		
		// cache some vars
		$a = deg2rad($angle);
		$sa = sin($a);
		$ca = cos($a);
		$loc = $this->_rect->get_location();
		$tx = $loc->get_x();
		$ty = $loc->get_y();
		
		// now calculate the points to paint
		// we'll rotate each point on the selected point
		$paint_points = array();
		foreach($points as $p)
		{
			$p[0] -= $ptx;
			$p[1] -= $pty;
			$paint_points[] = $tx + ($p[0] * $ca - $p[1] * $sa) + $ptx;
			$paint_points[] = $ty + ($p[0] * $sa + $p[1] * $ca) + $pty;
		}
		
		$points = $paint_points;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>