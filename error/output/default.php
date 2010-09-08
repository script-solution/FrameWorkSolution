<?php
/**
 * Contains the default-error-output-generator-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The default implementation of the output-generator-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Error_Output_Default extends FWS_Object implements FWS_Error_Output
{
	/**
	 * Wether the calltrace is displayed
	 *
	 * @var boolean
	 */
	private $_trace;
	
	/**
	 * Wether bbcode is displayed
	 *
	 * @var boolean
	 */
	private $_bbcode;
	
	/**
	 * Constructor
	 *
	 * @param boolean $trace wether the calltrace should be displayed
	 * @param boolean $bbcode wether bbcode should be displayed
	 */
	public function __construct($trace = true,$bbcode = true)
	{
		parent::__construct();
		$this->_trace = $trace;
		$this->_bbcode = $bbcode;
	}
	
	/**
	 * @see FWS_Error_Output::print_error()
	 *
	 * @param int $no
	 * @param string $msg
	 * @param string $file
	 * @param int $line
	 * @param array $backtrace
	 * @return string
	 */
	public function print_error($no,$msg,$file,$line,$backtrace)
	{
		// javascript-clap-code
		$res = '';
		if($this->_trace || $this->_bbcode)
			$res .= '<script type="text/javascript" src="'.FWS_Path::client_fw().'js/basic.js"></script>'."\n";
		
		$htmlmsg = str_replace("\n",'<br />',$msg);
		$res .= '<div style="font-size: 12px; padding-bottom: 5px;">'."\n";
		$res .= '<b><span style="color: #ff0000;">'.$htmlmsg.'</span></b>';

		// add html-backtrace
		if($this->_trace && $backtrace !== null)
		{
			$htmlbt = new FWS_Error_BTPrinter_HTML();
			$res .= $htmlbt->print_backtrace($backtrace);
		}
		else
		{
			$realfile = str_replace(realpath(FWS_Path::server_app()),'',$file);
			$realpath = str_replace($realfile,'',$file);
			$res .= ' in '.$realpath.'<b>'.$realfile.'</b>, line <b>'.$line.'</b>'."\n";
			$res .= '<br />'."\n";
		}
		
		if($this->_bbcode)
		{
			// add bbcode-block
			$rand_str = FWS_StringHelper::generate_random_key(10);
			$res .= '<a href="javascript:FWS_toggleElement(\'error_bbcode_'.$rand_str.'\');">Show BBCode';
			$res .= ' for the error-message (to post somewhere)</a>'."\n";
			$res .= '<div id="error_bbcode_'.$rand_str.'" style="padding: 5px; margin-top: 5px;';
			$res .= ' line-height: 15px; border: 1px dotted #AAAAAA; display: none;';
			$res .= ' font-family: courier new;">'."\n";
			$res .= '	<pre style="padding: 0px; margin: 0px;">'."\n";
			
			// add bbcode-message
			$res .= '[b][color=#FF0000]'.$msg.'[/color][/b]';
			if($this->_trace && $backtrace !== null)
			{
				$bbcbt = new FWS_Error_BTPrinter_BBCode();
				$res .= $bbcbt->print_backtrace($backtrace);
			}
			else
			{
				$realfile = str_replace(realpath(FWS_Path::server_app()),'',$file);
				$realpath = str_replace($file,'',$realfile);
				$res .= ' in '.$realpath.'[b]'.$realfile.'[/b], line [b]'.$line.'[/b]';
			}
			
			$res .= '	</pre>'."\n";
			$res .= '</div>'."\n";
		}
		$res .= '</div>'."\n";

		return $res;
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