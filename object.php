<?php
/**
 * Contains the base-object for all classes
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This is the base object for all class which can be instantiated in the framework.
 * It offers us a way to affect all classes in the framework. So for example to print
 * an object or do other things.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Object
{
	/**
	 * The next id that should be used
	 *
	 * @var unknown_type
	 */
	private static $_next_id = 1;
	
	/**
	 * The id of this object
	 *
	 * @var int
	 */
	protected $_object_id;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_object_id = self::$_next_id++;
	}
	
	public function __clone()
	{
		// we have to assign a new id to cloned objects
		$this->_object_id = self::$_next_id++;
	}
	
	/**
	 * @return int the id of this object
	 */
	public final function get_object_id()
	{
		return $this->_object_id;
	}
	
	/**
	 * Prints this object. The method does the following:
	 * <code>
	 * echo '<pre>'.$this.'</pre>';
	 * </code>
	 * 
	 * @param boolean $use_html print the object as HTML?
	 */
	public final function print_obj($use_html = true)
	{
		if($use_html)
			echo $this->__toString(true);
		else
			echo '<pre>'.$this->__toString(false).'</pre>';
	}
	
	/**
	 * Should return an array with all variables of the class that should be printed
	 * by __toString(). If you overwrite this method you may use:
	 * <code>array_merge(parent::get_print_vars(),get_object_vars($this))</code>
	 * By default you should return:
	 * <code>get_object_vars($this)</code>
	 *
	 * @return array an associative array with all variables
	 * @see get_object_vars()
	 */
	protected abstract function get_print_vars();
	
	/**
	 * The toString-method of all classes that inherit from {@link FWS_Object}
	 * 
	 * @param boolean $use_html do you want to print it as HTML? (true by default)
	 * @return string information about the object
	 */
	public function __toString($use_html = true)
	{
		return FWS_PrintUtils::obj_to_string($this,$this->get_print_vars(),$use_html);
	}
}
?>