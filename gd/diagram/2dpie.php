<?php
/**
 * Contains the 2d-pie-diagram
 * 
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd.diagram
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Draws the diagram-data as 2-dimensional pie
 *
 * @package			FrameWorkSolution
 * @subpackage	gd.diagram
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Diagram_2dPie extends FWS_Object implements FWS_GD_Diagram
{
	/**
	 * The data to display
	 *
	 * @var FWS_GD_DiagramData
	 */
	private $_data;
	
	/**
	 * The image
	 *
	 * @var FWS_GD_Image
	 */
	private $_image;
	
	/**
	 * Constructor
	 *
	 * @param FWS_GD_DiagramData $data the data to display
	 * @param FWS_GD_Image $image the image
	 */
	public function __construct($data,$image)
	{
		if(!($data instanceof FWS_GD_DiagramData))
			FWS_Helper::def_error('instance','data','FWS_GD_DiagramData',$data);
		if(!($image instanceof FWS_GD_Image))
			FWS_Helper::def_error('instance','image','FWS_GD_Image',$image);
		
		$this->_data = $data;
		$this->_image = $image;
	}
	
	/**
	 * @see FWS_GD_Diagram::draw_diagram()
	 *
	 * @param FWS_GD_Rectangle $rect
	 */
	public function draw_diagram($rect)
	{
		$g = $this->_image->get_graphics();
		$g->get_rect_view($rect)->fill($this->_data->get_diagram_bg());
		
		$pad = $this->_data->get_diagram_pad();
		$rect->shrink($pad * 2,$pad * 2);
		$rect->translate($pad,$pad);
		
		list($x,$y) = $rect->get_location()->get();
		list($w,$h) = $rect->get_size()->get();
		
		$data = $this->_data->get_data();
		$total = array_sum($data);
		$max = 0;
		foreach($data as $val)
		{
			if($val > $max)
				$max = $val;
		}
		
		$radius = min($w / 2,$h / 2);
		$xdiff = $w / 2 - $radius;
		$ydiff = $h / 2 - $radius;
		$ellipse = new FWS_GD_Circle(
			new FWS_GD_Point($x + $radius + $xdiff,$y + $radius + $ydiff),$radius
		);
		list($cx,$cy) = $ellipse->get_center()->get();
		
		// draw the ellipse-parts
		$i = 0;
		$last = 0;
		foreach($data as $title => $value)
		{
			$size = $total == 0 || $value == 0 ? 0 : 360 / ($total / $value);
			$percent = $max == 0 || $value == 0 ? 0 : 100 / ($max / $value);
			$color = $this->_data->get_color_of($i,$title,$value,$percent);

			$g->get_ellipse_view($ellipse)->fill_part($color,$last,$last + $size);
			
			$last += $size;
			$i++;
		}
		
		// now draw the text
		$i = 0;
		$last = 0;
		foreach($data as $title => $value)
		{
			$size = $total == 0 || $value == 0 ? 0 : 360 / ($total / $value);
			$percent = $max == 0 || $value == 0 ? 0 : 100 / ($max / $value);
						
			$p = new FWS_GD_Point(
				ceil($cx + cos(deg2rad($last)) * $radius),
				ceil($cy + sin(deg2rad($last)) * $radius)
			);
			$g->get_line_view(new FWS_GD_Line($p,$ellipse->get_center()))->draw(FWS_GD_Color::$WHITE);
			
			$bartitle = $this->_data->get_title_of($i,$title,$value,$percent);
			$barattr = $this->_data->get_attributes_of($i,$title,$value,$percent);
			$text = new FWS_GD_Text($bartitle,$barattr);
			$g->get_text_view($text)->draw_in_circle_part($ellipse,(int)$last,(int)($last + $size));
			
			$last += $size;
			$i++;
		}
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>
