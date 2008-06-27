<?php
/**
 * Contains the debug-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The error-handler for the library. Displays the errors in more detail.
 * You can change the output via {@link PLIB_Error_Handler::set_output_handler()} and you
 * can also set a logger to log the errors to a file, db or something like that.
 * <br>
 * Note that the error-handler defines a maximum number of errors that will be printed (and logged).
 * As soon as the limit has been reached the script will be stopped. But you may also change the
 * limit (default=50) via {@link PLIB_Error_Handler::set_max_errors()}.
 *
 * @package			PHPLib
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Error_Handler extends PLIB_Singleton
{
	/**
	 * @return PLIB_Error_Handler the instance of this class
	 */
	public static function get_instance()
	{
		return parent::_get_instance(get_class());
	}
	
	/**
	 * The maximum number of errors to report
	 *
	 * @var int
	 */
	private $_max_errors = 50;
	
	/**
	 * The number of errors that have been reported
	 *
	 * @var int
	 */
	private $_error_count = 0;
	
	/**
	 * The output-implementation
	 *
	 * @var PLIB_Error_Output
	 */
	private $_output;
	
	/**
	 * The logger-implementation
	 *
	 * @var PLIB_Error_Logger
	 */
	private $_logger = null;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_output = new PLIB_Error_Output_Default();
	}
	
	/**
	 * @return int the maximum errors that will be printed
	 */
	public function get_max_errors()
	{
		return $this->_max_errors;
	}
	
	/**
	 * Sets the maximum errors that will be printed
	 *
	 * @param int $count the new limit
	 */
	public function set_max_errors($count)
	{
		if(!PLIB_Helper::is_integer($count) || $count <= 0)
			PLIB_Helper::def_error('intgt0','count',$count);
		
		$this->_max_errors = $count;
	}
	
	/**
	 * Sets the output-handler to the given implementation
	 * 
	 * @param PLIB_Error_Output $output the output-handler
	 */
	public function set_output_handler($output)
	{
		if(!($output instanceof PLIB_Error_Output))
			PLIB_Helper::def_error('instance','output','PLIB_Error_Output',$output);
		
		$this->_output = $output;
	}
	
	/**
	 * Sets the logger to the given implementation
	 * 
	 * @param PLIB_Error_Logger $logger the logger
	 */
	public function set_logger($logger)
	{
		if(!($logger instanceof PLIB_Error_Logger))
			PLIB_Helper::def_error('instance','logger','PLIB_Error_Logger',$logger);
		
		$this->_logger = $logger;
	}
	
	/**
	 * The exception-handler
	 * 
	 * @param Exception $exception the exception
	 */
	public function handle_exception($exception)
	{
		// if somebody has used '@' to suppress errors, we will ignore them here
		// note that this means that nobody should set error-reporting to 0 because otherwise
		// we would log no error
		if(error_reporting() == 0 || $this->_max_errors())
			return;
		
		echo $this->get_error_message($exception->getCode(),$exception->getMessage(),$exception->getFile(),
			$exception->getLine(),$exception->getTrace());
		$this->_error_count++;
	}

	/**
	 * The error-handler
	 *
	 * @param int $errno the error-level
	 * @param string $msg_text the message
	 * @param string $errfile the file in which the error occurred
	 * @param int $errline the line in the file
	 */
	public function handle_error($no,$msg,$file,$line)
	{
		// if somebody has used '@' to suppress errors, we will ignore them here
		// note that this means that nobody should set error-reporting to 0 because otherwise
		// we would log no error
		if(error_reporting() == 0 || $this->_max_errors())
			return;

		echo $this->get_error_message($no,$msg,$file,$line);
		$this->_error_count++;
	}
	
	/**
	 * Builds the error-message for the given error including backtrace, if possible.
	 * Note that this method logs the error, too!
	 * 
	 * @param int $no the error-number
	 * @param string $msg the message
	 * @param string $file the file in which the error occurred
	 * @param int $line the line in the file
	 * @param array $backtrace you may specify the backtrace to print (optional)
	 * @return string the message
	 */
	public function get_error_message($no,$msg,$file,$line,$backtrace = null)
	{
		if(function_exists('debug_backtrace') && $backtrace === null)
			$backtrace = debug_backtrace();
		
		// build the backtrace if available
		$bt = null;
		if($backtrace !== null)
			$bt = $this->get_backtrace($backtrace);
		
		// log the error
		if($this->_logger !== null)
			$this->_logger->log($no,$msg,$file,$line,$bt);
		
		// hide this error from the user?
		if($no > 0 && (error_reporting() & $no) === 0)
			return '';
		
		// return the output
		return $this->_output->print_error($no,$msg,$file,$line,$bt);
	}
	
	/**
	 * Converts the given backtrace (from debug_backtrace() or exceptions) to an easier format.
	 * This can be used for example for the PLIB_Error_Output-implementations
	 *
	 * @param array $backtrace the backtrace
	 * @return array the "easier" backtrace
	 */
	public function get_backtrace($backtrace)
	{
		$bt = array();
		for($i = 0;$i < count($backtrace);$i++)
		{
			$item = array();
			if(isset($backtrace[$i]['file']))
			{
				$path_char = PLIB_String::strpos($backtrace[$i]['file'],'/') === false ? '\\' : '/';
				$item['path'] = dirname($backtrace[$i]['file']).$path_char;
				$item['file'] = basename($backtrace[$i]['file']);
			}
			
			if(isset($backtrace[$i]['line']))
				$item['line'] = $backtrace[$i]['line'];
			
			if(isset($backtrace[$i]['class']))
				$item['method'] = $backtrace[$i]['class'].'::'.$backtrace[$i]['function'];
			else if(isset($backtrace[$i]['function']))
				$item['function'] = $backtrace[$i]['function'];
			
			if(isset($backtrace[$i]['file']) && isset($backtrace[$i]['line']))
			{
				if(is_file($backtrace[$i]['file']))
				{
					$item['filepart'] = array();
					$content = file($backtrace[$i]['file']);
					$last_line = min(count($content) - 1,$backtrace[$i]['line'] + 5);
					for($l = max(0,$backtrace[$i]['line'] - 6);$l <= $last_line;$l++)
					{
						$line = preg_replace("/[\r\n]+/","",$content[$l]);
						$line_number = str_pad($l + 1,4,'0',STR_PAD_LEFT);
						$item['filepart'][$line_number] = $line;
					}
				}
			}
			
			$bt[] = $item;
		}
		
		// find a prefix that we can use for all paths
		$prefix = '';
		foreach($bt as $item)
		{
			if(isset($item['path']))
			{
				if($prefix == '')
					$prefix = $item['path'];
				else
				{
					if(strpos($item['path'],$prefix) === false)
					{
						$end = min(PLIB_String::strlen($prefix),PLIB_String::strlen($item['path']));
						for($i = 0;$i < $end;$i++)
						{
							$pc = PLIB_String::substr($prefix,$i,1);
							$ic = PLIB_String::substr($item['path'],$i,1);
							if($pc != $ic)
								break;
						}
						$prefix = PLIB_String::substr($prefix,0,$i);
					}
				}
			}
		}
		
		// cut the prefix to hide the absolute path
		foreach($bt as $k => $item)
		{
			if(isset($item['path']))
				$bt[$k]['path'] = str_replace($prefix,'/',$item['path']);
		}
		
		return $bt;
	}
	
	/**
	 * @return boolean returns true if the max-errors have been reached
	 */
	private function _max_errors()
	{
		if($this->_error_count >= $this->_max_errors)
		{
			if($this->_error_count == $this->_max_errors)
			{
				echo '<b>MAX ERRORS REACHED!</b>';
				$this->_error_count++;
			}
			return true;
		}
		
		return false;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>