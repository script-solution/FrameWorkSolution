<?php
/**
 * Contains the xml-language-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	highlighting.language
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The default language. Reads the definitions from an XML-file
 *
 * @package			FrameWorkSolution
 * @subpackage	highlighting.language
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Highlighting_Language_XML extends FWS_Object
	implements FWS_Highlighting_Language
{
	/**
	 * Empty attributes
	 *
	 * @var FWS_Highlighting_Attributes
	 */
	private static $_empty_attr = null;
	
	/**
	 * The name of the language
	 * 
	 * @var string
	 */
	private $_name;
	
	/**
	 * All string-quotes:
	 * <code>array(<id1> => <str1>,...)</code>
	 * 
	 * @var array
	 */
	private $_string_quotes = null;
	
	/**
	 * The multiline-comments:
	 * <code>array(<id1> => array(<from> => <to>),...)</code>
	 * 
	 * @var array
	 */
	private $_multiline_comments = null;
	
	/**
	 * The single-line-comments:
	 * <code>array(<id1> => <str1>,...)</code>
	 * 
	 * @var array
	 */
	private $_singleline_comments = null;
	
	/**
	 * All symbols:
	 * <code>array(<symbol1>,...)</code>
	 * 
	 * @var array
	 */
	private $_symbols = null;
	
	/**
	 * The regexp-list:
	 * <code>array(<id1> => array(<pattern>,<group>,<cs>),...)</code>
	 * 
	 * @var array
	 */
	private $_regexps = null;
	
	/**
	 * All different keywords that should be highlighted:
	 * <code>array(<id1> => array(<str1>,<str2>,...),...)</code>
	 * 
	 * @var array
	 */
	private $_keywords = null;
	
	/**
	 * The attributes for numbers
	 * 
	 * @var FWS_Highlighting_Attributes
	 */
	private $_number_attrs = null;
	
	/**
	 * The attributes for symbols
	 * 
	 * @var FWS_Highlighting_Attributes
	 */
	private $_symbol_attrs = null;
	
	/**
	 * The attributes for strings:
	 * <code>array(<id1> => <FWS_Highlighting_Attributes>,...)</code>
	 * 
	 * @var array
	 */
	private $_string_attrs = null;
	
	/**
	 * The attributes for keywords:
	 * <code>array(<id1> => <FWS_Highlighting_Attributes>,...)</code>
	 * 
	 * @var array
	 */
	private $_keyWord_attrs = null;
	
	/**
	 * The attributes for the different multi-line-comments:
	 * <code>array(<id1> => <FWS_Highlighting_Attributes>,...)</code>
	 * 
	 * @var array
	 */
	private $_mlComment_attrs = null;

	/**
	 * The attributes for the different single-line-comments:
	 * <code>array(<id1> => <FWS_Highlighting_Attributes>,...)</code>
	 * 
	 * @var array
	 */
	private $_slComment_attrs = null;
	
	/**
	 * The attributes for the different regexps:
	 * <code>array(<id1> => <FWS_Highlighting_Attributes>,...)</code>
	 * 
	 * @var array
	 */
	private $_regexp_attrs = null;
	
	/**
	 * Stores which keywords are case-sensitive:
	 * <code>array(<id1> => array(<cs>,<reqWord>),...)</code>
	 * 
	 * @var array
	 */
	private $_keyword_settings = array();
	
	/**
	 * Highlight numbers?
	 * 
	 * @var boolean
	 */
	private $_highlight_numbers = false;
	
	/**
	 * The escape-char
	 * 
	 * @var char
	 */
	private $_escape_char = '\\';
	
	/**
	 * Constructor
	 * 
	 * @param string $file the file with the highlighting-information
	 */
	public function __construct($file)
	{
		parent::__construct();
		
		if(self::$_empty_attr === null)
			self::$_empty_attr = new FWS_Highlighting_Attributes();
		
		$this->_read_from_file($file);
	}

	/**
	 * @see FWS_Highlighting_Language::get_attributes()
	 *
	 * @param element $element
	 * @return FWS_Highlighting_Attributes
	 */
	public function get_attributes($element)
	{
		switch($element)
		{
			case self::NUMBER:
				if($this->_number_attrs != null)
					return $this->_number_attrs;
				break;
			
			case self::SYMBOL:
				if($this->_symbol_attrs != null)
					return $this->_symbol_attrs;
				break;
		}
		
		return self::$_empty_attr;
	}

	/**
	 * @see FWS_Highlighting_Language::get_escape_char()
	 *
	 * @return char
	 */
	public function get_escape_char()
	{
		return $this->_escape_char;
	}

	/**
	 * @see FWS_Highlighting_Language::get_keyword_attributes()
	 *
	 * @param id $id
	 * @return FWS_Highlighting_Attributes
	 */
	public function get_keyword_attributes($id)
	{
		return $this->_get_attributes_for($this->_keyWord_attrs,$id);
	}

	/**
	 * @see FWS_Highlighting_Language::get_keyword_settings()
	 *
	 * @param id $id
	 * @return array
	 */
	public function get_keyword_settings($id)
	{
		if($this->_keyword_settings === null || !isset($this->_keyword_settings[$id]))
			return array();
		
		return $this->_keyword_settings[$id];
	}

	/**
	 * @see FWS_Highlighting_Language::get_keywords_of()
	 *
	 * @param mixed $key
	 * @return array
	 */
	public function get_keywords_of($key)
	{
		if($this->_keywords === null || !isset($this->_keywords[$key]))
			return array();
		
		return $this->_keywords[$key];
	}

	/**
	 * @see FWS_Highlighting_Language::get_keywords()
	 *
	 * @return array
	 */
	public function get_keywords()
	{
		return $this->_keywords;
	}

	/**
	 * @see FWS_Highlighting_Language::get_lang_name()
	 *
	 * @return string
	 */
	public function get_lang_name()
	{
		return $this->_name;
	}

	/**
	 * @see FWS_Highlighting_Language::get_ml_comment_attributes()
	 *
	 * @param id $id
	 * @return FWS_Highlighting_Attributes
	 */
	public function get_ml_comment_attributes($id)
	{
		return $this->_get_attributes_for($this->_mlComment_attrs,$id);
	}

	/**
	 * @see FWS_Highlighting_Language::get_multi_comment_limiters()
	 *
	 * @return array
	 */
	public function get_multi_comment_limiters()
	{
		return $this->_get_elements_for($this->_multiline_comments);
	}

	/**
	 * @see FWS_Highlighting_Language::get_regexp_attributes()
	 *
	 * @param id $id
	 * @return FWS_Highlighting_Attributes
	 */
	public function get_regexp_attributes($id)
	{
		return $this->_get_attributes_for($this->_regexp_attrs,$id);
	}

	/**
	 * @see FWS_Highlighting_Language::get_regexps()
	 *
	 * @return array
	 */
	public function get_regexps()
	{
		return $this->_get_elements_for($this->_regexps);
	}

	/**
	 * @see FWS_Highlighting_Language::get_single_comments()
	 *
	 * @return array
	 */
	public function get_single_comments()
	{
		return $this->_get_elements_for($this->_singleline_comments);
	}

	/**
	 * @see FWS_Highlighting_Language::get_sl_comment_attributes()
	 *
	 * @param id $id
	 * @return FWS_Highlighting_Attributes
	 */
	public function get_sl_comment_attributes($id)
	{
		return $this->_get_attributes_for($this->_slComment_attrs,$id);
	}

	/**
	 * @see FWS_Highlighting_Language::get_string_attributes()
	 *
	 * @param id $id
	 * @return FWS_Highlighting_Attributes
	 */
	public function get_string_attributes($id)
	{
		return $this->_get_attributes_for($this->_string_attrs,$id);
	}

	/**
	 * @see FWS_Highlighting_Language::get_string_quotes()
	 *
	 * @return array
	 */
	public function get_string_quotes()
	{
		return $this->_get_elements_for($this->_string_quotes);
	}

	/**
	 * @see FWS_Highlighting_Language::get_symbols()
	 *
	 * @return array
	 */
	public function get_symbols()
	{
		return $this->_get_elements_for($this->_symbols);
	}

	/**
	 * @see FWS_Highlighting_Language::highlight_numbers()
	 *
	 * @return boolean
	 */
	public function highlight_numbers()
	{
		return $this->_highlight_numbers;
	}
	
	/**
	 * Returns the elements for the given map
	 *
	 * @param array $attr the map
	 * @return array the elements
	 */
	private function _get_elements_for($map)
	{
		return $map === null ? array() : $map;
	}
	
	/**
	 * Returns the attributes which should be used for the given id in the given attribute-map
	 *
	 * @param array $attr the attribute-map
	 * @param mixed $id the id
	 * @return FWS_Highlighting_Attributes the attributes
	 */
	private function _get_attributes_for($attr,$id)
	{
		if($attr === null)
			return self::$_empty_attr;
		
		if(!isset($attr[$id]))
			return self::$_empty_attr;
		
		return $attr[$id];
	}
	
	/**
	 * Reads the highlight-settings from file
	 *
	 * @param string $file the file with the highlighting-information
	 */
	private function _read_from_file($file)
	{
		$contents = FWS_FileUtils::read($file);
		$xml = new SimpleXMLElement($contents);
		
		// general
		$this->_name = $xml->name;
		$this->_highlight_numbers = $xml->hlNumbers;
		$this->_escape_char = $xml->escapeChar;
		
		// string quotes
		$this->_string_quotes = array();
		foreach($xml->stringQuotes->def as $def)
			$this->_string_quotes[(string)$def['id']] = (string)$def;
		
		// comments
		$this->_singleline_comments = array();
		foreach($xml->slComments->def as $def)
			$this->_singleline_comments[(string)$def['id']] = (string)$def;
		
		$this->_multiline_comments = array();
		foreach($xml->mlComments->def as $def)
		{
			$this->_multiline_comments[(string)$def['id']] = array(
				(string)$def['start'] => (string)$def['end']
			);
		}
		
		// symbols
		$this->_symbols = array();
		foreach($xml->symbols->kw as $kw)
			$this->_symbols[] = (string)$kw;
		
		// regexp
		$this->_regexps = array();
		foreach($xml->regexps->def as $def)
		{
			$this->_regexps[(string)$def['id']] = array(
				(string)$def['pattern'],(string)$def['group'],(string)$def['cs']
			);
		}
		
		// keywords
		$this->_keywords = array();
		foreach($xml->keywords->def as $def)
		{
			$id = (string)$def['id'];
			$reqword = $def['reqWord'] == 'false' ? 0 : 1;
			$this->_keyword_settings[$id] = array((string)$def['cs'],$reqword);
			
			foreach($def->kw as $kw)
				$this->_keywords[$id][] = (string)$kw;
		}
		
		// attributes
		$this->_symbol_attrs = $this->_build_attributes($xml->colors->symbols);
		$this->_number_attrs = $this->_build_attributes($xml->colors->numbers);
		$this->_keyWord_attrs = array();
		$this->_regexp_attrs = array();
		$this->_mlComment_attrs = array();
		$this->_slComment_attrs = array();
		$this->_string_attrs = array();
		foreach($xml->colors[0] as $child)
		{
			switch($child->getName())
			{
				case 'keywords':
					$this->_keyWord_attrs[(string)$child['id']] = $this->_build_attributes($child);
					break;
				
				case 'strings':
					$this->_string_attrs[(string)$child['id']] = $this->_build_attributes($child);
					break;
				
				case 'regexp':
					$this->_regexp_attrs[(string)$child['id']] = $this->_build_attributes($child);
					break;
				
				case 'mlComments':
					$this->_mlComment_attrs[(string)$child['id']] = $this->_build_attributes($child);
					break;
				
				case 'slComments':
					$this->_slComment_attrs[(string)$child['id']] = $this->_build_attributes($child);
					break;
			}
		}
	}
	
	/**
	 * Builds the attributes for the given xml-element
	 *
	 * @param array $el the xml-element with the attributes
	 * @return FWS_Highlighting_Attributes the attributes
	 */
	private function _build_attributes($el)
	{
		$a = new FWS_Highlighting_Attributes();
		if(isset($el->attr))
		{
			foreach($el->attr as $attr)
			{
				$aid = FWS_Highlighting_Attributes::get_attribute_from_name($attr['name']);
				$a->set($aid,$attr['value']);
			}
		}
		return $a;
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>