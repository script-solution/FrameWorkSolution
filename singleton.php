<?php
/**
 * Contains the singleton-base-class
 *
 * @version			$Id: singleton.php 546 2008-04-10 10:14:49Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This should be the base-class of all singletons. It prevents that an instance can be
 * created and that the object of the class can be cloned.
 * <br>
 * Please provide a static method named get_instance() which looks like the following:
 * <code>
 * 	public static function get_instance()
 * 	{
 * 		return parent::_get_instance(get_class());
 * 	}
 * </code>
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Singleton extends PLIB_FullObject
{
	/**
	 * The instance of this class
	 *
	 * @var PLIB_Singleton
	 */
	private static $_instances = array();
	
	/**
	 * Indicates wether the constructor is locked
	 *
	 * @var boolean
	 */
	private static $_locked = true;
	
	/**
	 * Creates the object of the given class (once) and returns it
	 *
	 * @param string $name the class-name
	 * @return PLIB_Singleton the object of the given class
	 */
	protected static function _get_instance($name)
	{
		if(!isset(self::$_instances[$name]))
		{
			self::$_locked = false;
			self::$_instances[$name] = new $name();
			self::$_locked = true;
		}
		
		return self::$_instances[$name];
	}
	
	/**
	 * Constructor
	 * 
	 * @throws PLIB_Exceptions_UnsupportedMethod if _get_instance() is not used
	 */
	public function __construct()
	{
		if(self::$_locked)
			throw new PLIB_Exceptions_UnsupportedMethod('Since '.get_class($this).' is a singleton'
				.' you can\'t instantiate it but have to use the static get-instance-method!');
		
		parent::__construct();
	}
	
	/**
	 * @throws PLIB_Exceptions_UnsupportedMethod in all cases
	 */
	public function __clone()
	{
		throw new PLIB_Exceptions_UnsupportedMethod(
			'Since '.get_class($this).' is a singleton you can\'t clone it!'
		);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>