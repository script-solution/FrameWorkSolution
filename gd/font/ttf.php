<?php
/**
 * Contains the captcha-class
 * 
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	gd.font
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a TTF-font
 * 
 * @package			FrameWorkSolution
 * @subpackage	gd.font
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_GD_Font_TTF extends FWS_Object implements FWS_GD_Font
{
	/**
	 * The folder of the font-file
	 *
	 * @var string
	 */
	private $_folder;
	
	/**
	 * The name of the font
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * The font for the GD-functions
	 *
	 * @var string
	 */
	private $_font;
	
	/**
	 * Constructor
	 *
	 * @param string $file the font-file
	 */
	public function __construct($file)
	{
		parent::__construct();
		
		if(empty($file) || !is_file($file))
			FWS_Helper::error('"'.$file.'" is no file!');
		
		$this->_folder = dirname($file).'/';
		$this->_name = basename($file);
		$this->_font = $this->_get_ttf_font_path();
	}
	
	/**
	 * @return string the font for the GD-functions
	 */
	public function get_font()
	{
		return $this->_font;
	}
	
	public function get_bounds($text,$attr,$angle = 0)
	{
		// compute size with a zero angle
		$coords = imagettfbbox(
			$attr->get_size(),0,$this->_font,$text
		);
		
		// convert angle to radians
		$a = deg2rad($angle);
		
		// compute some usefull values
		$ca = cos($a);
		$sa = sin($a);
		
		// perform transformations
		$bounds = array();
		for($i = 0;$i < 7;$i += 2)
		{
			$bounds[$i] = round($coords[$i] * $ca + $coords[$i + 1] * $sa);
			$bounds[$i + 1] = round($coords[$i + 1] * $ca - $coords[$i] * $sa);
		}
		
		return $bounds;
	}
	
	public function get_size($text,$attr)
	{
		$bounds = imagettfbbox(
			$attr->get_size(),0,$attr->get_font()->get_font(),$text
		);
		
		$min_x = min($bounds[0],$bounds[2],$bounds[4],$bounds[6]);
		$max_x = max($bounds[0],$bounds[2],$bounds[4],$bounds[6]);
		
		$min_y = min($bounds[1],$bounds[3],$bounds[5],$bounds[7]);
		$max_y = max($bounds[1],$bounds[3],$bounds[5],$bounds[7]);
		return new FWS_GD_Dimension($max_x - $min_x,$max_y - $min_y);
	}
	
	public function draw($img,$text,$attr,$pos,$angle = 0)
	{
		return imagettftext(
			$img,
			$attr->get_size(),$angle,
			(int)$pos->get_x(),(int)$pos->get_y(),
			$attr->get_foreground()->get_color($img),
			$this->_font,
			$text
		) !== false;
	}
	
	public function get_line_size($attr)
	{
		return (int)round($attr->get_size() / 10);
	}
	
	public function get_line_pad($attr)
	{
		return 1 + (int)round($attr->get_size() / 10);
	}
	
	/**
	 * determines the ttf-font-path and returns it for the given file
	 *
	 * @return string the ttf-font-path
	 */
	private function _get_ttf_font_path()
	{
		$folder = $this->_folder;
		$filename = $this->_name;
		
		// correct folder and filename if necessary
		if($folder != '' && FWS_String::substr($folder,-1,1) != '/')
			$folder .= '/';
	
		// ensure it is a file
		$filename = basename($filename);
	
		if(function_exists('gd_info'))
			$gd = gd_info();
		// if the function is not defined, we guess that we have an old GD-version
		else
			$gd = array('GD Version' => '1.0');
	
		$ttf_font = $folder.$filename;
		$result = array();
		preg_match('/[0-9\.]+/',$gd['GD Version'],$result);
		if($result[0] < '2.0.18')
		{
			$file_without_ext = FWS_String::substr($filename,0,FWS_String::strrpos($filename,'.'));
			$ttf_font					= $file_without_ext;
			if(!@putenv('GDFONTPATH='.$folder))
				$ttf_font				= $folder.$filename;
		}
	
		return $ttf_font;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>