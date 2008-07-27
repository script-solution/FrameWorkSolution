<?php
/**
 * Contains the properties-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// set the default accessor
PLIB_Props::set_accessor(new PLIB_PropAccessor());

/**
 * This class contains the property-accessor that should be used. This allows you to exchange the
 * property-accessor to add or change the predefined properties.
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Props extends PLIB_UtilBase
{
	/**
	 * The property-accessor
	 *
	 * @var PLIB_PropAccessor
	 */
	private static $_accessor;
	
	/**
	 * @return PLIB_PropAccessor the property-accessor-instance
	 */
	public static function get()
	{
		return self::$_accessor;
	}
	
	/**
	 * Prints all properties
	 */
	public static function print_all()
	{
		echo '<pre>'.PLIB_PrintUtils::to_string(self::$_accessor->get_all()).'</pre>';
	}
	
	/**
	 * Sets the property-accessor for the properties
	 *
	 * @param PLIB_PropAccessor $accessor the accessor
	 */
	public static function set_accessor($accessor)
	{
		if(!($accessor instanceof PLIB_PropAccessor))
			PLIB_Helper::def_error('instance','accessor','PLIB_PropAccessor',$accessor);
		
		self::$_accessor = $accessor;
	}
}
?>