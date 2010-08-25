<?php
/**
 * Contains the captcha-class
 * 
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A captcha-image created with GD. You can specify many parameters to customize
 * the result to your needs.
 * 
 * Example:
 * <code>
 * $captcha = new FWS_GD_Captcha();
 * $captcha->set_number_of_chars(5);
 * $captcha->set_size(300,100);
 * $captcha->set_wave(true,10,6);
 * $captcha->create_image();
 * $captcha->send_image();
 * </code>
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Captcha extends FWS_Object
{
	/**
	 * All available chars
	 *
	 * @var array
	 */
	private $_chars = array(
		'0' => array(
			array(1,1,1,1,1,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,1,1,1,1,1),
		),
		'1' => array(
			array(0,1,1,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,1,1,1,1,1),
		),
		'2' => array(
			array(1,1,1,1,1,0),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(0,0,1,1,1,1),
			array(0,1,0,0,0,0),
			array(1,0,0,0,0,0),
			array(1,0,0,0,0,0),
			array(1,1,1,1,1,1),
		),
		'3' => array(
			array(1,1,1,1,1,1),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(0,1,1,1,1,1),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(1,1,1,1,1,1),
		),
		'4' => array(
			array(1,0,0,1,0,0),
			array(1,0,0,1,0,0),
			array(1,0,0,1,0,0),
			array(1,1,1,1,1,1),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
			array(0,0,0,1,0,0),
		),
		'5' => array(
			array(1,1,1,1,1,1),
			array(1,0,0,0,0,0),
			array(1,0,0,0,0,0),
			array(1,1,1,1,1,0),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(1,1,1,1,1,0),
		),
		'6' => array(
			array(1,1,1,1,1,1),
			array(1,0,0,0,0,0),
			array(1,0,0,0,0,0),
			array(1,1,1,1,1,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,1,1,1,1,1),
		),
		'7' => array(
			array(1,1,1,1,1,1),
			array(0,0,0,0,0,1),
			array(0,0,0,1,1,0),
			array(0,0,1,0,0,0),
			array(0,0,1,0,0,0),
			array(0,0,1,0,0,0),
			array(0,0,1,0,0,0),
		),
		'8' => array(
			array(1,1,1,1,1,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(0,1,1,1,1,0),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,1,1,1,1,1),
		),
		'9' => array(
			array(0,1,1,1,1,0),
			array(1,0,0,0,0,1),
			array(1,0,0,0,0,1),
			array(1,1,1,1,1,1),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(0,0,0,0,0,1),
			array(1,1,1,1,1,1),
		)
	);
	
	/**
	 * The image
	 *
	 * @var FWS_GD_Image
	 */
	private $_img;
	
	/**
	 * The graphics-object for the image
	 *
	 * @var FWS_GD_Graphics
	 */
	private $_g;
	
	/**
	 * The width of the image
	 *
	 * @var int
	 */
	private $_width = 350;
	
	/**
	 * The height of the image
	 *
	 * @var int
	 */
	private $_height = 80;
	
	/**
	 * The used background-color
	 *
	 * @var FWS_GD_Color
	 */
	private $_bg;
	
	/**
	 * Do you want to use transparency?
	 *
	 * @var boolean
	 */
	private $_use_transparency = true;
	
	/**
	 * The number of chars in the image
	 *
	 * @var int
	 */
	private $_number_of_chars = 6;
	
	/**
	 * The minimum size of a shape of a character
	 *
	 * @var int
	 */
	private $_shape_min_size = 4;
	
	/**
	 * The maximum size of a shape of a character
	 *
	 * @var int
	 */
	private $_shape_max_size = 6;
	
	/**
	 * The number of noise-shapes which will be randomly positioned in the image
	 *
	 * @var int
	 */
	private $_number_of_noise_shapes = 25;
	
	/**
	 * The difference of the angle. That means that the angle of the character will be:
	 * <code>- $this->_angle_difference ... + $this->_angle_difference</code>
	 *
	 * @var int
	 */
	private $_angle_difference = 20;
	
	/**
	 * The propability to move a shape of a character (1.0 = always; 0.0 = never)
	 *
	 * @var float
	 */
	private $_char_trans_propability = 0.1;
	
	/**
	 * The minimum difference of the position (the number of pixels to move)
	 *
	 * @var int
	 */
	private $_char_trans_min_diff = 1;
	
	/**
	 * The maximum difference of the position (the number of pixels to move)
	 *
	 * @var int
	 */
	private $_char_trans_max_diff = 3;
	
	/**
	 * The number of vertical lines in the image
	 *
	 * @var int
	 */
	private $_vertical_lines = 1;
	
	/**
	 * The number of horizontal lines in the image
	 *
	 * @var int
	 */
	private $_horizontal_lines = 1;
	
	/**
	 * The propability to use a TTF-font instead of the generated chars via different shapes
	 *
	 * @var int
	 */
	private $_ttf_font_propability = 0.5;
	
	/**
	 * An array of {@link FWS_GD_Font_TTF}-objects which may be used and the fontsize for each of them.
	 * <code>array(
	 * 	array(<font>,<size>),
	 * 	...
	 * )</code>
	 *
	 * @var array
	 */
	private $_ttf_fonts = array();
	
	/**
	 * The difference of the TTF-font-size
	 *
	 * @var int
	 */
	private $_ttf_font_size_difference = 5;
	
	/**
	 * The minimum number of background colors
	 *
	 * @var int
	 */
	private $_background_colors_min = 4;
	
	/**
	 * The maximum number of background colors
	 *
	 * @var int
	 */
	private $_background_colors_max = 8;
	
	/**
	 * Do you want to use a random background? (at least nearly random; only bg-colors which
	 * have a min. brightness will be used)
	 *
	 * @var boolean
	 */
	private $_background_random = true;
	
	/**
	 * The start-color for the background (if no random-background)
	 * This value will be used for all 3 color components
	 *
	 * @var int
	 */
	private $_background_color_start = 150;
	
	/**
	 * The end-color for the background (if no random-background)
	 * This value will be used for all 3 color components
	 *
	 * @var int
	 */
	private $_background_color_end = 200;
	
	/**
	 * Use waves in the image?
	 *
	 * @var boolean
	 */
	private $_use_waves = true;
	
	/**
	 * The period of the wave
	 *
	 * @var int
	 */
	private $_wave_period = 16;
	
	/**
	 * The amplitude of the wave
	 *
	 * @var int
	 */
	private $_wave_amplitude = 6;
	
	/**
	 * The distance of two noise-pixels in the background
	 *
	 * @var int
	 */
	private $_noise_distance = 5;
	
	/**
	 * The difference of the distance
	 *
	 * @var int
	 */
	private $_noise_distance_diff = 3;
	
	/**
	 * The start-color for the noise-pixel
	 * This value will be used for all 3 color components
	 *
	 * @var int
	 */
	private $_noise_color_start = 100;
	
	/**
	 * The end-color for the noise-pixel
	 * This value will be used for all 3 color components
	 *
	 * @var int
	 */
	private $_noise_color_end = 230;
	
	/**
	 * The number of lines which consist of shapes
	 *
	 * @var int
	 */
	private $_shape_lines = 1;
	
	/**
	 * The length of the shape-lines
	 *
	 * @var int
	 */
	private $_shape_line_length = 10;
	
	/**
	 * The chars we've used (just available after #create_image()!)
	 *
	 * @var string
	 */
	private $_used_chars = '';
	
	/**
	 * Optionally, the specified chars to use
	 *
	 * @var string
	 */
	private $_desired_chars = '';
	
	/**
	 * String for debugging
	 *
	 * @var string
	 */
	private $_debug = '';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_use_transparency = function_exists('imagecreatetruecolor');
		$this->_size = new FWS_GD_Dimension(350,80);
	}
	
	/**
	 * @return string the debugging-result
	 */
	public function get_debug()
	{
		return $this->_debug;
	}

	/**
	 * Sets the difference of the char-angle
	 * 
	 * @param int $diff the new value
	 */
	public function set_angle_difference($diff)
	{
		if(!FWS_Helper::is_integer($diff) || $diff < 0 || $diff >= 360)
			FWS_Helper::def_error('numbetween','diff',0,359,$diff);
		
		$this->_angle_difference = $diff;
	}

	/**
	 * Sets wether a random background should be used (at least nearly random; only
	 * bg-colors which have a min. brightness will be used)
	 * 
	 * @param boolean $random the new value
	 * @see set_background_color()
	 */
	public function set_background_random($random)
	{
		$this->_background_random = (boolean)$random;
	}

	/**
	 * Sets the start- and end-color of the background. This is just one component of the color
	 * which will be used for all three. That means if you specify 100 the color will be:
	 * <var>rgb(100,100,100)</var>
	 * 
	 * @param int $start the new value (0..255)
	 * @param int $end the new value (0..255)
	 * @see set_background_random()
	 */
	public function set_background_color($start,$end)
	{
		if(!FWS_Helper::is_integer($start) || $start < 0 || $start > 255)
			FWS_Helper::def_error('numbetween','start',0,255,$start);
		if(!FWS_Helper::is_integer($end) || $end < 0 || $end > 255)
			FWS_Helper::def_error('numbetween','end',0,255,$end);
		if($end < $start)
			FWS_Helper::error('$end should be >= $start!');
		
		$this->_background_color_start = $start;
		$this->_background_color_end = $end;
	}

	/**
	 * Sets the maximum and minimum number of background-colors
	 * 
	 * @param int $min the new minimum value (1,2,...)
	 * @param int $max the new maximum value (1,2,...)
	 */
	public function set_background_color_count($min,$max)
	{
		if(!FWS_Helper::is_integer($min) || $min <= 1)
			FWS_Helper::def_error('intgt1','min',$min);
		if(!FWS_Helper::is_integer($max) || $max <= 1)
			FWS_Helper::def_error('intgt1','max',$max);
		if($max < $min)
			FWS_Helper::error('$max should be >= $min!');
		
		$this->_background_colors_min = $min;
		$this->_background_colors_max = $max;
	}

	/**
	 * Sets the maximum and minimum number of pixels one char may be moved
	 * (to customize the chars a little bit)
	 * 
	 * @param int $min the new minimum value
	 * @param int $max the new maximum value
	 * @see set_char_trans_propability()
	 */
	public function set_char_trans_diff($min,$max)
	{
		if(!FWS_Helper::is_integer($min) || $min < 0)
			FWS_Helper::def_error('intge0','min',$min);
		if(!FWS_Helper::is_integer($max) || $max < 0)
			FWS_Helper::def_error('intge0','max',$max);
		if($max < $min)
			FWS_Helper::error('$max should be >= $min!');
		
		$this->_char_trans_min_diff = $min;
		$this->_char_trans_max_diff = $max;
	}

	/**
	 * Sets the propability for a movement of a char-element.
	 * 
	 * @param float $_char_trans_propability (0.0 .. 1.0)
	 * @see set_char_trans_diff()
	 */
	public function set_char_trans_propability($propability)
	{
		if(!is_numeric($propability) || $propability < 0 || $propability > 1)
			FWS_Helper::def_error('numbetween','propability',0,1,$propability);
		
		$this->_char_trans_propability = $propability;
	}

	/**
	 * Sets the size of the image
	 * 
	 * @param int $width the new width
	 * @param int $height the new height
	 */
	public function set_size($width,$height)
	{
		if(!FWS_Helper::is_integer($width) || $width <= 0)
			FWS_Helper::def_error('intgt0','width',$width);
		if(!FWS_Helper::is_integer($height) || $height <= 0)
			FWS_Helper::def_error('intgt0','height',$height);
		
		$this->_width = $width;
		$this->_height = $height;
	}

	/**
	 * Sets the start- and end-color of the noise. This is just one component of the color
	 * which will be used for all three. That means if you specify 100 the color will be:
	 * <var>rgb(100,100,100)</var>
	 * 
	 * @param int $start the start-color
	 * @param int $end the end-color
	 */
	public function set_noise_color($start,$end)
	{
		if(!FWS_Helper::is_integer($start) || $start < 0 || $start > 255)
			FWS_Helper::def_error('numbetween','start',0,255,$start);
		if(!FWS_Helper::is_integer($end) || $end < 0 || $end > 255)
			FWS_Helper::def_error('numbetween','end',0,255,$end);
		if($end < $start)
			FWS_Helper::error('$end should be >= $start!');
		
		$this->_noise_color_start = $start;
		$this->_noise_color_end = $end;
	}

	/**
	 * Sets the distance of two noise-pixels in the background. Additionally you
	 * can specify the difference of the distance because this will be done by random
	 * 
	 * @param int $distance the distance
	 * @param int $diff the difference
	 */
	public function set_noise_distance($distance,$diff)
	{
		if(!FWS_Helper::is_integer($distance) || $distance <= 0)
			FWS_Helper::def_error('intgt0','distance',$distance);
		if(!FWS_Helper::is_integer($diff) || $diff < 0)
			FWS_Helper::def_error('intge0','diff',$diff);
		
		$this->_noise_distance = $distance;
		$this->_noise_distance_diff = $diff;
	}

	/**
	 * Sets the number of chars in the image
	 * 
	 * @param int $number the new value
	 */
	public function set_number_of_chars($number)
	{
		if(!FWS_Helper::is_integer($number) || $number <= 0)
			FWS_Helper::def_error('intgt0','number',$number);
		
		$this->_number_of_chars = $number;
		$this->_desired_chars = '';
	}
	
	/**
	 * Sets the chars to use (sets the number of chars implicitly)
	 *
	 * @param string $chars the chars to use
	 */
	public function set_chars($chars)
	{
		$this->_desired_chars = (string)$chars;
		$this->_number_of_chars = strlen($chars);
	}

	/**
	 * Sets the number of shapes that will be put by random in the image to
	 * make it harder to determine which shapes belong to chars and which not.
	 * 
	 * @param int $number the new value
	 */
	public function set_number_of_noise_shapes($number)
	{
		if(!FWS_Helper::is_integer($number) || $number < 0)
			FWS_Helper::def_error('intge0','number',$number);
		
		$this->_number_of_noise_shapes = $number;
	}

	/**
	 * Sets the number and length of shape-lines. That are lines of shapes which
	 * will be put by random in the image to make it harder to read.
	 * 
	 * @param int $number the number of lines
	 * @param int $length the length of each line
	 */
	public function set_shape_lines($number,$length = 10)
	{
		if(!FWS_Helper::is_integer($number) || $number < 0)
			FWS_Helper::def_error('intge0','number',$number);
		if(!FWS_Helper::is_integer($length) || $length < 0)
			FWS_Helper::def_error('intge0','length',$length);
		
		$this->_shape_lines = $number;
		$this->_shape_line_length = $length;
	}

	/**
	 * Sets the size of the shapes
	 * 
	 * @param int $min the minimum size
	 * @param int $max the maximum size
	 */
	public function set_shape_size($min,$max)
	{
		if(!FWS_Helper::is_integer($min) || $min <= 0)
			FWS_Helper::def_error('intgt0','min',$min);
		if(!FWS_Helper::is_integer($max) || $max <= 0)
			FWS_Helper::def_error('intgt0','max',$max);
		if($max < $min)
			FWS_Helper::error('$max should be >= $min!');
		
		$this->_shape_min_size = $min;
		$this->_shape_max_size = $max;
	}

	/**
	 * Sets the propability to use a TTF-font instead of the self-build-one
	 * 
	 * @param float $_char_trans_propability (0.0 .. 1.0)
	 */
	public function set_ttf_font_propability($propability)
	{
		if(!is_numeric($propability) || $propability < 0 || $propability > 1)
			FWS_Helper::def_error('numbetween','propability',0,1,$propability);
		
		$this->_ttf_font_propability = $propability;
	}

	/**
	 * Sets the difference of all TTF-font sizes
	 * 
	 * @param int $size_diff the new value
	 */
	public function set_ttf_font_size_difference($size_diff)
	{
		if(!FWS_Helper::is_integer($size_diff) || $size_diff < 0)
			FWS_Helper::def_error('intge0','size_diff',$size_diff);
		
		$this->_ttf_font_size_difference = $size_diff;
	}

	/**
	 * Adds the given TTF-font to the list of the usable fonts.
	 * 
	 * @param FWS_GD_Font_TTF $font the font to add
	 * @param int $size the size for this font
	 */
	public function add_ttf_font($font,$size)
	{
		if(!($font instanceof FWS_GD_Font_TTF))
			FWS_Helper::def_error('instance','font','FWS_GD_Font_TTF',$font);
		if(!FWS_Helper::is_integer($size) || $size <= 0)
			FWS_Helper::def_error('intgt0','size',$size);
		
		$this->_ttf_fonts[] = array($font,$size);
	}

	/**
	 * Sets wether transparency should be used
	 * 
	 * @param boolean $use_trans the new value
	 */
	public function set_use_transparency($use_trans)
	{
		$this->_use_transparency = (boolean)$use_trans;
	}

	/**
	 * Sets wether the image should be manipulated via a wave
	 * 
	 * @param boolean $use_wave
	 */
	public function set_wave($use_wave,$amplitude = 6,$period = 16)
	{
		if(!FWS_Helper::is_integer($amplitude) || $amplitude <= 0)
			FWS_Helper::def_error('intgt0','amplitude',$amplitude);
		if(!FWS_Helper::is_integer($period) || $period <= 0)
			FWS_Helper::def_error('intgt0','period',$period);
		
		$this->_use_waves = (boolean)$use_wave;
		$this->_wave_amplitude = $amplitude;
		$this->_wave_period = $period;
	}

	/**
	 * Sets the number of horizontal lines that should be drawn on the image to make
	 * it harder to read.
	 * 
	 * @param int $lines the new value
	 */
	public function set_horizontal_lines($lines)
	{
		if(!FWS_Helper::is_integer($lines) || $lines < 0)
			FWS_Helper::def_error('intge0','lines',$lines);
		
		$this->_horizontal_lines = $lines;
	}

	/**
	 * Sets the number of vertical lines that should be drawn on the image to make
	 * it harder to read.
	 * 
	 * @param int $lines the new value
	 */
	public function set_vertical_lines($lines)
	{
		if(!FWS_Helper::is_integer($lines) || $lines < 0)
			FWS_Helper::def_error('intge0','lines',$lines);
		
		$this->_vertical_lines = $lines;
	}
	
	/**
	 * Creates the image and sends it to the browser
	 */
	public function create_image()
	{
		// generate background-color
		if($this->_background_random)
		{
			// generate a bright color because chars on brighter bgs are easier to read
			// (the given dark color will cause the algo to generate a color with a
			// min-brightness-distance which will lead to a bright color because a dark
			// color would have a too low distance)
			$color = new FWS_GD_Color(50,50,50);
			$this->_bg = $color->get_readable_random_foreground();
		}
		else
		{
			$color = mt_rand($this->_background_color_start,$this->_background_color_end);
			$this->_bg = new FWS_GD_Color($color,$color,$color);
		}
		
		// create image
		$truecolor = $this->_use_transparency && function_exists('imagecolorallocatealpha');
		$this->_img = new FWS_GD_Image($this->_width,$this->_height,$truecolor);
		$this->_g = $this->_img->get_graphics();
		$imgres = $this->_img->get_image();
		
		// it does not make sense to draw a color-fade if no transparency is supported
		// because the char-images which are copied into the image would overwrite
		// the colors
		if($truecolor)
		{
			list($bgr,$bgg,$bgb,) = $this->_bg->get_comps();
			$ncolors = mt_rand($this->_background_colors_min,$this->_background_colors_max);
			$colors = array();
			for($i = 0;$i < $ncolors;$i++)
			{
				if($this->_background_random)
				{
					$cr = max(0,min(255,$bgr + mt_rand(20,-20)));
					$cg = max(0,min(255,$bgg + mt_rand(20,-20)));
					$cb = max(0,min(255,$bgb + mt_rand(20,-20)));
				}
				else
					$cr = $cg = $cb = max(0,min(255,$bgr + mt_rand(20,-20)));
				
				$colors[] = array($cr,$cg,$cb);
			}
			
			// draw colorfade in the background
			$rect = new FWS_GD_Rectangle(0,0,$this->_width,$this->_height);
			$this->_g->get_rect_view($rect)->fill_colorfade($colors,1);
		}
		else
			$this->_img->set_background($this->_bg);
		
		// print chars
		$chars = '';
		$x = 5;
		for($i = 0;$i < $this->_number_of_chars;$i++)
		{
			$width = 0;
			$char = $this->_desired_chars != '' ? $this->_desired_chars[$i] : '';
			
			if(count($this->_ttf_fonts) > 0 &&
				$this->_ttf_font_propability > 0 &&
				mt_rand(1,1 / $this->_ttf_font_propability) == 1)
			{
				$this->add_ttf_char($x,$width,$char);
			}
			else
			{
				$height = 0;
				$charimg = $this->create_char_image($width,$height,$char);
				
				// rotate the image
				$angle = mt_rand(-$this->_angle_difference,$this->_angle_difference);
				$charimg->rotate($angle,$this->_bg);
		
				// copy into main image
				$y = mt_rand(0,$this->_height - $height);
				imagecopy($imgres,$charimg->get_image(),$x,$y,0,0,$width,$height);
				$charimg->destroy();
			}
			
			$x += $width + mt_rand(0,10);
			$chars .= $char;
		}
		
		// generate some grey colors
		$grey_colors = array();
		for($i = 0;$i < 10;$i++)
		{
			$grey_colors[] = FWS_GD_Color::get_random_grey_color(
				$this->_noise_color_start,$this->_noise_color_end
			);
		}
		
		// draw noise in the background
		for($y = 0;$y < $this->_height;$y += $this->_noise_distance)
		{
			for($x = 0;$x < $this->_width;$x += $this->_noise_distance)
			{
				$c = $grey_colors[mt_rand(0,9)];
				$xp = $x + mt_rand(-$this->_noise_distance_diff,$this->_noise_distance_diff);
				$yp = $y + mt_rand(-$this->_noise_distance_diff,$this->_noise_distance_diff);
				imagesetpixel($imgres,$xp,$yp,$c->get_color($imgres));
			}
		}
		
		for($i = 0;$i < $this->_number_of_noise_shapes;$i++)
		{
			$color = $this->_bg->get_readable_random_foreground();
			$width = mt_rand($this->_shape_min_size,$this->_shape_max_size);
			$height = mt_rand($this->_shape_min_size,$this->_shape_max_size);
			$x = mt_rand(0,$this->_width - $width);
			$y = mt_rand(0,$this->_height - $height);
			$this->draw_shape($this->_img,$x,$y,$width,$height,$color);
		}
		
		// draw the vertical lines
		for($i = 0;$i < $this->_vertical_lines;$i++)
		{
			$ex = mt_rand(-$this->_width / 8,$this->_width + $this->_width / 8);
			$ey = mt_rand(-$this->_height / 8,$this->_height + $this->_height / 8);
			$ew = mt_rand($this->_width / 2,$this->_width + $this->_width / 2);
			$eh = mt_rand($this->_height * 2,$this->_height * 4);
			$color = $this->_bg->get_readable_random_foreground();
			$ellipse = new FWS_GD_Ellipse(new FWS_GD_Point($ex,$ey),new FWS_GD_Dimension($ew,$eh));
			$this->_g->get_ellipse_view($ellipse)->draw($color);
		}
		
		// draw the horizontal lines
		for($i = 0;$i < $this->_horizontal_lines;$i++)
		{
			$ex = mt_rand(-$this->_width / 8,$this->_width + $this->_width / 8);
			$ey = mt_rand(-$this->_height / 8,$this->_height + $this->_height / 8);
			$ew = mt_rand($this->_width * 1.5,$this->_width * 2);
			$eh = mt_rand($this->_height / 2,$this->_height + $this->_height / 2);
			$color = $this->_bg->get_readable_random_foreground();
			$ellipse = new FWS_GD_Ellipse(new FWS_GD_Point($ex,$ey),new FWS_GD_Dimension($ew,$eh));
			$this->_g->get_ellipse_view($ellipse)->draw($color);
		}
		
		// draw some shape-lines through the image
		for($a = 0;$a < $this->_shape_lines;$a++)
		{
			$factor = mt_rand($this->_shape_min_size,$this->_shape_max_size);
			$x = mt_rand($this->_width / 3,2 * $this->_width / 3);
			$y = mt_rand($this->_width / 3,2 * $this->_height / 3);
			$color = $this->_bg->get_readable_random_foreground();
			$down = mt_rand(0,1) == 0;
			$right = mt_rand(0,1) == 0;
			for($i = 0;$i < $this->_shape_line_length;$i++)
			{
				$this->draw_shape($this->_img,$x,$y,$factor,$factor,$color);
				$x += $right ? $factor : -$factor;
				$y += $down ? $factor : -$factor;
			}
		}
		
		if($this->_use_waves)
		{
			// now add some waves to the image :)
			$this->_img->wave_region(
				$this->_img->get_bounds_rect(),$this->_wave_amplitude,$this->_wave_period
			);
		}
		
		// draw border
		$rect = new FWS_GD_Rectangle(0,0,$this->_width - 1,$this->_height - 1);
		$bordercolor = FWS_GD_Color::$DARK_GRAY;
		$this->_g->get_rect_view($rect)->draw($bordercolor);
		
		$this->_used_chars = $chars;
	}
	
	/**
	 * @return FWS_GD_Image the image-instance
	 */
	public function get_image()
	{
		return $this->_img;
	}
	
	/**
	 * Sends the image to the browser
	 */
	public function send_image()
	{
		$this->_img->send();
	}
	
	/**
	 * Returns the chars that have been drawn on the image. Note that they are just
	 * available <b>after</b> the call of {@link create_image()}!
	 *
	 * @return string the chars
	 */
	public function get_chars()
	{
		return $this->_used_chars;
	}
	
	/**
	 * Adds a TTF-char to the image
	 * 
	 * @param int $x the x-position to use
	 * @param int $width the width of the char
	 * @param char $char the character that has been created ('' = random)
	 */
	private function add_ttf_char($x,&$width,&$char)
	{
		$border = 10;
		if($char == '')
		{
			$char_names = array_keys($this->_chars);
			$char = $char_names[mt_rand(0,count($this->_chars) - 1)];
		}
		$color = $this->_bg->get_readable_random_foreground();
		$angle = mt_rand(-$this->_angle_difference,$this->_angle_difference);
		
		// determine ttf-parameters
		list($ttf,$size) = $this->_ttf_fonts[mt_rand(0,count($this->_ttf_fonts) - 1)];
		$fontsize = mt_rand(
			$size - $this->_ttf_font_size_difference,
			$size + $this->_ttf_font_size_difference
		);
	
		// determine char-width
		$attr = new FWS_GD_TextAttributes($ttf,$fontsize,$color);
		$text = new FWS_GD_Text($char,$attr);
		$w = $text->get_width();
		$width = $w + $border * 2;
		
		// draw char
		$y = mt_rand(-10,10);
		$rect = new FWS_GD_Rectangle($x,$y,$width,$this->_height);
		$this->_g->get_text_view($text)->draw_in_rect($rect,null,null,$angle);
	}
	
	/**
	 * Creates an image which contains character
	 * 
	 * @param int $img_width contains the used image-width after the call
	 * @param int $img_height contains the used image-height after the call
	 * @param char $char the character which the image contains ('' = random)
	 * @return FWS_GD_Image the created image
	 */
	private function create_char_image(&$img_width,&$img_height,&$char)
	{
		$border = 10;
		$factor = mt_rand($this->_shape_min_size,$this->_shape_max_size);
		$xwidth = count($this->_chars[0][0]);
		$ywidth = count($this->_chars[0]);
		$img_width = $xwidth * $factor + $border * 2;
		$img_height = $ywidth * $factor + $border * 2;
		
		// create the image with required size
		$truecolor = $this->_use_transparency && function_exists('imagecolorallocatealpha');
		$img = new FWS_GD_Image($img_width,$img_height,$truecolor);
		
		// set background
		if($truecolor)
			$trans_colour = new FWS_GD_Color(0,0,0,127);
		else
			$trans_colour = $this->_bg;
		$img->set_background($trans_colour);
		
		if($char == '')
		{
			$char_names = array_keys($this->_chars);
			$char = $char_names[mt_rand(0,count($this->_chars) - 1)];
		}
		$color = $this->_bg->get_readable_random_foreground();
		
		// draw the character
		$y = $border;
		foreach($this->_chars[$char] as $line)
		{
			$x = $border;
			foreach($line as $column)
			{
				// do we have to draw this part?
				if($column)
				{
					$rx = $x;
					$ry = $y;
					$rwidth = $factor;
					$rheight = $factor;
					
					// move this part?
					if($this->_char_trans_propability > 0 &&
						mt_rand(1,1 / $this->_char_trans_propability) == 1)
					{
						// move the character by a random number of pixel in a random direction
						$dir = mt_rand(0,3);
						switch($dir)
						{
							case 0:
								$rx += mt_rand($this->_char_trans_min_diff,$this->_char_trans_max_diff);
								break;
							case 1:
								$rx -= mt_rand($this->_char_trans_min_diff,$this->_char_trans_max_diff);
								break;
							case 2:
								$ry += mt_rand($this->_char_trans_min_diff,$this->_char_trans_max_diff);
								break;
							case 3:
								$ry -= mt_rand($this->_char_trans_min_diff,$this->_char_trans_max_diff);
								break;
						}
					}
					
					$used_color = $color;
					if(mt_rand(0,4) == 0)
						$used_color = $this->_bg->get_readable_random_foreground();
					
					$this->draw_shape($img,$rx,$ry,$rwidth,$rheight,$used_color);
				}
				
				// move to the next part in this line
				$x += $factor;
			}
			
			// move to next line
			$y += $factor;
		}
		
		return $img;
	}
	
	/**
	 * Draws a random shape at the given position and given size
	 * 
	 * @param FWS_GD_Image $img the image
	 * @param int $x the x-coordinate
	 * @param int $y the y-coordinate
	 * @param int $width the width of the shape
	 * @param int $height the height of the shape
	 * @param FWS_GD_Color $color the color to use
	 */
	private function draw_shape($img,$x,$y,$width,$height,$color)
	{
		$imgres = $img->get_image();
		$g = $img->get_graphics();
		
		// which shape to use?
		$shape = mt_rand(0,4);
		switch($shape)
		{
			// draws a cross with 2 lines
			case 0:
				imagesetthickness($imgres,2);
				$g->draw_line(new FWS_GD_Point($x,$y),new FWS_GD_Point($x + $width,$y + $width),$color);
				$g->draw_line(new FWS_GD_Point($x,$y + $height),new FWS_GD_Point($x + $width,$y),$color);
				imagesetthickness($imgres,1);
				break;
			// a filled ellipse
			case 1:
				$center = new FWS_GD_Point($x + $width / 2,$y + $height / 2);
				$ellipse = new FWS_GD_Ellipse($center,new FWS_GD_Dimension($width,$height));
				$g->get_ellipse_view($ellipse)->fill($color);
				break;
			// a filled triangle
			case 2:
				$values = array(
					$x + $width / 2,$y,
					$x,$y + $height,
					$x + $width,$y + $height
				);
				imagefilledpolygon($imgres,$values,3,$color->get_color($imgres));
				break;
			// a filled rectangle
			case 3:
				$rect = new FWS_GD_Rectangle($x,$y,$width,$height);
				$g->get_rect_view($rect)->fill($color);
				break;
			// a filled ellipse
			case 4:
				$center = new FWS_GD_Point($x + $width / 2,$y + $height / 2);
				$ellipse = new FWS_GD_Ellipse($center,new FWS_GD_Dimension($width,$height));
				$g->get_ellipse_view($ellipse)->fill($color);
				break;
		}
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>