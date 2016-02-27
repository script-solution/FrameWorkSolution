<?php
/**
 * Contains the download-renderer-class
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
 * The download-renderer sends a file to the browser. You can specify the file or send an
 * arbitrary string as file. Additionally you can specify the filename that should be displayed
 * and whether the default headers should be set.
 * <br>
 * By default the download-renderer displays messages in plain-text instead of sending the file
 * if any messages have been set.
 *
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Document_Renderer_Download extends FWS_Object implements FWS_Document_Renderer
{
	/**
	 * The file which content should be sent to the browser
	 *
	 * @var string
	 */
	private $_file = null;
	
	/**
	 * The string that should be sent to the browser
	 *
	 * @var string
	 */
	private $_content = null;
	
	/**
	 * The name for the download (by default the filename, if the file is known)
	 *
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Whether the default headers should be set
	 *
	 * @var boolean
	 */
	private $_set_header = true;

	/**
	 * Sets the content that should be sent to the browser.
	 * 
	 * @param string $content the content to send
	 * @param string $name the name of the download
	 */
	public final function set_content($content,$name)
	{
		$this->set_name($name);
		$this->_content = $content;
	}

	/**
	 * Sets the file that should be sent to the browser. Please specify the complete path!
	 * You may specify the name of the download via {@link set_name}. If you don't do that
	 * the filename of the specified file will be used.
	 * 
	 * @param string $file the file to send
	 */
	public final function set_file($file)
	{
		if(!is_file($file))
			FWS_Helper::error('"'.$file.'" is no file or doesn\'t exist!');
		
		$this->_file = $file;
	}

	/**
	 * Sets the name of the download (which will be displayed in the browser)
	 * 
	 * @param string $name the name
	 */
	public final function set_name($name)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		$this->_name = $name;
	}
	
	/**
	 * Controls whether the default download-headers should be set
	 *
	 * @param boolean $set the new value
	 */
	public final function set_headers($set)
	{
		$this->_set_header = (bool)$set;
	}

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
		
		// handle messages
		$msgs = FWS_Props::get()->msgs();
		if($msgs->contains_msg())
		{
			$this->handle_msgs($msgs);
			return $this->_content;
		}
		
		// build download
		$filetype = 'application/octet-stream';
		if($this->_file !== null)
		{
			// determine content-length and filetype
			if($fileinfo = @getimagesize($this->_file))
				$filetype = 'application/'.$fileinfo['mime'];
			if($this->_set_header && $filesize = @filesize($this->_file))
				$doc->set_header('Content-Length',(string)$filesize);
			
			// set default name
			if($this->_name === null)
				$this->_name = basename($this->_file);
		}
		else if($this->_name === null)
			FWS_Helper::error('You have to set the name for the download if'
				.' you don\'t send an existing file!');
		
		if($this->_set_header)
		{
			$doc->set_header('Content-Description','File Transfer');
			$doc->set_header('Content-Type',$filetype);
			$doc->set_header('Content-Disposition','attachment; filename="'.$this->_name.'"');
		}
		
		if($this->_file !== null)
			$this->_content = FWS_FileUtils::read($this->_file);
		else if($this->_content === null)
			FWS_Helper::error('You have to set the content to send if you don\'t send a file!');
		
		return $this->_content;
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
		
		$this->_content = $str;
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