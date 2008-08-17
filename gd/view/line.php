<?php
/**
 * Contains the line-view-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The view for a line which allows the painting of it
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.view
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_GD_View_Line extends FWS_GD_View
{
	/**
	 * The line
	 *
	 * @var FWS_GD_Line
	 */
	protected $_line;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_Image $img the image
	 * @param FWS_GD_Line $line the line
	 */
	public function __construct($img,$line)
	{
		parent::__construct($img);
		
		if(!($line instanceof FWS_GD_Line))
			FWS_Helper::def_error('instance','line','FWS_GD_Line',$line);
		
		$this->_line = $line;
	}
	
	/**
	 * Draws the line with the given color
	 *
	 * @param FWS_GD_Color $color
	 * @return the result of imageline()
	 */
	public final function draw($color)
	{
		if(!($color instanceof FWS_GD_Color))
			FWS_Helper::def_error('instance','color','FWS_GD_Color',$color);
		
		$from = $this->_line->get_from();
		$to = $this->_line->get_to();
		$img = $this->get_image_res();
		return imageline(
			$img,
			$from->get_x(),$from->get_y(),
			$to->get_x(),$to->get_y(),
			$color->get_color($img)
		);
	}
	
	/**
	 * Draws this line as color-fade with the given colors and the given step-width
	 *
	 * @param array $colors an array with all colors that should be used
	 * @param int $step the step-size
	 */
	public final function draw_colorfade($colors,$step = 1)
	{
		if(!FWS_Helper::is_integer($step) || $step <= 0)
			FWS_Helper::def_error('intgt0','step',$step);
		
		$distance = $this->_line->get_length();
		$cf = new FWS_GD_ColorFade($distance,$distance / $step,$colors);
		$cfcolors = $cf->get_colors();
		
		$img = $this->get_image_res();
		list($x,$y) = $this->_line->get_from()->get();
		$to = $this->_line->get_to();
		$x_step = ($to->get_x() - $x) / count($cfcolors);
		$y_step = ($to->get_y() - $y) / count($cfcolors);
		foreach($cfcolors as $color)
		{
			imageline($img,$x,$y,$x + $x_step,$y + $y_step,$color->get_color($img));
			$x += $x_step;
			$y += $y_step;
		}
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>