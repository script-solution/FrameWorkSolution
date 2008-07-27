<?php
/**
 * Contains the 2d-bars-diagram
 * 
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd.diagram
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Draws the diagram-data as 2-dimensional bars
 *
 * @package			PHPLib
 * @subpackage	gd.diagram
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Diagram_2dBars extends PLIB_Object implements PLIB_GD_Diagram
{
	/**
	 * The data to display
	 *
	 * @var PLIB_GD_DiagramData
	 */
	private $_data;
	
	/**
	 * The image
	 *
	 * @var PLIB_GD_Image
	 */
	private $_image;
	
	/**
	 * Constructor
	 *
	 * @param PLIB_GD_DiagramData $data the data to display
	 * @param PLIB_GD_Image $image the image
	 */
	public function __construct($data,$image)
	{
		if(!($data instanceof PLIB_GD_DiagramData))
			PLIB_Helper::def_error('instance','data','PLIB_GD_DiagramData',$data);
		if(!($image instanceof PLIB_GD_Image))
			PLIB_Helper::def_error('instance','image','PLIB_GD_Image',$image);
		
		$this->_data = $data;
		$this->_image = $image;
	}
	
	/**
	 * @see PLIB_GD_Diagram::draw_diagram()
	 *
	 * @param PLIB_GD_Rectangle $rect
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
		$datasize = count($data);
		$spacing = 5;
		$barwidth = ($w - ($datasize - 1) * $spacing) / $datasize;
		
		$max = 0;
		foreach($data as $val)
		{
			if($val > $max)
				$max = $val;
		}
		
		$i = 0;
		$textpad = 2;
		$textpos = PLIB_GD_BoxPosition::$BOTTOM_CENTER;
		foreach($data as $title => $value)
		{
			$ypad = $max == 0 ? 0 : $h * ($value / $max);
			$percent = $max == 0 || $value == 0 ? 0 : 100 / ($max / $value);
			$barrect = new PLIB_GD_Rectangle($x,$y + ($h - $ypad),$barwidth,$ypad);
			$barcolor = $this->_data->get_color_of($i,$title,$value,$percent);
			$g->get_rect_view($barrect)->fill_3d($barcolor);
			
			$bartitle = $this->_data->get_title_of($i,$title,$value,$percent);
			$barattr = $this->_data->get_attributes_of($i,$title,$value,$percent);
			$text = new PLIB_GD_Text($bartitle,$barattr);
			$g->get_text_view($text)->draw_in_rect_vertically($barrect,$textpad,$textpos);
			
			$x += $barwidth + $spacing;
			$i++;
		}
	}

	/**
	 * @see PLIB_Object::get_print_vars()
	 *
	 * @return array
	 */
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>