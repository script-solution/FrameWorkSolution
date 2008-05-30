<?php
/**
 * Contains the default-error-output-generator-class
 *
 * @version			$Id: default.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The default implementation of the output-generator-interface
 * 
 * @package			PHPLib
 * @subpackage	error.output
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Error_Output_Default extends PLIB_FullObject implements PLIB_Error_Output
{
	/**
	 * @see PLIB_Error_Output::print_error()
	 *
	 * @param int $no
	 * @param string $msg
	 * @param string $file
	 * @param int $line
	 * @param array $backtrace
	 */
	public function print_error($no,$msg,$file,$line,$backtrace)
	{
		// javascript-clap-code
		$res = '<script type="text/javascript" src="'.PLIB_Path::lib().'js/basic.js"></script>'."\n";
		
		$htmlmsg = str_replace("\n",'<br />',$msg);
		$res .= '<div style="font-size: 12px; padding-bottom: 5px;">'."\n";
		$res .= '<b><span style="color: #ff0000;">'.$htmlmsg.'</span></b>';

		// add html-backtrace
		if($backtrace !== null)
		{
			$htmlbt = new PLIB_Error_BTPrinter_HTML();
			$res .= $htmlbt->print_backtrace($backtrace);
		}
		else
		{
			$realfile = str_replace(realpath(PLIB_Path::inner()),'',$file);
			$realpath = str_replace($realfile,'',$file);
			$res .= ' in '.$realpath.'<b>'.$realfile.'</b>, line <b>'.$line.'</b>'."\n";
			$res .= '<br />'."\n";
		}
		
		// add bbcode-block
		$rand_str = PLIB_StringHelper::generate_random_key(10);
		$res .= '<a href="javascript:PLIB_toggleElement(\'error_bbcode_'.$rand_str.'\');">Show BBCode';
		$res .= ' for the error-message (to post somewhere)</a>'."\n";
		$res .= '<div id="error_bbcode_'.$rand_str.'" style="padding: 5px; margin-top: 5px;';
		$res .= ' line-height: 15px; border: 1px dotted #AAAAAA; display: none;';
		$res .= ' font-family: courier new;">'."\n";
		$res .= '	<pre style="padding: 0px; margin: 0px;">'."\n";
		
		// add bbcode-message
		$res .= '[b][color=#FF0000]'.$msg.'[/color][/b]';
		if($backtrace !== null)
		{
			$bbcbt = new PLIB_Error_BTPrinter_BBCode();
			$res .= $bbcbt->print_backtrace($backtrace);
		}
		else
		{
			$realfile = str_replace(realpath(PLIB_Path::inner()),'',$file);
			$realpath = str_replace($file,'',$realfile);
			$res .= ' in '.$realpath.'[b]'.$realfile.'[/b], line [b]'.$line.'[/b]';
		}
		
		$res .= '	</pre>'."\n";
		$res .= '</div>'."\n";
		$res .= '</div>'."\n";

		return $res;
	}

	/**
	 * @see PLIB_Object::_get_print_vars()
	 *
	 * @return array
	 */
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>