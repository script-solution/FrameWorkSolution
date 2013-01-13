<?php
/**
 * Contains the ellipse-view-class
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
 * The view for a ellipse which allows the painting of it
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_View_Ellipse extends FWS_GD_View
{
	/**
	 * The ellipse
	 *
	 * @var FWS_GD_Ellipse
	 */
	protected $_ellipse;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $img the image
	 * @param FWS_GD_Ellipse $ellipse the ellipse
	 */
	public function __construct($img,$ellipse)
	{
		parent::__construct($img);
		if(!($ellipse instanceof FWS_GD_Ellipse))
			FWS_Helper::def_error('instance','ellipse','FWS_GD_Ellipse',$ellipse);
		
		$this->_ellipse = $ellipse;
	}
	
	/**
	 * Draws the ellipse with the given color
	 *
	 * @param FWS_GD_Color $color the color
	 * @return bool the result of imageellipse()
	 */
	public final function draw($color)
	{
		return $this->_paint($color,'');
	}
	
	/**
	 * Draws the given part of the ellipse with given color
	 *
	 * @param FWS_GD_Color $color the color
	 * @param int $start the start-degree
	 * @param int $end the end-degree
	 * @return bool the result of imagearc()
	 */
	public final function draw_part($color,$start,$end)
	{
		return $this->_paint_part($color,$start,$end,'');
	}
	
	/**
	 * Fills the ellipse with the given color
	 *
	 * @param FWS_GD_Color $color the color
	 * @return bool the result of imagefilledellipse()
	 */
	public final function fill($color)
	{
		return $this->_paint($color,'filled');
	}
	
	/**
	 * Fills the given part of the ellipse with given color
	 *
	 * @param FWS_GD_Color $color the color
	 * @param int $start the start-degree
	 * @param int $end the end-degree
	 * @param int $type the type: IMG_ARC_PIE, IMG_ARC_CHORD, IMG_ARC_NOFILL, IMG_ARC_EDGED
	 * @return bool the result of imagefilledarc()
	 */
	public final function fill_part($color,$start,$end,$type = IMG_ARC_PIE)
	{
		return $this->_paint_part($color,$start,$end,'filled',$type);
	}
	
	/**
	 * Fills the ellipse with a color-fade of the given color and the given step-width.
	 *
	 * @param array $colors an array with all colors that should be used
	 * @param int $step the step-size
	 */
	public final function fill_colorfade($colors,$step = 1)
	{
		$cf = new FWS_GD_ColorFade(360,360 / $step,$colors);
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
	 * @param FWS_GD_Color $color the color
	 * @param string $func an empty string for draw() and 'filled' for fill()
	 * @return bool the result of image*ellipse()
	 */
	private function _paint($color,$func)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
		list($x,$y) = $this->_ellipse->get_center()->get();
		$size = $this->_ellipse->get_size();
		$funcname = 'image'.$func.'ellipse';
		$img = $this->get_image_res();
		return $funcname($img,$x,$y,$size->get_width(),$size->get_height(),$color->get_color($img));
	}
	
	/**
	 * Does the actual painting (a part of the ellipse)
	 *
	 * @param FWS_GD_Color $color the color
	 * @param int $start the start-degree
	 * @param int $end the end-degree
	 * @param string $func an empty string for draw() and 'filled' for fill()
	 * @param int $type the type: IMG_ARC_PIE, IMG_ARC_CHORD, IMG_ARC_NOFILL, IMG_ARC_EDGED
	 * 	(for filled)
	 * @return bool the result of image*arc()
	 */
	private function _paint_part($color,$start,$end,$func,$type = -1)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		if($func != '' && !in_array($type,array(IMG_ARC_PIE,IMG_ARC_CHORD,IMG_ARC_NOFILL,IMG_ARC_EDGED)))
		{
			FWS_Helper::def_error('inarray','type',
				array(IMG_ARC_PIE,IMG_ARC_CHORD,IMG_ARC_NOFILL,IMG_ARC_EDGED),$type);
		}
		
		list($x,$y) = $this->_ellipse->get_center()->get();
		list($w,$h) = $this->_ellipse->get_size()->get();
		$img = $this->get_image_res();
		if($func == 'filled')
			return imagefilledarc($img,$x,$y,$w,$h,$start,$end,$color->get_color($img),$type);
		
		return imagearc($img,$x,$y,$w,$h,$start,$end,$color->get_color($img));
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>