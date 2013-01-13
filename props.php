<?php
/**
 * Contains the properties-class
 * 
 * @package			FrameWorkSolution
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// set the default accessor
FWS_Props::set_accessor(new FWS_PropAccessor());

/**
 * This class contains the property-accessor that should be used. This allows you to exchange the
 * property-accessor to add or change the predefined properties.
 * <br>
 * Properties are intended to provide an easy, uniform and extendable way of accessing objects
 * (or whatever) that are needed in the whole application. It is easy and uniform since you
 * can request a property at all places in your app in the same way and do not have to care about
 * the differences (maybe one class is a singleton with 'get_instance', another one with 'getinst'
 * or a class is no singleton and so on). Additionally the concept is extendable
 * because you can inherit from the prop-loader and -accessor to change predefined properties
 * or add new ones.
 * <br>
 * Another benefit is that the nasty object-passing-by-parameter is not necessary for those objects
 * that are used at arbitrary places. I think in most apps that are user, session, db, locale,
 * input, tpl and msgs.
 * <br>
 * The framework uses the properties, too. Simply because some packages / classes depend on
 * properties (for example cookies depends on input since the cookies have to be read via
 * the input-class). But this shouldn't be a problem because you can inherit from the proploader
 * and therefore change attributes of the properties or exchange them before they are used in
 * the framework (properties are loaded as soon as they are requested the first time and the
 * init.php does not use any of them, so you can set your proploader/propaccessor afterwards
 * and the framework will use your properties).
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @see FWS_PropLoader
 * @see FWS_PropAccessor
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