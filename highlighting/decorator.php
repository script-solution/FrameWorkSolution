<?php
/**
 * Contains the highlighting-decorator-interface
 *
 * @version			$Id: decorator.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for the highlighting-decorators. That means the classes that use HTML, BBCode
 * or something similar to actually highlight the text.
 *
 * @package			PHPLib
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Highlighting_Decorator
{
	/**
	 * Should open all given attributes and return the result
	 *
	 * @param PLIB_Highlighting_Attributes $attr the attributes
	 * @param string $text the text for which the attributes should be applied
	 * @return string the result
	 */
	public function open_attributes($attr,$text);
	
	/**
	 * Should close all given attributes and return the result
	 *
	 * @param PLIB_Highlighting_Attributes $attr the attributes
	 * @return string the result
	 */
	public function close_attributes($attr);
	
	/**
	 * Gets a text-part from the code (may be highlighted or not) and should return the string
	 * that should be added to the highlighted text. So you may for example apply htmlspecialchars()
	 * on it or something like that.
	 *
	 * @param string $text the text
	 * @return string the resulting text
	 */
	public function get_text($text);
}
?>