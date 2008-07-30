<?php
/**
 * Contains the view-base-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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