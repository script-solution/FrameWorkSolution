<?php
/**
 * TODO: describe the file
 *
 * @version			$Id$
 * @package			Boardsolution
 * @subpackage	main
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

abstract class PLIB_Page extends PLIB_Document
{
	/**
	 * Indicates wether any output should be done
	 *
	 * @var boolean
	 */
	private $_output = true;
	
	/**
	 * Indicates wether the header should be shown
	 *
	 * @var boolean
	 */
	private $_show_header = true;
	
	/**
	 * Indicates wether the content should be shown
	 *
	 * @var boolean
	 */
	private $_show_content = true;
	
	/**
	 * Indicates wether the footer should be shown
	 *
	 * @var boolean
	 */
	private $_show_footer = true;
	
	/**
	 * All available actions for this page
	 *
	 * @var array
	 */
	private $_actions = array();
	
	/**
	 * The location of this page
	 *
	 * @var array
	 */
	private $_location = array();
	
	/**
	 * @see PLIB_Document::render()
	 *
	 * @return string
	 */
	public final function render()
	{
		$res = '';
		try
		{
			$this->before_start();
			
			// init the page
			$this->init();
			
			// header
			if($this->_output && $this->_show_header)
				$this->header();
		
			// content
			if($this->_show_content)
			{
				ob_start();
				$this->content();
				$res .= ob_get_contents();
				ob_clean();
			}
			
			// footer
			if($this->_output && $this->_show_footer)
				$this->footer();
			
			// render everything
			if($this->_output)
			{
				$this->before_render();
				$res .= parent::render();
			}
			
			$this->before_finish();
			
			// finish the document
			$this->finish();
		}
		catch(PLIB_Exceptions_Critical $e)
		{
			$res = $e->__toString();
		}
		
		return $res;
	}
	
	/**
	 * This method will be called at the very beginning (in every case!)
	 */
	protected function before_start()
	{
		// do nothing by default
	}
	
	/**
	 * This method will be called before the rendering will be done. If the output is disabled
	 * the method will NOT be called!
	 */
	protected function before_render()
	{
		// do nothing by default
	}
	
	/**
	 * This method will be called at the very end before finish() will be called (in every case!)
	 */
	protected function before_finish()
	{
		// do nothing by default
	}
	
	/**
	 * @return boolean wether any output should be done
	 */
	public final function is_output_enabled()
	{
		return $this->_output;
	}
	
	/**
	 * Sets wether any output should be done
	 *
	 * @param boolean $output the new value
	 */
	public final function set_output_enabled($output)
	{
		$this->_output = $output ? true : false;
	}
	
	/**
	 * @return boolean wether the header will be shown
	 */
	public final function is_header_shown()
	{
		return $this->_show_header;
	}
	
	/**
	 * Sets wether the header will be shown
	 *
	 * @param boolean $header the new value
	 */
	public final function set_show_header($header)
	{
		$this->_show_header = $header ? true : false;
	}
	
	/**
	 * @return boolean wether the content will be shown
	 */
	public final function is_content_shown()
	{
		return $this->_show_content;
	}
	
	/**
	 * Sets wether the content will be shown
	 *
	 * @param boolean $content the new value
	 */
	public final function set_show_content($content)
	{
		$this->_show_content = $content ? true : false;
	}
	
	/**
	 * @return boolean wether the footer will be shown
	 */
	public final function is_footer_shown()
	{
		return $this->_show_footer;
	}
	
	/**
	 * Sets wether the footer will be shown
	 *
	 * @param boolean $footer the new value
	 */
	public final function set_show_footer($footer)
	{
		$this->_show_footer = $footer ? true : false;
	}
	
	/**
	 * Will be called before the page will be build. This can be used to set parameters for the page
	 * or something similar.
	 */
	protected function init()
	{
		// by default we do nothing
	}
	
	/**
	 * Adds the header to the page
	 *
	 * If any kind of error appears (for example: a parameter is invalid), please call
	 * report_error() to let the module know that something went wrong.
	 *
	 * @see report_error()
	 */
	protected abstract function header();
	
	/**
	 * The method which should build the content for the page
	 *
	 * If any kind of error appears (for example: a parameter is invalid), please call
	 * report_error() to let the module know that something went wrong.
	 *
	 * @see report_error()
	 */
	protected abstract function content();
	
	/**
	 * Adds the footer to the page
	 *
	 * If any kind of error appears (for example: a parameter is invalid), please call
	 * report_error() to let the module know that something went wrong.
	 *
	 * @see report_error()
	 */
	protected abstract function footer();
	
	/**
	 * This method will be used to determine the location
	 * it should return an array with the location parts.
	 *
	 * For example if you have a location like:
	 * 'Home' => 'YourPage' => 'YourSubPage'
	 * you should return something like:
	 * <code>
	 * 	array(
	 * 		'YourPage' => 'index.php?action=yourpage',
	 * 		'YourSubPage' => 'index.php?action=yourpage&amp;mode=subpage'
	 * 	)
	 * </code>
	 * Note that an empty URL will lead to a text instead of a link!
	 *
	 * @see PLIB_Helper::generate_location()
	 * @return array an array of the following form: <code>array(<name> => <url>[, ...])</code>
	 */
	public final function get_location()
	{
		return $this->_location;
	}
	
	/**
	 * Sets the location of this page.
	 * <br>
	 * For example if you have a location like:
	 * 'Home' => 'YourPage' => 'YourSubPage'
	 * you should return something like:
	 * <code>
	 * 	array(
	 * 		'YourPage' => 'index.php?action=yourpage',
	 * 		'YourSubPage' => 'index.php?action=yourpage&amp;mode=subpage'
	 * 	)
	 * </code>
	 * Note that an empty URL will lead to a text instead of a link!
	 *
	 * @param string $name the name of the breadcrumb
	 * @param string $url the URL of the breadcrumb (may be empty if it should be no link)
	 */
	public final function add_breadcrumb($name,$url = '')
	{
		$this->_location[] = array($name,$url);
	}

	/**
	 * You may use this method to define some actions for your module.
	 * Please return an associative array of the following form:
	 * <code>
	 * 	array(
	 * 		<actionID> => <actionName>,
	 * 		...
	 *  )
	 * </code>
	 * Note that <actionName> has to be in the filename:
	 * <code><prefix><actionName>.php</code>
	 * You can specify the prefix in the action-performer.
	 * Additionally the file(-path) has to be:
	 * <code>modules/<moduleName>/action_<actionName>.php</code>
	 *
	 * @return array the actions
	 */
	public final function get_actions()
	{
		return $this->_actions;
	}
	
	/**
	 * Adds the given actions for this page.
	 * Note that <var>$name</var> has to be in the filename:
	 * <code><prefix>$name.php</code>
	 * You can specify the prefix in the action-performer.
	 * Additionally the file(-path) has to be:
	 * <code>modules/<moduleName>/action_$name.php</code>
	 * <br>
	 * The parameter <var>$name</var> may also be an array with the name as first element. You
	 * can put additional elements in the array which will be passed as arguments to the
	 * perform_action()-method.
	 *
	 * @param mixed $id the id of the action
	 * @param mixed $name the name of the action
	 */
	public final function add_action($id,$name)
	{
		$this->_actions[$id] = $name;
	}

	/**
	 * checks the user has access to this module
	 *
	 * @return boolean true if the user is allowed to use this module
	 */
	public function has_access()
	{
		return true;
	}
}
?>