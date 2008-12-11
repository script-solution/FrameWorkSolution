<?php
/**
 * Contains the point-class
 * 
 * @version			$Id$
 * @package			FrameWorkSolution
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
 * Additionally you have access to the corresponding {@link FWS_GD_Graphics}-object which
 * provides many methods to paint something on the image.
 * <br>
 * An example for the usage:
 * <code>
 * $img = new FWS_GD_Image(100,80);
 * $g = $img->get_graphics();
 * $g->draw_line(new FWS_GD_Point(0,0),new FWS_GD_Point(100,80),FWS_GD_Color::$RED);
 * $img->send('png');
 * $img->destroy();
 * </code>
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Image extends FWS_Object
{
	/**
	 * Loads the image of given type from file
	 *
	 * @param string $file the file to load
	 * @param string $type the image-type: jpeg,png,gif,wbmp,xbm
	 * @return FWS_GD_Image the image-instance
	 */
	public static function load_from($file,$type = 'png')
	{
		if(!is_file($file))
			FWS_Helper::error('"'.$file.'" is no file!');
		if(!in_array($type,array('jpeg','png','gif','wbmp','xbm')))
			FWS_Helper::def_error('inarray','type',array('jpeg','png','gif','wbmp','xbm'),$type);
		
		$func = 'imagecreatefrom'.$type;
		$image = $func($file);
		$img = new FWS_GD_Image(1,1);
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
	 * @var FWS_GD_Color
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
		
		if(!FWS_Helper::is_integer($width) || $width <= 0)
			FWS_Helper::def_error('intgt0','width',$width);
		if(!FWS_Helper::is_integer($height) || $height <= 0)
			FWS_Helper::def_error('intgt0','height',$height);
		
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
		if(function_exists('imageantialias'))
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
	 * @return FWS_GD_Graphics the graphics-object for this image
	 */
	public function get_graphics()
	{
		if(!isset(self::$_graphics[$this->_object_id]))
			self::$_graphics[$this->_object_id] = new FWS_GD_Graphics($this);
		
		return self::$_graphics[$this->_object_id];
	}
	
	/**
	 * @return FWS_GD_Rectangle the bounds-rectangle of the image
	 */
	public function get_bounds_rect()
	{
		return new FWS_GD_Rectangle(0,0,$this->_width - 1,$this->_height - 1);
	}
	
	/**
	 * Rotates the image by the given angle
	 *
	 * @param int $angle the angle
	 * @param FWS_GD_Color $bgcolor the background-color for the rotation
	 * @param boolean $ignore_transparence if enabled transparent colors are ignored (otherwise kept)
	 */
	public function rotate($angle,$bgcolor,$ignore_transparence = false)
	{
		if(!FWS_Helper::is_integer($angle))
			FWS_Helper::def_error('integer','angle',$angle);
		if(!($bgcolor instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','bgcolor','FWS_GD_Color',$bgcolor);
		
		// just available if PHP was compiled with bundled version of GD
		if(function_exists('imagerotate'))
			imagerotate($this->_image,$angle,$bgcolor->get_color($this->_image),(bool)$ignore_transparence);
		else
			$this->_rotate_self($angle,$bgcolor);
	}
	
	/**
	 * Rotates the image by the given angle (manually)
	 *
	 * @param int $angle the angle
	 * @param FWS_GD_Color $bgcolor the background-color for the rotation
	 * @param boolean $ignore_transparence if enabled transparent colors are ignored (otherwise kept)
	 */
	private function _rotate_self($angle,$bgcolor)
	{
		$width = $this->_width;
		$height = $this->_height;
		if($this->is_truecolor())
		{
			$nimg = imagecreatetruecolor($width,$height);
			$trans_colour = imagecolorallocatealpha($nimg,0,0,0,127);
		}
		else
		{
			$nimg = imagecreate($width,$height);
			$trans_colour = imagecolorallocate(
				$nimg,$bgcolor->get_red(),$bgcolor->get_green(),$bgcolor->get_blue()
			);
		}
    imagefill($nimg,0,0,$trans_colour);
		
		$dx_x = $this->_rot_x(-$angle, 1, 0);
    $dx_y = $this->_rot_y(-$angle, 1, 0);
    $dy_x = $this->_rot_x(-$angle, 0, 1);
    $dy_y = $this->_rot_y(-$angle, 0, 1);
    
    $x0 = $this->_rot_x(-$angle,-$width / 2,-$height / 2) + $width / 2;
    $y0 = $this->_rot_y(-$angle,-$width / 2,-$height / 2) + $height / 2;
    
    $img = $this->_image;
    $x1 = $x0;
    $y1 = $y0;
    for($y = 0;$y < $height;$y++)
    {
      $x2 = $x1;
      $y2 = $y1;
      for($x = 0;$x < $width;$x++)
      {
      	if($x2 >= 0 && $x2 < $width && $y2 >= 0 && $y2 < $height)
					$pixel = imagecolorat($img,$x2,$y2);
				else
					$pixel = $trans_colour;
				
				imagesetpixel($nimg,$x,$y,$pixel);
				$x2 += $dx_x;
				$y2 += $dx_y;
      }
      $x1 += $dy_x;
      $y1 += $dy_y;
    }
		
		imagedestroy($img);
		$this->_image = $nimg;
	}
	
	/**
	 * Rotates the x position of a point.
	 * 
	 * @param int $angle the angle to rotate by
	 * @param int $x the x-position
	 * @param int $y the y-position
	 * @return the new x-position
	 */
	private function _rot_x($angle,$x,$y)
	{
		$cos = cos($angle / 180 * pi());
		$sin = sin($angle / 180 * pi());
		return $x * $cos + $y * -$sin;
	}
  
	/**
	 * Rotates the y position of a point.
	 * 
	 * @param int $angle the angle to rotate by
	 * @param int $x the x-position
	 * @param int $y the y-position
	 * @return the new y-position
	 */
	private function _rot_y($angle,$x,$y)
	{
		$cos = cos($angle / 180 * pi());
		$sin = sin($angle / 180 * pi());
		return $x * $sin + $y * $cos;
	}
	
	/**
	 * Waves the given rectangle in this image
	 *
	 * @param FWS_GD_Rectangle $rect the rectangle to wave
	 * @param float $amplitude the amplitude of the wave
	 * @param int $period the period of the wave
	 */
	public function wave_region($rect,$amplitude = 4.5,$period = 30)
	{
		if(!($rect instanceof FWS_GD_Rectangle))
			FWS_Helper::def_error('instance','rect','FWS_GD_Rectangle',$rect);
		if(!FWS_Helper::is_integer($amplitude) || $amplitude <= 0)
			FWS_Helper::def_error('intgt0','amplitude',$amplitude);
		if(!FWS_Helper::is_integer($period) || $period <= 0)
			FWS_Helper::def_error('intgt0','period',$period);
		
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
	 * @param FWS_GD_Color $color the color
	 */
	public function set_background($color)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
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
	 * Outputs the image in the given format
	 *
	 * @param string $format the format (png,jpeg,gif,wbmp,xbm)
	 */
	public function output($format = 'png')
	{
		switch($format)
		{
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
			default:
				imagepng($this->_image);
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
		if(!headers_sent())
		{
			header('Content-type: image/'.$format);
			if(!$allow_cache)
				header('Cache-control: no-cache, no-store');
			
			$this->output($format);
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
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>