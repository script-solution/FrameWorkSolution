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
 * This class contains some convenience methods for drawing and methods to create instances
 * of the view-classes.
 * <br>
 * By default you don't need to instantiate this class if you use {@link FWS_GD_Image} as your
 * image (which is recommended). FWS_GD_Image creates an object of this class automatically and
 * gives to access to it.
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Graphics extends FWS_Object
{
	/**
	 * The image
	 *
	 * @var FWS_GD_Image
	 */
	private $_img;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $img the image
	 */
	public function __construct($img)
	{
		parent::__construct();
		
		if(!($img instanceof FWS_GD_Image))
			FWS_Helper::def_error('instance','img','FWS_GD_Image',$img);
		
		$this->_img = $img;
	}
	
	/**
	 * Builds a view for the given text and returns it
	 *
	 * @param FWS_GD_Text $text the text
	 * @return FWS_GD_View_Text the text-view
	 */
	public function get_text_view($text)
	{
		return new FWS_GD_View_Text($this->_img,$text);
	}
	
	/**
	 * Builds a view for the given line and returns it
	 *
	 * @param FWS_GD_Line $line the line
	 * @return FWS_GD_View_Line the line-view
	 */
	public function get_line_view($line)
	{
		return new FWS_GD_View_Line($this->_img,$line);
	}
	
	/**
	 * Builds a view for the given ellipse (or circle) and returns it
	 *
	 * @param FWS_GD_Ellipse $ellipse the ellipse
	 * @return FWS_GD_View_Ellipse the ellipse-view
	 */
	public function get_ellipse_view($ellipse)
	{
		return new FWS_GD_View_Ellipse($this->_img,$ellipse);
	}
	
	/**
	 * Builds a view for the given rectangle and returns it
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle
	 * @return FWS_GD_View_Rectangle the rectangle-view
	 */
	public function get_rect_view($rect)
	{
		return new FWS_GD_View_Rectangle($this->_img,$rect);
	}
	
	/**
	 * Builds a polygon-view for the given points and returns it
	 *
	 * @param array $points an array with points: <code>array(<x1>,<y1>,<x2>,<y2>,...)</code>
	 * @return FWS_GD_View_Polygon the polygon-view
	 */
	public function get_poly_view($points)
	{
		return new FWS_GD_View_Polygon($this->_img,$points);
	}
	
	/**
	 * Draws a point at the given position with given radius.
	 *
	 * @param FWS_GD_Point $pos the position
	 * @param int $radius the radius of the point
	 * @param FWS_GD_Color $color the color
	 * @return bool the result of imagefilledellipse()
	 */
	public function draw_point($pos,$radius,$color)
	{
		$ellipse = new FWS_GD_Ellipse($pos,new FWS_GD_Dimension($radius * 2,$radius * 2));
		return $this->get_ellipse_view($ellipse)->fill($color);
	}
	
	/**
	 * Draws a line from <var>($x1,$y1)</var> to <var>($x2,$y2)</var> in the given color
	 *
	 * @param int|float $x1 the source-x-coordinate
	 * @param int|float $y1 the source-y-coordinate
	 * @param int|float $x2 the target-x-coordinate
	 * @param int|float $y2 the target-y-coordinate
	 * @param FWS_GD_Color $color the color
	 * @return boolean the result of imageline()
	 */
	public function draw_line_int($x1,$y1,$x2,$y2,$color)
	{
		return $this->get_line_view(new FWS_GD_Line($x1,$y1,$x2,$y2))->draw($color);
	}
	
	/**
	 * Draws a line from the given source-point to the target-point in the given color
	 *
	 * @param FWS_GD_Point $from the source-point
	 * @param FWS_GD_Point $to the target-point
	 * @param FWS_GD_Color $color the color
	 * @return boolean the result of imageline()
	 */
	public function draw_line($from,$to,$color)
	{
		return $this->get_line_view(new FWS_GD_Line($from,$to))->draw($color);
	}
	
	/**
	 * Draws a rectangle with the two given points
	 *
	 * @param FWS_GD_Point $from the first point
	 * @param FWS_GD_Point $to the second point
	 * @param FWS_GD_Color $color the color
	 * @return boolean the result of imagerectangle()
	 */
	public function draw_custom_rect($from,$to,$color)
	{
		if(!($from instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','from','FWS_GD_Point',$from);
		if(!($to instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','to','FWS_GD_Point',$to);
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
		$img = $this->_img->get_image();
		return imagerectangle(
			$img,
			(int)$from->get_x(),(int)$from->get_y(),
			(int)$to->get_x(),(int)$to->get_y(),
			$color->get_color($img)
		);
	}
	
	/**
	 * Filles a rectangle with the two given points
	 *
	 * @param FWS_GD_Point $from the first point
	 * @param FWS_GD_Point $to the second point
	 * @param FWS_GD_Color $color the color
	 * @return boolean the result of imagefilledrectangle()
	 */
	public function fill_custom_rect($from,$to,$color)
	{
		if(!($from instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','from','FWS_GD_Point',$from);
		if(!($to instanceof FWS_GD_Point))
			FWS_Helper::def_error('instance','to','FWS_GD_Point',$to);
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
		$img = $this->_img->get_image();
		return imagefilledrectangle(
			$img,
			(int)$from->get_x(),(int)$from->get_y(),
			(int)$to->get_x(),(int)$to->get_y(),
			$color->get_color($img)
		);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>