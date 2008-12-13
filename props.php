<?php
/**
 * Contains the properties-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// set the default accessor
FWS_Props::set_accessor(new FWS_PropAccessor());

/**
 * This class contains the property-accessor that should be used. This allows you to exchange the
 * property-accessor to add or change the predefined properties.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Props extends FWS_UtilBase
{
	/**
	 * The property-accessor
	 *
	 * @var FWS_PropAccessor
	 */
	private static $_accessor;
	
	/**
	 * @return FWS_PropAccessor the property-accessor-instance
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
		echo '<pre>'.FWS_Printer::to_string(self::$_accessor->get_all()).'</pre>';
	}
	
	/**
	 * Sets the property-accessor for the properties
	 *
	 * @param FWS_PropAccessor $accessor the accessor
	 */
	public static function set_accessor($accessor)
	{
		if(!($accessor instanceof FWS_PropAccessor))
			FWS_Helper::def_error('instance','accessor','FWS_PropAccessor',$accessor);
		
		self::$_accessor = $accessor;
	}
}
?>