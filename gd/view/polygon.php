<?php
/**
 * Contains the polygon-view-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The view to draw a polygon
 *
 * @package			PHPLib
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_View_Polygon extends PLIB_GD_View
{
	/**
	 * The points of the polygon
	 *
	 * @var array
	 */
	protected $_points;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Image $img the image
	 * @param array $points an array with points: <code>array(<x1>,<y1>,<x2>,<y2>,...)</code>
	 */
	public function __construct($img,$points)
	{
		parent::__construct($img);
		
		if(!is_array($points) || count($points) % 2 != 0)
			PLIB_Helper::def_error('$points is invalid. Expected is an array with a even'
				.' number of elements!');
		
		$this->_points = $points;
	}
	
	/**
	 * Draws this polygon with the given color. You can specify a position at which it should be
	 * drawn. That means that the x- and y-position will be added to the points of the polygon.
	 * By default <var>(0,0)</var> will be used.
	 *
	 * @param PLIB_GD_Color $color the color to use
	 * @param PLIB_GD_Point $pos the position (<var>(0,0)</var> by default)
	 * @return int the result of imagepolygon()
	 */
	public final function draw($color,$pos = null)
	{
		return $this->_paint($color,$pos,'');
	}
	
	/**
	 * Fills this polygon with the given color. You can specify a position at which it should be
	 * drawn. That means that the x- and y-position will be added to the points of the polygon.
	 * By default <var>(0,0)</var> will be used.
	 *
	 * @param PLIB_GD_Color $color the color to use
	 * @param PLIB_GD_Point $pos the position (<var>(0,0)</var> by default)
	 * @return int the result of imagefilledpolygon()
	 */
	public final function fill($color,$pos = null)
	{
		return $this->_paint($color,$pos,'filled');
	}
	
	/**
	 * Does the actual painting
	 *
	 * @param PLIB_GD_Color $color the color to use
	 * @param PLIB_GD_Point $pos the position
	 * @param string $func the function. 'filled' or ''
	 * @return int the result of image*polygon()
	 */
	private function _paint($color,$pos,$func)
	{
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		if($pos !== null && !($pos instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','pos','PLIB_GD_Point',$pos);
		
		// default position?
		if($pos === null)
			$pos = new PLIB_GD_Point(0,0);
		
		// build points
		$points = array();
		for($i = 0,$l = count($this->_points);$i < $l;$i += 2)
		{
			$points[$i] = $pos->get_x() + $this->_points[$i];
			$points[$i + 1] = $pos->get_y() + $this->_points[$i + 1];
		}
		
		// draw polygon
		$img = $this->_img->get_image();
		$funcname = 'image'.$func.'polygon';
		return $funcname($img,$points,count($points) / 2,$color->get_color($img));
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>