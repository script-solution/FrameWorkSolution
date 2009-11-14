<?php
/**
 * Contains the highlighting-processor-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The highlighting-processor which actually highlights the code.
 * Usage:
 * <code>
 * $lang = new FWS_Highlighting_Language_XML('yourPath/php.xml');
 * $decorator = new FWS_Highlighting_Decorator_HTML();
 * $hl = new FWS_Highlighting_Processor($yourText,$lang,$decorator);
 * echo $hl->highlight();
 * </code>
 * Please note that the processor assumes that newlines are represented just by '\n'!
 *
 * @package			FrameWorkSolution
 * @subpackage	highlighting
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Highlighting_Processor extends FWS_Object
{
	/**
	 * Represents a string
	 */
	const STRING				= 0;
	
	/**
	 * Represents a single-line-comment
	 */
	const SL_COMMENT		= 1;
	
	/**
	 * Represents a multi-line-comment
	 */
	const ML_COMMENT		= 2;
	
	/**
	 * Represents a number
	 */
	const NUMBER				= 3;
	
	/**
	 * Represents a regular expression
	 */
	const REGEX					= 4;
	
	/**
	 * Represents a symbol
	 */
	const SYMBOL				= 5;
	
	/**
	 * Represents a keyword
	 */
	const KEYWORDS			= 6;
	
	/**
	 * The text
	 *
	 * @var string
	 */
	private $_text;
	
	/**
	 * The highlighter
	 *
	 * @var FWS_Highlighting_Language
	 */
	private $_hl;
	
	/**
	 * The decorator
	 *
	 * @var FWS_Highlighting_Decorator
	 */
	private $_decorator;
	
	/**
	 * The area-counter
	 *
	 * @var int
	 */
	private $_area_counter = 0;
	
	/**
	 * An array with all denied positions
	 *
	 * @var array
	 */
	private $_area_positions = array();
	
	/**
	 * Constructor
	 *
	 * @param string $text the text to highlight
	 * @param FWS_Highlighting_Language $hl the highlighter
	 * @param FWS_Highlighting_Decorator $decorator the decorator
	 */
	public function __construct($text,$hl,$decorator)
	{
		if(!($hl instanceof FWS_Highlighting_Language))
			FWS_Helper::def_error('instance','hl','FWS_Highlighting_Language',$hl);
		if(!($decorator instanceof FWS_Highlighting_Decorator))
			FWS_Helper::def_error('instance','decorator','FWS_Highlighting_Decorator',$decorator);
		
		$this->_text = (string)$text;
		$this->_hl = $hl;
		$this->_decorator = $decorator;
	}
	
	/**
	 * Highlights the specified text and returns the result
	 * 
	 * @return string the result-text
	 */
	public function highlight()
	{
		// copy to local variables
		$t = $this->_text;
		$hl = $this->_hl;
		
		$deniedareas = array();
		$string_quotes = $hl->get_string_quotes();
		$slTypes = $hl->get_single_comments();
		$mlTypes = $hl->get_multi_comment_limiters();
		$escChar = $hl->get_escape_char();
		
		// build regex to find all string-quotes, ml- and sl-comments
		$regex = '/(?:'."\\n|";
		foreach($string_quotes as $q)
			$regex .= preg_quote($q,'/').'|';
		foreach($slTypes as $sl)
			$regex .= preg_quote($sl,'/').'|';
		foreach($mlTypes as $ml)
			$regex .= preg_quote(key($ml),'/').'|'.preg_quote(current($ml),'/').'|';
		$regex = FWS_String::substr($regex,0,-1).')/s';
		$matches = array();
		preg_match_all($regex,$t,$matches,PREG_OFFSET_CAPTURE);
		$matches = $matches[0];
	
		$unicode = array();
		// if no mb-functions are used (e.g. they are not supported) we simply use the byte-positions
		// as offset
		if(FWS_String::get_use_mb_functions())
		{
			// unfortunatly preg_match_all() with PREG_OFFSET_CAPTURE does always count bytes for the
			// offset (this doesn't change with modifier "u").
			// therefore we search for all multibyte characters in the text and save their position
			preg_match_all('/[\x{80}-\x{10FFFF}]/u',$t,$unicode,PREG_OFFSET_CAPTURE);
			$unicode = $unicode[0];
		}
		$uclen = count($unicode);
		
		// build fast-access arrays
		$mlstarts = array();
		$mlends = array();
		foreach($mlTypes as $id => $ml)
		{
			$mlstarts[key($ml)] = $id;
			$mlends[$id] = current($ml);
		}
		$sls = array_flip($slTypes);
		$strqs = array_flip($string_quotes);
		
		// init some flags and vars
		$cStart = -1;
		$sStart = -1;
		$sStartChar = 0;
		$commentId = null;
		$inStr = false;
		$inComment = false;
		$strlen = FWS_String::strlen($t);
		
		$len = count($matches);
		
		// at first we have to correct the positions for multibyte strings
		if($uclen > 0)
		{
			$ucchars = 0;
			$ucpos = 0;
			for($i = 0;$i < $len;$i++)
			{
				list(,$mpos) = $matches[$i];
				
				// count all multibyte characters in front of the current position
				for(;$ucpos < $uclen;$ucpos++)
				{
					if($unicode[$ucpos][1] > $mpos)
						break;
					// we have to count the number of bytes because it may be more than 2 bytes long
					$ucchars += strlen($unicode[$ucpos][0]) - 1;
				}
				
				// substract the number 
				$matches[$i][1] -= $ucchars;
			}
		}
		
		// now go through the matches and search for comments and strings
		for($i = 0;$i < $len;$i++)
		{
			list($mstr,$mpos) = $matches[$i];
			
			// string
			if(!$inComment && isset($strqs[$mstr]))
			{
				// is it not escaped?
				$c = 0;
				for($x = $mpos - 1;$x >= 0;$x--)
				{
					$char = FWS_String::substr($t,$x,1);
					if($char != $escChar)
						break;
					$c++;
				}
				
				if($c % 2 == 0)
				{
					// is it a closing char?
					if($mstr == $sStartChar && $inStr)
					{
						$inStr = false;
						$this->_add_area($deniedareas,$sStart,$mpos + 1,self::STRING,$strqs[$mstr]);
					}
					// just add the start if it is a real start (not in other elements)
					else if(!$inStr)
					{
						$sStart = $mpos;
						$sStartChar = $mstr;
						$inStr = true;
					}
				}
			}
			// comments
			else if(!$inStr)
			{
				// search for comment starts
				if(!$inComment)
				{
					// single line
					if(isset($sls[$mstr]))
					{
						// search for the newline
						$lineend = $strlen;
						for($i++;$i < $len;$i++)
						{
							$lineend = $matches[$i][1];
							if($matches[$i][0] == "\n")
								break;
						}
						
						$this->_add_area($deniedareas,$mpos,$lineend,self::SL_COMMENT,$sls[$mstr]);
						continue;
					}
					
					// multiline
					if(isset($mlstarts[$mstr]))
					{
						$cStart = $mpos;
						$inComment = true;
						$commentId = $mlstarts[$mstr];
					}
				}
				
				// multiline end?
				if($inComment && !isset($mlstarts[$mstr]) && ($endId = array_search($mstr,$mlends)) !== null)
				{
					// same comment-type?
					if($commentId == $endId)
					{
						$inComment = false;
						$this->_add_area($deniedareas,$cStart,$mpos + 2,self::ML_COMMENT,$endId);
					}
				}
			}
		}
		
		// replace keywords
		foreach($hl->get_keywords() as $id => $kws)
		{
			list($cs,$reqword) = $hl->get_keyword_settings($id);
			$this->_replace_words($kws,$unicode,self::KEYWORDS,$id,$t,$deniedareas,$cs,$reqword);
		}
		
		// numbers
		if($hl->highlight_numbers())
		{
			$matches = array();
			preg_match_all('/\\b-?(\\d+|\\d*\\.\\d+)\\b/',$t,$matches,PREG_OFFSET_CAPTURE);
			$ucchars = 0;
			$ucpos = 0;
			foreach($matches[0] as $match)
			{
				list($mstr,$pos) = $match;
				
				// count all multibyte characters in front of the current position
				for(;$ucpos < $uclen;$ucpos++)
				{
					if($unicode[$ucpos][1] > $pos)
						break;
					// we have to count the number of bytes because it may be more than 2 bytes long
					$ucchars += strlen($unicode[$ucpos][0]) - 1;
				}
				
				// substract the number 
				$pos -= $ucchars;
				
				$end = $pos + FWS_String::strlen($mstr);
				$this->_add_area($deniedareas,$pos,$end,self::NUMBER);
			}
		}
		
		// other regexps
		if(count($hl->get_regexps()) > 0)
		{
			foreach($hl->get_regexps() as $id => $regexp)
			{
				@list($pattern,$group,$cs) = $regexp;
				if(!$group)
					$group = 0;
				
				$matches = array();
				preg_match_all('/'.$pattern.'/'.(!$cs ? 'i' : ''),$t,$matches,PREG_OFFSET_CAPTURE);
				$ucchars = 0;
				$ucpos = 0;
				foreach($matches[$group] as $match)
				{
					list($mstr,$pos) = $match;
					
					// count all multibyte characters in front of the current position
					for(;$ucpos < $uclen;$ucpos++)
					{
						if($unicode[$ucpos][1] > $pos)
							break;
						// we have to count the number of bytes because it may be more than 2 bytes long
						$ucchars += strlen($unicode[$ucpos][0]) - 1;
					}
					
					// substract the number
					$pos -= $ucchars;
					
					$end = $pos + FWS_String::strlen($mstr);
					$this->_add_area($deniedareas,$pos,$end,self::REGEX,$id);
				}
			}
		}
		
		// we're not going to highlight symbols here because this would take too long and isn't
		// really important IMO
		
		// symbols
		//$this->_replace_words($hl->get_symbols(),$unicode,self::SYMBOL,null,$t,$deniedareas,true,false);
		
		// the areas have to be sorted in ascending order
		usort($deniedareas,array($this,'_sort_areas'));
		
		// build new text
		$attrs = array();
		foreach(array_keys($hl->get_string_quotes()) as $id)
			$attrs[self::STRING][$id] = $hl->get_string_attributes($id);
		foreach(array_keys($hl->get_single_comments()) as $id)
			$attrs[self::SL_COMMENT][$id] = $hl->get_sl_comment_attributes($id);
		foreach(array_keys($hl->get_multi_comment_limiters()) as $id)
			$attrs[self::ML_COMMENT][$id] = $hl->get_ml_comment_attributes($id);
		foreach(array_keys($hl->get_regexps()) as $id)
			$attrs[self::REGEX][$id] = $hl->get_regexp_attributes($id);
		foreach(array_keys($hl->get_keywords()) as $id)
			$attrs[self::KEYWORDS][$id] = $hl->get_keyword_attributes($id);
		$attrs[self::NUMBER] = $hl->get_attributes(FWS_Highlighting_Language::NUMBER);
		$attrs[self::SYMBOL] = $hl->get_attributes(FWS_Highlighting_Language::SYMBOL);
		
		$res = '';
		$p = 0;
		foreach($deniedareas as $area)
		{
			@list($s,$e,$type,$id) = $area;
			
			// add text between areas
			if($s != $p)
				$res .= $this->_decorator->get_text(FWS_String::substr($t,$p,$s - $p));
		
			// determine attributes
			if(isset($area[3]) && $id !== null)
				$attr = $attrs[$type][$id];
			else
				$attr = $attrs[$type];
			
			// add area
			$attrtext = FWS_String::substr($t,$s,$e - $s);
			$res .= $this->_decorator->open_attributes($attr,$attrtext);
			$res .= $this->_decorator->get_text($attrtext);
			$res .= $this->_decorator->close_attributes($attr);
			
			// set current pos
			$p = $e;
		}
		
		// add the rest
		if($p != $len)
			$res .= $this->_decorator->get_text(FWS_String::substr($t,$p));
		// no stuff for highlighting found?
		else if($len == 0)
			$res = $t;
		
		return $res;
	}
	
	/**
	 * The sort-function for the areas
	 *
	 * @param array $a the first element
	 * @param array $b the second element
	 * @return int the result
	 */
	private function _sort_areas($a,$b)
	{
		if($a[0] == $b[0])
			return $a[1] - $b[1];
		return $a[0] - $b[0];
	}
	
	/**
	 * Sorts the words descending by length
	 *
	 * @param string $a the first word
	 * @param string $b the second word
	 * @return int the compare-result
	 */
	private function _sort_words($a,$b)
	{
		return FWS_String::strlen($b) - FWS_String::strlen($a);
	}
	
	/**
	 * Searches all entries in the given treemap and highlights them with the given
	 * attributes.
	 *
	 * @param array $words the treeMap with the words to highlight
	 * @param array $unicode all unicode-char positions in the text
	 * @param int $type the type of words
	 * @param int $id the id
	 * @param string $t the text of the paragraph
	 * @param array $deniedareas the list with the denied areas
	 * @param boolean $cs match case-sensitive?
	 * @param boolean $reqword are words required? (word-boundary on each side)
	 */
	private function _replace_words($words,$unicode,$type,$id,$t,&$deniedareas,$cs,$reqword)
	{
		// nothing to do?
		if(count($words) == 0)
			return;
		
		// we assume here that the words are sorted descending by length
		// because we want to prefer longer words
		
		// now we build the pattern for the regular expression. it seems to be slightly faster to
		// use preg_match_all() just once for all words.
		$pattern = '/'.($reqword ? '\\b' : '').'(';
		foreach($words as $word)
			$pattern .= preg_quote($word,'/').'|';
		$pattern = FWS_String::substr($pattern,0,-1);
		$pattern .= ')'.($reqword ? '\\b' : '').'/'.(!$cs ? 'i' : '');
		
		// now find all matches
		$matches = array();
		$ucchars = 0;
		$ucpos = 0;
		$uclen = count($unicode);
		preg_match_all($pattern,$t,$matches,PREG_OFFSET_CAPTURE);
		foreach($matches[0] as $match)
		{
			list($mstr,$pos) = $match;
			
			// count all multibyte characters in front of the current position
			for(;$ucpos < $uclen;$ucpos++)
			{
				if($unicode[$ucpos][1] > $pos)
					break;
				// we have to count the number of bytes because it may be more than 2 bytes long
				$ucchars += strlen($unicode[$ucpos][0]) - 1;
			}
			
			// substract the number
			$pos -= $ucchars;
			
			$end = $pos + FWS_String::strlen($mstr);
			$this->_add_area($deniedareas,$pos,$end,$type,$id);
		}
	}
	
	/**
	 * Adds the given area to the areas
	 *
	 * @param array $areas the areas
	 * @param int $start the start-position
	 * @param int $end the end-position
	 * @param int $type the highlight-type
	 * @param mixed $id the id for highlighting (optional)
	 */
	private function _add_area(&$areas,$start,$end,$type,$id = null)
	{
		// at first we have to collect all of the positions we want to highlight that are already
		// occupied
		$cl = $end - $start;
		$ols = array();
		for($i = $start;$i < $end;$i++)
		{
			if(isset($this->_area_positions[$i]))
				$ols[$this->_area_positions[$i]] = 1;
		}
		
		// if there are any we have to check if we want to overwrite them
		if(count($ols) > 0)
		{
			// if there is any area which is longer than our one we want to leave the current one
			$olkeys = array_keys($ols);
			foreach($olkeys as $ol)
			{
				// calcuate the length of this area
				$otherlen = ($ol - ($ol % 100000)) / 100000;
				if($cl <= $otherlen)
					return;
			}
			
			// otherwise we remove the existing areas
			foreach($olkeys as $ol)
			{
				$index = $ol % 100000;
				unset($areas[$index]);
			}
		}
		
		// we use 100000 as multiplier here because no area should be that long and using an array
		// for both infos would cost very much memory
		$no = 100000 * $cl + $this->_area_counter;
		
		// occupy all positions
		for($i = $start;$i < $end;$i++)
			$this->_area_positions[$i] = $no;
		
		// store area
		$areas[$this->_area_counter++] = array($start,$end,$type,$id);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>