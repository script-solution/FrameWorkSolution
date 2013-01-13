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
 * This class generates the colors for a color-fade of a given distance and a given number of
 * steps. Additionally you can get a color at a given position.
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_ColorFade extends FWS_Object
{
	/**
	 * The distance from the beginning to the end of the color-fade
	 *
	 * @var FWS_GD_Point
	 */
	private $_distance;
	
	/**
	 * An array with all colors that should be used
	 *
	 * @var array
	 */
	private $_colors;
	
	/**
	 * The number of steps
	 *
	 * @var int
	 */
	private $_steps;
	
	/**
	 * Constructor
	 *
	 * @param int $distance the distance from the beginning to the end of the color-fade
	 * @param int $steps the number of steps
	 * @param array $colors an array with all colors that should be used. These may be instances of
	 * 	FWS_GD_Color or arrays with the 3 or 4 color-components
	 */
	public function __construct($distance,$steps,$colors)
	{
		parent::__construct();
		
		if(!is_numeric($distance) || $distance <= 0)
			FWS_Helper::def_error('numgt0','distance',$distance);
		if(!is_numeric($steps) || $steps <= 0)
			FWS_Helper::def_error('numgt0','steps',$steps);
		if(!is_array($colors) || count($colors) < 2)
			FWS_Helper::error('Please provide at least 2 colors!');
		
		// add alpha channel if necessary
		if($colors[0] instanceof FWS_GD_Color)
		{
			foreach($colors as $k => $c)
			{
				if(!($c instanceof FWS_GD_Color))
					FWS_Helper::error('The element with key='.$k.' in the colors-array is no valid color!');
				
				$colors[$k] = $c->get_comps(true);
			}
		}
		else
		{
			foreach($colors as $k => $c)
			{
				if(!is_array($c) || count($c) < 3)
					FWS_Helper::error('The element with key='.$k.' in the colors-array is no valid color!');
				
				if(!isset($c[3]))
					$colors[$k][3] = 0;
			}
		}
		
		$this->_distance = $distance;
		$this->_steps = $steps;
		$this->_colors = $colors;
	}
	
	/**
	 * Determines the color in the color-fade at given position
	 *
	 * @param int $position the position of which you would like to know the color
	 * @return FWS_GD_Color the color at given position
	 */
	public function get_color_at($position)
	{
		if(!is_numeric($position) || $position < 0 || $position >= $this->_steps)
			FWS_Helper::def_error('numbetween','position',0,$this->_steps - 1,$position);
		
		// cache some vars
		$colors = &$this->_colors;
		$distance = &$this->_distance;
		$steps = &$this->_steps;
		
		// determine sizes
		$nareas = count($colors) - 1;
		$area_width = $distance / $nareas;
		$step_count = $steps / $nareas;
		$step = $area_width / $step_count;
		
		$pos = $position * $step;
		$area = round(($pos - ($pos % $area_width)) / $area_width);
		$pos_in_area = round($pos - $area * $area_width);
		$start_color = $colors[$area];
		$end_color = $colors[$area + 1];
		
		$r_dist = $end_color[0] - $start_color[0];
		$g_dist = $end_color[1] - $start_color[1];
		$b_dist = $end_color[2] - $start_color[2];
	
		$r_step = $r_dist / $area_width;
		$g_step = $g_dist / $area_width;
		$b_step = $b_dist / $area_width;
		
		return new FWS_GD_Color(
			(int)($start_color[0] + $r_step * $pos_in_area),
			(int)($start_color[1] + $g_step * $pos_in_area),
			(int)($start_color[2] + $b_step * $pos_in_area)
		);
	}
	
	/**
	 * Determines the colors for the positions and returns them
	 *
	 * @return array the colors
	 */
	public function get_colors()
	{
		// cache some vars
		$colors = &$this->_colors;
		$distance = &$this->_distance;
		$steps = &$this->_steps;
		
		// determine sizes
		$nareas = count($colors) - 1;
		$area_width = $distance / $nareas;
		$step_count = $steps / $nareas;
		$step = $area_width / $step_count;
		
		$res = array();
		for($i = 0;$i < $nareas;$i++)
		{
			$r_dist = $colors[$i][0] - $colors[$i + 1][0];
			$g_dist = $colors[$i][1] - $colors[$i + 1][1];
			$b_dist = $colors[$i][2] - $colors[$i + 1][2];
			$a_dist = $colors[$i][3] - $colors[$i + 1][3];
			
			$r_step = $r_dist / $area_width;
			$g_step = $g_dist / $area_width;
			$b_step = $b_dist / $area_width;
			$a_step = $a_dist / $area_width;
			
			$r = $colors[$i][0];
			$g = $colors[$i][1];
			$b = $colors[$i][2];
			$a = $colors[$i][3];
			for($x = 0;$x < $step_count;$x++)
			{
				$res[] = new FWS_GD_Color((int)$r,(int)$g,(int)$b,(int)$a);
				
				$r -= $r_step * $step;
				$g -= $g_step * $step;
				$b -= $b_step * $step;
				$a -= $a_step * $step;
			}
		}
		
		return $res;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>