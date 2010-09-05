<?php
/**
 * Contains the mutexfile-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The class implements a file that can be used mutual exclusive via flock(). I.e. it lets you
 * open a file, read and write from/to it, and close it and prevents that anyone else can access it
 * in between.
 * If the file is already locked the class will wait with usleep() until it is unlocked. You can
 * configure the sleep-time.
 * 
 * Example:
 * <code>
 * try {
 *   $mutex = new FWS_MutexFile('yourfile');
 *   $content = $mutex->read();
 *   // do stuff
 *   $mutex->write($content);
 * }
 * catch(Exception $e)
 * {
 *   echo "Open failed: " . $e->getMessage();
 * }
 * </code>
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_MutexFile extends FWS_Object
{
	/**
	 * File-pointer
	 * 
	 * @var resource
	 */
	private $_fp;
	
	/**
	 * Wether its currently locked
	 * 
	 * @var bool
	 */
	private $_locked;
	
	/**
	 * The file to use for locking
	 *
	 * @var string
	 */
	private $_file;
	
	/**
	 * The sleep-time in microseconds
	 *
	 * @var int
	 */
	private $_sleeptime;
	
	/**
	 * Constructor
	 *
	 * @param string $file the file to use as mutex
	 * @param int $sleeptime the time to sleep between checks (in microseconds); 0 = don't sleep
	 */
	public function __construct($file,$sleeptime = 10000)
	{
		if(empty($file))
			FWS_Helper::def_error('notempty','file',$file);
		
		// take care of not-existing files; "r+" does not create it, but we need it because we want
		// to read and write and start at the beginning.
		if(!is_file($file))
			$this->_fp = fopen($file,'w+');
		else
			$this->_fp = fopen($file,'r+');	
		if($this->_fp === false)
			throw new Exception('Unable to open "'.$file.'"');
		$this->_file = $file;
		$this->_locked = false;
		$this->set_sleep_time($sleeptime);
	}
	
	/**
	 * Destructor. Ensures that the file is unlocked and closed
	 */
	public function __destruct()
	{
		$this->close();
	}
	
	/**
	 * @return int the time to wait between checks in microseconds; 0 = don't sleep
	 */
	public function get_sleep_time()
	{
		return $this->_sleeptime;
	}
	
	/**
	 * Sets the sleep-time to wait between checks
	 * 
	 * @param int $time the time (in microseconds); 0 = don't sleep
	 */
	public function set_sleep_time($time)
	{
		if(!FWS_Helper::is_integer($time) || $time < 0)
			FWS_Helper::def_error('intge0','time',$time);
		$this->_sleeptime = $time;
	}
	
	/**
	 * Aquires the lock
	 */
	public function aquire()
	{
		if($this->_fp === false)
			FWS_Helper::error('The file is already closed');
		if(!$this->_locked)
		{
			// wait until it has been locked successfully
			while(!flock($this->_fp,LOCK_EX))
			{
				if($this->_sleeptime > 0)
					usleep($this->_sleeptime);
			}
			$this->_locked = true;
		}
	}
	
	/**
	 * Reads the file-content and returns it. It is required to lock the file before!
	 * 
	 * @return bool|string the file-content or false if it failed
	 */
	public function read()
	{
		if(!$this->_locked)
			FWS_Helper::error('Please aquire the lock first!');
		if($this->_fp === false)
			FWS_Helper::error('The file is already closed');
		rewind($this->_fp);
		$res = '';
		while(!feof($this->_fp))
			$res .= fread($this->_fp,4096);
		return $res;
	}
	
	/**
	 * Writes the given string to file. It is required to lock the file before!
	 * 
	 * @param string $string the string to write
	 * @return bool|int the number of written bytes or false if failed
	 */
	public function write($string)
	{
		if(!$this->_locked)
			FWS_Helper::error('Please aquire the lock first!');
		if($this->_fp === false)
			FWS_Helper::error('The file is already closed');
		
		ftruncate($this->_fp,0);
		rewind($this->_fp);
		return fwrite($this->_fp,$string);
	}
	
	/**
	 * Releases the lock
	 */
	public function release()
	{
		if($this->_locked && $this->_fp !== false)
		{
			flock($this->_fp,LOCK_UN);
			$this->_locked = false;
		}
	}
	
	/**
	 * Releases the lock, if necessary and closes the file
	 */
	public function close()
	{
		$this->release();
		if($this->_fp !== false)
		{
			fclose($this->_fp);
			$this->_fp = false;
		}
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>