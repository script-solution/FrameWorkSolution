<?php
/**
 * Contains the text-attributes-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A collection of attributes for TTF-texts which are used by {@link PLIB_GD_View_Text}.
 *
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_TextAttributes extends PLIB_FullObject
{
	/**
	 * The font to use
	 *
	 * @var PLIB_GD_Font
	 */
	private $_font;
	
	/**
	 * The font-size
	 *
	 * @var int
	 */
	private $_size;
	
	/**
	 * The foreground-color
	 *
	 * @var PLIB_GD_Color
	 */
	private $_foreground;
	
	/**
	 * The background-color
	 *
	 * @var PLIB_GD_Color
	 */
	private $_background = null;
	
	/**
	 * The border-color
	 *
	 * @var PLIB_GD_Color
	 */
	private $_bordercolor = null;
	
	/**
	 * The size of the border
	 *
	 * @var int
	 */
	private $_bordersize = 1;
	
	/**
	 * Wether the text should be underlined
	 *
	 * @var boolean
	 */
	private $_underline = false;
	
	/**
	 * Wether the text should be overlined
	 *
	 * @var boolean
	 */
	private $_overline = false;
	
	/**
	 * Wether the text should be striked through
	 *
	 * @var boolean
	 */
	private $_strike = false;
	
	/**
	 * Wether the text should have a shadow
	 *
	 * @var boolean
	 */
	private $_shadow = false;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Font $font the font of the text
	 * @param int $size the font-size
	 * @param PLIB_GD_Color $foreground the foreground to use (black by default)
	 * @param PLIB_GD_Color $background the background to use (none by default)
	 */
	public function __construct($font,$size,$foreground = null,$background = null)
	{
		parent::__construct();
		
		$this->set_font($font);
		$this->set_size($size);
		$this->set_foreground($foreground !== null ? $foreground : PLIB_GD_Color::$BLACK);
		if($background !== null)
			$this->set_background($background);
	}

	/**
	 * @return PLIB_GD_Font the font
	 */
	public final function get_font()
	{
		return $this->_font;
	}

	/**
	 * Sets the font
	 * 
	 * @param PLIB_GD_Font $font the new value
	 */
	public final function set_font($font)
	{
		if(!($font instanceof PLIB_GD_Font))
			PLIB_Helper::def_error('instance','font','PLIB_GD_Font',$font);
		
		$this->_font = $font;
	}

	/**
	 * @return int the font-size
	 */
	public final function get_size()
	{
		return $this->_size;
	}

	/**
	 * Sets the font-size
	 * 
	 * @param int $size the new value
	 */
	public final function set_size($size)
	{
		if(!PLIB_Helper::is_integer($size) || $size <= 0)
			PLIB_Helper::def_error('intgt0','size',$size);
		
		$this->_size = $size;
	}

	/**
	 * @return PLIB_GD_Color the foreground-color
	 */
	public final function get_foreground()
	{
		return $this->_foreground;
	}

	/**
	 * Sets the foreground-color
	 * 
	 * @param PLIB_GD_Color $color the new color
	 */
	public final function set_foreground($color)
	{
		if(!($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		$this->_foreground = $color;
	}

	/**
	 * @return PLIB_GD_Color the background-color (null = none)
	 */
	public final function get_background()
	{
		return $this->_background;
	}

	/**
	 * Sets the background-color
	 * 
	 * @param PLIB_GD_Color $color the new color (null = none)
	 */
	public final function set_background($color)
	{
		if($color !== null && !($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		$this->_background = $color;
	}
	
	/**
	 * @return int the border-size
	 */
	public final function get_border_size()
	{
		return $this->_bordersize;
	}
	
	/**
	 * Sets the border size
	 * 
	 * @param int $size the new value
	 */
	public final function set_border_size($size)
	{
		if(!PLIB_Helper::is_integer($size) || $size <= 0)
			PLIB_Helper::def_error('intgt0','size',$size);
		
		return $this->_bordersize = $size;
	}

	/**
	 * @return PLIB_GD_Color the border-color (null = none)
	 */
	public final function get_border()
	{
		return $this->_bordercolor;
	}

	/**
	 * Sets the border-color
	 * 
	 * @param PLIB_GD_Color $color the new color (null = none)
	 */
	public final function set_border($color)
	{
		if($color !== null && !($color instanceof PLIB_GD_Color))
			PLIB_Helper::def_error('instance','color','PLIB_GD_Color',$color);
		
		$this->_bordercolor = $color;
	}
	
	/**
	 * @return boolean
	 */
	public final function get_underline()
	{
		return $this->_underline;
	}
	
	/**
	 * Sets wether the text should be underlined
	 *
	 * @param boolean $val the new value
	 */
	public final function set_underline($val)
	{
		$this->_underline = (bool)$val;
	}

	/**
	 * @return boolean
	 */
	public final function get_overline()
	{
		return $this->_overline;
	}
	
	/**
	 * Sets wether the text should be overlined
	 *
	 * @param boolean $val the new value
	 */
	public final function set_overline($val)
	{
		$this->_overline = (bool)$val;
	}

	/**
	 * @return boolean
	 */
	public final function get_strike()
	{
		return $this->_strike;
	}
	
	/**
	 * Sets wether the text should be striked through
	 *
	 * @param boolean $val the new value
	 */
	public final function set_strike($val)
	{
		$this->_strike = (bool)$val;
	}

	/**
	 * @return boolean
	 */
	public final function get_shadow()
	{
		return $this->_shadow;
	}
	
	/**
	 * Sets wether the text should have shadow
	 *
	 * @param boolean $val the new value
	 */
	public final function set_shadow($val)
	{
		$this->_shadow = (bool)$val;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>