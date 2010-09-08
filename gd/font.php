<?php
/**
 * Contains the font-interface
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all fonts that can be used
 *
 * @package			FrameWorkSolution
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_GD_Font
{
	/**
	 * Determines the bounds of the ttf-text. The result will be:
	 * <code>
	 * 	0  	lower left corner, X position
	 * 	1 	lower left corner, Y position
	 * 	2 	lower right corner, X position
	 * 	3 	lower right corner, Y position
	 * 	4 	upper right corner, X position
	 * 	5 	upper right corner, Y position
	 * 	6 	upper left corner, X position
	 * 	7 	upper left corner, Y position
	 * </code>
	 * 
	 * @param string $text the text
	 * @param FWS_GD_TextAttributes $attr the attributes
	 * @param int $angle the angle with which the text should be drawn
	 * @return array an array with all coordinates. see imagettfbbox()
	 */
	public function get_bounds($text,$attr,$angle = 0);
	
	/**
	 * Determines the size of the text
	 *
	 * @param string $text the text
	 * @param FWS_GD_TextAttributes $attr the attributes
	 * @return FWS_GD_Dimension the size of the text
	 */
	public function get_size($text,$attr);
	
	/**
	 * Draws the given text at the given position and the given attributes
	 *
	 * @param resource $img the image-resource
	 * @param string $text the text to draw
	 * @param FWS_GD_TextAttributes $attr the attributes
	 * @param FWS_GD_Point $pos the position (bottom left at the base-line)
	 * @param int $angle the angle of the text
	 * @return bool true if successfull
	 */
	public function draw($img,$text,$attr,$pos,$angle = 0);
	
	/**
	 * @param FWS_GD_TextAttributes $attr the attributes
	 * @return int the line-size that should be used for underlines or overlines depending on
	 * 	the font-size
	 */
	public function get_line_size($attr);
	
	/**
	 * @param FWS_GD_TextAttributes $attr the attributes
	 * @return int the padding that should be used for underlines or overlines depending on
	 * 	the font-size
	 */
	public function get_line_pad($attr);
}
?>