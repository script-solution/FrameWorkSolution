<?php
/**
 * Contains the SQL-parser-class
 *
 * @version			$Id: sqlparser.php 684 2008-05-10 14:48:24Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A utitity to parse a SQL-string or -file and return all found statements
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_SQLParser extends PLIB_UtilBase
{
	/**
	 * Single line comment
	 */
	const SL_COMMENT = 1;
	
	/**
	 * Multi line comment
	 */
	const ML_COMMENT = 2;
	
	/**
	 * Single string (')
	 */
	const SGL_STRING = 4;
	
	/**
	 * Double string (")
	 */
	const DBL_STRING = 8;
	
	/**
	 * Cmd string (`)
	 */
	const CMD_STRING = 16;
	
	/**
	 * Collects all statements in the given SQL-file
	 * 
	 * @param string $filename the sql-file
	 * @return array a numeric array with all SQL-statements in the file
	 */
	public static function get_statements_from_File($filename)
	{
		return self::get_statements(PLIB_FileUtils::read($filename));
	}
	
	/**
	 * Collects all statements in the given SQL-string
	 * 
	 * @param string $sql the SQL-queries
	 * @return array a numeric array with all SQL-statements
	 */
	public static function get_statements($sql)
	{
		$statements = array();
		
		$matches = array();
		preg_match_all(
			// a little trick to get this quicker...
			// we match all quotes, that are not preceded by exactly 1 backslash.
			// if we have 0 backslashes we can determine this quickly by checking wether the
			// last character is not a backslash. if we have 2 or more we will use the is_escaped()
			// method. but this should happen nearly never...
			"/(?:(?<![^\\\\]\\\\)\"|'|`)|(?:\n|\r|#|--|\\/\\*|\\*\\/|;)/s",
			$sql,
			$matches,
			PREG_OFFSET_CAPTURE
		);
	
		$m = array();
		// if no mb-functions are used (e.g. they are not supported) we simply use the byte-positions
		// as offset
		if(PLIB_String::get_use_mb_functions())
		{
			// unfortunatly preg_match_all() with PREG_OFFSET_CAPTURE does always count bytes for the
			// offset (this doesn't change with modifier "u").
			// therefore we search for all multibyte characters in the text and save their position
			preg_match_all('/[\x{80}-\x{10FFFF}]/u',$sql,$m,PREG_OFFSET_CAPTURE);
			$m = $m[0];
		}
		
		$status = 0;
		$temp = '';
		$last = 0;
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
				case '"':
					// we don't want to find strings in comments (they are uninteresting)
					
					// you may ask why we can directly access the bytes here although it should be multibyte
					// compatible. The reason is the following:
					// assume we have a mb-char directly in front of a quote. There are 2 cases:
					// 1. the last byte of the mb-char is a backslash:
					//    this means that the condition until "|| !self::_..." is false and we will determine
					//    it with the method (which is ok, of course).
					// 2. the last byte of the mb-char is not a backslash:
					//    this means that the condition is true. this is also ok, because the mb-char
					//    (the complete one) is of course no backslash to escape the quote.
					
					if($match[1] == 0 || $sql[$match[1] + $mbchars - 1] != '\\' ||
						!self::_is_escaped($sql,$match[1]))
					{
						if($status == 0)
							$status = self::DBL_STRING;
						else if($status == self::DBL_STRING)
							$status = 0;
					}
					break;
				
				case '\'':
					// we don't want to find strings in comments (they are uninteresting)
					if($match[1] == 0 || $sql[$match[1] + $mbchars - 1] != '\\' ||
						!self::_is_escaped($sql,$match[1]))
					{
						if($status == 0)
							$status = self::SGL_STRING;
						else if($status == self::SGL_STRING)
							$status = 0;
					}
					break;
				
				case '`':
					// we don't want to find strings in comments (they are uninteresting)
					if($match[1] == 0 || $sql[$match[1] + $mbchars - 1] != '\\' ||
						!self::_is_escaped($sql,$match[1]))
					{
						if($status == 0)
							$status = self::CMD_STRING;
						else if($status == self::CMD_STRING)
							$status = 0;
					}
					break;
				
				// leave single-comments
				case "\n":
				case "\r":
					if($status == self::SL_COMMENT)
					{
						$status = 0;
						$last = $match[1] + 1;
					}
					break;
				
				// enter single comments
				case '#':
				case '--':
					// not in a string or comment?
					if($status == 0)
					{
						$status = self::SL_COMMENT;
						$temp .= PLIB_String::substr($sql,$last,$match[1] - $last);
					}
					break;
				
				// multiline comment end
				case '*'.'/':
					if($status == self::ML_COMMENT)
					{
						$status = 0;
						$last = $match[1] + 2;
					}
					break;
				
				// multiline comment start
				case '/'.'*':
					if($status == 0)
					{
						$status = self::ML_COMMENT;
						$temp .= PLIB_String::substr($sql,$last,$match[1] - $last);
					}
					break;
				
				// detect the statement-separator
				case ';':
					// we just want to do this if we are not in a comment or string
					if($status == 0)
					{
						$temp .= PLIB_String::substr($sql,$last,$match[1] - $last);
						$statements[] = trim($temp);
						$temp = '';
						$last = $match[1] + 1;
					}
					break;
			}
		}
		
		return $statements;
	}
	
	/**
	 * determines if the given position in the string is escaped
	 * 
	 * @param string $string the string
	 * @param int $pos the position of the character
	 * @return true if the character is escaped
	 */
	private static function _is_escaped($string,$pos)
	{
		$count = 0;
		for($i = $pos - 1;$i >= 0;$i--)
		{
			$c = PLIB_String::substr($string,$i,1);
			if($c != '\\')
				break;
			
			$count++;
		}
		
		return $count % 2 == 1;
	}
}
?>