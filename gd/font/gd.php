<?php
/**
 * Contains the gd-font-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd.font
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation for the simple font in gd
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.font
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Font_GD extends FWS_Object implements FWS_GD_Font
{
	/**
	 * @see FWS_GD_Font::draw()
	 *
	 * @param resource $img
	 * @param string $text
	 * @param FWS_GD_TextAttributes $attr
	 * @param FWS_GD_Point $pos
	 * @param int $angle
	 * @return int
	 */
	public function draw($img,$text,$attr,$pos,$angle = 0)
	{
		list(,$h) = $this->get_size($text,$attr)->get();
		if($angle % 180 != 0 && $angle % 90 == 0)
		{
			return imagestringup(
				$img,$attr->get_size(),$pos->get_x() - $h,$pos->get_y(),
				$text,$attr->get_foreground()->get_color($img)
			);
		}
		
		return imagestring(
			$img,$attr->get_size(),$pos->get_x(),$pos->get_y() - $h,
			$text,$attr->get_foreground()->get_color($img)
		);
	}

	/**
	 * @see FWS_GD_Font::get_bounds()
	 *
	 * @param string $text
	 * @param FWS_GD_TextAttributes $attr
	 * @param int $angle
	 * @return array
	 */
	public function get_bounds($text,$attr,$angle = 0)
	{
		list($w,$h) = $this->get_size($text,$attr)->get();
		if($angle % 180 != 0 && $angle % 90 == 0)
		{
			return array(
				0,0,
				0,-$w,
				-$h,-$w,
				-$h,0
			);
		}
		
		return array(
			0,0,
			$w,0,
			$w,-$h,
			0,-$h
		);
	}

	/**
	 * @see FWS_GD_Font::get_size()
	 *
	 * @param string $text
	 * @param FWS_GD_TextAttributes $attr
	 * @return FWS_GD_Dimension
	 */
	public function get_size($text,$attr)
	{
		$fs = $attr->get_size();
		return new FWS_GD_Dimension(
			imagefontwidth($fs) * strlen($text),
			imagefontheight($fs)
		);
	}

	/**
	 * @see FWS_GD_Font::get_line_pad()
	 *
	 * @param FWS_GD_TextAttributes $attr
	 * @return int
	 */
	public function get_line_pad($attr)
	{
		switch($attr->get_size())
		{
			case 1:
			case 2:
			case 3:
			case 4:
				return 1;
			default:
				return 2;
		}
	}

	/**
	 * @see FWS_GD_Font::get_line_size()
	 *
	 * @param FWS_GD_TextAttributes $attr
	 * @return int
	 */
	public function get_line_size($attr)
	{
		switch($attr->get_size())
		{
			case 1:
			case 2:
			case 3:
			case 4:
				return 1;
			default:
				return 2;
		}
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>