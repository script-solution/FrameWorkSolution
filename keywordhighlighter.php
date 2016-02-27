<?php
/**
 * Contains the keyword-highlighter-class
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

/**
 * Can highlight keywords in a HTML-string.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_KeywordHighlighter extends FWS_Object
{
	/**
	 * A numeric array with all keywords that should be highlighted
	 *
	 * @var array
	 */
	private $_keywords;
	
	/**
	 * The prefix for all keywords
	 *
	 * @var string
	 */
	private $_prefix;
	
	/**
	 * The suffix for all keywords
	 *
	 * @var string
	 */
	private $_suffix;
	
	/**
	 * Constructor
	 *
	 * @param array $keywords an numeric array with all specified keywords
	 * @param string $prefix the prefix for the keywords
	 * @param string $suffix the suffix for the keywords
	 */
	public function __construct($keywords,$prefix = '<span class="keyword">',$suffix = '</span>')
	{
		parent::__construct();
		
		if(!is_array($keywords))
			FWS_Helper::def_error('array','keywords',$keywords);
		
		$this->_keywords = $keywords;
		$this->_prefix = $prefix;
		$this->_suffix = $suffix;
	}
	
	/**
	 * Highlights the keywords in the given text
	 * 
	 * @param string $text the text
	 * @return string the modified text with highlighted keywords
	 */
	public function highlight($text)
	{
		// nothing to do?
		$num = count($this->_keywords);
		if($num == 0)
			return $text;
		
		// build keyword regex-part
		$pos = array();
		$kws = array();
		foreach($this->_keywords as $kw)
			$kws[] = preg_quote($kw,'/');
		$kwstr = implode('|',$kws);
		
		// search for all other keywords
		$matches = array();
		if(preg_match_all('/('.$kwstr.')/i',$text,$matches,PREG_OFFSET_CAPTURE))
		{
			foreach($matches[1] as $match)
				$pos[] = $match;
		}
		
		// now search all special chars
		$special = array();
		preg_match_all('/[;&<>]/',$text,$special,PREG_OFFSET_CAPTURE);
		$special = $special[0];
		
		$m = array();
		// if no mb-functions are used (e.g. they are not supported) we simply use the byte-positions
		// as offset
		if(FWS_String::get_use_mb_functions())
		{
			// unfortunatly preg_match_all() with PREG_OFFSET_CAPTURE does always count bytes for the
			// offset (this doesn't change with modifier "u").
			// therefore we search for all multibyte characters in the text and save their position
			preg_match_all('/[\x{80}-\x{10FFFF}]/u',$text,$m,PREG_OFFSET_CAPTURE);
			$m = $m[0];
		}
		
		$mbchars = 0;
		$mbpos = 0;
		$mlen = count($m);
		$specialpos = 0;
		$speciallen = count($special);
		$is_match = true;
		$p = 0;
		$result = '';
		foreach($pos as $match)
		{
			// count all multibyte characters in front of the current position
			$end = $match[1];
			for(;$mbpos < $mlen;$mbpos++)
			{
				if($m[$mbpos][1] > $end)
					break;
				// we have to count the number of bytes because it may be more than 2 bytes long
				$mbchars += strlen($m[$mbpos][0]) - 1;
			}
			
			// substract the number 
			$match[1] -= $mbchars;
			
			// add the text in front of the match
			if($match[1] > $p)
				$result .= FWS_String::substr($text,$p,$match[1] - $p);
			
			// check whether it is a valid match
			// note that can we use the byte-position here because we search just for ASCII chars
			// and don't need the position for other things
			$end = $match[1] + $mbchars;
			for(;$specialpos < $speciallen && $special[$specialpos][1] < $end;$specialpos++)
			{
				$c = $special[$specialpos][0];
				if($c == '<' || $c == '&')
					$is_match = false;
				else if($c == '>' || $c == ';')
					$is_match = true;
			}
			
			// highlight match?
			if($is_match)
				$result .= $this->_prefix.$match[0].$this->_suffix;
			else
				$result .= $match[0];
			
			// continue after the match
			$p = $match[1] + FWS_String::strlen($match[0]);
		}
		
		// is there anything left?
		$len = FWS_String::strlen($text);
		if($p < $len)
			$result .= FWS_String::substr($text,$p);
		
		return $result;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>