<?php
/**
 * Contains the raw-renderer-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
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
 * The raw-renderer simply sends the set string to the browser. This is for example intended for
 * AJAX-usages or RSS-feeds.
 * You have to set what should be sent to the browser via <var>set_content()</var>.
 * <br>
 * By default the renderer displays messages in plain-text if any messages have been set.
 *
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Document_Renderer_Raw extends FWS_Object implements FWS_Document_Renderer
{
	/**
	 * The content that should be printed
	 *
	 * @var string
	 */
	private $_content;
	
	/**
	 * @see FWS_Document_Renderer::render()
	 *
	 * @param FWS_Document $doc
	 * @return string
	 */
	public function render($doc)
	{
		// run the module
		$doc->get_module()->run();
		
		$msgs = FWS_Props::get()->msgs();
		if($msgs->contains_msg())
			$this->handle_msgs($msgs);
		
		return $this->_content;
	}
	
	/**
	 * Sets the content of this renderer
	 *
	 * @param string $content the content
	 */
	public function set_content($content)
	{
		$this->_content = $content;
	}

	/**
	 * Handles the collected messages
	 * 
	 * @param FWS_Document_Messages $msgs
	 */
	protected function handle_msgs($msgs)
	{
		$str = '';
		foreach($msgs->get_all_messages() as $type => $amsgs)
		{
			if(count($amsgs) > 0)
			{
				$str .= $msgs->get_type_name($type).":\n";
				foreach($amsgs as $msg)
					$str .= "\t".$msg."\n";
			}
		}
		
		$this->set_content($str);
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