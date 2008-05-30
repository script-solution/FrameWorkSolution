<?php
/**
 * Contains the view-base-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for all views
 *
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_GD_View extends PLIB_FullObject
{
	/**
	 * The image
	 *
	 * @var PLIB_GD_Image
	 */
	protected $_img;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Image $image the image-object
	 */
	public function __construct($image)
	{
		parent::__construct();
		
		if(!($image instanceof PLIB_GD_Image))
			PLIB_Helper::def_error('instance','image','PLIB_GD_Image',$image);
		
		$this->_img = $image;
	}
	
	/**
	 * @return PLIB_GD_Graphics the graphics-object
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