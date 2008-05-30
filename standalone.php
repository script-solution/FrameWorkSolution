<?php
/**
 * Contains the standalone-base-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class of all standalone-files.
 * A standalone-file is a kind of module that will not be displayed in the page (between
 * header and footer) but alone. That means you can use this for popups, AJAX-requests,
 * image-generations, ...
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @see PLIB_Helper::get_standalone_name()
 */
abstract class PLIB_Standalone extends PLIB_Component
{
	/**
	 * Should return the template you want to use. An empty value indicates that no template
	 * should be displayed.
	 * By default it returns the suffix of the class-name followed by ".htm".
	 * 
	 * @return string the template to use
	 */
	public function get_template()
	{
		$classname = get_class($this);
		$pos = PLIB_String::strpos($classname,'Standalone_');
		if($pos == -1)
		{
			PLIB_Helper::error('Invalid classname "'.$classname
				.'". Please name it like <prefix>Standalone_<suffix>!');
		}
		
		$prefixlen = $pos + 11;
		return PLIB_String::strtolower(PLIB_String::substr($classname,$prefixlen)).'.htm';
	}
	
	/**
	 * Indicates wether output-buffering should be used.
	 * Please overwrite this method if you want to disable it!
	 *
	 * @return boolean true if so
	 */
	public function use_output_buffering()
	{
		return true;
	}
}
?>