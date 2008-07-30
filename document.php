<?php
/**
 * Contains the document-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class can be used to build a the response that is send to the browser. You can set
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
	 * Redirect information:
	 * <pre>
	 * 	array(
	 * 		'url' => &lt;URL&gt;,
	 * 		'time' => &lt;wait_time&gt;
	 *	)
	 * </pre>
	 * Contains false if no redirect is required
	 *
	 * @var mixed
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
			
			$this->_module = $this->load_module();
		}
		catch(FWS_Exceptions_Critical $e)
		{
			echo $e;
		}
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
	 * @return mixed information about a redirect:
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
	 * @param string $url the target-URL
	 * @param int $time the number of seconds to wait
	 */
	public final function request_redirect($url,$time = 3)
	{
		$this->_redirect = array(
			'url' => $url,
			'time' => $time
		);
	}
	
	/**
	 * Redirects the user to the given URL. Takes care of IIS and other stuff.
	 * Will immediately quit the current script!
	 *
	 * @param string $url the URL where you want to redirect to
	 * 	Note that you have to start with {@link FWS_Path::client_app()}! (or http://)
	 */
	public final function redirect($url)
	{
		if(empty($url))
			FWS_Helper::def_error('notempty','url',$url);

		$this->finish();

		header("Connection: close");
		header("HTTP/1.1 303 REDIRECT");

		if(!FWS_String::starts_with($url,'http://'))
		{
			$parts = explode('/',FWS_Path::outer());
			$path = '';
			for($i = 0;$i < 3;$i++)
				$path .= $parts[$i] . '/';

			if(FWS_String::starts_with($url,'/'))
				$url = FWS_String::substr($url,1);

			$url = $path.$url;
		}

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
	 * Returns the default renderer. If it is already set the instance will be returned. Otherwise
	 * it will be created, set and returned.
	 *
	 * @return FWS_Document_Renderer_HTML_Default the default renderer
	 */
	public function use_default_renderer()
	{
		throw new FWS_Exceptions_UnsupportedMethod("This method is not implemented");
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
		$db = FWS_Props::get()->db();

		if($sessions instanceof FWS_Session_Manager)
			$sessions->finalize();

		if($db instanceof FWS_MySQL)
			$db->disconnect();
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
	 * @return mixed the encoding if the client supports it or false
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
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>