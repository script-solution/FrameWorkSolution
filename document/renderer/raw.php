<?php
/**
 * Contains the raw-renderer-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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
	 * @see FWS_Object::get_print_vars()
	 *
	 * @return array
	 */
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>