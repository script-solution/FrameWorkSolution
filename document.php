<?php
/**
 * Contains the document-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// TODO move some stuff (actions, ...) to the page-class

/**
 * This class can be used to build a the response that is send to the browser.
 * <br>
 * You can set various attributes for the document such as MIME-type, charset, title, javascript-
 * files, CSS-files and so on.
 * <br>
 * The method {@link render} renders the set template, sets the headers, compresses the result, if
 * you like, and returns it.
 * <br>
 * Note that you have to call <var>_finish()</var> before the script ends because
 * this method performs actions like closing the db-connection, writing session-data
 * to the storage and so on.
 * <br>
 * Additionally it contains a timer to measure the time for the script and you may set
 * errors to the document
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @see finish()
 */
abstract class PLIB_Document extends PLIB_Object
{
	/**
	 * The action-performer-object
	 *
	 * @var PLIB_Actions_Performer
	 */
	protected $_action_perf;

	/**
	 * The result of the action in this run.
	 *
	 * @see perform_actions()
	 * @var integer
	 */
	private $_action_result = 0;
	
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
	private $_use_gzip = true;
	
	/**
	 * The document title
	 *
	 * @var string
	 */
	private $_title = '';
	
	/**
	 * The template that should be displayed
	 *
	 * @var string
	 */
	private $_template = null;
	
	/**
	 * Stores wether the document has been shown successfully or something unexpected
	 * has happened (missing parameter, no access, ...).
	 *
	 * @var boolean
	 */
	private $_error = false;
	
