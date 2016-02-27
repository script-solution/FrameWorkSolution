<?php
/**
 * Contains the class to shrink a JS-file
 * 
 * @package			FrameWorkSolution
 * @subpackage	js
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
 * A class to shrink a Javascript-file
 * 
 * @package			FrameWorkSolution
 * @subpackage	js
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_JS_FileShrinker extends FWS_Object
{
	/**
	 * The input-file
	 * 
	 * @var string
	 */
	private $_input_file;
	
	/**
	 * The file-content we're working on
	 * 
	 * @var string
	 */
	private $_content;
	
	/**
	 * The length of the content
	 * 
	 * @var integer
	 */
	private $_len;
	
	/**
	 * Constructor
	 * 
	 * @param string $input_file the input-file
	 */
	public function __construct($input_file)
	{
		parent::__construct();
		
		// check parameters
		if(empty($input_file))
			FWS_Helper::def_error('notempty','input_file',$input_file);
		
		if(!is_file($input_file))
			FWS_Helper::error('Invalid input file "'.$input_file.'"');
		
		$this->_input_file = $input_file;
		
		$this->_content = FWS_FileUtils::read($input_file);
		$this->_content = preg_replace('/\r\n|\r/',"\n",$this->_content);
		$this->_len = strlen($this->_content);
	}
	
	/**
	 * Performs the "shrinking" and returns the content
	 * 
	 * @return string the shrinked file-content
	 */
	public function get_shrinked_content()
	{
		// Note that we use the string as array (which is not multibyte-safe) for performance reasons
		$output = '';
		$ta = $this->_content;
		
		// init some flags
		$keep_next_whitespace = false;
		$in_line_comment = false;
		$in_multi_comment = false;
		$in_single_string = false;
		$in_double_string = false;
		
		for($i = 0;$i < $this->_len;$i++)
		{
			$c = $ta[$i];
			$next = $i < $this->_len - 1 ? $ta[$i + 1] : 0;
			
			switch($c)
			{
				case '\'':
					// skip strings in comments
					if(!$in_line_comment && !$in_multi_comment)
					{
						// string-start /-end?
						if(!$in_double_string && !$this->_is_escaped($i))
							$in_single_string = !$in_single_string;
						$output .= $c;
					}
					break;
				
				case '"':
					// skip strings in comments
					if(!$in_line_comment && !$in_multi_comment)
					{
						// string-start /-end?
						if(!$in_single_string && !$this->_is_escaped($i))
							$in_double_string = !$in_double_string;
						$output .= $c;
					}
					break;
				
				case '/':
					// are we in a string?
					if($in_single_string || $in_double_string)
						$output .= $c;
					// comment start?
					else if(!$in_line_comment && !$in_multi_comment)
					{
						// multiline-comment?
						if($next == '*')
						{
							$in_multi_comment = true;
							$i++;
						}
						// line-comment?
						else if($next == '/')
						{
							$in_line_comment = true;
							$i++;
						}
						else
							$output .= $c;
					}
					break;
				
				case '*':
					// comment-end?
					if($in_multi_comment && $next == '/')
					{
						$in_multi_comment = false;
						$i++;
					}
					// just skip in comments
					else if(!$in_line_comment && !$in_multi_comment)
						$output .= $c;
					break;
				
				case ' ':
				case "\t":
				case "\0":
					// don't skip whitespace in strings
					if($in_single_string || $in_double_string || $keep_next_whitespace)
					{
						$output .= $c;
						if($keep_next_whitespace)
							$keep_next_whitespace = false;
					}
					// or in front of specific keywords
					// by default we have to check whether "in" follows at $i + 1. But thats too slow
					else if($next == 'i')
						$output .= $c;
					break;
				
				case "\n":
					// line-comments end here
					$in_line_comment = false;
					
					// don't skip line-endings in strings
					if($in_single_string || $in_double_string)
						$output .= $c;
					// do we have to keep one whitespace?
					else if($keep_next_whitespace)
					{
						$output .= ' ';
						$keep_next_whitespace = false;
					}
					// or does a specific keyword follow?
					// by default we have to check whether "in" follows at $i + 1. But thats too slow
					else if($next == 'i')
						$output .= $c;
					break;
				
				// all other chars
				default:
					// skip comments
					if(!$in_line_comment && !$in_multi_comment)
					{
						// we have to leave a space after specific keywords
						// by default we have to check whether one of the following keywords follows:
						// 'in','var','else','new','function','return','typeof'
						// but thats too slow. So we check just for the first character
						if(!$keep_next_whitespace && !$in_single_string && !$in_double_string &&
							($c == 'i' || $c == 'v' || $c == 'e' || $c == 'n' || $c == 'f' || $c == 'r' || $c == 't'))
							$keep_next_whitespace = true;
						
						$output .= $c;
					}
					break;
			}
		}
		
		return $output;
	}
	
	/**
	 * Checks whether the given position is escaped. Or in other words: if the number of "\"
	 * in front of this position is odd.
	 * 
	 * @param int $i the current position
	 * @return bool true if the position is escaped
	 */
	private function _is_escaped($i)
	{
		$c = 0;
		for($i--;$i >= 0;$i--)
		{
			if($this->_content[$i] != '\\')
				break;
		}
		
		return $c % 2 == 1;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>