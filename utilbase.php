<?php
/**
 * Contains the util-base-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This should be the base-class for all classes that contain just static methods.
 * It prevents the instantiation and cloning of the class.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_UtilBase
{
	/**
	 * Constructor
	 * 
	 * @throws FWS_Exceptions_UnsupportedMethod in all cases
	 */
	public function __construct()
	{
		throw new FWS_Exceptions_UnsupportedMethod('Since '.get_class($this).' contains just static'
			.' methods you can\'t instantiate the class!');
	}
	
	/**
	 * @throws FWS_Exceptions_UnsupportedMethod in all cases
	 */
	public function __clone()
	{
		throw new FWS_Exceptions_UnsupportedMethod(
			'Since '.get_class($this).' contains just static methods you can\'t clone the class!'
		);
	}
}
?>