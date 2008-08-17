<?php
/**
 * Contains the base-html-renderer-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The base-class for all HTML-renderer. It contains some attributes that are typical for HTML-
 * documents which can be manipulated and retrieved at the end to pass them to a template or what
 * ever.
 * <br>
 * You may use this class as base-class for your HTML-renderer if you want to build a different
 * one than the default-implementation (header, content, footer, action-execution, ...)
 * <br>
 * You can and have to implement the render-method by yourself.
 *
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Document_Renderer_HTML_Base extends FWS_Object implements FWS_Document_Renderer
{
	/**
	 * The document title
	 *
	 * @var string
	 */
	private $_title = '';
	
	/**
	 * An array of CSS files that should be used
	 *
	 * @var array
	 */
	private $_css_files = array();
	
	/**
	 * An array of CSS-blocks that should be used
	 *
	 * @var array
	 */
	private $_css_blocks = array();
	
	/**
	 * An array of javascript files that should be used
	 *
	 * @var array
	 */
	private $_js_files = array();
	
	/**
	 * An array of javascript-blocks that should be used
	 *
	 * @var array
	 */
	private $_js_blocks = array();
	
	/**
	 * @return string the document-title
	 */
	public final function get_title()
	{
		return $this->_title;
	}
	
	/**
	 * Sets the title for the document
	 *
	 * @param string $title the new title
	 */
	public final function set_title($title)
	{
		$this->_title = (string)$title;
	}
	
	/**
	 * @return array all CSS files in the format:
	 * 	<code>
	 * 	array(array('file' => ..., 'media' => ..., 'type => ...), ...)
	 * 	</code>
	 */
	public final function get_css_files()
	{
		$files = array();
		foreach($this->_css_files as $file => $attr)
		{
			$files[] = array(
				'src' => $file,
				'media' => $attr[1],
				'type' => $attr[0]
			);
		}
		return $files;
	}
	
	/**
	 * @return array all CSS blocks in the format:
	 * 	<code>
	 * 	array(array('code' => ..., 'type' => ...), ...)
	 * 	</code>
	 */
	public final function get_css_blocks()
	{
		$blocks = array();
		foreach($this->_css_blocks as $type => $code)
		{
			$blocks[] = array(
				'code' => $code,
				'src' => $type
			);
		}
		return $blocks;
	}
	
	/**
	 * @return array all javascript files in the format:
	 * 	<code>
	 * 	array(array('file' => ..., 'type => ...), ...)
	 * 	</code>
	 */
	public final function get_js_files()
	{
		$files = array();
		foreach($this->_js_files as $file => $type)
		{
			$files[] = array(
				'src' => $file,
				'type' => $type
			);
		}
		return $files;
	}
	
	/**
	 * @return array all javascript blocks in the format:
	 * 	<code>
	 * 	array(array('code' => ..., 'type' => ...), ...)
	 * 	</code>
	 */
	public final function get_js_blocks()
	{
		$blocks = array();
		foreach($this->_js_blocks as $type => $code)
		{
			$blocks[] = array(
				'code' => $code,
				'type' => $type
			);
		}
		return $blocks;
	}
	
	/**
	 * Adds the given CSS file with given MIME-type and for the given media to the document
	 *
	 * @param string $file the file
	 * @param string $type the MIME-type (text/css by default)
	 * @param string $media the media (null by default)
	 */
	public final function add_css_file($file,$type = 'text/css',$media = null)
	{
		$type = FWS_String::strtolower($type);
		$this->_css_files[$file] = array($type,$media);
	}
	
	/**
	 * Adds the given CSS-definitions with given MIME-type to the document
	 *
	 * @param string $block your CSS definitions
	 * @param string $type the MIME-type (text/css by default)
	 */
	public final function add_css_block($block,$type = 'text/css')
	{
		$type = FWS_String::strtolower($type);
		if(isset($this->_css_blocks[$type]))
			$this->_css_blocks[$type] .= "\n".$block;
		else
			$this->_css_blocks[$type] = $block;
	}
	
	/**
	 * Adds the given javascript-file to the document
	 *
	 * @param string $file the file
	 * @param string $type the MIME-type (text/javascript by default)
	 */
	public final function add_js_file($file,$type = 'text/javascript')
	{
		$type = FWS_String::strtolower($type);
		$this->_js_files[$file] = $type;
	}
	
	/**
	 * Adds the given javascript block to the document
	 *
	 * @param string $block your javascript
	 * @param string $type the MIME-type (text/javascript by default)
	 */
	public final function add_js_block($block,$type = 'text/javascript')
	{
		$type = FWS_String::strtolower($type);
		if(isset($this->_js_blocks[$type]))
			$this->_js_blocks[$type] .= "\n".$block;
		else
			$this->_js_blocks[$type] = $block;
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