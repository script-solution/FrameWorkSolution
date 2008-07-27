<?php
/**
 * Contains the point-class
 * 
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class contains some convenience methods for drawing and methods to create instances
 * of the view-classes.
 * <br>
 * By default you don't need to instantiate this class if you use {@link PLIB_GD_Image} as your
 * image (which is recommended). PLIB_GD_Image creates an object of this class automatically and
 * gives to access to it.
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Graphics extends PLIB_Object
{
	/**
	 * The image
	 *
	 * @var PLIB_GD_Image
	 */
	private $_img;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Image $img the image
	 */
	public function __construct($img)
	{
		parent::__construct();
		
		if(!($img instanceof PLIB_GD_Image))
			PLIB_Helper::def_error('instance','img','PLIB_GD_Image',$img);
		
		$this->_img = $img;
	}
	
	/**
	 * Builds a view for the given text and returns it
	 *
	 * @param PLIB_GD_Text $text the text
	 * @return PLIB_GD_View_Text the text-view
	 */
	public function get_text_view($text)
	{
		return new PLIB_GD_View_Text($this->_img,$text);
	}
	
	/**
	 * Builds a view for the given line and returns it
	 *
	 * @param PLIB_GD_Line $line the line
	 * @return PLIB_GD_View_Line the line-view
	 */
	public function get_line_view($line)
	{
		return new PLIB_GD_View_Line($this->_img,$line);
	}
	
	/**
	 * Builds a view for the given ellipse (or circle) and returns it
	 *
	 * @param PLIB_GD_Ellipse $ellipse the ellipse
	 * @return PLIB_GD_View_Ellipse the ellipse-view
	 */
	public function get_ellipse_view($ellipse)
	{
		return new PLIB_GD_View_Ellipse($this->_img,$ellipse);
	}
	
	/**
	 * Builds a view for the given rectangle and returns it
	 *
	 * @param PLIB_GD_Rectangle $rect the rectangle
	 * @return PLIB_GD_View_Rectangle the rectangle-view
	 */
	public function get_rect_view($rect)
	{
		return new PLIB_GD_View_Rectangle($this->_img,$rect);
	}
	
	/**
	 * Builds a polygon-view for the given points and returns it
	 *
	 * @param array $points an array with points: <code>array(<x1>,<y1>,<x2>,<y2>,...)</code>
	 * @return PLIB_GD_View_Polygon the polygon-view
	 */
	public function get_poly_view($points)
	{
		return new PLIB_GD_View_Polygon($this->_img,$points);
	}
	
	/**
	 * Draws a point at the given position with given radius.
	 *
	 * @param PLIB_GD_Point $pos the position
	 * @param int $radius the radius of the point
	 * @param PLIB_GD_Color $color the color
	 * @return int the result of imagefilledellipse()
	 */
	public function draw_point($pos,$radius,$color)
	{
		$ellipse = new PLIB_GD_Ellipse($pos,new PLIB_GD_Dimension($radius * 2,$radius * 2));
		return $this->get_ellipse_view($ellipse)->fill($color);
	}
	
	/**
	 * Draws a line from <var>($x1,$y1)</var> to <var>($x2,$y2)</var> in the given color
	 *
	 * @param int $x1 the source-x-coordinate
	 * @param int $y1 the source-y-coordinate
	 * @param int $x2 the target-x-coordinate
	 * @param int $y2 the target-y-coordinate
	 * @param PLIB_GD_Color $color the color
	 * @return boolean the result of imageline()
	 */
	public function draw_line_int($x1,$y1,$x2,$y2,$color)
	{
		return $this->get_line_view(new PLIB_GD_Line($x1,$y1,$x2,$y2))->draw($color);
	}
	
	/**
	 * Draws a line from the given source-point to the target-point in the given color
	 *
	 * @param PLIB_GD_Point $from the source-point
	 * @param PLIB_GD_Point $to the target-point
	 * @param PLIB_GD_Color $color the color
	 * @return boolean the result of imageline()
	 */
	public function draw_line($from,$to,$color)
	{
		return $this->get_line_view(new PLIB_GD_Line($from,$to))->draw($color);
	}
	
	/**
	 * Draws a rectangle with the two given points
	 *
	 * @param PLIB_GD_Point $from the first point
	 * @param PLIB_GD_Point $to the second point
	 * @param PLIB_GD_Color $color the color
	 * @return boolean the result of imagerectangle()
	 */
	public function draw_custom_rect($from,$to,$color)
	{
		if(!($from instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','from','PLIB_GD_Point',$from);
		if(!($to instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','to','PLIB_GD_Point',$to);
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		$img = $this->_img->get_image();
		return imagerectangle(
			$img,
			$from->get_x(),$from->get_y(),
			$to->get_x(),$to->get_y(),
			$color->get_color($img)
		);
	}
	
	/**
	 * Filles a rectangle with the two given points
	 *
	 * @param PLIB_GD_Point $from the first point
	 * @param PLIB_GD_Point $to the second point
	 * @param PLIB_GD_Color $color the color
	 * @return boolean the result of imagefilledrectangle()
	 */
	public function fill_custom_rect($from,$to,$color)
	{
		if(!($from instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','from','PLIB_GD_Point',$from);
		if(!($to instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','to','PLIB_GD_Point',$to);
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		$img = $this->_img->get_image();
		return imagefilledrectangle(
			$img,
			$from->get_x(),$from->get_y(),
			$to->get_x(),$to->get_y(),
			$color->get_color($img)
		);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>