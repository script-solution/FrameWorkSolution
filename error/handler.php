<?php
/**
 * Contains the debug-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The error-handler for the framework. Displays the errors in more detail.
 * You can change the output via {@link FWS_Error_Handler::set_output_handler()} and you
 * can also set a logger to log the errors to a file, db or something like that.
 * <br>
 * Note that the error-handler defines a maximum number of errors that will be printed (and logged).
 * As soon as the limit has been reached the script will be stopped. But you may also change the
 * limit (default=50) via {@link FWS_Error_Handler::set_max_errors()}.
 *
 * @package			FrameWorkSolution
 * @subpackage	error
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Error_Handler extends FWS_Singleton
{
	/**
	 * @return FWS_Error_Handler the instance of this class
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
	 * @var FWS_Error_Output
	 */
	private $_output;
	
	/**
	 * The logger-implementation
	 *
	 * @var FWS_Error_Logger
	 */
	private $_logger = null;
	
	/**
	 * All FWS_Error_AllowedFiles implementations
	 *
	 * @var array
	 */
	private $_allowedfiles = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		if(PHP_SAPI == 'cli')
			$this->_output = new FWS_Error_Output_Plain();
		else
			$this->_output = new FWS_Error_Output_Default();
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
		if(!FWS_Helper::is_integer($count) || $count <= 0)
			FWS_Helper::def_error('intgt0','count',$count);
		
		$this->_max_errors = $count;
	}
	
	/**
	 * Sets the output-handler to the given implementation
	 * 
	 * @param FWS_Error_Output $output the output-handler
	 */
	public function set_output_handler($output)
	{
		if(!($output instanceof FWS_Error_Output))
			FWS_Helper::def_error('instance','output','FWS_Error_Output',$output);
		
		$this->_output = $output;
	}
	
	/**
	 * Sets the logger to the given implementation
	 * 
	 * @param FWS_Error_Logger $logger the logger
	 */
	public function set_logger($logger)
	{
		if(!($logger instanceof FWS_Error_Logger))
			FWS_Helper::def_error('instance','logger','FWS_Error_Logger',$logger);
		
		$this->_logger = $logger;
	}
	
	/**
	 * Adds the given listener to the list. It will be asked as soon as a file-content is about
	 * to be displayed so that the listener can prevent that.
	 *
	 * @param FWS_Error_AllowedFiles $listener the listener
	 */
	public function add_allowedfiles_listener($listener)
	{
		if(!($listener instanceof FWS_Error_AllowedFiles))
			FWS_Helper::def_error('instance','listener','FWS_Error_AllowedFiles',$listener);
		
		$this->_allowedfiles[] = $listener;
	}
	
	/**
	 * Removes the listener from the list
	 *
	 * @param FWS_Error_AllowedFiles $listener the listener
	 */
	public function remove_allowedfiles_listener($listener)
	{
		$i = array_search($listener,$this->_allowedfiles,true);
		if($i !== false)
			unset($this->_allowedfiles[$i]);
	}
	
	/**
	 * The exception-handler
	 * 
	 * @param Exception $exception the exception
	 */
	public function handle_exception($exception)
	{
		$no = $exception->getCode();
		// if somebody has used '@' to suppress errors, we will ignore them here
		// note that this means that nobody should set error-reporting to 0 because otherwise
		// we would log no error
		if((error_reporting() & $no) == 0 || $this->_max_errors())
			return;
		
		echo $this->get_error_message($no,$exception->getMessage(),$exception->getFile(),
			$exception->getLine(),$exception->getTrace());
		$this->_error_count++;
	}

	/**
	 * The error-handler
	 *
	 * @param int $no the error-level
	 * @param string $msg the message
	 * @param string $file the file in which the error occurred
	 * @param int $line the line in the file
	 */
	public function handle_error($no,$msg,$file,$line)
	{
		// if somebody has used '@' to suppress errors, we will ignore them here
		// note that this means that nobody should set error-reporting to 0 because otherwise
		// we would log no error
		if((error_reporting() & $no) == 0 || $this->_max_errors())
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
		try
		{
			if($this->_logger !== null)
				$this->_logger->log($no,$msg,$file,$line,$bt);
		}
		catch(Exception $e)
		{
			// ignore
		}
		
		// hide this error from the user?
		if($no > 0 && (error_reporting() & $no) === 0)
			return '';
		
		// return the output
		try
		{
			return $this->_output->print_error($no,$msg,$file,$line,$bt);
		}
		catch(Exception $e)
		{
			return '';
		}
	}
	
	/**
	 * Converts the given backtrace (from debug_backtrace() or exceptions) to an easier format.
	 * This can be used for example for the FWS_Error_Output-implementations
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
				$path_char = FWS_String::strpos($backtrace[$i]['file'],'/') === false ? '\\' : '/';
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
				if(is_file($backtrace[$i]['file']) && $this->_can_display_file($backtrace[$i]['file']))
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
						$end = min(FWS_String::strlen($prefix),FWS_String::strlen($item['path']));
						for($i = 0;$i < $end;$i++)
						{
							$pc = FWS_String::substr($prefix,$i,1);
							$ic = FWS_String::substr($item['path'],$i,1);
							if($pc != $ic)
								break;
						}
						$prefix = FWS_String::substr($prefix,0,$i);
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
	 * Checks wether the given file may be displayed
	 *
	 * @param string $file the file
	 * @return boolean true if so
	 */
	private function _can_display_file($file)
	{
		foreach($this->_allowedfiles as $l)
		{
			if(!$l->can_display_file($file))
				return false;
		}
		return true;
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
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>