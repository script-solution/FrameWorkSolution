<?php
/**
 * Contains the view-base-class
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
 * The base-class for all views
 *
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_GD_View extends FWS_Object
{
	/**
	 * The image
	 *
	 * @var FWS_GD_Image
	 */
	protected $_img;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $image the image-object
	 */
	public function __construct($image)
	{
		parent::__construct();
		
		if(!($image instanceof FWS_GD_Image))
			FWS_Helper::def_error('instance','image','FWS_GD_Image',$image);
		
		$this->_img = $image;
	}
	
	/**
	 * @return FWS_GD_Graphics the graphics-object
	 */
	protected final function get_graphics()
	{
		return $this->_img->get_graphics();
	}
	
	/**
	 * @return resource the GD-image
	 */
	protected final function get_image_res()
	{
		return $this->_img->get_image();
	}
}
?>