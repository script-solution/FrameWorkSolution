<?php
/**
 * Contains the text-view-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd.view
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
 * The view to draw some text with truetype-fonts
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_View_Text extends FWS_GD_View
{
	/**
	 * The text to draw
	 *
	 * @var FWS_GD_Text
	 */
	protected $_text;
	
	/**
	 * The angle of the text in degree
	 *
	 * @var int
	 */
	protected $_angle;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $img the image
	 * @param FWS_GD_Text $text the text to draw
	 */
	public function __construct($img,$text)
	{
		parent::__construct($img);
		
		if(!($text instanceof FWS_GD_Text))
			FWS_Helper::def_error('instance','text','FWS_GD_Text',$text);
		
		$this->_text = $text;
	}
	
	/**
	 * Draws the text simply at the given position
	 *
	 * @param FWS_GD_Point $pos the upper-left corner of the text
	 * @return bool the result of imagettftext()
	 */
	public final function draw_at_pos($pos)
	{
		$bounds = $this->_text->get_bounds(0);
		list(,,,$l) = $this->_text->get_margin();
		$tpos = $pos->derive(-$l - $bounds[0] + 1,$this->_text->get_ascent());
		
		$this->get_graphics()->draw_point($tpos,3,FWS_GD_Color::$ORANGE);
		
		return $this->draw_text($tpos,0);
	}
	
	/**
	 * Draws the text at the given line. You can specify a padding to the line and the position
	 * of the text: {@link FWS_GD_BoxPosition::FIRST}, {@link FWS_GD_BoxPosition::MIDDLE},
	 * {@link FWS_GD_BoxPosition::LAST}.
	 *
	 * @param FWS_GD_Line $line the line
	 * @param int $padding the padding to the line
	 * @param int $pos the position ({@link FWS_GD_BoxPosition::FIRST} by default)
	 * @return bool the result of imagettftext()
	 */
	public final function draw_at_line($line,$padding = 0,$pos = FWS_GD_BoxPosition::FIRST)
	{
		if(!($line instanceof FWS_GD_Line))
			FWS_Helper::def_error('instance','line','FWS_GD_Line',$line);
		if(!FWS_Helper::is_integer($padding) || $padding < 0)
			FWS_Helper::def_error('intge0','padding',$padding);
		if(!in_array($pos,array(FWS_GD_BoxPosition::FIRST,FWS_GD_BoxPosition::MIDDLE,
				FWS_GD_BoxPosition::LAST)))
		{
			FWS_Helper::def_error(
				'inarray','pos',
				array(FWS_GD_BoxPosition::FIRST,FWS_GD_BoxPosition::MIDDLE,FWS_GD_BoxPosition::LAST),
				$pos
			);
		}
		
		list($x1,$y1) = $line->get_from()->get();
		list($x2,$y2) = $line->get_to()->get();
		
		// determine the position in the rectangle
		if($pos != FWS_GD_BoxPosition::MIDDLE)
		{
			if($y1 > $y2)
			{
				if($x1 > $x2)
				{
					$first = FWS_GD_BoxPosition::$BOTTOM_RIGHT;
					$last = FWS_GD_BoxPosition::$TOP_LEFT;
				}
				else
				{
					$first = FWS_GD_BoxPosition::$BOTTOM_LEFT;
					$last = FWS_GD_BoxPosition::$TOP_RIGHT;
				}
			}
			else
			{
				if($x1 > $x2)
				{
					$first = FWS_GD_BoxPosition::$TOP_RIGHT;
					$last = FWS_GD_BoxPosition::$BOTTOM_LEFT;
				}
				else
				{
					$first = FWS_GD_BoxPosition::$TOP_LEFT;
					$last = FWS_GD_BoxPosition::$BOTTOM_RIGHT;
				}
			}
		}
		
		// set position depending on $pos
		if($pos == FWS_GD_BoxPosition::FIRST)
			$bp = $first;
		else if($pos == FWS_GD_BoxPosition::MIDDLE)
			$bp = FWS_GD_BoxPosition::$CENTER_CENTER;
		else
			$bp = $last;
		
		// build rectangle
		$sx = $x1 > $x2 ? $x2 : $x1;
		$sy = $y1 > $y2 ? $y2 : $y1;
		$start = new FWS_GD_Point($sx,$sy);
		$size = new FWS_GD_Dimension(abs($x2 - $x1),abs($y2 - $y1));
		$linerect = new FWS_GD_Rectangle($start,$size);
		list($w,$h) = $size->get();
		list($tw,$th) = $this->_text->get_size()->get();
		
		if($pos != FWS_GD_BoxPosition::MIDDLE)
		{
			// add padding corresponding to the proportion of the rectangle
			$vertpadd = $w > $h ? ($h == 0 ? 0 : $padding / ($w / $h)) : $padding;
			$vertpadd += $th;
			$horipadd = $w > $h ? $padding : ($w == 0 ? 0 : $padding / ($h / $w));
			$horipadd += $tw;
			$linerect->translate(-$horipadd,-$vertpadd);
			$linerect->grow(
				$horipadd * 2,
				$vertpadd * 2
			);
		}
		// this doesn't work for the middle-position
		else
		{
			$xdiff = ($x2 - $x1);
			$slope = $xdiff == 0 ? 0 : ($y2 - $y1) / $xdiff;
			// slightly negative
			if($slope <= 0 && $slope > -1)
				$linerect->translate(0,-($padding + $th));
			// negative
			else if($slope < -1)
				$linerect->translate($padding + $tw,0);
			// slightly positive
			else if($slope > 0 && $slope < 1)
				$linerect->translate(0,$padding + $th);
			// positive
			else
				$linerect->translate(-($padding + $tw),0);
		}
		
		return $this->draw_in_rect($linerect,null,$bp);
	}
	
	/**
	 * Draws the text in the center of the given circle-part
	 *
	 * @param FWS_GD_Circle $circle the circle
	 * @param int $start the start-angle (in degree)
	 * @param int $end the end-angle (in degree)
	 * @return bool the result of imagettftext()
	 */
	public final function draw_in_circle_part($circle,$start,$end)
	{
		if(!($circle instanceof FWS_GD_Circle))
			FWS_Helper::def_error('instance','circle','FWS_GD_Circle',$circle);
		if(!FWS_Helper::is_integer($start))
			FWS_Helper::def_error('integer','start',$start);
		if(!FWS_Helper::is_integer($end))
			FWS_Helper::def_error('integer','end',$end);
		
		$radius = $circle->get_radius();
		$rstart = deg2rad($start);
		$rend = deg2rad($end);
		$p1 = $circle->get_center();
		list($x,$y) = $p1->get();
		$p2 = new FWS_GD_Point($x + cos($rstart) * $radius,$y + sin($rstart) * $radius);
		$p3 = new FWS_GD_Point($x + cos($rend) * $radius,$y + sin($rend) * $radius);
		return $this->draw_in_triangle($p1,$p2,$p3);
	}
	
	/**
	 * Draws the text in the center of the given triangle
	 *
	 * @param FWS_GD_Point $p1 the first point
	 * @param FWS_GD_Point $p2 the second point
	 * @param FWS_GD_Point $p3 the third point
	 * @return bool the result of imagettftext()
	 */
	public final function draw_in_triangle($p1,$p2,$p3)
	{
		if(!($p1 instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','p1','FWS_GD_Point',$p1);
		if(!($p2 instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','p2','FWS_GD_Point',$p2);
		if(!($p3 instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','p3','FWS_GD_Point',$p3);
		
		$tc = array(
			($p1->get_x() + $p2->get_x() + $p3->get_x()) / 3,
			($p1->get_y() + $p2->get_y() + $p3->get_y()) / 3,
		);
		$rect = new FWS_GD_Rectangle($tc[0] - 3,$tc[1] - 3,6,6);
		return $this->draw_in_rect($rect);
	}
	
	/**
	 * Draws the text in the given rectangle at the specified position vertically.
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 * @param int $pad the padding to the rectangle
	 * @param FWS_GD_BoxPosition $pos the position in the rectangle
	 * @return bool the result of imagettftext()
	 */
	public final function draw_in_rect_vertically($rect,$pad = 0,$pos = null)
	{
		if(!($rect instanceof FWS_GD_Rectangle))
			FWS_Helper::def_error('instance','rect','FWS_GD_Rectangle',$rect);
		if(!FWS_Helper::is_integer($pad) || $pad < 0)
			FWS_Helper::def_error('intge0','pad',$pad);
		if($pos !== null && !($pos instanceof FWS_GD_BoxPosition))
			FWS_Helper::def_error('instance','pos','FWS_GD_BoxPosition',$pos);
		
		if($pos === null)
			$pos = new FWS_GD_BoxPosition();
		
		list($w,$h) = $this->_text->get_size()->get();
		$a = 90;
		$tpos = $this->get_string_position($rect,new FWS_GD_Padding($pad),$pos,$a);
		
		switch($pos->get_hpos())
		{
			case FWS_GD_BoxPosition::FIRST:
				switch($pos->get_vpos())
				{
					case FWS_GD_BoxPosition::FIRST:
						$tpos->translate($pad / 2,$w);
						break;
					case FWS_GD_BoxPosition::MIDDLE:
						$tpos->translate($pad / 2 + $h / 2,$w / 2);
						break;
					case FWS_GD_BoxPosition::LAST:
						$tpos->translate($pad / 2 + $h,-$pad / 2);
						break;
				}
				break;
			case FWS_GD_BoxPosition::MIDDLE:
				switch($pos->get_vpos())
				{
					case FWS_GD_BoxPosition::FIRST:
						$tpos->translate(-$h / 2,$w / 2);
						break;
					case FWS_GD_BoxPosition::MIDDLE:
						$tpos->translate(0,-$pad / 2);
						break;
					case FWS_GD_BoxPosition::LAST:
						$tpos->translate($h / 2,-$w / 2 - $pad / 2);
						break;
				}
				break;
			case FWS_GD_BoxPosition::LAST:
				switch($pos->get_vpos())
				{
					case FWS_GD_BoxPosition::FIRST:
						$tpos->translate(-$h,0);
						break;
					case FWS_GD_BoxPosition::MIDDLE:
						$tpos->translate(-$h / 2,-$w / 2);
						break;
					case FWS_GD_BoxPosition::LAST:
						$tpos->translate(0,-$w - $pad / 2);
						break;
				}
				break;
		}
		
		return $this->draw_text($tpos,$a);
	}
	
	/**
	 * Draws the text in the given rectangle at the specified position.
	 * The text will be rotated on the given position in the rectangle!
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 * @param FWS_GD_Padding $padding the padding to the rectangle
	 * @param FWS_GD_BoxPosition $pos the position in the rectangle
	 * @param int $angle the angle of the text
	 * @return bool the result of imagettftext()
	 */
	public final function draw_in_rect($rect,$padding = null,$pos = null,$angle = 0)
	{
		if(!($rect instanceof FWS_GD_Rectangle))
			FWS_Helper::def_error('instance','rect','FWS_GD_Rectangle',$rect);
		if($padding !== null && !($padding instanceof FWS_GD_Padding))
			FWS_Helper::def_error('instance','padding','FWS_GD_Padding',$padding);
		if($pos !== null && !($pos instanceof FWS_GD_BoxPosition))
			FWS_Helper::def_error('instance','pos','FWS_GD_BoxPosition',$pos);
		
		if($padding === null)
			$padding = new FWS_GD_Padding();
		if($pos === null)
			$pos = new FWS_GD_BoxPosition();

		// draw text
		$tpos = $this->get_string_position($rect,$padding,$pos,$angle);
		return $this->draw_text($tpos,$angle);
	}
	
	/**
	 * Does the actual painting
	 *
	 * @param FWS_GD_Point $tpos the position of the text
	 * @param int $angle the angle
	 * @return bool the result of imagettftext()
	 */
	protected final function draw_text($tpos,$angle)
	{
		$attr = $this->_text->get_attributes();
		$fg = $attr->get_foreground();
		$img = $this->_img->get_image();
		$linesize = $this->_text->get_line_size();
		$linepad = $this->_text->get_line_pad();
		if($attr->get_background() !== null || $attr->get_border() !== null)
			$bounds = $this->_text->get_bounds($angle);
		
		// draw background
		if($attr->get_background() !== null)
			$this->get_graphics()->get_poly_view($bounds)->fill($attr->get_background(),$tpos);
		
		// draw border?
		if($attr->get_border() !== null)
		{
			$size = $attr->get_border_size();
			if($size > 1)
			{
				FWS_GD_Utils::add_padding_custom($bounds,-$size / 2,-$size / 2,-$size / 2,-$size / 2,$angle);
				imagesetthickness($img,$size);
			}
			
			$this->get_graphics()->get_poly_view($bounds)->draw($attr->get_border(),$tpos);
			
			if($size > 1)
				imagesetthickness($img,1);
		}
		
		// draw shadow?
		if($attr->get_shadow())
		{
			$fgdark = clone $fg;
			$fgdark->darker(50);
			$attr->set_foreground($fgdark);
			$attr->get_font()->draw($img,$this->_text->get_text(),$attr,$tpos->derive(2,2),$angle);
			$attr->set_foreground($fg);
		}
		
		// draw the text
		$res = $attr->get_font()->draw($img,$this->_text->get_text(),$attr,$tpos,$angle);
		
		// determine margin
		if($attr->get_underline() || $attr->get_strike() || $attr->get_overline())
			$margin = $this->_text->get_margin();
		
		// underline the text?
		if($attr->get_underline())
		{
			$ulbounds = $this->_text->get_bounds($angle,false);
			$h = $this->_text->get_height(false);
			FWS_GD_Utils::add_padding_custom(
				$ulbounds,0,$margin[1] - 1,
				$margin[2] - $linesize / 2,0,
				$angle
			);
			$this->_draw_line($tpos,$ulbounds,$linesize,$fg);
		}
		
		// strike the text?
		if($attr->get_strike())
		{
			$slbounds = $this->_text->get_bounds($angle,false);
			$h = $this->_text->get_height(false);
			FWS_GD_Utils::add_padding_custom(
				$slbounds,0,$margin[1],-($h / 2),0,$angle
			);
			$this->_draw_line($tpos,$slbounds,$linesize,$fg);
		}
		
		// overline the text?
		if($attr->get_overline())
		{
			$olbounds = $this->_text->get_bounds($angle,false);
			$h = $this->_text->get_height(false);
			FWS_GD_Utils::add_padding_custom(
				$olbounds,0,$margin[1] - 1,
				-$h - $linepad - $linesize / 2 + ($attr->get_border() !== null ? 0 : 1),
				0,$angle
			);
			$this->_draw_line($tpos,$olbounds,$linesize,$fg);
		}
		
		return $res;
	}
	
	/**
	 * Draws a line (underline, overline, strike) over the text
	 *
	 * @param FWS_GD_Point $pos the position
	 * @param array $bounds the bounds-array for the line
	 * @param int $size the size of the line
	 * @param FWS_GD_Color $fg the color
	 */
	private function _draw_line($pos,$bounds,$size,$fg)
	{
		imagesetthickness($this->_img->get_image(),$size);
		$lview = $this->get_graphics()->get_line_view(
			new FWS_GD_Line(
				$pos->get_x() + $bounds[0],$pos->get_y() + $bounds[1],
				$pos->get_x() + $bounds[2],$pos->get_y() + $bounds[3]
			)
		);
		$lview->draw($fg);
		imagesetthickness($this->_img->get_image(),1);
	}
	
	/**
	 * Determines the position of the text (for imagettftext())
	 * 
	 * @param FWS_GD_Rectangle $rect the rectangle
	 * @param FWS_GD_Padding $padding the padding to the rectangle
	 * @param FWS_GD_BoxPosition $pos the position in the rectangle
	 * @param int $angle the angle
	 * @return FWS_GD_Point the position
	 */
	protected final function get_string_position($rect,$padding,$pos,$angle)
	{
		// cache some vars
		list($x,$y) = $rect->get_location()->get();
		list($w,$h) = $rect->get_size()->get();
		
		// add padding to the rectangle
		$x += $padding->get_left();
		$y += $padding->get_top();
		$w -= $padding->get_left() + $padding->get_right();
		$h -= $padding->get_top() + $padding->get_bottom();
		
		list($tw,$th) = $this->_text->get_size()->get();
		$ascent = $this->_text->get_ascent();
		$descent = $this->_text->get_descent();
		$a = deg2rad($angle);
		$cosa = cos($a);
		$cospi2a = cos(M_PI_2 - $a);
		
		// determine horizontal position
		switch($pos->get_hpos())
		{
			case FWS_GD_BoxPosition::MIDDLE:
				// move rotation point to horizontal center
				$x -= $cosa * ($tw / 2);
				$y += $cospi2a * ($tw / 2);
				// move text to horizontal center
				$x += $w / 2;
				break;
			case FWS_GD_BoxPosition::LAST:
				// move rotation point to horizontal right
				$x -= $cosa * $tw;
				$y += $cospi2a * $tw;
				// move text to horizontal right
				$x += $w;
				break;
		}
		
		// determine vertical position
		switch($pos->get_vpos())
		{
			case FWS_GD_BoxPosition::FIRST:
				// move rotation point to vertical top
				$x -= (-$th + $descent) * $cospi2a;
				$y -= (-$th + $descent) * $cosa;
				break;
			case FWS_GD_BoxPosition::MIDDLE:
				// move rotation point to vertical center (of the text)
				$x += ($ascent / 2  - $descent / 2) * $cospi2a;
				$y += ($ascent / 2  - $descent / 2) * $cosa;
				// move text to the vertical center
				$y += $h / 2;
				break;
			case FWS_GD_BoxPosition::LAST:
				// move rotation point to vertical bottom (of the text)
				$x -= ($descent) * $cospi2a;
				$y -= ($descent) * $cosa;
				// move text to the vertical bottom
				$y += $h;
				break;
		}
		
		// By default GD rotates a text slightly left and bottom of the bottom-left.
		// We want to rotate it directly at the bottom-left. Therefore we adjust the position
		// using the padding ($bounds[0] and $bounds[1]).
		//$bounds = $this->_text->get_bounds($angle,false);
		//$dist = sqrt($bounds[1] * $bounds[1] + $bounds[0] * $bounds[0]) - $this->_text->get_descent(false);
		//$x -= $cosa * $dist;
		//$y += $cospi2a * $dist;
		// FIXME this doesn't work for small fonts, right? :/
		
		return new FWS_GD_Point($x,$y);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>