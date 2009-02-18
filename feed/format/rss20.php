<?php
/**
 * Contains the rss 2.0 format-implementation
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	feed.format
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The format-implementation for RSS 2.0
 *
 * @package			FrameWorkSolution
 * @subpackage	feed.format
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Feed_Format_RSS20 extends FWS_Object implements FWS_Feed_Format
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
		$xml .= '<rss version="2.0">'."\n";
		$xml .= '	<channel>'."\n";
		$xml .= '		<title>'.$doc->get_title().'</title>'."\n";
		if($doc->get_link() !== null)
			$xml .= '		<link>'.$doc->get_link().'</link>'."\n";
		if($doc->get_description() !== null)
			$xml .= '		<description>'.$doc->get_description().'</description>'."\n";
		$xml .= '		<pubDate>'.FWS_Date::get_formated_date('D, j M Y G:i:s T',$doc->get_date());
		$xml .= '</pubDate>'."\n";
		$xml .= "\n";
		
		foreach($doc->get_items() as $item)
		{
			/* @var $item FWS_Feed_Item */
			$xml .= '		<item>'."\n";
			$xml .= '			<title>'.$item->get_title().'</title>'."\n";
			$xml .= '			<description><![CDATA['.$item->get_content().']]></description>'."\n";
			$xml .= '			<link>'.$item->get_link().'</link>'."\n";
			$xml .= '			<author>'.$item->get_author().'</author>'."\n";
			$xml .= '			<pubDate>'.FWS_Date::get_formated_date('D, j M Y G:i:s T',$item->get_date());
			$xml .=	'</pubDate>'."\n";
			$xml .= '			<guid>'.$item->get_id().'</guid>'."\n";
			$xml .= '		</item>'."\n";
		}
		
		$xml .= '	</channel>'."\n";
		$xml .= '</rss>'."\n";
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