<?php
/**
 * Contains the gd-image-renderer-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
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
 * The GD-image-renderer sends an image generated with the GD-interface of the framework to the
 * browser. You can set the image-object ({@link FWS_GD_Image}) which should be sent.
 * Additionally you can set the format of the image.
 * <br>
 * If any messages exist the renderer will generate a different image which contains the message
 * <code>$locale->lang('error_occurred')</code>.
 * 
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Document_Renderer_GDImage extends FWS_Object implements FWS_Document_Renderer
{
	/**
	 * The image to render
	 *
	 * @var FWS_GD_Image
	 */
	private $_image;
	
	/**
	 * The image-format to use
	 *
	 * @var string
	 */
	private $_format = 'png';
	
	/**
	 * Wether the image may be cached
	 *
	 * @var boolean
	 */
	private $_allow_cache = false;
	
	/**
	 * @return FWS_GD_Image the image (null if not set)
	 */
	public final function get_image()
	{
		return $this->_image;
	}
	
	/**
	 * Sets the image for the renderer
	 *
	 * @param FWS_GD_Image $image the image
	 */
	public final function set_image($image)
	{
		if(!($image instanceof FWS_GD_Image))
			FWS_Helper::def_error('instance','image','FWS_GD_Image',$image);
		
		$this->_image = $image;
	}
	
	/**
	 * The image-format to use
	 *
	 * @param string $format the new value
	 */
	public final function set_format($format)
	{
		$this->_format = $format;
	}
	
	/**
	 * Sets wether caching is allowed
	 *
	 * @param boolean $allow the new value
	 */
	public final function set_allow_cache($allow)
	{
		$this->_allow_cache = (bool)$allow;
	}
	
	/**
	 * @see FWS_Document_Renderer::render()
	 *
	 * @param FWS_Document $doc
	 * @return string
	 */
	public function render($doc)
	{
		$msgs = FWS_Props::get()->msgs();
		
		// run the module
		$doc->get_module()->run();
		
		// any msgs?
		if($msgs->contains_msg())
			$this->handle_msgs($msgs);
		
		if($this->_image === null)
			FWS_Helper::error('Please set the image first!');
		
		// send the image and catch it
		ob_start();
		$this->_image->output($this->_format);
		$this->_image->destroy();
		$result = ob_get_contents();
		ob_clean();
		
		$doc->set_header('Content-Type','image/'.$this->_format);
		
		return $result;
	}

	/**
	 * Handles the collected messages
	 *
	 * @param FWS_Document_Messages $msgs
	 */
	protected function handle_msgs($msgs)
	{
		$locale = FWS_Props::get()->locale();
		
		$font = new FWS_GD_Font_GD();
		$attr = new FWS_GD_TextAttributes($font,4,FWS_GD_Color::$BLACK);
		$text = new FWS_GD_Text(html_entity_decode($locale->lang('error_occurred')),$attr);
		$size = $text->get_size();
		$size->increase(20,20);
		$img = new FWS_GD_Image((int)$size->get_width(),(int)$size->get_height());
		$img->set_background(FWS_GD_Color::$WHITE);
		$g = $img->get_graphics();
		$g->get_text_view($text)->draw_in_rect(
			$img->get_bounds_rect(),null,FWS_GD_BoxPosition::$CENTER_CENTER
		);
		$g->get_rect_view($img->get_bounds_rect())->draw(FWS_GD_Color::$LIGHT_GRAY);
		
		$this->set_image($img);
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>