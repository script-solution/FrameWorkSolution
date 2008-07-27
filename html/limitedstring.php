<?php
/**
 * Contains a class to cut a HTML-string to a given length.
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */
 
/**
 * A class to cut a HTML-string to a maximum length. This should work with tables and
 * similar structures in most cases, too.
 * 
 * Usage:
 * <code>
 * 	$lhtml = new PLIB_HTML_LimitedString('<p>test <a href="#foo">bar</a> blub</p>',3);
 *  $lhtml->set_more_text(' ... <a href="somePage.htm">Read more</a>');
 * 	echo $lhtml->get();
 * </code>
 * 
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_HTML_LimitedString extends PLIB_Object
{
	/**
	 * The input-string
	 *
	 * @var string
	 */
	private $_input;
	
	/**
	 * The max. number of _visible_ chars
	 *
	 * @var integer
	 */
	private $_limit;
	
	/**
	 * All tags which have no end-tag (e.g. &lt;br /&gt;)
	 *
	 * @var array
	 */
	private	$_short_tags = array(
		'area' => true,'base' => true,'basefont' => true,'br' => true,'col' => true,'frame' => true,
		'hr' => true,'img' => true,'input' => true,'isindex' => true,'link' => true,'meta' => true,
		'param' => true
	);
	
	/**
	 * All tags that don't allow #PCDATA
	 *
	 * @var array
	 */
	private $_no_content_tags = array(
		'applet' => true,'area' => true,'base' => true,'basefont' => true,'col' => true,
		'colgroup' => true,'dir' => true,'dl' => true,'form' => true,'frame' => true,
		'frameset' => true,'head' => true,'html' => true,'map' => true,'menu' => true,
		'ol' => true,'optgroup' => true,'table' => true,'tbody' => true,'tfoot' => true,
		'thead' => true,'tr' => true,'ul' => true
	);
	
	/**
	 * All table-row-tags
	 *
	 * @var array
	 */
	private $_tbl_row_tags = array(
		'tr' => true
	);
	
	/**
	 * All table-column-tags
	 *
	 * @var array
	 */
	private $_tbl_col_tags = array(
		'td' => true,'th' => true
	);
	
	/**
	 * All currently open tags (the last opened is in the last position in the array)
	 *
	 * @var array
	 */
	private $_open_tags = array();
	
	/**
	 * The type of the columns to add if necessary
	 *
	 * @var string
	 */
	private $_last_td_type = null;
	
	/**
	 * Stores the number of cols per row for the current table
	 *
	 * @var integer
	 */
	private $_cols_per_row = 0;
	
	/**
	 * The number of columns in the current row
	 *
	 * @var integer
	 */
	private $_row_col_count = 0;
	
	/**
	 * Have we already found one closing tag for a row?
	 *
	 * @var boolean
	 */
	private $_found_row_close = false;
	
	/**
	 * The text which will be added to show the user that there is more
	 *
	 * @var string
	 */
	private $_more_text = '...';
	
	/**
	 * Will be true if the string has been cut
	 *
	 * @var boolean
	 */
	private $_has_cut = false;
	
	/**
	 * Constructor
	 * 
	 * @param string $input the HTML-string to cut
	 * @param int $limit the max. number of _visible_ characters
	 */
	public function __construct($input,$limit)
	{
		parent::__construct();
		
		if(!is_string($input))
			PLIB_Helper::def_error('string','input',$input);
		if(!PLIB_Helper::is_integer($limit) || $limit <= 0)
			PLIB_Helper::def_error('intgt0','limit',$limit);
		
		$this->_input = $input;
		$this->_limit = $limit;
	}
	
	/**
	 * Sets the text that should be display if the string has cut to show the user that there is
	 * something more. By default it is "...".
	 * 
	 * @param string $text the new value
	 */
	public function set_more_text($text)
	{
		$this->_more_text = $text;
	}
	
	/**
	 * @return boolean wether the string has been cut
	 */
	public function has_cut()
	{
		return $this->_has_cut;
	}
	
	/**
	 * Cuts the given HTML-string to the given length. Only the visible chars will be counted.
	 * 
	 * @param string $input the input-string
	 * @param int $limit the maximum length you want to allow
	 * @return string the result-string
	 */
	public function get()
	{
		// do we have to cut the string?
		$real_len = PLIB_String::strlen(strip_tags($this->_input));
		if($real_len > $this->_limit)
		{
			$this->_has_cut = true;
			
			// ensure that the limit is >= 0 and we want to count the "...", too
			$limit = max(0,$this->_limit - PLIB_String::strlen(strip_tags($this->_more_text)));
			$output = '';
			$c = 0;
			for($i = 0,$len = PLIB_String::strlen($this->_input);$i < $len;$i++)
			{
				// search the next tag-start
				$pos = PLIB_String::strpos($this->_input,'<',$i);
				
				// there is no tag anymore?
				if($pos === false)
				{
					// does it fit?
					if($c + ($len - $i) <= $limit)
						$output .= PLIB_String::substr($this->_input,$i);
					else
					{
						$rem = $this->_get_html_part($c,$i,$i + $limit - $c,end($this->_open_tags));
						$this->_close_remaining($output,$rem);
					}
					break;
				}
				
				// count the visible chars to the next "<"
				$visible_chars = 0;
				$last_was_space = false;
				$noctag = isset($this->_no_content_tags[end($this->_open_tags)]);
				for($a = $i;$a < $pos;$a++)
				{
					$vc = PLIB_String::substr($this->_input,$a,1);
					
					// have we found a &...;? so don't count it
					if($vc == '&')
					{
						$a = PLIB_String::strpos($this->_input,';',$a);
						$visible_chars++;
						$last_was_space = false;
						continue;
					}
					
					$is_wp = PLIB_String::is_whitespace($vc);
					if((!$noctag && (!$is_wp || !$last_was_space)) || !$is_wp)
					{
						$last_was_space = $is_wp;
						$visible_chars++;
					}
				}
				
				// does the string fit?
				if($c + $visible_chars <= $limit)
				{
					// search the tag-end
					$end = PLIB_String::strpos($this->_input,'>',$pos);
					$is_closing_tag = PLIB_String::substr($this->_input,$pos + 1,1) == '/';
					
					// closing tag?
					if($is_closing_tag)
					{
						$tagname = PLIB_String::substr($this->_input,$pos + 2,$end - $pos - 2);
						$tagname = PLIB_String::strtolower($tagname);
						array_pop($this->_open_tags);
					}
					else
					{
						// search the tagname-end
						for($x = $pos;$x < $len;$x++)
						{
							$xc = PLIB_String::substr($this->_input,$x,1);
							// whitespace or ">"?
							if($xc == '>' || PLIB_String::is_whitespace($xc))
								break;
						}
						$tagname = PLIB_String::substr($this->_input,$pos + 1,$x - $pos - 1);
						$tagname = PLIB_String::strtolower($tagname);
						
						// just add the tag if it is no short-tag
						if(PLIB_String::substr($this->_input,$end - 1,1) != '/' &&
								!isset($this->_short_tags[$tagname]))
						{
							array_push($this->_open_tags,$tagname);
						}
					}
					
					// determine the width of this column
					$col_width = 1;
					$is_col = isset($this->_tbl_col_tags[$tagname]);
					if($is_col && !$is_closing_tag)
					{
						// store last td-type
						$this->_last_td_type = $tagname;
						
						$preg_res = array();
						$arguments = PLIB_String::substr($this->_input,$pos + 1,$end - ($pos + 1));
						if(preg_match('/colspan\s*=\s*"?\s*(\d+)\s*"?/i',$arguments,$preg_res))
						{
							$col_width = $preg_res[1];
							$this->_row_col_count += $preg_res[1];
						}
						else
							$this->_row_col_count++;
					}
					
					// reset everything if it is a table-tag
					if($tagname == 'table')
					{
						$this->_found_row_close = false;
						$this->_cols_per_row = 0;
					}
					// count the number of columns in the first row
					else if($is_col && !$this->_found_row_close && !$is_closing_tag)
						$this->_cols_per_row += $col_width;
					// reset the current col-count if we've found a row-tag
					else if(isset($this->_tbl_row_tags[$tagname]) && $is_closing_tag)
					{
						$this->_row_col_count = 0;
						$this->_found_row_close = true;
					}
					
					// collect the visible string to count the chars and take care of &...;
					if($is_closing_tag)
						$current_tag = $tagname;
					else if(count($this->_open_tags) >= 2)
						$current_tag = $this->_open_tags[count($this->_open_tags) - 2];
					else
						$current_tag = null;
					$visible = $this->_get_html_part($c,$i,$pos,$current_tag);
					
					// append to output and continue at the end-position of the tag
					$output .= $visible.PLIB_String::substr($this->_input,$pos,$end - $pos + 1);
					$i = $end;
				}
				else
				{
					// add all chars we can add
					$rem = $this->_get_html_part($c,$i,$i + $limit - $c,end($this->_open_tags));
					$this->_close_remaining($output,$rem);
					break;
				}
			}
			
			$res = $output;
		}
		else
			$res = $this->_input;
		
		return $res;
	}
	
	/**
	 * Closes all remaining elements and adds the given text at the appropriate position
	 * 
	 * @param string $output a reference to the output-string
	 * @param string $text the text to add
	 */
	private function _close_remaining(&$output,$text)
	{
		// at first we have to close all tags that don't allow content
		$tlen = count($this->_open_tags);
		while($tlen > 0 && isset($this->_no_content_tags[$this->_open_tags[$tlen - 1]]))
		{
			$output .= '</'.$this->_open_tags[$tlen - 1].'>';
			$tlen--;
		}
		
		// append the text
		$output .= $text.$this->_more_text;
		
		// close the last td first, if necessary
		while($tlen > 0 && isset($this->_tbl_col_tags[$this->_open_tags[$tlen - 1]]))
		{
			$output .= '</'.$this->_open_tags[$tlen - 1].'>';
			$tlen--;
		}
		
		// add additional tds if necessary
		for($a = $this->_row_col_count;$a < $this->_cols_per_row;$a++)
			$output .= '<'.$this->_last_td_type.'>&nbsp;</'.$this->_last_td_type.'>';
		
		// close the open tags
		for($a = $tlen - 1;$a >= 0;$a--)
			$output .= '</'.$this->_open_tags[$a].'>';
	}
	
	/**
	 * Returns a part of the given html-string, counts the number of visible chars and takes
	 * care of &...;
	 * 
	 * @param int $count a reference to a counter that will be increased by the number of
	 * 	visible chars
	 * @param int $start the start-position in the string
	 * @param int $end the end-position in the string
	 * @param string $current_tag the name of the current tag in which we are
	 * @return string the part
	 */
	private function _get_html_part(&$count,$start,$end,$current_tag)
	{
		$noctag = isset($this->_no_content_tags[$current_tag]);
		$last_was_space = false;
		$str = '';
		for($i = $start;$i < $end;$i++)
		{
			$c = PLIB_String::substr($this->_input,$i,1);
			
			// have we found a &...;? so don't count it
			if($c == '&')
			{
				$aend = PLIB_String::strpos($this->_input,';',$i);
				$str .= PLIB_String::substr($this->_input,$i,$aend - $i + 1);
				$count++;
				$i = $aend;
				$last_was_space = false;
				continue;
			}
			
			$is_wp = PLIB_String::is_whitespace($c);
			$str .= $c;
			if((!$noctag && (!$is_wp || !$last_was_space)) || !$is_wp)
			{
				$count++;
				$last_was_space = $is_wp;
			}
		}
		return $str;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>