<?php
/**
 * Contains the atom format-implementation
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	feed.format
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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