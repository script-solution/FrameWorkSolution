<?php
/**
 * Contains the point-class
 * 
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A wrapper for a GD-image which provides an easier interface to it. The class contains
 * the image and provides methods for saving the image, sending it to the browser, rotate
 * it and other stuff.
 * <br>
 * Additionally you have access to the corresponding {@link PLIB_GD_Graphics}-object which
 * provides many methods to paint something on the image.
 * <br>
 * An example for the usage:
 * <code>
 * $img = new PLIB_GD_Image(100,80);
 * $g = $img->get_graphics();
 * $g->draw_line(new PLIB_GD_Point(0,0),new PLIB_GD_Point(100,80),PLIB_GD_Color::$RED);
 * $img->send('png');
 * $img->destroy();
 * </code>
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Image extends PLIB_FullObject
{
	/**
	 * Loads the image of given type from file
	 *
	 * @param string $file the file to load
	 * @param string $type the image-type: jpeg,png,gif,wbmp,xbm
	 * @return PLIB_GD_Image the image-instance
	 */
	public static function load_from($file,$type = 'png')
	{
		if(!is_file($file))
			PLIB_Helper::error('"'.$file.'" is no file!');
		if(!in_array($type,array('jpeg','png','gif','wbmp','xbm')))
			PLIB_Helper::def_error('inarray','type',array('jpeg','png','gif','wbmp','xbm'),$type);
		
		$func = 'imagecreatefrom'.$type;
		$image = $func($file);
		$img = new PLIB_GD_Image(1,1);
		$img->_width = imagesx($image);
		$img->_height = imagesy($image);
		$img->_image = $image;
		return $img;
	}
	
	/**
	 * The graphics-objects for all images
	 *
	 * @var array
	 */
	private static $_graphics = array();
	
	/**
	 * The timer (to measure the rendering-time)
	 *
	 * @var PLIB_Timer
	 */
	private $_timer;
	
	/**
	 * The width of the image
	 *
	 * @var int
	 */
	private $_width;
	
	/**
	 * The height of the image
	 *
	 * @var int
	 */
	private $_height;
	
	/**
	 * The background-color (if known)
	 *
	 * @var PLIB_GD_Color
	 */
	private $_bgcolor = null;
	
	/**
	 * The image
	 *
	 * @var resource
	 */
	private $_image;
	
	/**
	 * Constructor
	 *
	 * @param int $width the width of the image
	 * @param int $height the height of the image
	 * @param boolean $truecolor create a truecolor-image?
	 */
	public function __construct($width,$height,$truecolor = true)
	{
		parent::__construct();
		
		if(!PLIB_Helper::is_integer($width) || $width <= 0)
			PLIB_Helper::def_error('intgt0','width',$width);
		if(!PLIB_Helper::is_integer($height) || $height <= 0)
			PLIB_Helper::def_error('intgt0','height',$height);
		
		$this->_timer = new PLIB_Timer();
		$this->_width = $width;
		$this->_height = $height;
		if($truecolor)
		{
			$this->_image = imagecreatetruecolor($width,$height);
			imagesavealpha($this->_image,true);
		}
		else
			$this->_image = imagecreate($width,$height);
	}
	
	/**
	 * @return resource the image-resource
	 */
	public function get_image()
	{
		return $this->_image;
	}
	
	/**
	 * @return boolean wether this image is a truecolor-image
	 */
	public function is_truecolor()
	{
		return imageistruecolor($this->_image);
	}
	
	/**
	 * Sets wether antialiasing should be used.
	 * <br>
	 * Activate the fast drawing antialiased methods for lines and wired polygons. It does not
	 * support alpha components. It works using a direct blend operation. It works only with
	 * truecolor images.
	 * Thickness and styled are not supported.
	 * <br>
	 * Using antialiased primitives with transparent background color can end with some
	 * unexpected results. The blend method uses the background color as any other colors.
	 * The lack of alpha component support does not allow an alpha based antialiasing method. 
	 *
	 * @param boolean $antialiasing the new value
	 */
	public function set_antialiasing($antialiasing)
	{
		imageantialias($this->_image,$antialiasing);
	}
	
	/**
	 * Sets wether alpha-blending should be used.
	 * <br>
	 * In blending mode, the alpha channel component of the color supplied to all drawing function,
	 * such as imagesetpixel() determines how much of the underlying color should be allowed to
	 * shine through. As a result, gd automatically blends the existing color at that point with
	 * the drawing color, and stores the result in the image. The resulting pixel is opaque.
	 * <br>
	 * In non-blending mode, the drawing color is copied literally with its alpha channel
	 * information, replacing the destination pixel. Blending mode is not available when drawing
	 * on palette images.
	 *
	 * @param boolean $alphablending the new value
	 */
	public function set_alphablending($alphablending)
	{
		imagealphablending($this->_image,$alphablending);
	}
	
	/**
	 * @return PLIB_GD_Graphics the graphics-object for this image
	 */
	public function get_graphics()
	{
		if(!isset(self::$_graphics[$this->_object_id]))
			self::$_graphics[$this->_object_id] = new PLIB_GD_Graphics($this);
		
		return self::$_graphics[$this->_object_id];
	}
	
	/**
	 * @return PLIB_GD_Rectangle the bounds-rectangle of the image
	 */
	public function get_bounds_rect()
	{
		return new PLIB_GD_Rectangle(0,0,$this->_width,$this->_height);
	}
	
	/**
	 * Rotates the image by the given angle
	 *
	 * @param int $angle the angle
	 * @param PLIB_GD_Color $bgcolor the background-color for the rotation
	 * @param boolean $ignore_transparence if enabled transparent colors are ignored (otherwise kept)
	 */
	public function rotate($angle,$bgcolor,$ignore_transparence = false)
	{
		if(!PLIB_Helper::is_integer($angle))
			PLIB_Helper::def_error('integer','angle',$angle);
		if(!($bgcolor instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','bgcolor','PLIB_GD_Color',$bgcolor);
		
		imagerotate($this->_image,$angle,$bgcolor->get_color($this->_image),(bool)$ignore_transparence);
	}
	
	/**
	 * Waves the given rectangle in this image
	 *
	 * @param PLIB_GD_Rectangle $rect the rectangle to wave
	 * @param float $amplitude the amplitude of the wave
	 * @param int $period the period of the wave
	 */
	public function wave_region($rect,$amplitude = 4.5,$period = 30)
	{
		if(!($rect instanceof PLIB_GD_Rectangle))
			PLIB_Helper::def_error('instance','rect','PLIB_GD_Rectangle',$rect);
		if(!PLIB_Helper::is_integer($amplitude) || $amplitude <= 0)
			PLIB_Helper::def_error('intgt0','amplitude',$amplitude);
		if(!PLIB_Helper::is_integer($period) || $period <= 0)
			PLIB_Helper::def_error('intgt0','period',$period);
		
		// cache vars of the rectangle
		list($x,$y) = $rect->get_location()->get();
		list($w,$h) = $rect->get_size()->get();
		
		// A greater multiplicator leads to better results but costs much more time and memory
		// therefore its better to use 1 by default, I think
		$mult = 2;
		if(imageistruecolor($this->_image))
			$img2 = imagecreatetruecolor($w * $mult, $h * $mult);
		else
			$img2 = imagecreate($w * $mult, $h * $mult);
		imagecopyresampled($img2,$this->_image,0,0,$x,$y,$w * $mult,$h * $mult,$w,$h);
		
		// wave horizontal
		$rperiod = mt_rand($period - 5,$period + 5);
		for($i = 0,$end = ($w * $mult);$i < $end;$i += 2)
		{
			imagecopy(
				$img2,
				$img2,
				$x + $i - 2,
				$y + sin($i / $rperiod) * $amplitude,
				$x + $i,
				$y,
				2,
				$h * $mult
			);
	  }
	  
	  // wave vertical
		$rperiod = mt_rand($period - 5,$period + 5);
	  for($i = 0,$end = ($h * $mult);$i < $end;$i += 2)
	  {
	  	imagecopy(
				$img2,
				$img2,
				$x + sin($i / $rperiod) * $amplitude,
				$y + $i - 2,
				$x,
				$y + $i,
				$w * $mult,
				2
			);
	  }
	  
	  // Resample it down again
		imagecopyresampled($this->_image,$img2,$x,$y,0,0,$w,$h,$w * $mult,$h * $mult);
		imagedestroy($img2);
	}
	
	/**
	 * Sets the background to the given color
	 *
	 * @param PLIB_GD_Color $color the color
	 */
	public function set_background($color)
	{
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		imagefill($this->_image,0,0,$color->get_color($this->_image));
		$this->_bgcolor = $color;
	}
	
	/**
	 * Saves the image to the given file in the given format. Note that the quality you specify
	 * does not affect gifs, wbmps and xmbs.
	 * Does not destroy (imagedestroy()) the image!
	 *
	 * @param string $file the file where to store the image
	 * @param string $format the format (png,jpeg,gif,wbmp,xbm)
	 * @param int $quality the quality (0..100)
	 */
	public function save($file,$format = 'png',$quality = 100)
	{
		switch($format)
		{
			case 'png':
				$compression = 9 - ($quality == 0 ? 0 : 9 / (100 / $quality));
				imagepng($this->_image,$file,$compression);
				break;
			case 'jpeg':
				imagejpeg($this->_image,$file,$quality);
				break;
			case 'gif':
				imagegif($this->_image,$file);
				break;
			case 'wbmp':
				imagewbmp($this->_image,$file);
				break;
			case 'xbm':
				imagexbm($this->_image,$file);
				break;
		}
	}
	
	/**
	 * Sends the image to the browser in the given format.
	 * Will destroy (imagedestroy()) the image!
	 *
	 * @param string $format the image-format (png,jpeg,gif,wbmp,xbm)
	 * @param boolean $allow_cache do you want to allow a caching of the image?
	 */
	public function send($format = 'png',$allow_cache = false)
	{
		// TODO make this optional
		/*if($this->_bgcolor !== null)
			$color = $this->_bgcolor->get_readable_random_foreground();
		else
			$color = PLIB_GD_Color::$WHITE;
		$attr = new PLIB_GD_TextAttributes(new PLIB_GD_Font_GD(),2,$color);
		$text = new PLIB_GD_Text('Rendertime: '.$this->_timer->stop().' sec.',$attr);
		$this->get_graphics()->get_text_view($text)->draw_in_rect(
			$this->get_bounds_rect(),new PLIB_GD_Padding(2),PLIB_GD_BoxPosition::$BOTTOM_RIGHT
		);*/
		
		if(!headers_sent())
		{
			header('Content-type: image/'.$format);
			if(!$allow_cache)
				header('Cache-control: no-cache, no-store');
			
			switch($format)
			{
				case 'png':
					imagepng($this->_image);
					break;
				case 'jpeg':
					imagejpeg($this->_image);
					break;
				case 'gif':
					imagegif($this->_image);
					break;
				case 'wbmp':
					imagewbmp($this->_image);
					break;
				case 'xbm':
					imagexbm($this->_image);
					break;
			}
		}
		else
			echo 'Unable to send image: Headers already sent!';
		
		$this->destroy();
	}
	
	/**
	 * Destroys the image
	 */
	public function destroy()
	{
		unset(self::$_graphics[$this->_object_id]);
		imagedestroy($this->_image);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>