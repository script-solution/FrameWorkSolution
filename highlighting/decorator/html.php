<?php
/**
 * Contains the html-highlighting-decorator class
 * 
 * @package			FrameWorkSolution
 * @subpackage	highlighting.decorator
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
 * The HTML-implementation of the decorator.
 *
 * @package			FrameWorkSolution
 * @subpackage	highlighting.decorator
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Highlighting_Decorator_HTML extends FWS_Object
	implements FWS_Highlighting_Decorator
{
	/**
	 * @see FWS_Highlighting_Decorator::open_attributes()
	 *
	 * @param FWS_Highlighting_Attributes $attr
	 * @param string $text
	 * @return string
	 */
	public function open_attributes($attr,$text)
	{
		$res = '';
		$span_attr = array();
		$found_url = false;
		foreach($attr->get_all() as $k => $v)
		{
			switch($k)
			{
				case FWS_Highlighting_Attributes::FONT_COLOR:
					$span_attr['color'] = $v;
					break;
				case FWS_Highlighting_Attributes::BG_COLOR:
					$span_attr['background-color'] = $v;
					break;
				case FWS_Highlighting_Attributes::FONT_FAMILY:
					$span_attr['font-family'] = $v;
					break;
				case FWS_Highlighting_Attributes::FONT_SIZE:
					$span_attr['font-size'] = $v.'px';
					break;
				case FWS_Highlighting_Attributes::BOLD:
					$res .= '<b>';
					break;
				case FWS_Highlighting_Attributes::UNDERLINE:
					$res .= '<u>';
					break;
				case FWS_Highlighting_Attributes::ITALIC:
					$res .= '<i>';
					break;
				case FWS_Highlighting_Attributes::STRIKE:
					$res .= '<s>';
					break;
				case FWS_Highlighting_Attributes::URL:
				case FWS_Highlighting_Attributes::EMAIL:
					// we want to allow just one of both
					if(!$found_url)
					{
						// allow the user to use the variable {text} for the text to highlight
						$url = $k == FWS_Highlighting_Attributes::URL ? $v : 'mailto:'.$v;
						$url = str_replace('{text}',$text,$url);
						$res .= '<a target="_blank" href="'.$url.'">';
						$found_url = true;
					}
					break;
				case FWS_Highlighting_Attributes::POSITION:
					if($v == FWS_Highlighting_Attributes::POS_SUBSCRIPT)
						$res .= '<sub>';
					else if($v == FWS_Highlighting_Attributes::POS_SUPERSCRIPT)
						$res .= '<sup>';
					break;
			}
		}
		
		// now build the span-tag if required
		if(count($span_attr) > 0)
		{
			$res .= '<span style="';
			foreach($span_attr as $k => $v)
				$res .= $k.': '.$v.';';
			$res .= '">';
		}
		
		return $res;
	}
	
	/**
	 * @see FWS_Highlighting_Decorator::close_attributes()
	 *
	 * @param FWS_Highlighting_Attributes $attr
	 * @return string
	 */
	public function close_attributes($attr)
	{
		$res = '';
		$found_url = false;
		$close_span = false;
		$attributes = $attr->get_all();
		foreach(array_reverse(array_keys($attributes)) as $k)
		{
			switch($k)
			{
				case FWS_Highlighting_Attributes::FONT_COLOR:
				case FWS_Highlighting_Attributes::BG_COLOR:
				case FWS_Highlighting_Attributes::FONT_FAMILY:
				case FWS_Highlighting_Attributes::FONT_SIZE:
					$close_span = true;
					break;
				case FWS_Highlighting_Attributes::BOLD:
					$res .= '</b>';
					break;
				case FWS_Highlighting_Attributes::UNDERLINE:
					$res .= '</u>';
					break;
				case FWS_Highlighting_Attributes::ITALIC:
					$res .= '</i>';
					break;
				case FWS_Highlighting_Attributes::STRIKE:
					$res .= '</s>';
					break;
				case FWS_Highlighting_Attributes::URL:
				case FWS_Highlighting_Attributes::EMAIL:
					// we want to allow just one of both
					if(!$found_url)
					{
						$res .= '</a>';
						$found_url = true;
					}
					break;
				case FWS_Highlighting_Attributes::POSITION:
					if($attributes[$k] == FWS_Highlighting_Attributes::POS_SUBSCRIPT)
						$res .= '</sub>';
					else if($attributes[$k] == FWS_Highlighting_Attributes::POS_SUPERSCRIPT)
						$res .= '</sup>';
					break;
			}
		}
		
		// do we have to close the span-tag?
		if($close_span)
			$res = '</span>'.$res;
		
		return $res;
	}

	/**
	 * @see FWS_Highlighting_Decorator::get_text()
	 *
	 * @param string $text
	 * @return string
	 */
	public function get_text($text)
	{
		$text = htmlspecialchars($text,ENT_QUOTES);
		$text = str_replace(' ','&nbsp;',$text);
		$text = str_replace("\n",'<br />',$text);
		$text = str_replace("\t",'&nbsp;&nbsp;',$text);
		return $text;
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
