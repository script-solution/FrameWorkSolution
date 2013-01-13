<?php
/**
 * Contains the http-class
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
 * Provides a simple API to perform POST-/GET-requests via the HTTP-protocol.
 * Example:
 * <code>
 * $http = new FWS_HTTP('yourServer',80);
 * if(($reply = $http->get('/file.php')) !== false)
 * 	echo $reply;
 * else
 * 	echo 'Error: '.$http->get_error_message();
 * </code>
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_HTTP extends FWS_Object
{
	/**
	 * The host
	 *
	 * @var string
	 */
	private $_host;
	
	/**
	 * The port
	 *
	 * @var int
	 */
	private $_port;
	
	/**
	 * The timeout
	 *
	 * @var int
	 */
	private $_timeout;
	
	/**
	 * The last error-message
	 *
	 * @var string
	 */
	private $_error = '';
	
	/**
	 * The last error-number
	 *
	 * @var int
	 */
	private $_errno = -1;
	
	/**
	 * The last received headers
	 *
	 * @var array
	 */
	private $_headers = array();
	
	/**
	 * Constructor
	 *
	 * @param string $host the host to connect to
	 * @param int $port the port to connect to
	 * @param int $timeout the timeout in seconds
	 */
	public function __construct($host,$port = 80,$timeout = 10)
	{
		if(empty($host))
			FWS_Helper::def_error('notempty','host',$host);
		if(!FWS_Helper::is_integer($port) || $port <= 0)
			FWS_Helper::def_error('intgt0','port',$port);
		if(!FWS_Helper::is_integer($timeout) || $timeout <= 0)
			FWS_Helper::def_error('intgt0','timeout',$timeout);
		
		$this->_host = $host;
		$this->_port = $port;
		$this->_timeout = $timeout;
	}
	
	/**
	 * @return string the last error-message
	 */
	public function get_error_message()
	{
		return $this->_error;
	}
	
	/**
	 * @return int the last error-code
	 */
	public function get_error_code()
	{
		return $this->_errno;
	}
	
	/**
	 * @return array an associative array with all values of the header that has been received for
	 * 	the last request
	 */
	public function get_header()
	{
		return $this->_headers;
	}
	
	/**
	 * Performs a GET-request for the given path
	 *
	 * @param string $path the path (may include parameter)
	 * @return string|boolean the reply (without header) or false if failed
	 */
	public function get($path)
	{
		if(empty($path) || !is_string($path) || $path[0] != '/')
			FWS_Helper::error('Please provide a valid path (not empty and starting with /)');
		
		$out = "GET ".$path." HTTP/1.1\r\n";
		$out .= "Host: ".$this->_host."\r\n";
		$out .= "Connection: Close\r\n\r\n";
		return $this->_send_request($out);
	}
	
	/**
	 * Performs a POST-request for the given path with the given variables
	 *
	 * @param string $path the path
	 * @param array $vars an associative array with the vars to send via POST
	 * @return string|boolean the reply (without header) or false if failed
	 */
	public function post($path,$vars)
	{
		if(empty($path) || !is_string($path) || $path[0] != '/')
			FWS_Helper::error('Please provide a valid path (not empty and starting with /)');
		if(!is_array($vars))
			FWS_Helper::def_error('array','vars',$vars);
		
		$data = $this->_get_post_data($vars);
		$out = "POST ".$path." HTTP/1.1\r\n";
		$out .= "Host: ".$this->_host."\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-Length: ".FWS_String::strlen($data)."\r\n";
		$out .= "Connection: Close\r\n\r\n";
		$out .= $data;
		return $this->_send_request($out);
	}
	
	/**
	 * Builds the data to send from the given array
	 *
	 * @param array $vars an associative array with the vars to send via POST
	 * @param string $pre the prefix for the keys
	 * @return string the post-data
	 */
	private function _get_post_data($vars,$pre = '')
	{
		$data = '';
		$i = 0;
		$len = count($vars);
		foreach($vars as $k => $v)
		{
			if(is_array($v))
				$data .= $this->_get_post_data($v,$pre != '' ? $pre.'['.$k.']' : $k);
			else
			{
				$name = $pre != '' ? urlencode($pre.'['.$k.']') : urlencode($k);
				$data .= $name.'='.urlencode($v);
			}
			
			if($i < $len - 1)
				$data .= '&';
			$i++;
		}
		
		return $data;
	}
	
	/**
	 * Sends the given request
	 * 
	 * @param string $request the request to send
	 * @return string|bool the reply or false if failed
	 */
	private function _send_request($request)
	{
		$this->_errno = -1;
		$this->_error = '';
		$sock = @fsockopen($this->_host,$this->_port,$this->_errno,$this->_error,$this->_timeout);
		if(!$sock)
			return false;
		
		// send request
		fwrite($sock,$request);

		// read reply
		$reply = '';
		while(!feof($sock))
			$reply .= fgets($sock,128);
		fclose($sock);
		
		// TODO is this correct?
		// check reply code
		if(!FWS_String::starts_with($reply,'HTTP/1.1 200'))
		{
			$matches = array();
			preg_match('/^HTTP\/[\d\.]+\s+(\d+)\s+(.*)/',$reply,$matches);
			$this->_error = $matches[2];
			$this->_errno = $matches[1];
			return false;
		}
		
		// determine header-end
		$cut = FWS_String::strpos($reply,"\r\n\r\n");
		if($cut === false)
		{
			$this->_error = 'Invalid reply';
			return false;
		}
		
		// save headers
		$this->_headers = array();
		$headers = FWS_String::substr($reply,0,$cut);
		$lines = preg_split('/[\r\n]/',$headers);
		foreach($lines as $line)
		{
			$dotpos = FWS_String::strpos($line,':');
			if($dotpos === false)
				continue;
			
			$this->_headers[FWS_String::substr($line,0,$dotpos)] = trim(FWS_String::substr($line,$dotpos + 1));
		}
		
		// return reply
		$reply = FWS_String::substr($reply,$cut + 4);
		if(!isset($this->_headers['Transfer-Encoding']) ||
				$this->_headers['Transfer-Encoding'] != 'chunked')
			return $reply;
		
		// read chunks
		$p = 0;
		$len = strlen($reply);
		$res = '';
		do {
			if($p >= $len)
				break;
			$nl = strpos($reply,"\r\n",$p);
			if($nl === false)
				break;
			$count = hexdec(trim(substr($reply,$p,$nl - $p)));
			if($count > 0)
			{
				$res .= substr($reply,$nl + 2,$count);
				$p = $nl + 4 + $count;
			}
		}
		while($count > 0);
		
		return $res;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>