	/**
	 * The header that should be set
	 *
	 * @var array
	 */
	private $_header = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		try
		{
			parent::__construct();
			
			$this->_action_perf = $this->load_action_perf();
			
			// set document
			PLIB_Props::get()->set_doc($this);
		}
		catch(PLIB_Exceptions_Critical $e)
		{
			echo $e;
		}
	}
	
	/**
	 * Renders the page and returns the result
	 * 
	 * @return string the result
	 */
	public function render()
	{
		$res = '';
		if($this->_template !== null)
		{
			$tpl = PLIB_Props::get()->tpl();
			$res = $tpl->parse_template($this->_template);
		
			// compress the result?
			if($this->_use_gzip)
				$res = $this->gzip($res);
		}
		
		// set header
		$this->send_header();
		
		return $res;
	}
	
	/**
	 * Sends all set headers
	 */
	public function send_header()
	{
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
			PLIB_Helper::def_error('notempty','tpl',$tpl);
		
		$this->_template = $tpl;
	}
	
	/**
	 * Sets the given header
	 *
	 * @param string $name the header-name
	 * @param string $value the header-value
	 * @param boolean $overwrite wether the header-value should be overwritten, if necessary
	 */
	public final function set_header($name,$value,$overwrite = true)
	{
		if($overwrite || !isset($this->_header[$name]))
			$this->_header[$name] = $value;
	}
	
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
		$type = PLIB_String::strtolower($type);
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
		$type = PLIB_String::strtolower($type);
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
		$type = PLIB_String::strtolower($type);
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
		$type = PLIB_String::strtolower($type);
		if(isset($this->_js_blocks[$type]))
			$this->_js_blocks[$type] .= "\n".$block;
		else
			$this->_js_blocks[$type] = $block;
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
	 * 	Note that you have to start with {@link PLIB_Path::client_app()}! (or http://)
	 */
	public final function redirect($url)
	{
		if(empty($url))
			PLIB_Helper::def_error('notempty','url',$url);

		$this->finish();

		header("Connection: close");
		header("HTTP/1.1 303 REDIRECT");

		if(!PLIB_String::starts_with($url,'http://'))
		{
			$parts = explode('/',PLIB_Path::outer());
			$path = '';
			for($i = 0;$i < 3;$i++)
				$path .= $parts[$i] . '/';

			if(PLIB_String::starts_with($url,'/'))
				$url = PLIB_String::substr($url,1);

			$url = $path.$url;
		}

		header('Location: '.$url);
		exit;
	}
	
	/**
	 * Finishes the page. Closes the database-connection and other things
	 */
	public function finish()
	{
		$sessions = PLIB_Props::get()->sessions();
		$db = PLIB_Props::get()->db();

		if($sessions instanceof PLIB_Session_Manager)
			$sessions->finalize();

		if($db instanceof PLIB_MySQL)
			$db->disconnect();
	}

	/**
	 * Should include, instantiate and return the action-performer-object.
	 * You may overwrite this method to change the behaviour
	 *
	 * @return PLIB_Actions_Performer the action-performer
	 */
	protected function load_action_perf()
	{
		$c = new PLIB_Actions_Performer();
		return $c;
	}
	
	/**
	 * Loads the action-class in the given module with given name, adds it to the action-performer
	 * and returns it.
	 *
	 * @param int $id the id of the action
	 * @param string $module the module-name
	 * @param string $name the name of the action
	 * @param string $folder the folder of the modules (starting at PLIB_Path::server_app())
	 * @return PLIB_Actions_Base the action or null if an error occurred
	 * @see add_action()
	 */
	public final function add_module_action($id,$module,$name,$folder)
	{
		PLIB_FileUtils::ensure_trailing_slash($folder);
		$cfolder = PLIB_Path::server_app().$folder.$module;
		if(!is_dir($cfolder))
			PLIB_Helper::error('"'.$cfolder.'" is no folder!');
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
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
			PLIB_Helper::error('"'.$file.'" is no file!');
		
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
	 * Reports an error in this module
	 * 
	 * @see error_occurred()
	 */
	public function set_error()
	{
		$this->_error = true;
	}

	/**
	 * Returns wether the module has been shown successfully or something unexpected
	 * has happened (missing parameter, no access, ...).
	 *
	 * @return boolean wether an error has been occurred
	 * @see set_error()
	 */
	public function error_occurred()
	{
		return $this->_error;
	}
	
	/**
	 * Instantiates {@link PLIB_HTML_Formular} with the action-result of the document,
	 * adds it as 'form' to the template with all methods allowed and returns
	 * the instance.
	 *
	 * @return PLIB_HTML_Formular the created formular
	 */
	public function request_formular()
	{
		$doc = PLIB_Props::get()->doc();
		$tpl = PLIB_Props::get()->tpl();

		$form = new PLIB_HTML_Formular($doc->get_action_result() === -1);
		$tpl->add_array('form',$form);
		$tpl->add_allowed_method('form','*');
		return $form;
	}

	/**
	 * Reports an error and stores that the module has not finished in a correct way.
	 * Note that you have to specify a message if the type is no error and no no-access-msg!
	 *
	 * @param int $type the type. see PLIB_Messages::MSG_TYPE_*
	 * @param string $message you can specify the message to display here, if you like
	 */
	public function report_error($type = PLIB_Messages::MSG_TYPE_ERROR,$message = '')
	{
		$locale = PLIB_Props::get()->locale();
		$msgs = PLIB_Props::get()->msgs();

		// determine message to report
		$msg = '';
		if($message !== '')
			$msg = $message;
		else
		{
			switch($type)
			{
				case PLIB_Messages::MSG_TYPE_NO_ACCESS:
					$msg = $locale->lang('permission_denied');
					break;
				
				case PLIB_Messages::MSG_TYPE_ERROR:
					$msg = $locale->lang('invalid_page');
					break;
					
				default:
					PLIB_Helper::error('Missing message or invalid type: '.$type);
			}
		}
		
		// report error
		$this->set_error();
		$msgs->add_message($msg,$type);
	}
	
	/**
	 * Compresses the result with GZip
	 *
	 * @param string $res the result
	 * @return string the modified result
	 */
	public final function gzip($res)
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
		// note that we can't use PLIB_String (with multibyte enabled) here
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
		$input = PLIB_Props::get()->input();

		$encoding = $input->get_var('HTTP_ACCEPT_ENCODING','server',PLIB_Input::STRING);
		if($encoding === null)
	    return false;

		if(PLIB_String::strpos($encoding,'x-gzip') !== false)
			return 'x-gzip';
		if(PLIB_String::strpos($encoding,'gzip') !== false)
			return 'gzip';

		return false;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>