<?php
/**
 * Contains the default html-renderer-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The default HTML-renderer. Has a header-, content- and footer-section. Each of them can be
 * disabled. The render-result will be the result of the template-rendering. You can set the
 * template that should be used.
 * <br>
 * Note that you have to implement the header and footer! You may also overwrite the content-
 * method and other things. And you may react on events that are fired during the execution
 * of the render-method.
 * <br>
 * Additionally it is responsible for the actions. You can add actions to this class (or lets
 * better say the action-performer, which this class holds), perform the actions and retrieve
 * the result. Note that you have to perform the actions by yourself!
 * <br>
 * It contains also the breadcrumbs which can be added step by step and be build to a string
 * at the end.
 *
 * @package			FrameWorkSolution
 * @subpackage	document.renderer
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Document_Renderer_HTML_Default extends FWS_Document_Renderer_HTML_Base
{
	/**
	 * The template that should be displayed
	 *
	 * @var string
	 */
	private $_template = null;
	
	/**
	 * Wether the current user has access to this page
	 *
	 * @var boolean
	 */
	private $_has_access = true;
	
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
	 * The breadcrumbs of this page
	 *
	 * @var array
	 */
	private $_breadcrumbs = array();

	/**
	 * The result of the action in this run.
	 *
	 * @see perform_actions()
	 * @var integer
	 */
	private $_action_result = 0;
	
	/**
	 * The action-performer-object
	 *
	 * @var FWS_Actions_Performer
	 */
	protected $_action_perf;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		try
		{
			parent::__construct();
			
			$this->_action_perf = $this->load_action_perf();
		}
		catch(FWS_Exceptions_Critical $e)
		{
			echo $e;
		}
	}

	/**
	 * @return boolean true if the current user has access to this page
	 */
	public final function has_access()
	{
		return $this->_has_access;
	}
	
	/**
	 * Sets wether the current user has access to this page
	 *
	 * @param boolean $has_access the new value
	 */
	public final function set_has_access($has_access)
	{
		$this->_has_access = (bool)$has_access;
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
	 * @return string the template that should be displayed (null if not set)
	 */
	public final function get_template()
	{
		return $this->_template;
	}
	
	/**
	 * Sets the template that should be displayed
	 *
	 * @param string $tpl the template
	 */
	public final function set_template($tpl)
	{
		if(empty($tpl))
			FWS_Helper::def_error('notempty','tpl',$tpl);
		
		$this->_template = $tpl;
	}
	
	/**
	 * Adds a breadcrumb to this renderer
	 * 
	 * @param string $name the name of the breadcrumb
	 * @param string $url the URL of the breadcrumb (may be empty if it should be no link)
	 */
	public final function add_breadcrumb($name,$url = '')
	{
		$this->_breadcrumbs[] = array($name,$url);
	}
	
	/**
	 * Adds the given action to the action-performer. The file-path has to be:
	 * <code><prefix><moduleName>/action_$name.php</code>
	 * You can specify the prefix in the action-performer.
	 * <br>
	 * The parameter <var>$name</var> may also be an array with the name as first element. You
	 * can put additional elements in the array which will be passed as arguments to the
	 * perform_action()-method.
	 *
	 * @param mixed $id the id of the action
	 * @param string $name the name of the action
	 */
	public final function add_action($id,$name)
	{
		$doc = FWS_Props::get()->doc();
		$this->_action_perf->add_actions($doc->get_module_name(),array($id => $name));
	}
	
	/**
	 * Loads the action-class in the given module with given name, adds it to the action-performer
	 * and returns it.
	 *
	 * @param int $id the id of the action
	 * @param string $module the module-name
	 * @param string $name the name of the action
	 * @param string $folder the folder of the modules (starting at FWS_Path::server_app())
	 * @return FWS_Actions_Base the action or null if an error occurred
	 * @see add_action()
	 */
	public final function add_module_action($id,$module,$name,$folder)
	{
		FWS_FileUtils::ensure_trailing_slash($folder);
		$cfolder = FWS_Path::server_app().$folder.$module;
		if(!is_dir($cfolder))
			FWS_Helper::error('"'.$cfolder.'" is no folder!');
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		
		$file = $cfolder.'/action_'.$name.'.php';
		if(is_file($file))
		{
			$prefix = $this->_action_perf->get_prefix();
			include_once($file);
			$classname = $prefix.$module.'_'.$name;
			if(class_exists($classname))
			{
				$action = new $classname($id);
				$this->_action_perf->add_action($action);
				return $action;
			}
		}
		else
			FWS_Helper::error('"'.$file.'" is no file!');
		
		return null;
	}

	/**
	 * Performs the necessary action
	 * You will find the result in get_action_result()
	 */
	public final function perform_actions()
	{
		$this->_action_result = $this->_action_perf->perform_actions();
	}

	/**
	 * @return the result of the action in this run. This is:
	 * <pre>
	 * 	-1 = error
	 * 	 0 = success / nothing done
	 * 	 1 = success + status-page
	 * </pre>
	 */
	public final function get_action_result()
	{
		return $this->_action_result;
	}
	
	/**
	 * Generates the location-string
	 *
	 * @param FWS_Document_Renderer_HTML_Default $renderer the current page
	 * @param string $linkclass the linkclass to use. Use an empty string if you want to use a class.
	 * @param string $sep the separator ( &raquo; by default)
	 * @return array the position and document-title
	 */
	protected function get_breadcrumbs($linkclass = '',$sep = ' &raquo; ')
	{
		$links = array();
		foreach($this->_breadcrumbs as $item)
		{
			list($name,$url) = $item;
			if($url == '')
				$links[] = $name;
			else
			{
				$link = '<a ';
				if($linkclass)
					$link .= 'class="'.$linkclass.'" ';
				$link .= 'href="'.$url.'">'.$name.'</a>';
				$links[] = $link;
			}
		}
		
		return implode($sep,$links);
	}

	/**
	 * Should include, instantiate and return the action-performer-object.
	 * You may overwrite this method to change the behaviour
	 *
	 * @return FWS_Actions_Performer the action-performer
	 */
	protected function load_action_perf()
	{
		$c = new FWS_Actions_Performer();
		return $c;
	}
	
	/**
	 * @see FWS_Document_Renderer::render()
	 *
	 * @param FWS_Document $doc
	 */
	public final function render($doc)
	{
		$res = '';
		try
		{
			$this->before_start();
			
			// header
			if($this->_show_header)
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
			if($this->_show_footer)
				$this->footer();
			
			// render everything
			$this->before_render();
			if($this->_template !== null)
			{
				$tpl = FWS_Props::get()->tpl();
				$res .= $tpl->parse_template($this->_template);
			}
			
			$this->before_finish();
		}
		catch(FWS_Exceptions_Critical $e)
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
	protected function content()
	{
		$doc = FWS_Props::get()->doc();
		$tpl = FWS_Props::get()->tpl();
		
		// run the module
		if($doc->get_module() !== null)
		{
			$tpl->set_template($this->get_template());
			
			$doc->get_module()->run();
			
			$tpl->restore_template();
		}
	}
	
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
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>