<?php
/**
 * Contains the attributes-class for the highlighting
 * 
 * @package			FrameWorkSolution
 * @subpackage	highlighting
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

/**
 * Stores all attributes that should be applied to some text
 * 
 * @package			FrameWorkSolution
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Highlighting_Attributes extends FWS_Object
{
	/**
	 * Represents the value "sup" for the position
	 */
	const POS_SUPERSCRIPT		= 1;
	
	/**
	 * Represents the value "sub" for the position
	 */
	const POS_SUBSCRIPT			= 2;
	
	/**
	 * Represents the default text-position
	 */
	const POS_NORMAL				= 3;
	

	/**
	 * The highlight-attribute<br>
	 * The value is a java.awt.Color object
	 */
	const HIGHLIGHT			= 11;

	/**
	 * Background-color<br>
	 * The value is a java.awt.Color object
	 */
	const BG_COLOR			= 10;

	/**
	 * An email-address<br>
	 * The value is a java.lang.String object
	 */
	const EMAIL					= 9;

	/**
	 * An URL<br>
	 * The value is a java.lang.String object
	 */
	const URL						= 8;

	/**
	 * The position of the text<br>
	 * The value is a java.lang.Byte object with the values: TextAttributes.POS_NORMAL,
	 * TextAttributes.POS_SUPERSCRIPT or TextAttributes.POS_SUBSCRIPT
	 */
	const POSITION			= 7;

	/**
	 * Strike<br>
	 * The value is a java.lang.Boolean object
	 */
	const STRIKE				= 6;

	/**
	 * Underlined<br>
	 * The value is a java.lang.Boolean object
	 */
	const UNDERLINE			= 5;

	/**
	 * Italic<br>
	 * The value is a java.lang.Boolean object
	 */
	const ITALIC				= 4;

	/**
	 * Bold<br>
	 * The value is a java.lang.Boolean object
	 */
	const BOLD					= 3;

	/**
	 * The font-color<br>
	 * The value is a java.awt.Color object
	 */
	const FONT_COLOR		= 2;

	/**
	 * The font-size<br>
	 * The value is a java.lang.Integer object
	 */
	const FONT_SIZE			= 1;

	/**
	 * The font-family<br>
	 * The value is simply the string with the font-family-name (case-insensitive)
	 */
	const FONT_FAMILY		= 0;
	
	
	/**
	 * The allowed fonts
	 * 
	 * @var array
	 */
	private static $_allowedFonts = array(
		"verdana","tahoma","courier new","times new roman","sans serif",
		"arial","comic sans ms"
	);

	/**
	 * debugging-information
	 * 
	 * @param int $attribute the attribute you are looking for
	 * @return string the name of the given attribute
	 * @see get_attribute_from_name()
	 */
	public static function get_attribute_name($attribute)
	{
		switch($attribute)
		{
			case self::FONT_FAMILY:
				return 'fontFamily';
			case self::FONT_SIZE:
				return 'fontSize';
			case self::FONT_COLOR:
				return 'fontColor';
			case self::BOLD:
				return 'bold';
			case self::ITALIC:
				return 'italic';
			case self::UNDERLINE:
				return 'underline';
			case self::STRIKE:
				return 'strike';
			case self::POSITION:
				return 'pos';
			case self::URL:
				return 'URL';
			case self::EMAIL:
				return 'email';
			case self::BG_COLOR:
				return 'bgColor';

			default:
				FWS_Helper::error('The attribute "'.$attribute.'" is unknown!');
				return '';
		}
	}
	
	/**
	 * Determines the attribute for the given name
	 * 
	 * @param string $name the attribute-name
	 * @return int the attribute or null
	 * @see get_attribute_name()
	 */
	public static function get_attribute_from_name($name)
	{
		switch($name)
		{
			case 'fontFamily':
				return self::FONT_FAMILY;
			case 'fontSize':
				return self::FONT_SIZE;
			case 'fontColor':
				return self::FONT_COLOR;
			case 'bold':
				return self::BOLD;
			case 'italic':
				return self::ITALIC;
			case 'underline':
				return self::UNDERLINE;
			case 'strike':
				return self::STRIKE;
			case 'pos':
				return self::POSITION;
			case 'URL':
				return self::URL;
			case 'email':
				return self::EMAIL;
			case 'bgColor':
				return self::BG_COLOR;
			
			default:
				FWS_Helper::error('The attribute "'.$name.'" is unknown!');
				return -1;
		}
	}
	
	/**
	 * Ensures that the value for the given attribute is valid. If it is not
	 * the corresponding default value will be returned
	 * 
	 * @param int $attribute the attibute
	 * @param mixed $val the value to check
	 * @return mixed the valid value for the attribute
	 */
	public static function get_valid_value_for($attribute,$val)
	{
		if(self::is_toggle_attribute($attribute))
			return $attribute == 'true' || $attribute == '1';
		
		if($attribute == self::FONT_FAMILY)
		{
			if(isset(self::$_allowedFonts[FWS_String::strtolower($val)]))
				return $val;
			
			return "verdana";
		}
		
		if($attribute == self::FONT_SIZE)
		{
			if($val >= 0 && $val <= 29)
				return $val;
			
			return 12;
		}
		
		if($attribute == self::FONT_COLOR || $attribute == self::BG_COLOR)
		{
			if(preg_match('/^#[a-f0-9]{6}$/i',$val))
				return $val;
			
			return '#000000';
		}
		
		if($attribute == self::POSITION)
		{
			if($val == self::POS_SUBSCRIPT)
				return $val;
			return self::POS_SUPERSCRIPT;
		}
	
		return $val;
	}
	
	/**
	 * checks whether the given attribute is a toggle-attribute
	 * 
	 * @param int $attribute the attribute to check
	 * @return boolean true if it is a toggle-attribute
	 */
	public static function is_toggle_attribute($attribute)
	{
		return $attribute == self::BOLD || $attribute == self::ITALIC ||
					 $attribute == self::UNDERLINE || $attribute == self::STRIKE;
	}
	
	
	/**
	 * All set attributes:
	 * <code>
	 * 	array(
	 * 		<attrId> => <value>,
	 * 		...
	 * 	)
	 * </code>
	 *
	 * @var array
	 */
	private $_attributes = array();
	
	/**
	 * Returns the value of the given attribute.
	 * The type of <code>$value</code> depends on the attribute:
	 * <ul>
	 * 	<li>FONT_COLOR, BG_COLOR, HIGHLIGHT: hexadecimal color, e.g. '#123456'</li>
	 * 	<li>BOLD, ITALIC, UNDERLINE, STRIKE: boolean</li>
	 * 	<li>FONT_SIZE: integer</li>
	 * 	<li>FONT_FAMILY, EMAIL, URL: string</li>
	 * 	<li>POSITION: integer: {@link self::POS_NORMAL},
	 * 		{@link self::POS_SUPERSCRIPT} or {@link self::POS_SUBSCRIPT}</li>
	 * </ul>
	 *
	 * @param int $attribute the attribute-id
	 * @return mixed the value of the given attribute or null if not set
	 */
	public function get($attribute)
	{
		if(isset($this->_attributes[$attribute]))
			return $this->_attributes[$attribute];
		
		return null;
	}
	
	/**
	 * Returns the list of set attributes:
	 * <code>
	 * 	array(
	 * 		<attrId> => <value>,
	 * 		...
	 * 	)
	 * </code>
	 *
	 * @return array the attributes
	 */
	public function get_all()
	{
		return $this->_attributes;
	}
	
	/**
	 * Sets the given attribute to given value.
	 * The type of <code>$value</code> depends on the attribute:
	 * <ul>
	 * 	<li>FONT_COLOR, BG_COLOR, HIGHLIGHT: hexadecimal color, e.g. '#123456'</li>
	 * 	<li>BOLD, ITALIC, UNDERLINE, STRIKE: boolean</li>
	 * 	<li>FONT_SIZE: integer</li>
	 * 	<li>FONT_FAMILY, EMAIL, URL: string</li>
	 * 	<li>POSITION: integer: {@link self::POS_NORMAL},
	 * 		{@link self::POS_SUPERSCRIPT} or {@link self::POS_SUBSCRIPT}</li>
	 * </ul>
	 *
	 * @param int $attribute the attribute-id
	 * @param mixed $value the value
	 */
	public function set($attribute,$value)
	{
		if(!FWS_Helper::is_integer($attribute) || $attribute < 0 || $attribute > 11)
			FWS_Helper::def_error('numbetween','attribute',0,11,$attribute);
		
		$this->_attributes[$attribute] = self::get_valid_value_for($attribute,$value);
	}
	
	/**
	 * Removes the given attribute from the set ones
	 *
	 * @param int $attribute the attribute-id
	 */
	public function remove($attribute)
	{
		unset($this->_attributes[$attribute]);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>