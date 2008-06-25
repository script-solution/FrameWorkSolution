<?php
/**
 * Contains the color-class
 * 
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// init the static colors
PLIB_GD_Color::$BLACK = new PLIB_GD_Color(0,0,0);
PLIB_GD_Color::$BLUE = new PLIB_GD_Color(0,0,255);
PLIB_GD_Color::$CYAN = new PLIB_GD_Color(0,255,255);
PLIB_GD_Color::$DARK_GRAY = new PLIB_GD_Color(64,64,64);
PLIB_GD_Color::$GRAY = new PLIB_GD_Color(128,128,128);
PLIB_GD_Color::$GREEN = new PLIB_GD_Color(0,255,0);
PLIB_GD_Color::$LIGHT_GRAY = new PLIB_GD_Color(192,192,192);
PLIB_GD_Color::$MAGENTA = new PLIB_GD_Color(255,0,255);
PLIB_GD_Color::$ORANGE = new PLIB_GD_Color(255,200,0);
PLIB_GD_Color::$PINK = new PLIB_GD_Color(255,175,175);
PLIB_GD_Color::$RED = new PLIB_GD_Color(255,0,0);
PLIB_GD_Color::$WHITE = new PLIB_GD_Color(255,255,255);
PLIB_GD_Color::$YELLOW = new PLIB_GD_Color(255,255,0);

/**
 * Represents a color which can be specified and "exported" in multiple formats.
 * An alpha-value is also supported.
 * 
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Color extends PLIB_FullObject
{
	/**
	 * Represents black (0,0,0)
	 */
	public static $BLACK;
	
	/**
	 * Represents blue (0,0,255)
	 */
	public static $BLUE;
	
	/**
	 * Represents cyan (0,255,255)
	 */
	public static $CYAN;
	
	/**
	 * Represents dark gray (64,64,64)
	 */
	public static $DARK_GRAY;
	
	/**
	 * Represents gray (128,128,128)
	 */
	public static $GRAY;
	
	/**
	 * Represents green (0,255,0)
	 */
	public static $GREEN;
	
	/**
	 * Represents light gray (192,192,192)
	 */
	public static $LIGHT_GRAY;
	
	/**
	 * Represents margenta (255,0,255)
	 */
	public static $MAGENTA;
	
	/**
	 * Represents orange (255,200,0)
	 */
	public static $ORANGE;
	
	/**
	 * Represents pink (255,175,175)
	 */
	public static $PINK;
	
	/**
	 * Represents red (255,0,0)
	 */
	public static $RED;
	
	/**
	 * Represents white (255,255,255)
	 */
	public static $WHITE;
	
	/**
	 * Represents yellow (255,255,0)
	 */
	public static $YELLOW;
	
	/**
	 * Cache colors so that we don't create too many
	 *
	 * @var array
	 */
	private static $_color_cache = array();
	
	/**
	 * Calculates a random color
	 * 
	 * @return PLIB_GD_Color the created color
	 */
	public static function get_random_color()
	{
		$comp = mt_rand(0,2);
		$value = mt_rand(0,255);
		switch($comp)
		{
			case 0:
				return new PLIB_GD_Color($value,0,0);
			case 1:
				return new PLIB_GD_Color(0,$value,0);
			default:
				return new PLIB_GD_Color(0,0,$value);
		}
	}
	
	/**
	 * generates a random grey-color
	 * 
	 * @param int $start the start-color (0...255)
	 * @param int $end the end-color (0...255)
	 * @return PLIB_GD_Color the created color
	 */
	public static function get_random_grey_color($start,$end)
	{
		$value = mt_rand($start,$end);
		return new PLIB_GD_Color($value,$value,$value);
	}
	
	/**
	 * The red-value
	 *
	 * @var int
	 */
	private $_r;
	
	/**
	 * The green-value
	 *
	 * @var int
	 */
	private $_g;
	
	/**
	 * The blue-value
	 *
	 * @var int
	 */
	private $_b;
	
	/**
	 * The alpha-value
	 *
	 * @var int
	 */
	private $_alpha;
	
	/**
	 * There are multiple ways to create a color:
	 * <ul>
	 * 	<li>__construct(): will result in #00000000</li>
	 * 	<li>__construct($red,$green,$blue): $red,$green and $blue in the range 0..255</li>
	 * 	<li>__construct($red,$green,$blue,$alpha): $red,$green,$blue and $alpha in the
	 * 	range 0..255</li>
	 * 	<li>__construct($comps): where $comps is an numeric array with the 3 or 4 components</li>
	 * 	<li>__construct($hex): where $hex is the hexadecimal representation
	 * 	(with or without '#')</li>
	 * </ul>
	 */
	public function __construct($arg1 = null,$arg2 = null,$arg3 = null,$arg4 = null)
	{
		parent::__construct();
		
		switch(func_num_args())
		{
			case 0:
				$this->set_comps(array(0,0,0,0));
				break;
			
			case 1:
				if(is_array($arg1))
					$this->set_comps($arg1);
				else
					$this->set_hex($arg1);
				break;
			
			case 3:
				$this->set_comps(array($arg1,$arg2,$arg3,0));
				break;
			
			case 4:
				$this->set_comps(array($arg1,$arg2,$arg3,$arg4));
				break;
			
			default:
				PLIB_Helper::error('Invalid number of arguments!');
				break;
		}
	}
	
	/**
	 * Calculates the brightness of this color. Note that this method ignores the alpha-value!
	 *
	 * @return float the brightness of this color (0...255)
	 */
	public function get_brightness()
	{
		return $this->_r * 0.299 + $this->_g * 0.587 + $this->_b * 0.114;
	}
	
	/**
	 * Makes the color brighter by the given amount
	 * Note that this method does not use the alpha-value!
	 *
	 * @param int $scale the amount to add
	 */
	public function brighter($amount = 5)
	{
		$this->_r = min(255,(int)($this->_r + $amount));
		$this->_g = min(255,(int)($this->_g + $amount));
		$this->_b = min(255,(int)($this->_b + $amount));
	}
	
	/**
	 * Makes the color darker by the given amount
	 * Note that this method does not use the alpha-value!
	 *
	 * @param int $scale the amount to substract
	 */
	public function darker($amount = 5)
	{
		$this->_r = max(0,(int)($this->_r - $amount));
		$this->_g = max(0,(int)($this->_g - $amount));
		$this->_b = max(0,(int)($this->_b - $amount));
	}
	
	/**
	 * Builds the hexadecimal representation of the color (in uppercase)
	 *
	 * @param boolean $with_alpha append the alpha value at the end?
	 * @param boolean $with_hash with the '#' char?
	 * @return string the color in hex-format
	 */
	public function get_hex($with_alpha = true,$with_hash = true)
	{
		$c = $with_hash ? '#' : '';
		$c .= PLIB_StringHelper::ensure_2_chars(dechex($this->_r));
		$c .= PLIB_StringHelper::ensure_2_chars(dechex($this->_g));
		$c .= PLIB_StringHelper::ensure_2_chars(dechex($this->_b));
		if($with_alpha)
			$c .= PLIB_StringHelper::ensure_2_chars(dechex($this->_alpha));
		return PLIB_String::strtoupper($c);
	}
	
	/**
	 * Sets the color to the given hexadecimal representation.
	 * Note that the string has to have at least 6 chars and may have a '#' at the beginning.
	 *
	 * @param string $hex the hexadecimal color
	 */
	public function set_hex($hex)
	{
		if(empty($hex))
			PLIB_Helper::def_error('notempty','hex',$hex);
		
		if(PLIB_String::substr($hex,0,1) == '#')
			$hex = PLIB_String::substr($hex,1);
		
		$len = PLIB_String::strlen($hex);
		if($len != 6 && $len != 8)
			PLIB_Helper::error($hex.' is no valid color!');
		
		$comps = array(
			hexdec(PLIB_String::substr($hex,0,2)),
			hexdec(PLIB_String::substr($hex,2,2)),
			hexdec(PLIB_String::substr($hex,4,2))
		);
		if($len > 6)
			$comps[] = hexdec(PLIB_String::substr($hex,6,2));
		
		$this->set_comps($comps);
	}
	
	/**
	 * @param boolean $alpha do you want to include the alpha-value?
	 * @return array a numeric array with the 3/4 color-components (red,green,blue[,alpha])
	 */
	public function get_comps($alpha = true)
	{
		if($alpha)
			return array($this->_r,$this->_g,$this->_b,$this->_alpha);
		
		return array($this->_r,$this->_g,$this->_b);
	}
	
	/**
	 * Sets the color components to the given ones. You may specify the alpha-value as
	 * 4th element.
	 *
	 * @param array $comps a numeric array with the three or four components
	 */
	public function set_comps($comps)
	{
		if(!is_array($comps) || (count($comps) != 3 && count($comps) != 4))
			PLIB_Helper::error('$comps is no valid array!');
		
		// check all components
		$x = 0;
		foreach($comps as $c)
		{
			if(!$this->_is_valid_comp($c,$x == 3 ? 127 : 255))
				PLIB_Helper::error('Parameter '.($x + 1).' ('.$c.') is no valid color-component!');
			$x++;
		}
		
		// everything is ok
		$this->_r = (int)$comps[0];
		$this->_g = (int)$comps[1];
		$this->_b = (int)$comps[2];
		if($x == 4)
			$this->_alpha = (int)$comps[3];
		else
			$this->_alpha = 0;
	}
	
	/**
	 * @return int the red-value (0..255)
	 */
	public function get_red()
	{
		return $this->_r;
	}
	
	/**
	 * Sets the red-value
	 *
	 * @param int $red the new value (0..255)
	 */
	public function set_red($red)
	{
		if(!$this->_is_valid_comp($red))
			PLIB_Helper::error($red.' is no valid color-component!');
		
		$this->_r = $red;
	}
	
	/**
	 * @return int the green-value (0..255)
	 */
	public function get_green()
	{
		return $this->_g;
	}
	
	/**
	 * Sets the green-value
	 *
	 * @param int $green the new value (0..255)
	 */
	public function set_green($green)
	{
		if(!$this->_is_valid_comp($green))
			PLIB_Helper::error($green.' is no valid color-component!');
		
		$this->_g = $green;
	}
	
	/**
	 * @return int the blue-value (0..255)
	 */
	public function get_blue()
	{
		return $this->_b;
	}
	
	/**
	 * Sets the blue-value
	 *
	 * @param int $blue the new value (0..255)
	 */
	public function set_blue($blue)
	{
		if(!$this->_is_valid_comp($blue))
			PLIB_Helper::error($blue.' is no valid color-component!');
		
		$this->_b = $blue;
	}
	
	/**
	 * @return int the alpha-value (0..127)
	 */
	public function get_alpha()
	{
		return $this->_alpha;
	}
	
	/**
	 * Sets the alpha-value
	 *
	 * @param int $alpha the new value (0..127)
	 */
	public function set_alpha($alpha)
	{
		if(!$this->_is_valid_comp($alpha,127))
			PLIB_Helper::error($alpha.' is no valid alpha-value!');
		
		$this->_alpha = $alpha;
	}
	
	/**
	 * Builds a GD-color with imagecolorallocate() for the given image.
	 * If the alpha-value is NOT 0 imagecolorallocatealpha() will be used!
	 *
	 * @param resource $img the image
	 * @return int the color
	 */
	public function get_color($img)
	{
		// do we have the color in the cache?
		// Note that we cache the colors because if we don't have a truecolor-image
		// GD limits the number of colors to 255 even if the color has already been created
		if(isset(self::$_color_cache[$this->_r.$this->_g.$this->_b.$this->_alpha]))
			return self::$_color_cache[$this->_r.$this->_g.$this->_b.$this->_alpha];
		
		if($this->_alpha != 0)
			$c = imagecolorallocatealpha($img,$this->_r,$this->_g,$this->_b,$this->_alpha);
		else
			$c = imagecolorallocate($img,$this->_r,$this->_g,$this->_b);
		
		// store to cache
		self::$_color_cache[$this->_r.$this->_g.$this->_b.$this->_alpha] = $c;
		
		return $c;
	}

	/**
	 * Uses this color as background and tries to calculate a "good" color which will be easy to
	 * read on the background. Note that this method ignores the alpha-value!
	 * 
	 * @return PLIB_GD_Color the foreground-color
	 */
	public function get_readable_random_foreground()
	{
		// inspired by the formular of W3C:
		// 		((Red value X 299) + (Green value X 587) + (Blue value X 114)) / 1000
		// which tries to calculate the brightness of a color
		
		$bg = $this->get_comps();
		$bg_brightness = $bg[0] * 0.299 + $bg[1] * 0.587 + $bg[2] * 0.114;
		$min_dist = 120;
		
		$color = array();
		$sum = 0;
		$i = 0;
		$multis = array(0.299,0.587,0.114);
		$keys = array(0,1,2);
		
		// we shuffle the keys because we want to start with a random color-component
		// (otherwise the colors would get too equal)
		shuffle($keys);
		list(,$first) = each($keys);
		
		// generate the first color completly random
		$color[$first] = mt_rand(0,255);
		$sum += $color[$first] * $multis[$first];
		$above = $bg_brightness - $sum > $bg_brightness / 2;
		
		// now generate the other colors and try to reach the min-distance
		while(list(,$key) = each($keys))
		{
			$limit = max(0,min(255,abs(($min_dist - abs($bg_brightness - $sum)) / $multis[$key])));
			if($above)
				$color[$key] = mt_rand(0,$limit);
			else
				$color[$key] = mt_rand($limit,255);
			$sum += $color[$key] * $multis[$key];
		}
		
		// is the brightness-difference ok?
		$bright_diff = abs($bg_brightness - $sum);
		if($bright_diff < $min_dist)
		{
			$missing = $min_dist - $bright_diff;
			
			// if our fg-brightness is greater than the bg-brightness we might want to
			// adjust upwards
			if($sum > $bg_brightness)
			{
				// is it not possible to reach the min-distance by adjusting upwards?
				if($sum > (255 - $missing))
				{
					$up = false;
					$missing += $min_dist;
				}
				// ok, so do it
				else
					$up = true;
			}
			// otherwise we might want to adjust downwards
			else
			{
				// not possible?
				if($sum < $missing)
				{
					$up = true;
					$missing += $min_dist;
				}
				else
					$up = false;
			}
			
			// now adjust the colors step by step to reach the min-distance
			for($i = 0;$i < 3;$i++)
			{
				if($up)
					$color[$i] = (int)min(255,$color[$i] + $missing / $multis[$i]);
				else
					$color[$i] = (int)max(0,$color[$i] - $missing / $multis[$i]);
				
				// recalculate missing
				$sum = $color[0] * 0.299 + $color[1] * 0.587 + $color[2] * 0.114;
				$missing = $min_dist - abs($bg_brightness - $sum);
			}
		}
		
		// ensure that the components are valid
		foreach($color as $i => $v)
			$color[$i] = max(0,min(255,$v));
		
		return new PLIB_GD_Color($color);
	}
	
	/**
	 * Checks wether the given color-component is valid (in the range of 0..$limit)
	 *
	 * @param mixed $comp the component
	 * @param int $limit the max-value
	 * @return boolean true if valid
	 */
	private function _is_valid_comp($comp,$limit = 255)
	{
		return PLIB_Helper::is_integer($comp) && $comp >= 0 && $comp <= $limit;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>
