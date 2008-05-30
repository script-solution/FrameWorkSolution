<?php
/**
 * Contains the util-base-class
 *
 * @version			$Id: utilbase.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This should be the base-class for all classes that contain just static methods.
 * It prevents the instantiation and cloning of the class.
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_UtilBase
{
	/**
	 * Constructor
	 * 
	 * @throws PLIB_Exceptions_UnsupportedMethod in all cases
	 */
	public function __construct()
	{
		throw new PLIB_Exceptions_UnsupportedMethod('Since '.get_class($this).' contains just static'
			.' methods you can\'t instantiate the class!');
	}
	
	/**
	 * @throws PLIB_Exceptions_UnsupportedMethod in all cases
	 */
	public function __clone()
	{
		throw new PLIB_Exceptions_UnsupportedMethod(
			'Since '.get_class($this).' contains just static methods you can\'t clone the class!'
		);
	}
}
?>