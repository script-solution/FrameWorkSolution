<?php
/**
 * Contains the highlight-language-interface
 *
 * @version			$Id: language.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all highlighting-languages
 * 
 * @package			PHPLib
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Highlighting_Language
{
	/**
	 * Represents a number
	 */
	const NUMBER									= 0;
	
	/**
	 * Represents a symbol
	 */
	const SYMBOL									= 1;
	
	/**
	 * Returns the name of the language for which this highlighter is written
	 * 
	 * @return string the language-name
	 */
	public function get_lang_name();

	/**
	 * Returns an array of arrays with all different keyword-types that
	 * should be highlighted. It maps an id to an array with all keywords.
	 * The id will be used for the attributes.
	 * 
	 * @return array the map: <code>array(<id1> => array(<str1>,<str2>,...),...)</code>
	 * @see get_keyword_attributes()
	 * @see get_keyword_settings()
	 */
	public function get_keywords();

	/**
	 * Returns all keywords of the given type
	 * 
	 * @param mixed $key the key in the keywords-list
	 * @return array the list
	 * @see get_keyword_attributes()
	 * @see get_keyword_settings()
	 */
	public function get_keywords_of($key);
	
	/**
	 * Returns a map with multi-line comment-types. The value should be a Pair
	 * for the start and end.
	 * The id will be used for the attributes.
	 * 
	 * @return array the map: <code>array(<id1> => array(<from> => <to>),...)</code>
	 * @see get_ml_comment_attributes()
	 */
	public function get_multi_comment_limiters();
	
	/**
	 * Returns a map with all single-line-comment-starts.
	 * The id will be used for the attributes.
	 * 
	 * @return array the map: <code>array(<id1> => <str1>,...)</code>
	 * @see get_sl_comment_attributes()
	 */
	public function get_single_comments();
	
	/**
	 * A list with all strings that are special symbols in the language
	 * 
	 * @return array all symbols
	 */
	public function get_symbols();
	
	/**
	 * Returns a map with all string-types.
	 * The id will be used for the attributes.
	 * 
	 * @return array the map: <code>array(<id1> => <str1>,...)</code>
	 */
	public function get_string_quotes();
	
	/**
	 * The escape-character for strings
	 * 
	 * @return char the escape-character
	 */
	public function get_escape_char();
	
	/**
	 * Returns a map with regular expressions which should be highlighter.
	 * The id will be used for the attributes.
	 *
	 * @return array the map: <code>array(<id1> => array(<pattern>,<group>,<cs>),...)</code>
	 * @see get_regexp_attributes()
	 */
	public function get_regexps();
	
	/**
	 * Returns settings for the keywords with given id
	 * 
	 * @param id the id in the keyword-list
	 * @return array the settings: <code>array(<cs>,<reqWord>)</code>
	 * @see get_keywords()
	 */
	public function get_keyword_settings($id);
	
	/**
	 * @return wether numbers should be highlighted
	 */
	public function highlight_numbers();
	
	/**
	 * Returns the attributes for the regular expression with given index
	 * 
	 * @param id the id in the regexp-list
	 * @return PLIB_Highlighting_Attributes the attributes-object with the attributes
	 */
	public function get_regexp_attributes($id);
	
	/**
	 * Returns the attributes for the single-line-comment with given index
	 * 
	 * @param id the comment-id
	 * @return PLIB_Highlighting_Attributes the attributes-object with the attributes
	 */
	public function get_sl_comment_attributes($id);

	/**
	 * Returns the attributes for the multi-line-comment with given index
	 * 
	 * @param id the comment-id
	 * @return PLIB_Highlighting_Attributes the attributes-object with the attributes
	 */
	public function get_ml_comment_attributes($id);

	/**
	 * Returns the attributes for the string with given index
	 * 
	 * @param id the string-id
	 * @return PLIB_Highlighting_Attributes the attributes-object with the attributes
	 */
	public function get_string_attributes($id);
	
	/**
	 * Returns the attributes for the keywords of given type
	 * 
	 * @param id the id in the keyword-list
	 * @return PLIB_Highlighting_Attributes the attributes-object with the attributes
	 */
	public function get_keyword_attributes($id);
	
	/**
	 * This method will be used to determine the style of a highlight-element.
	 * You can use arbitrary formating. The <var>element</var> will be one of
	 * the following:
	 * <ul>
	 * 	<li>FUNCTION</li>
	 * 	<li>DATATYPE</li>
	 * 	<li>NUMBER</li>
	 * 	<li>SYMBOL</li>
	 * </ul>
	 * 
	 * @param element the element
	 * @return PLIB_Highlighting_Attributes the attributes for that element
	 */
	public function get_attributes($element);
}
?>