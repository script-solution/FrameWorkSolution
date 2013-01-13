<?php
/**
 * Contains the atom format-implementation
 * 
 * @package			FrameWorkSolution
 * @subpackage	feed.format
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
 * The format-implementation for Atom
 *
 * @package			FrameWorkSolution
 * @subpackage	feed.format
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Feed_Format_Atom extends FWS_Object implements FWS_Feed_Format
{
	/**
	 * @see FWS_Feed_Format::render()
	 *
	 * @param FWS_Feed_Document $doc
	 * @return string
	 */
	public function render($doc)
	{
		$xml = '<?xml version="1.0" encoding="'.$doc->get_encoding().'"?>'."\n";
		$xml .= '<feed xmlns="http://www.w3.org/2005/Atom">'."\n";
		if($doc->get_author() !== null)
		{
			$xml .= '	<author>'."\n";
			$xml .= '		<name>'.$doc->get_author().'</name>'."\n";
			$xml .= '	</author>'."\n";
		}
		$xml .= '	<title>'.$doc->get_title().'</title>'."\n";
		$xml .= '	<updated>'.FWS_Date::get_formated_date('Y-m-d\TH:i:s\Z',$doc->get_date());
		$xml .= '</updated>'."\n";
		if($doc->get_id() !== null)
			$xml .= '	<id>'.$doc->get_id().'</id>'."\n";
		$xml .= "\n";
		
		foreach($doc->get_items() as $item)
		{
			/* @var $item FWS_Feed_Item */
			$xml .= '	<entry>'."\n";
			$xml .= '		<title>'.$item->get_title().'</title>'."\n";
			$xml .= '		<summary type="html"><![CDATA['.$item->get_content().']]></summary>'."\n";
			$xml .= '		<author>'."\n";
			$xml .= '			<name>'.$item->get_author().'</name>'."\n";
			$xml .= '		</author>'."\n";
			$xml .= '		<link href="'.$item->get_link().'"/>'."\n";
			$xml .= '		<updated>'.FWS_Date::get_formated_date('Y-m-d\TH:i:s\Z',$item->get_date());
			$xml .=	'</updated>'."\n";
			$xml .= '		<id>'.$item->get_id().'</id>'."\n";
			$xml .= '	</entry>'."\n";
		}
		
		$xml .= '</feed>'."\n";
		return $xml;
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