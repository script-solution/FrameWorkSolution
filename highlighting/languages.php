<?php
/**
 * Contains the languages-class
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
 * The container and loader for the highlighting-languages
 * 
 * @package			FrameWorkSolution
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Highlighting_Languages extends FWS_UtilBase
{
	/**
	 * All known languages
	 *
	 * @var array
	 */
	private static $_langs = null;
	
	/**
	 * Ensures that the languages are loaded
	 *
	 * @param string $file the file with the languages
	 */
	public static function ensure_inited($file)
	{
		if(self::$_langs !== null)
			return;
		
		self::$_langs = array();
		$contents = FWS_FileUtils::read($file);
		$xml = new SimpleXMLElement($contents);
		
		if(isset($xml->language))
		{
			$folder = dirname($file);
			foreach($xml->language as $language)
			{
				if(isset($language['id']) && isset($language['name']) && isset($language['file']))
				{
					$id = (string)$language['id'];
					self::$_langs[$id] = array(
						'id' => $id,
						'name' => (string)$language['name'],
						'file' => $folder.'/'.(string)$language['file']
					);
				}
			}
		}
	}
	
	/**
	 * Checks wether the given language exists.
	 * Please call self::ensure_inited() first!
	 *
	 * @param string $id the language-id
	 * @return boolean true if it exists
	 */
	public static function contains_lang($id)
	{
		if(self::$_langs === null)
			FWS_Helper::error('Please call self::ensure_inited() first!');
		
		return isset(self::$_langs[$id]);
	}
	
	/**
	 * Returns the name of the given language.
	 * Please call self::ensure_inited() first!
	 *
	 * @param string $id the language-id
	 * @return string the language-name
	 */
	public static function get_language_name($id)
	{
		if(self::$_langs === null)
			FWS_Helper::error('Please call self::ensure_inited() first!');
		
		if(!isset(self::$_langs[$id]))
			return null;
		
		return self::$_langs[$id]['name'];
	}
	
	/**
	 * Please call self::ensure_inited() first!
	 * 
	 * @return array all registered languages
	 */
	public static function get_languages()
	{
		if(self::$_langs === null)
			FWS_Helper::error('Please call self::ensure_inited() first!');
		
		return self::$_langs;
	}
}
?>