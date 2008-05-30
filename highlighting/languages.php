<?php
/**
 * Contains the languages-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The container and loader for the highlighting-languages
 * 
 * @package			PHPLib
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Highlighting_Languages extends PLIB_UtilBase
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
		$contents = PLIB_FileUtils::read($file);
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
			PLIB_Helper::error('Please call self::ensure_inited() first!');
		
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
			PLIB_Helper::error('Please call self::ensure_inited() first!');
		
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
			PLIB_Helper::error('Please call self::ensure_inited() first!');
		
		return self::$_langs;
	}
}
?>