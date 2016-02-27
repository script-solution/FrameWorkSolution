<?php
/**
 * Contains the text-class
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
 * Represents a text with attributes which may be drawn by the text-view-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Text extends FWS_Object
{
	/**
	 * The text to draw
	 *
	 * @var string
	 */
	private $_text;
	
	/**
	 * The attributes of the text
	 *
	 * @var FWS_GD_TextAttributes
	 */
	private $_attr;
	
	/**
	 * Constructor
	 *
	 * @param string $text the text to draw
	 * @param FWS_GD_TextAttributes $attr the attributes
	 */
	public function __construct($text,$attr)
	{
		parent::__construct();
		
		if(!($attr instanceof FWS_GD_TextAttributes))
			FWS_Helper::def_error('instance','attr','FWS_GD_TextAttributes',$attr);
		
		$this->set_text($text);
		$this->_text = $text;
		$this->_attr = $attr;
	}
	
	/**
	 * @return FWS_GD_TextAttributes the attributes of the text
	 */
	public function get_attributes()
	{
		return $this->_attr;
	}

	/**
	 * @return string the text to draw
	 */
	public function get_text()
	{
		return $this->_text;
	}

	/**
	 * Sets the text to draw
	 * 
	 * @param string $text the new value
	 */
	public function set_text($text)
	{
		$this->_text = (string)$text;
	}
	
	/**
	 * Determines the center-coordinates of the text based on the given coordinates
	 *
	 * @param FWS_GD_Point $pos the lower-left char-corner
	 * @param int $angle the angle with which the text should be drawn
	 * @return FWS_GD_Point the coordinates of the center
	 */
	public function get_center($pos,$angle = 0)
	{
		$bounds = $this->get_bounds($angle);
		$ex = $bounds[0] + ($bounds[2] - $bounds[0]) / 2;
		$ey = $bounds[1] - ($bounds[1] - $bounds[3]) / 2;
		
		$fx = $bounds[4] - ($bounds[4] - $bounds[6]) / 2;
		$fy = $bounds[5] + ($bounds[7] - $bounds[5]) / 2;
		
		$gx = $fx + ($ex - $fx) / 2;
		$gy = $fy + ($ey - $fy) / 2;
		
		return $pos->derive($gx,$gy);
	}
	
	/**
	 * Returns the bounds-rectangle of the text (not rotated!)
	 *
	 * @param FWS_GD_Point $pos the start-position (upper-left-corner)
	 * @return FWS_GD_Rectangle the bounds-rectangle
	 */
	public function get_rectangle($pos)
	{
		if(!($pos instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','pos','FWS_GD_Point',$pos);
		
		$bounds = $this->get_bounds();
		$from = new FWS_GD_Point($pos->get_x() + $bounds[6],$pos->get_y() + $bounds[7]);
		$size = new FWS_GD_Dimension($bounds[2] - $bounds[6],$bounds[3] - $bounds[7]);
		return new FWS_GD_Rectangle($from,$size);
	}
	
	/**
	 * Determines the bounds of the ttf-text. The result will be:
	 * <code>
	 * 	0  	lower left corner, X position
	 * 	1 	lower left corner, Y position
	 * 	2 	lower right corner, X position
	 * 	3 	lower right corner, Y position
	 * 	4 	upper right corner, X position
	 * 	5 	upper right corner, Y position
	 * 	6 	upper left corner, X position
	 * 	7 	upper left corner, Y position
	 * </code>
	 * 
	 * @param int $angle the angle with which the text should be drawn
	 * @param boolean $note_attr whether the attributes (underline, overline, shadow) should be
	 * 	noticed for calculating the bounds
	 * @return array an array with all coordinates. see imagettfbbox()
	 */
	public function get_bounds($angle = 0,$note_attr = true)
	{
		$bounds = $this->_attr->get_font()->get_bounds($this->_text,$this->_attr,$angle);
		// add padding corresponding to the attributes
		if($note_attr)
			$this->_add_style_padding($bounds,$angle);
		
		return $bounds;
	}
	
	/**
	 * Returns the margin of the text. That means all additional stuff around the text ifself.
	 *
	 * @return array the margin: <code>array(<top>,<right>,<bottom>,<left>)</code>
	 */
	public function get_margin()
	{
		$fullbounds = $this->get_bounds(0,true);
		$bounds = $this->get_bounds(0,false);
		$margin = array(
			abs($fullbounds[7] - $bounds[7]),
			abs($fullbounds[2] - $bounds[2]),
			abs($fullbounds[1] - $bounds[1]),
			abs($fullbounds[0] - $bounds[0]),
		);
		return $margin;
	}
	
	/**
	 * Calculates the ascent of the text (depends on the text-size, border and whether its overlined)
	 *
	 * @param boolean $note_attr whether the attributes should be noticed
	 * @return int the ascent
	 */
	public function get_ascent($note_attr = true)
	{
		$h = $this->get_height(false);
		$a = $h - $this->get_descent(false);
		if($note_attr)
		{
			if($this->_attr->get_overline())
			{
				$size = $this->get_line_size();
				$pad = $this->get_line_pad();
				$a += $size + $pad;
			}
			if($this->_attr->get_border() !== null)
				$a += $this->_attr->get_border_size();
		}
		
		return $a;
	}
	
	/**
	 * Calculates the descent of the text (depends on the border and whether its underlined)
	 *
	 * @param boolean $note_attr whether the attributes should be noticed
	 * @return int the descent
	 */
	public function get_descent($note_attr = true)
	{
		$tsize = $this->get_size(false);
		$dsize = $this->_attr->get_font()->get_size('d',$this->_attr);
		
		$d = (int)$tsize->get_height() - (int)$dsize->get_height();
		if($note_attr)
		{
			if($this->_attr->get_border() !== null)
				$d += $this->_attr->get_border_size();
			if($this->_attr->get_underline())
			{
				$size = $this->get_line_size();
				$pad = $this->get_line_pad();
				$d += $size + $pad;
			}
		}
		
		return $d;
	}
	
	/**
	 * @return int the line-size that should be used for underlines or overlines depending on
	 * 	the font-size
	 */
	public function get_line_size()
	{
		return $this->_attr->get_font()->get_line_size($this->_attr);
	}
	
	/**
	 * @return int the padding that should be used for underlines or overlines depending on
	 * 	the font-size
	 */
	public function get_line_pad()
	{
		return $this->_attr->get_font()->get_line_pad($this->_attr);
	}
	
	/**
	 * Determines the width of the text
	 *
	 * @param boolean $note_attr whether the attributes (underline, overline, shadow) should be
	 * 	noticed for calculating the bounds
	 * @return int the width
	 */
	public function get_width($note_attr = true)
	{
		return (int)$this->get_size($note_attr)->get_width();
	}
	
	/**
	 * Determines the height of the text
	 *
	 * @param boolean $note_attr whether the attributes (underline, overline, shadow) should be
	 * 	noticed for calculating the bounds
	 * @return int the height
	 */
	public function get_height($note_attr = true)
	{
		return (int)$this->get_size($note_attr)->get_height();
	}
	
	/**
	 * Determines the size of the text
	 *
	 * @param boolean $note_attr whether the attributes (underline, overline, shadow) should be
	 * 	noticed for calculating the bounds
	 * @return FWS_GD_Dimension the size of the text
	 */
	public function get_size($note_attr = true)
	{
		$size = $this->_attr->get_font()->get_size($this->_text,$this->_attr);
		if($note_attr)
		{
			$margin = $this->get_margin();
			$size->increase($margin[1] + $margin[3],$margin[0] + $margin[2]);
		}
		
		return $size;
	}
	
	/**
	 * Determines the size of the text in the rotated state.
	 * 
	 * @return FWS_GD_Dimension the size of the text
	 */
	public function get_rotated_size()
	{
		$ep = $this->get_extreme_points();
		return new FWS_GD_Dimension($ep[1] - $ep[0],$ep[3] - $ep[2]);
	}
	
	/**
	 * Returns the "extreme"-points of the text-bounds. That are the minimum / maximum
	 * x- and y-values.
	 *
	 * @param int $angle the angle with which the text should be drawn
	 * @return array the extreme-points:
	 * 	<code>array(<minX>,<maxX>,<minY>,<maxY>)</code>
	 */
	public function get_extreme_points($angle = 0)
	{
		$bounds = $this->get_bounds($angle);
		$min_x = min($bounds[0],$bounds[2],$bounds[4],$bounds[6]);
		$max_x = max($bounds[0],$bounds[2],$bounds[4],$bounds[6]);
		
		$min_y = min($bounds[1],$bounds[3],$bounds[5],$bounds[7]);
		$max_y = max($bounds[1],$bounds[3],$bounds[5],$bounds[7]);
		return array($min_x,$max_x,$min_y,$max_y);
	}
	
	/**
	 * Adds padding depending on the styles
	 *
	 * @param array $bounds the bounds
	 * @param int $angle the angle
	 */
	private function _add_style_padding(&$bounds,$angle)
	{
		$t = 0;
		$r = 1;
		$b = 0;
		$l = 1;
		$size = $this->get_line_size();
		$pad = $this->get_line_pad();
		if($this->_attr->get_overline())
			$t += $size + $pad;
		if($this->_attr->get_underline())
			$b += $size + $pad - 1;
		if($this->_attr->get_shadow())
		{
			$r += $this->_attr->get_border() !== null ? 1 : 3;
			$b += 2;
		}
		if($this->_attr->get_border() !== null)
		{
			$border_size = $this->_attr->get_border_size();
			$t += $border_size + $this->_attr->get_overline() ? 1 : 0;
			$r += $border_size + $this->_attr->get_shadow() ? 1 : 0;
			$b += $border_size;
			$l += $border_size - 1;
		}
		
		FWS_GD_Utils::add_padding_custom($bounds,$t,$r,$b,$l,$angle);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>