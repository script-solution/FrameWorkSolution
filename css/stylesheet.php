<?php
/**
 * Contains the stylesheet class
 * 
 * @package			FrameWorkSolution
 * @subpackage	css
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
 * A simple parser for CSS-files. The supported grammar can be described (roughly) by the following:
 * <pre>
 * stylesheet:	medialist
 * medialist:		medialist '@media' ws ident+ ws '{' blocklist '}' | blocklist | €
 * blocklist:		blocklist selector '{' proplist '}' | €
 * proplist:		proplist ident ':' value ';' | €
 * selector:		( anysel )( ( '>' | '+' | ws ) anysel )+
 * anysel:			universal | id | class | type | attr
 * universal:		'*'
 * id:					[ ident | '*' ] '#' ident
 * class:				[ ident | '*' ] '.' ident
 * type:				ident
 * attr:				[ ident | '*' ] '[' ident [ '=' | '~=' | '|=' ident] ']'
 * value:				regex([^;]+)
 * ident:				regex([a-zA-Z\-_][a-zA-Z0-9\-_]*)
 * ws:					regex([ \n\r\t\f]*)
 * </pre>
 * Or in other words, it supports:
 * <ul>
 * 	<li>@import, @charset, @media</li>
 * 	<li>id-selector, class-selector, type-selector, universal-selector, attribute-selector,
 * 	pseudo-selector and "connected" selectors</li>
 * 	<li>All attributes and values since we don't care about them :)</li>
 * </ul>
 * With a few limitations:
 * <ul>
 * 	<li>No unicode</li>
 * 	<li>No multiple classes in class-selector</li>
 * 	<li>And probably some other advanced/special stuff I don't know about...</li>
 * </ul>
 * 
 * An usage-example:
 * <code>
 * $css = new FWS_CSS_StyleSheet(FWS_FileUtils::read('myfile.css'));
 * echo Printer::to_string($css->get_rulesets_for_class('myclass'));
 * FWS_FileUtils::write('myfile.css',$css->__toString());
 * </code>
 * 
 * @package			FrameWorkSolution
 * @subpackage	css
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_CSS_StyleSheet extends FWS_Object
{
	/**
	 * The regex for an identifier
	 *
	 * @var string
	 */
	const IDENT_REGEX = '(?:[A-Za-z\-_][A-Za-z0-9\-_]*)';
	
	/**
	 * The regex for a string (without quotes)
	 *
	 * @var string
	 */
	const STR_REGEX = '(?:[^"\\\\]*(?:\\\\.[^"\\\\]*)*)';
	
	/**
	 * All blocks in this stylesheet (comments and rulesets)
	 *
	 * @var array
	 */
	protected $_blocks = array();
	
	/**
	 * Constructor
	 *
	 * @param string $css the CSS-code to parse
	 */
	public function __construct($css)
	{
		parent::__construct();
		
		$this->parse((string)$css);
	}
	
	/**
	 * Adds the given block to the block-list
	 *
	 * @param FWS_CSS_Block $block the block
	 */
	public function add_block($block)
	{
		if(!($block instanceof FWS_CSS_Block))
			FWS_Helper::def_error('instance','block','FWS_CSS_Block',$block);
		
		$this->_blocks[] = $block;
	}
	
	/**
	 * Returns the block with given index
	 *
	 * @param int $index the index
	 * @return FWS_CSS_Block the block or null if the index is invalid
	 */
	public function get_block($index)
	{
		if($index >= 0 && $index < count($this->_blocks))
			return $this->_blocks[$index];
		return null;
	}
	
	/**
	 * @return array all blocks found in the document (instances of FWS_CSS_Block)
	 */
	public function get_blocks()
	{
		return $this->_blocks;
	}
	
	/**
	 * Removes the blocks with given indices
	 *
	 * @param array $indices an array with indices
	 * @return int the number of removed blocks
	 */
	public function remove_blocks($indices)
	{
		if(!is_array($indices))
			FWS_Helper::def_error('array','indices',$indices);
		
		$n = count($this->_blocks);
		foreach($indices as $i)
		{
			if($i >= 0 && $i < $n)
				unset($this->_blocks[$i]);
		}
		// set new indices
		sort($this->_blocks);
		return $n - count($this->_blocks);
	}
	
	/**
	 * Searches through all blocks and returns all rulesets that are for the given-media-type
	 * (maybe more).
	 *
	 * @param string $media the media-type, null = no media
	 * @return array the rulesets
	 */
	public function get_rulesets_for_media($media)
	{
		return $this->get_rulesets($this->get_rulesetsi_for_media($media));
	}
	
	/**
	 * Searches through all blocks and returns an array with all rulesets that have any id-selector
	 * with given id
	 *
	 * @param string $id the id
	 * @return array the rulesets
	 */
	public function get_rulesets_for_id($id)
	{
		return $this->get_rulesets($this->get_rulesetsi_for_id($id));
	}
	
	/**
	 * Searches through all blocks and returns an array with all rulesets that have any class-selector
	 * with given class
	 *
	 * @param string $class the class-name
	 * @return array the rulesets
	 */
	public function get_rulesets_for_class($class)
	{
		return $this->get_rulesets($this->get_rulesetsi_for_class($class));
	}
	
	/**
	 * Searches through all blocks and returns an array with all rulesets that have any selector
	 * with given tagname
	 *
	 * @param string $tagname the tag-name
	 * @return array the rulesets
	 */
	public function get_rulesets_for_tagname($tagname)
	{
		return $this->get_rulesets($this->get_rulesetsi_for_tagname($tagname));
	}
	
	/**
	 * Searches through all blocks and returns the first ruleset that has the given name
	 *
	 * @param string $name the name to search for
	 * @return FWS_CSS_Block_Ruleset the ruleset or null if not found
	 */
	public function get_ruleset_by_name($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET && $block->get_name() == $name)
				return $block;
		}
		return null;
	}
	
	/**
	 * Collects all rulesets that have the given name
	 *
	 * @param string $name the name to search for
	 * @return array an array of FWS_CSS_Block_Ruleset's
	 */
	public function get_rulesets_by_name($name)
	{
		return $this->get_rulesets($this->get_rulesetsi_by_name($name));
	}
	
	/**
	 * Returns all rulesets with given indices
	 *
	 * @param array $indices an array with indices
	 * @return array the rulesets
	 */
	public function get_rulesets($indices)
	{
		if(!is_array($indices))
			FWS_Helper::def_error('array','indices',$indices);
		
		$rulesets = array();
		$n = count($this->_blocks);
		foreach($indices as $i)
		{
			if($i >= 0 && $i < $n)
				$rulesets[] = $this->_blocks[$i];
		}
		return $rulesets;
	}
	
	/**
	 * Removes all rulesets that are for the given-media-type
	 *
	 * @param string $media the media-type, null = no media
	 * @return int the number of removed blocks
	 */
	public function remove_rulesets_for_media($media)
	{
		return $this->remove_blocks($this->get_rulesetsi_for_media($media));
	}
	
	/**
	 * Removes all rulesets that have any id-selector with given id
	 *
	 * @param string $id the id
	 * @return int the number of removed blocks
	 */
	public function remove_rulesets_for_id($id)
	{
		return $this->remove_blocks($this->get_rulesetsi_for_id($id));
	}
	
	/**
	 * Removes all rulesets that have any class-selector with given class
	 *
	 * @param string $class the class-name
	 * @return int the number of removed blocks
	 */
	public function remove_rulesets_for_class($class)
	{
		return $this->remove_blocks($this->get_rulesetsi_for_class($class));
	}
	
	/**
	 * Remoevs all rulesets that have any selector with given tagname
	 *
	 * @param string $tagname the tag-name
	 * @return int the number of removed blocks
	 */
	public function remove_rulesets_for_tagname($tagname)
	{
		return $this->remove_blocks($this->get_rulesetsi_for_tagname($tagname));
	}
	
	/**
	 * Removes all rulesets that have the given name
	 *
	 * @param string $name the name to search for
	 * @return int the number of removed blocks
	 */
	public function remove_rulesets_by_name($name)
	{
		return $this->remove_blocks($this->get_rulesetsi_by_name($name));
	}
	
	/**
	 * Searches through all blocks and returns all ruleset-indices that are for the given-media-type
	 * (maybe more).
	 *
	 * @param string $media the media-type, null = no media
	 * @return array an array with the indices
	 */
	protected function get_rulesetsi_for_media($media)
	{
		if($media !== null && empty($media))
			FWS_Helper::def_error('notempty','media',$media);
		
		$indices = array();
		$i = 0;
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET)
			{
				$m = $block->get_media();
				if($media === null && $m === null || ($m !== null && in_array($media,$m)))
					$indices[] = $i;
			}
			$i++;
		}
		return $indices;
	}
	
	/**
	 * Searches through all blocks and returns an array with all ruleset-indices that have any
	 * id-selector with given id
	 *
	 * @param string $id the id
	 * @return array the ruleset-indices
	 */
	protected function get_rulesetsi_for_id($id)
	{
		if(empty($id))
			FWS_Helper::def_error('notempty','id',$id);
		
		$indices = array();
		$i = 0;
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET)
			{
				foreach($block->get_all_selectors() as $sel)
				{
					if($sel instanceof FWS_CSS_Selector_ID && $sel->get_id() == $id)
					{
						$indices[] = $i;
						break;
					}
				}
			}
			$i++;
		}
		return $indices;
	}
	
	/**
	 * Searches through all blocks and returns an array with all ruleset-indices that have any
	 * class-selector with given class
	 *
	 * @param string $class the class-name
	 * @return array the ruleset-indices
	 */
	protected function get_rulesetsi_for_class($class)
	{
		if(empty($class))
			FWS_Helper::def_error('notempty','class',$class);
		
		$indices = array();
		$i = 0;
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET)
			{
				foreach($block->get_all_selectors() as $sel)
				{
					if($sel instanceof FWS_CSS_Selector_Class && $sel->get_class() == $class)
					{
						$indices[] = $i;
						break;
					}
				}
			}
			$i++;
		}
		return $indices;
	}
	
	/**
	 * Searches through all blocks and returns an array with all ruleset-indices that have any
	 * selector for given tagname
	 *
	 * @param string $tagname the tag-name
	 * @return array the ruleset-indices
	 */
	protected function get_rulesetsi_for_tagname($tagname)
	{
		if(empty($tagname))
			FWS_Helper::def_error('notempty','tagname',$tagname);
		
		$indices = array();
		$i = 0;
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET)
			{
				foreach($block->get_all_selectors() as $sel)
				{
					if($sel instanceof FWS_CSS_Selector_Type && $sel->get_tagname() == $tagname)
					{
						$indices[] = $i;
						break;
					}
				}
			}
			$i++;
		}
		return $indices;
	}
	
	/**
	 * Collects all rulesets that have the given name
	 *
	 * @param string $name the name to search for
	 * @return array an array with the indices
	 */
	protected function get_rulesetsi_by_name($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		$indices = array();
		$i = 0;
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET && $block->get_name() == $name)
				$indices[] = $i;
			$i++;
		}
		return $indices;
	}
	
	/**
	 * Parses the given CSS-code and builds the block-array
	 *
	 * @param string $str the code to parse
	 */
	protected function parse($str)
	{
		// array(array(<comment>,<selector>,<properties>),...)
		$blocks = array();
		
		// match all tokens in which we are interested
		$matches = array();
		preg_match_all('/\/\*|\*\/|"|{|}|@|;/',$str,$matches,PREG_OFFSET_CAPTURE);
	
		$m = array();
		// if no mb-functions are used (e.g. they are not supported) we simply use the byte-positions
		// as offset
		if(FWS_String::get_use_mb_functions())
		{
			// unfortunatly preg_match_all() with PREG_OFFSET_CAPTURE does always count bytes for the
			// offset (this doesn't change with modifier "u").
			// therefore we search for all multibyte characters in the text and save their position
			preg_match_all('/[\x{80}-\x{10FFFF}]/u',$str,$m,PREG_OFFSET_CAPTURE);
			$m = $m[0];
		}
		
		$incomment = false;
		$instr = false;
		$inruleset = false;
		$inatrule = false;
		$buffer = '';
		$block = array();
		$lastpos = 0;
		$mbchars = 0;
		$mbpos = 0;
		$mlen = count($m);
		foreach($matches[0] as $match)
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
			
			switch($match[0])
			{
				// strings
				case '"':
					if(!$incomment && !$this->_is_escaped($str,$match[1]))
						$instr = !$instr;
					break;
				
				// comments
				case '/*':
					if(!$incomment && !$instr)
					{
						$buffer .= FWS_String::substr($str,$lastpos,$match[1] - $lastpos);
						$incomment = true;
						$lastpos = $match[1];
					}
					break;
				case '*/':
					if(!$instr)
					{
						$incomment = false;
						$blocks[] = array(FWS_String::substr($str,$lastpos,$match[1] + 2 - $lastpos));
						// skip */
						$lastpos = $match[1] + 2;
					}
					break;
				
				// at-rules
				case ';':
					if(!$incomment && !$instr && $inatrule)
					{
						$sub = FWS_String::substr($str,$lastpos,$match[1] + 1 - $lastpos);
						$blocks[] = array(trim($sub));
						$inatrule = false;
						// skip ;
						$lastpos = $match[1] + 1;
					}
					break;
				case '@':
					if(!$incomment && !$instr)
					{
						$inatrule = true;
						$lastpos = $match[1];
					}
					break;
				
				// rulesets
				case '{':
					if(!$incomment && !$instr)
					{
						if(!$inatrule)
						{
							// put selector in block
							$buffer .= FWS_String::substr($str,$lastpos,$match[1] - $lastpos);
							$block[1] = trim($buffer);
							$buffer = '';
							$inruleset = true;
						}
						else
						{
							$sub = FWS_String::substr($str,$lastpos,$match[1] - $lastpos);
							$blocks[] = array(trim($sub));
							$inatrule = false;
						}
						
						// skip {
						$lastpos = $match[1] + 1;
					}
					break;
				case '}':
					if(!$incomment && !$instr)
					{
						if($inruleset)
						{
							// put properties in block
							$buffer .= FWS_String::substr($str,$lastpos,$match[1] - $lastpos);
							$block[2] = trim($buffer);
							$blocks[] = $block;
							$buffer = '';
							$inruleset = false;
						}
						else
							$blocks[] = array('@mediaend');
						
						// skip }
						$lastpos = $match[1] + 1;
					}
					break;
			}
		}
		
		// build blocks
		$media = null;
		$this->_blocks = array();
		foreach($blocks as $block)
		{
			// comment or at-rule
			if(isset($block[0]))
			{
				if($block[0] == '@mediaend')
					$media = null;
				else if(FWS_String::strtolower(FWS_String::substr($block[0],0,6)) == '@media')
				{
					$types = preg_replace('/^@media\s+/i','',$block[0]);
					$media = preg_split('/\s*,\s*/',$types,-1,PREG_SPLIT_NO_EMPTY);
					if(count($media) == 0)
						$media = null;
				}
				else if(FWS_String::strtolower(FWS_String::substr($block[0],0,7)) == '@import')
				{
					$matches = array();
					if(preg_match('/^@import\s+url\(\s*"?('.self::STR_REGEX.')"?\s*\)\s*(.*?)\s*;/i',
							$block[0],$matches) ||
						preg_match('/^@import\s+"('.self::STR_REGEX.')"\s*(.*?)\s*;/i',$block[0],$matches))
					{
						$mediatypes = preg_split('/\s*,\s*/',$matches[2],-1,PREG_SPLIT_NO_EMPTY);
						$this->_blocks[] = new FWS_CSS_Block_Import($matches[1],$mediatypes);
					}
				}
				else if(FWS_String::strtolower(FWS_String::substr($block[0],0,8)) == '@charset')
				{
					if(preg_match('/^@charset\s+"('.self::STR_REGEX.')"\s*;/i',$block[0],$matches))
						$this->_blocks[] = new FWS_CSS_Block_Charset($matches[1]);
				}
				else
					$this->_blocks[] = new FWS_CSS_Block_Comment($block[0]);
			}
			// ruleset
			else
			{
				$sels = $this->_get_selectors($block[1]);
				$props = $this->_get_properties($block[2]);
				if(count($sels) > 0)
					$this->_blocks[] = new FWS_CSS_Block_Ruleset($sels,$props,$media);
			}
		}
	}
	
	/**
	 * Checks wether the given position is escaped (by backslashes)
	 *
	 * @param string $str the string
	 * @param int $pos the position
	 * @return boolean true if escaped
	 */
	private function _is_escaped($str,$pos)
	{
		$c = 0;
		for($pos--;$pos >= 0;$pos--)
		{
			if(FWS_String::substr($str,$pos,1) != '\\')
				break;
			$c++;
		}
		return $c % 2 == 1;
	}
	
	/**
	 * Parses an associative array from the given string. That means it assumes that the format
	 * of $str is:
	 * <code>(<name>:<value>;)*</code>
	 * 
	 * @param string $str the CSS-code
	 * @return array all found properties
	 */
	protected function _get_properties($str)
	{
		$props = array();
		$matches = array();
		preg_match_all('/('.self::IDENT_REGEX.')\s*:\s*([^;]+);?/i',$str,$matches);
		foreach(array_keys($matches[0]) as $k)
			$props[$matches[1][$k]] = $matches[2][$k];
		return $props;
	}
	
	/**
	 * Parses the given selectors-string and returns the selector-instances
	 * 
	 * @param string $str the selectors
	 * @return array an array with all found selectors
	 */
	protected function _get_selectors($str)
	{
		$sels = array();
		
		// remove whitespace to simplify parsing
		$delims = array('#',',','\.','\+','\>','\[','\]','=','~=','\|=');
		$regexs = array();
		foreach($delims as $delim)
			$regexs[] = '/\s*('.$delim.')\s*/';
		$str = preg_replace($regexs,'\\1',$str);
		
		// walk to all selectors
		$parts = FWS_Array_Utils::advanced_explode(',',$str);
		foreach($parts as $part)
		{
			// split by connector
			$sparts = preg_split('/(\s+|\+|\>)/',$part,-1,PREG_SPLIT_DELIM_CAPTURE);
			
			// build initial selector
			$sel = $this->_get_selector($sparts[0]);
			if($sel !== null)
			{
				// connect others, if there are more
				for($i = 1,$n = count($sparts);$i < $n;$i += 2)
				{
					$rsel = $this->_get_selector($sparts[$i + 1]);
					if($rsel !== null)
						$sel = new FWS_CSS_Selector_Connector($sel,$sparts[$i],$rsel);
				}
				$sels[] = $sel;
			}
		}
		return $sels;
	}
	
	/**
	 * Parses the given string (a single selector) and creates an object from the corresponding
	 * class.
	 *
	 * @param string $str the selector
	 * @return FWS_CSS_Selector the selector
	 */
	protected function _get_selector($str)
	{
		$sel = null;
		// remove pseudo
		$pseudo = '';
		if(($p = FWS_String::strpos($str,':')) !== false)
		{
			$pseudo = FWS_String::substr($str,$p + 1);
			$str = FWS_String::substr($str,0,$p);
		}
		
		// determine type of selector and create the appropriate object
		$matches = array();
		if(FWS_String::strpos($str,'.') !== false)
		{
			// class
			if(preg_match('/^('.self::IDENT_REGEX.'|\*)?\.('.self::IDENT_REGEX.')$/i',$str,$matches))
				$sel = new FWS_CSS_Selector_Class($matches[2],$matches[1]);
		}
		else if(FWS_String::strpos($str,'#') !== false)
		{
			// id
			if(preg_match('/^('.self::IDENT_REGEX.'|\*)?#('.self::IDENT_REGEX.')$/i',$str,$matches))
				$sel = new FWS_CSS_Selector_ID($matches[2],$matches[1]);
		}
		else if(FWS_String::strpos($str,'[') !== false)
		{
			// attribute
			$regex = '/^('.self::IDENT_REGEX.'|\*)?\[('.self::IDENT_REGEX.')(?:(=|~=|\|=)"(.*?)")?\]$/i';
			if(preg_match($regex,$str,$matches))
			{
				if(isset($matches[3]))
					$sel = new FWS_CSS_Selector_Attribute($matches[2],$matches[3],$matches[4],$matches[1]);
				else
				{
					$sel = new FWS_CSS_Selector_Attribute(
						$matches[2],FWS_CSS_Selector_Attribute::OP_EXIST,null,$matches[1]
					);
				}
			}
		}
		else
		{
			// type / universal
			$sel = new FWS_CSS_Selector_Type($str);
		}
		
		// decorate with pseudo if present
		if($pseudo)
			$sel = new FWS_CSS_Selector_Pseudo($sel,$pseudo);
		
		return $sel;
	}
	
	/**
	 * Builds the CSS-Code that can be stored to file or something like that
	 *
	 * @return string the CSS-code
	 */
	public function __toString()
	{
		$css = '';
		$indent = '';
		$lastmedia = null;
		foreach($this->_blocks as $block)
		{
			if($block->get_type() == FWS_CSS_Block::RULESET && $block->get_media() !== $lastmedia)
			{
				if($lastmedia !== null)
					$css .= '}'."\n\n";
				$media = $block->get_media();
				if($media !== null)
				{
					$indent = "\t";
					$css .= '@media '.implode(', ',$media).' {'."\n";
				}
				else
					$indent = '';
				$lastmedia = $media;
			}
			$css .= $block->to_css($indent)."\n\n";
		}
		return $css;
	}

	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>