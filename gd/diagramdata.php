<?php
/**
 * Contains the diagram-data-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all diagram-datas. The diagrams use this interface to get the data itself,
 * the text for the data-elements, the fill-color and so on.
 *
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_GD_DiagramData
{
	/**
	 * @return array the data that should be displayed: <code>array(<key> => <numericValue>,...)</code>
	 */
	public function get_data();
	
	/**
	 * Should return the title to display for the given key
	 *
	 * @param int $no the number of the item
	 * @param mixed $key the key from the array of {@link get_data()}
	 * @param mixed $value the value from the array of {@link get_data()}
	 * @param float $percent the percentage
	 * @return string the title to display
	 */
	public function get_title_of($no,$key,$value,$percent);
	
	/**
	 * Should return the attributes that should be used for the given key
	 *
	 * @param int $no the number of the item
	 * @param mixed $key the key from the array of {@link get_data()}
	 * @param mixed $value the value from the array of {@link get_data()}
	 * @param float $percent the percentage
	 * @return PLIB_GD_TextAttributes the attributes
	 */
	public function get_attributes_of($no,$key,$value,$percent);
	
	/**
	 * Should return the fill-color for the given key
	 *
	 * @param int $no the number of the item
	 * @param mixed $key the key from the array of {@link get_data()}
	 * @param mixed $value the value from the array of {@link get_data()}
	 * @param float $percent the percentage
	 * @return PLIB_GD_Color the color
	 */
	public function get_color_of($no,$key,$value,$percent);
	
	/**
	 * @return PLIB_GD_Color the background-color for the diagram
	 */
	public function get_diagram_bg();
	
	/**
	 * @return int the padding for the whole diagram
	 */
	public function get_diagram_pad();
}
?>