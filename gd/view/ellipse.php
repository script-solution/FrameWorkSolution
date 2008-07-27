<?php
/**
 * Contains the ellipse-view-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The view for a ellipse which allows the painting of it
 *
 * @package			PHPLib
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_View_Ellipse extends PLIB_GD_View
{
	/**
	 * The ellipse
	 *
	 * @var PLIB_GD_Ellipse
	 */
	protected $_ellipse;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Image $img the image
	 * @param PLIB_GD_Ellipse $ellipse the ellipse
	 */
	public function __construct($img,$ellipse)
	{
		parent::__construct($img);
		if(!($ellipse instanceof PLIB_GD_Ellipse))
			PLIB_Helper::def_error('instance','ellipse','PLIB_GD_Ellipse',$ellipse);
		
		$this->_ellipse = $ellipse;
	}
	
	/**
	 * Draws the ellipse with the given color
	 *
	 * @param PLIB_GD_Color $color the color
	 * @return the result of imageellipse()
	 */
	public final function draw($color)
	{
		return $this->_paint($color,'');
	}
	
	/**
	 * Draws the given part of the ellipse with given color
	 *
	 * @param PLIB_GD_Color $color the color
	 * @param int $start the start-degree
	 * @param int $end the end-degree
	 * @return the result of imagearc()
	 */
	public final function draw_part($color,$start,$end)
	{
		$this->_paint_part($color,$start,$end,'');
	}
	
	/**
	 * Fills the ellipse with the given color
	 *
	 * @param PLIB_GD_Color $color the color
	 * @return the result of imagefilledellipse()
	 */
	public final function fill($color)
	{
		return $this->_paint($color,'filled');
	}
	
	/**
	 * Fills the given part of the ellipse with given color
	 *
	 * @param PLIB_GD_Color $color the color
	 * @param int $start the start-degree
	 * @param int $end the end-degree
	 * @param int $type the type: IMG_ARC_PIE, IMG_ARC_CHORD, IMG_ARC_NOFILL, IMG_ARC_EDGED
	 * @return the result of imagefilledarc()
	 */
	public final function fill_part($color,$start,$end,$type = IMG_ARC_PIE)
	{
		$this->_paint_part($color,$start,$end,'filled',$type);
	}
	
	/**
	 * Fills the ellipse with a color-fade of the given color and the given step-width.
	 *
	 * @param array $colors an array with all colors that should be used
	 * @param int $step the step-size
	 */
	public final function fill_colorfade($colors,$step = 1)
	{
		$cf = new PLIB_GD_ColorFade(360,360 / $step,$colors);
		$cfcolors = $cf->get_colors();
		
		$angle = 0;
		list($x,$y) = $this->_ellipse->get_center()->get();
		list($w,$h) = $this->_ellipse->get_size()->get();
		$img = $this->get_image_res();
		foreach($cfcolors as $color)
		{
			imagefilledarc(
				$img,
				$x,$y,$w,$h,
				$angle,$angle + $step,
				$color->get_color($img),
				IMG_ARC_PIE
			);
			$angle += $step;
		}
	}
	
	/**
	 * Does the actual painting
	 *
	 * @param PLIB_GD_Color $color the color
	 * @param string $func an empty string for draw() and 'filled' for fill()
	 * @return the result of image*ellipse()
	 */
	private function _paint($color,$func)
	{
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		list($x,$y) = $this->_ellipse->get_center()->get();
		$size = $this->_ellipse->get_size();
		$funcname = 'image'.$func.'ellipse';
		$img = $this->get_image_res();
		return $funcname($img,$x,$y,$size->get_width(),$size->get_height(),$color->get_color($img));
	}
	
	/**
	 * Does the actual painting (a part of the ellipse)
	 *
	 * @param PLIB_GD_Color $color the color
	 * @param int $start the start-degree
	 * @param int $end the end-degree
	 * @param string $func an empty string for draw() and 'filled' for fill()
	 * @param int $type the type: IMG_ARC_PIE, IMG_ARC_CHORD, IMG_ARC_NOFILL, IMG_ARC_EDGED
	 * 	(for filled)
	 * @return the result of image*arc()
	 */
	private function _paint_part($color,$start,$end,$func,$type = -1)
	{
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		if($func != '' && !in_array($type,array(IMG_ARC_PIE,IMG_ARC_CHORD,IMG_ARC_NOFILL,IMG_ARC_EDGED)))
		{
			PLIB_Helper::def_error('inarray','type',
				array(IMG_ARC_PIE,IMG_ARC_CHORD,IMG_ARC_NOFILL,IMG_ARC_EDGED),$type);
		}
		
		list($x,$y) = $this->_ellipse->get_center()->get();
		list($w,$h) = $this->_ellipse->get_size()->get();
		$img = $this->get_image_res();
		if($func == 'filled')
			return imagefilledarc($img,$x,$y,$w,$h,$start,$end,$color->get_color($img),$type);
		
		return imagearc($img,$x,$y,$w,$h,$start,$end,$color->get_color($img));
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>