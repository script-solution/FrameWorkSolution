<?php
/**
 * Contains the base-object for all classes
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This is the base object for all class which can be instantiated in the library.
 * It offers us a way to affect all classes in the library. So for example to print
 * an object or do other things.
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_Object
{
	/**
	 * The next id that should be used
	 *
	 * @var unknown_type
	 */
	private static $_next_id = 1;
	
	/**
	 * The properties
	 *
	 * @var array
	 */
	private static $_properties = array();
	
	/**
	 * Returns the property with given name
	 *
	 * @param string $name the property-name
	 * @return mixed the property
	 */
	public static function get_prop($name)
	{
		if(isset(self::$_properties[$name]))
			return self::$_properties[$name];
		
		PLIB_Helper::error('The property "'.$name.'" does not exist!');
		return null;
	}
	
	/**
	 * Checks wether the given property exists
	 *
	 * @param string $name the property-name
	 * @return boolean true if so
	 */
	public static function prop_exists($name)
	{
		return isset(self::$_properties[$name]);
	}
	
	/**
	 * Sets the property with given name to given value
	 *
	 * @param string $name the property-name
	 * @param mixed $value the property-value
	 */
	public static function set_prop($name,$value)
	{
		self::$_properties[$name] = $value;
	}
	
	/**
	 * Prints all properties
	 */
	public static function print_all_properties()
	{
		$str = '';
		foreach(self::$_properties as $name => $o)
		{
			if(is_object($o))
				$str .= $name.'='.$o->__toString()."\n\n";
			else if(is_array($o))
				$str .= $name.'='.print_r($o,1)."\n\n";
			else
				$str .= $name.'='.$o."\n\n";
		}
		echo '<pre>'.$str.'</pre>';
	}
	
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
	 * Returns the property with given name
	 *
	 * @param string $var the property-name
	 * @return mixed the value
	 */
	public final function __get($var)
	{
		if(isset(self::$_properties[$var]))
			return self::$_properties[$var];
		
		PLIB_Helper::error('The property "'.$var.'" does not exist!');
		return null;
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
	 * <code>array_merge(parent::_get_print_vars(),get_object_vars($this))</code>
	 * By default you should return:
	 * <code>get_object_vars($this)</code>
	 *
	 * @return array an associative array with all variables
	 * @see get_object_vars()
	 */
	protected abstract function _get_print_vars();
	
	/**
	 * The toString-method of all classes that inherit from {@link PLIB_Object}
	 * 
	 * @param boolean $use_html do you want to print it as HTML? (true by default)
	 * @return string information about the object
	 */
	public function __toString($use_html = true)
	{
		return PLIB_PrintUtils::obj_to_string($this,$this->_get_print_vars(),$use_html);
	}
}
?>