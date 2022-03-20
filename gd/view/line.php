<?php
/**
 * Contains the line-view-class
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
 * The view for a line which allows the painting of it
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_View_Line extends FWS_GD_View
{
	/**
	 * The line
	 *
	 * @var FWS_GD_Line
	 */
	protected $_line;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $img the image
	 * @param FWS_GD_Line $line the line
	 */
	public function __construct($img,$line)
	{
		parent::__construct($img);
		
		if(!($line instanceof FWS_GD_Line))
			FWS_Helper::def_error('instance','line','FWS_GD_Line',$line);
		
		$this->_line = $line;
	}
	
	/**
	 * Draws the line with the given color
	 *
	 * @param FWS_GD_Color $color
	 * @return bool the result of imageline()
	 */
	public final function draw($color)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
		$from = $this->_line->get_from();
		$to = $this->_line->get_to();
		$img = $this->get_image_res();
		return imageline(
			$img,
			(int)$from->get_x(),(int)$from->get_y(),
			(int)$to->get_x(),(int)$to->get_y(),
			$color->get_color($img)
		);
	}
	
	/**
	 * Draws this line as color-fade with the given colors and the given step-width
	 *
	 * @param array $colors an array with all colors that should be used
	 * @param int $step the step-size
	 */
	public final function draw_colorfade($colors,$step = 1)
	{
		if(!FWS_Helper::is_integer($step) || $step <= 0)
			FWS_Helper::def_error('intgt0','step',$step);
		
		$distance = $this->_line->get_length();
		$cf = new FWS_GD_ColorFade((int)$distance,(int)($distance / $step),$colors);
		$cfcolors = $cf->get_colors();
		
		$img = $this->get_image_res();
		list($x,$y) = $this->_line->get_from()->get();
		$to = $this->_line->get_to();
		$x_step = ($to->get_x() - $x) / count($cfcolors);
		$y_step = ($to->get_y() - $y) / count($cfcolors);
		foreach($cfcolors as $color)
		{
			imageline(
				$img,
				(int)$x,(int)$y,
				(int)($x + $x_step),(int)($y + $y_step),
				$color->get_color($img)
			);
			$x += $x_step;
			$y += $y_step;
		}
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>