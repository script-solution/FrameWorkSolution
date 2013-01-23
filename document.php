<?php
/**
 * Contains the document-class
 * 
 * @package			FrameWorkSolution
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
 * This class can be used to build the response that is send to the browser. You can set
 * the things such as MIME-type, charset, headers in general, wether GZip should be used,
 * wether a redirection should be done and so on.
 * <br>
 * The document contains an instance of {@link FWS_Document_Renderer} which should build the
 * result that should be sent to the browser. This may be an HTML-page, an image, a download
 * or anything else.
 * <br>
 * A document is also responsible for loading the module and storing it. But you may also "disable"
 * modules by returning <var>null</var> in load_module().
 * <br>
 * The method {@link render} will return the result that should be sent to the browser. By default
 * it grabs the result from the renderer, sends the headers, compresses the result if required,
 * finishes the document (closing db-connection, ...) and returns the result.
 * <br>
 * At the beginning of the render-method <var>prepare_rendering()</var> will be called. By default
 * this method inits the module. This gives the module (among other things) the chance to exchange
 * the renderer, setting document-parameters and so on. 
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Document extends FWS_Object
{
	/**
	 * Includes the module given by the action-parameter and returns the name.
	 * This is the default way to handle modules. The method assumes
	 * that the modules are at:
	 * <code>FWS_Path::server_app().$folder.$action.'/module.php'</code>
	 * The classes have to have the name:
	 * <code>$prefix.$action</code>
	 *
	 * @param string $prefix the prefix for the module-class-names
	 * @param string $action_param the name of the action-get-parameter
	 * @param string $default the default-module-name
	 * @param string $folder the folder of the modules (starting at {@link FWS_Path::server_app()})
	 * @return string the module-name
	 */
	public static function load_module_def($prefix = 'FWS_Module_',$action_param = 'action',
		$default = 'index',$folder = 'modules/')
	{
		if(empty($action_param))
			FWS_Helper::def_error('notempty','action_param',$action_param);
		if(empty($default))
			FWS_Helper::def_error('notempty','default',$default);
		if(!is_dir(FWS_Path::server_app().$folder))
			FWS_Helper::error('"'.FWS_Path::server_app().$folder.'" is no folder!');
		
		$input = FWS_Props::get()->input();
		$folder = FWS_FileUtils::ensure_trailing_slash($folder);
		$action = $input->get_var($action_param,'get',FWS_Input::IDENTIFIER);
	
		// try to load the module
		$filename = FWS_Path::server_app().$folder.$action.'/module.php';
		if(file_exists($filename))
		{
			include_once($filename);
			if(class_exists($prefix.$action))
				return $action;
		}
	
		// use default module
		include_once(FWS_Path::server_app().$folder.$default.'/module.php');
		if(class_exists($prefix.$default))
			return $default;
	
		FWS_Helper::error(
			'Unable to load a module. The default module "'.$default.'" does not exist!',
			E_USER_ERROR
		);
	
		return '';
	}
	
	/**
	 * Redirect information:
	 * <pre>
	 * 	array(
	 * 		'url' => &lt;URL&gt;,
	 * 		'time' => &lt;wait_time&gt;
	 *	)
	 * </pre>
	 * Contains false if no redirect is required
	 *
	 * @var array|boolean
	 */
	private $_redirect = false;
	
	/**
	 * The MIME-type of the document
	 *
	 * @var string
	 */
	private $_mimetype = 'text/html';
	
	/**
	 * The charset for the document
	 *
	 * @var string
	 */
	private $_charset = 'UTF-8';
	
	/**
	 * The header that should be set
	 *
	 * @var array
	 */
	private $_header = array();
	
	/**
	 * Wether the browser may cache the result
	 *
	 * @var boolean
	 */
	private $_allow_cache = false;
	
	/**
	 * Wether GZip should be used to compress the output
	 *
	 * @var boolean
	 */
	private $_use_gzip = false;
	
	/**
	 * The profiler
	 *
	 * @var FWS_Profiler
	 */
	private $_prof;
	
	/**
	 * The module
	 *
	 * @var FWS_Module
	 */
	private $_module;
	
	/**
	 * The name of the current module
	 *
	 * @var string
	 */
	protected $_module_name;
	
	/**
	 * The renderer-instance for this document
	 * 
	 * @var FWS_Document_Renderer
	 */
	private $_renderer = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		try
		{
			parent::__construct();
			
			$this->_prof = new FWS_Profiler();
			$this->_prof->start();
			$this->_module = $this->load_module();
		}
		catch(FWS_Exception_Critical $e)
		{
			echo $e;
		}
	}
	
	/**
	 * @return FWS_Profiler the profiler that has been started at the document-creation
	 */
	public final function get_profiler()
	{
		return $this->_prof;
	}
	
	/**
	 * @return string the MIME-type for the document
	 */
	public final function get_mimetype()
	{
		return $this->_mimetype;
	}
	
	/**
	 * Sets the MIME-type for the document
	 *
	 * @param string $mimetype your MIME-type
	 */
	public final function set_mimetype($mimetype)
	{
		$this->_mimetype = (string)$mimetype;
	}
	
	/**
	 * @return string the charset for the document
	 */
	public final function get_charset()
	{
		return $this->_charset;
	}
	
	/**
	 * Sets the charset for the document
	 *
	 * @param string $charset your charset
	 */
	public final function set_charset($charset)
	{
		$this->_charset = (string)$charset;
	}
	
	/**
	 * @return boolean wether the browser may cache the result
	 */
	public final function is_caching_allowed()
	{
		return $this->_allow_cache;
	}
	
	/**
	 * Sets wether the browser may cache the result
	 *
	 * @param boolean $caching the new value
	 */
	public final function set_caching_allowed($caching)
	{
		$this->_allow_cache = $caching ? true : false;
	}
	
	/**
	 * @return boolean wether the page is compressed with GZip
	 */
	public final function is_gzip()
	{
		return $this->_use_gzip;
	}
	
	/**
	 * Sets wether the page is compressed with GZip
	 *
	 * @param boolean $gzip the new value
	 */
	public final function set_gzip($gzip)
	{
		$this->_use_gzip = $gzip ? true : false;
	}
	
	/**
	 * @return array|boolean information about a redirect:
	 * <pre>
	 * 	array(
	 * 		'url' => &lt;URL&gt;,
	 * 		'time' => &lt;wait_time&gt;
	 *	)
	 * </pre>
	 * or false if no redirect has been requested.
	 */
	public final function get_redirect()
	{
		return $this->_redirect;
	}
	
	/**
	 * Requests a redirect via meta-tag to the given URL after the given time (in seconds).
	 * 
	 * @param FWS_URL|string $url the target-URL
	 * @param int $time the number of seconds to wait
	 */
	public final function request_redirect($url,$time = 3)
	{
		if(!is_string($url) && !($url instanceof FWS_URL))
			FWS_Helper::def_error('instance','url','FWS_URL',$url);
		
		if($url instanceof FWS_URL)
		{
			$url->set_absolute(true);
			$url->set_separator('&');
			$url = $url->to_url();
		}
		else
			$url = str_replace('&amp;','&',$url);
		
		$this->_redirect = array(
			'url' => $url,
			'time' => $time
		);
	}
	
	/**
	 * Redirects the user to the given URL. Takes care of IIS and other stuff.
	 * Will immediately quit the current script!
	 * 
	 * @param FWS_URL|string $url the URL where you want to redirect to. It has to be either an instance
	 * 	of {@link FWS_URL} or an absolute URL in form of a string
	 */
	public final function redirect($url)
	{
		if(!is_string($url) && !($url instanceof FWS_URL))
			FWS_Helper::def_error('instance','url','FWS_URL',$url);
		
		if($url instanceof FWS_URL)
		{
			$url->set_absolute(true);
			$url->set_separator('&');
			$url = $url->to_url();
		}
		else
			$url = str_replace('&amp;','&',$url);
		
		$this->finish();

		header('Connection: close');
		header('HTTP/1.1 303 REDIRECT');
		header('Location: '.$url);
		exit;
	}
	
	/**
	 * @return FWS_Module the module that is used (may be null)
	 */
	public final function get_module()
	{
		return $this->_module;
	}
	
	/**
	 * @return string the name of the module that is used (may be null)
	 */
	public final function get_module_name()
	{
		return $this->_module_name;
	}
	
	/**
	 * @throws FWS_Exception_UnsupportedMethod since a subclass has to overwrite this method
	 */
	public function use_default_renderer()
	{
		throw new FWS_Exception_UnsupportedMethod('This method is not implemented');
	}
	
	/**
	 * Sets the GD-image-renderer, if not already done and returns it
	 *
	 * @return FWS_Document_Renderer_GDImage the GD-image-renderer
	 */
	public function use_gdimage_renderer()
	{
		if($this->_renderer instanceof FWS_Document_Renderer_GDImage)
			return $this->_renderer;
		
		$this->_renderer = new FWS_Document_Renderer_GDImage();
		return $this->_renderer;
	}
	
	/**
	 * Sets the raw-renderer, if not already done and returns it
	 *
	 * @return FWS_Document_Renderer_Raw the plain-renderer
	 */
	public function use_raw_renderer()
	{
		if($this->_renderer instanceof FWS_Document_Renderer_Raw)
			return $this->_renderer;
		
		$this->_renderer = new FWS_Document_Renderer_Raw();
		return $this->_renderer;
	}
	
	/**
	 * Sets the download-renderer, if not already done and returns it
	 *
	 * @return FWS_Document_Renderer_Download the download-renderer
	 */
	public function use_download_renderer()
	{
		if($this->_renderer instanceof FWS_Document_Renderer_Download)
			return $this->_renderer;
		
		$this->_renderer = new FWS_Document_Renderer_Download();
		return $this->_renderer;
	}
	
	/**
	 * @return FWS_Document_Renderer the current renderer (null = not set)
	 */
	public final function get_renderer()
	{
		return $this->_renderer;
	}
	
	/**
	 * Sets the renderer that should be used
	 *
	 * @param FWS_Document_Renderer $renderer the renderer
	 */
	public final function set_renderer($renderer)
	{
		if(!($renderer instanceof FWS_Document_Renderer))
			FWS_Helper::def_error('instance','renderer','FWS_Document_Renderer',$renderer);
		
		$this->_renderer = $renderer;
	}
	
	/**
	 * Sets the given header
	 *
	 * @param string $name the header-name
	 * @param string $value the header-value
	 * @param boolean $overwrite wether the header-value should be overwritten, if existing
	 */
	public final function set_header($name,$value,$overwrite = true)
	{
		if($overwrite || !isset($this->_header[$name]))
			$this->_header[$name] = $value;
	}
	
	/**
	 * Renders the page and returns the result
	 * 
	 * @return string the result
	 */
	public function render()
	{
		$this->prepare_rendering();
		
		if($this->_renderer === null)
			FWS_Helper::error('Please specify the renderer that should be used!');
		
		// render the document
		$res = $this->_renderer->render($this);
		
		// send header and return result
		$this->_send_header();
		$this->finish();
		
		// use gzip, if required
		if($this->is_gzip())
			$res = $this->_gzip($res);
		return $res;
	}

	/**
	 * Determines the module to load and returns it
	 *
	 * @return BS_Front_Module the module (may be null)
	 */
	protected function load_module()
	{
		// by default we can't load a module
		return null;
	}
	
	/**
	 * This will be called before the renderer will be used. So you can overwrite this method
	 * and add any initialisation stuff.
	 * <br>
	 * By default just the module will be initialized
	 */
	protected function prepare_rendering()
	{
		// init the module
		if($this->_module !== null)
			$this->_module->init($this);
	}
	
	/**
	 * Finishes the page. Closes the database-connection and other things
	 */
	protected function finish()
	{
		$sessions = FWS_Props::get()->sessions();
		$sessions->finalize();
	}
	
	/**
	 * Sends all set headers
	 */
	private function _send_header()
	{
		// TODO change that!
		$this->set_header('Content-Type',$this->_mimetype.'; charset='.$this->_charset,false);
		if(!$this->_allow_cache)
		{
			// Expires in the past
			$this->set_header('Expires','Mon, 1 Jan 2001 00:00:00 GMT');
			// Always modified
	 		$this->set_header('Last-Modified',gmdate('D, d M Y H:i:s').' GMT');
	 		$this->set_header(
	 			'Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
	 		);
			// HTTP 1.0
			$this->set_header('Pragma','no-cache');
		}
		
		if(!headers_sent())
		{
			foreach($this->_header as $name => $value)
				header($name.': '.$value);
		}
	}
	
	/**
	 * Compresses the result with GZip
	 *
	 * @param string $res the result
	 * @return string the modified result
	 */
	private function _gzip($res)
	{
		// don't do it if the server does it already
		if(ini_get('output_handler') == 'ob_gzhandler')
			return $res;
		
		// determine the encoding for the client
		$encoding = $this->_get_client_encoding();
		if($encoding === false)
			return $res;
		
		// we need the zlib
		if(!extension_loaded('zlib') || ini_get('zlib.output_compression'))
			return $res;

		$this->set_header('Content-Encoding',$encoding);

		ob_start('ob_gzhandler');
		echo $res;
		$res = ob_get_contents();
		ob_end_clean();
		
		$level = 4;
		$gzip_size = strlen($res);
		$gzip_crc = crc32($res);
		$res = gzcompress($res,$level);
		// note that we can't use FWS_String (with multibyte enabled) here
		$res = substr($res,0,-4);

		$result = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		$result .= $res;
		$result .= pack('V',$gzip_crc);
		$result .= pack('V',$gzip_size);
		return $result;
	}

	/**
	 * Checks wether the client accepts gzip
	 *
	 * @return string|boolean the encoding if the client supports it or false
	 */
	private function _get_client_encoding()
	{
		$input = FWS_Props::get()->input();

		$encoding = $input->get_var('HTTP_ACCEPT_ENCODING','server',FWS_Input::STRING);
		if($encoding === null)
			return false;

		if(FWS_String::strpos($encoding,'x-gzip') !== false)
			return 'x-gzip';
		if(FWS_String::strpos($encoding,'gzip') !== false)
			return 'gzip';

		return false;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>