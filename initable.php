<?php
/**
 * Contains the initable-interface
 *
 * @version			$Id: initable.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * An interface which indicates that a class can be / has to be initialized.
 * This is used in {@link PLIB_Document} which loads all properties automaticly and
 * initializes them via dependency-lists.
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Initable
{
	/**
	 * Initializes the class
	 */
	public function init();
}