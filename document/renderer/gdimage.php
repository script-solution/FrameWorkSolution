<?php
/**
 * Contains the gd-image-renderer-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The GD-image-renderer sends an image generated with the GD-interface of the library to the
 * browser. You can set the image-object ({@link PLIB_GD_Image}) which should be sent.
 * Additionally you can set the format of the image.
 * <br>
 * If any messages exist the renderer will generate a different image which contains the message
 * <code>$locale->lang('error_occurred')</code>.
 * 
 * @package			PHPLib
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Document_Renderer_GDImage extends PLIB_Object implements PLIB_Document_Renderer
{
	/**
	 * The image to render
	 *
	 * @var PLIB_GD_Image
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
	 * @return PLIB_GD_Image the image (null if not set)
	 */
	public final function get_image()
	{
		return $this->_image;
	}
	
	/**
	 * Sets the image for the renderer
	 *
	 * @param PLIB_GD_Image $image the image
	 */
	public final function set_image($image)
	{
		if(!($image instanceof PLIB_GD_Image))
			PLIB_Helper::def_error('instance','image','PLIB_GD_Image',$image);
		
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
	 * @see PLIB_Document_Renderer::render()
	 *
	 * @param PLIB_Document $doc
	 * @return string
	 */
	public function render($doc)
	{
		$msgs = PLIB_Props::get()->msgs();
		
		// run the module
		$doc->get_module()->run();
		
		// any msgs?
		if($msgs->contains_msg())
			$this->handle_msgs($msgs);
		
		if($this->_image === null)
			PLIB_Helper::error('Please set the image first!');
		
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
	 * @param PLIB_Document_Messages $msgs
	 */
	protected function handle_msgs($msgs)
	{
		$locale = PLIB_Props::get()->locale();
		
		$font = new PLIB_GD_Font_GD();
		$attr = new PLIB_GD_TextAttributes($font,4,PLIB_GD_Color::$BLACK);
		$text = new PLIB_GD_Text(html_entity_decode($locale->lang('error_occurred')),$attr);
		$size = $text->get_size();
		$size->increase(20,20);
		$img = new PLIB_GD_Image($size->get_width(),$size->get_height());
		$img->set_background(PLIB_GD_Color::$WHITE);
		$g = $img->get_graphics();
		$g->get_text_view($text)->draw_in_rect(
			$img->get_bounds_rect(),null,PLIB_GD_BoxPosition::$CENTER_CENTER
		);
		$g->get_rect_view($img->get_bounds_rect())->draw(PLIB_GD_Color::$LIGHT_GRAY);
		
		$this->set_image($img);
	}

	/**
	 * @see PLIB_Object::get_print_vars()
	 *
	 * @return array
	 */
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>