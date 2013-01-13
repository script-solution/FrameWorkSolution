<?php
/**
 * Contains the input-class
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
 * This class collects and manages all incoming data.
 * With this class you can be sure that the variable you request is of the expected type and
 * contains no potentially dangerous data.
 * <br>
 * NOTE: By default all data will be already escaped, no matter what the value of magic_quotes_gpc is.
 * Additionally all data will be modified via <code>htmlspecialchars(<var>,ENT_QUOTES).</code>
 * All get- and cookie-variables will not contain line-wraps and all line-wraps
 * in post-values will be replaced by \n.
 * All get-values will not contain the sequence "..", for security reasons.
 * You can change the behaviour via set-methods. But do this BEFORE you use any other method of
 * this class!
 * <br>
 * Note that this class is a singleton. So you can access it from everywhere via
 * {@link FWS_Input::get_instance()}.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Input extends FWS_Singleton
{
	/**
	 * Represents an integer
	 */
	const INTEGER						= 0;
	
	/**
	 * Represents a float
	 */
	const FLOAT							= 1;
	
	/**
	 * Represents a string
	 */
	const STRING						= 2;
	
	/**
	 * Represents a id
	 */
	const ID								= 3;
	
	/**
	 * Represents a boolean
	 */
	const BOOL							= 4;
	
	/**
	 * Represents a boolean stored as 0/1
	 */
	const INT_BOOL					= 5;
	
	/**
	 * Represents an alpha value
	 */
	const ALPHA							= 6;
	
	/**
	 * Represents an alphanumeric value
	 */
	const ALPHA_NUM					= 7;
	
	/**
	 * Represents an identifier, that means a-zA-Z0-9_.
	 */
	const IDENTIFIER				= 8;
	
	/**
	 * Represents an hexadecimal string with 32 chars.
	 * This may for example be usefull for md5-hashs
	 */
	const HEX_32						= 9;
	
	/**
	 * Returns the instance of this object
	 * 
	 * @return FWS_Input the instance
	 */
	public static function get_instance()
	{
		return parent::_get_instance(get_class());
	}
	
	/**
	 * All predefined values with the selected type.
	 *
	 * @var array
	 */
	private $_predef_values = array();
	
	/**
	 * Contains the value of get_magic_quotes_gpc()
	 *
	 * @var boolean
	 */
	private $_magic_quotes;

	/**
	 * All input-values of $_GET,$_POST,$_COOKIE and $_SERVER
	 *
	 * @var array
	 */
	private $_inputs = null;
	
	/**
	 * Wether htmlspecialchars() should be used for all values
	 *
	 * @var boolean
	 */
	private $_use_htmlspecialchars = true;
	
	/**
	 * Wether values should be escaped
	 *
	 * @var boolean
	 */
	private $_escape_values = true;
	
	/**
	 * Wether ".." should be removed from GET-values
	 *
	 * @var boolean
	 */
	private $_remove_dotdot = true;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_magic_quotes = get_magic_quotes_gpc();
	}

	/**
	 * @return boolean wether values should be escaped
	 */
	public function get_escape_values()
	{
		return $this->_escape_values;
	}

	/**
	 * Sets wether values should be escaped
	 * 
	 * @param boolean $escape the new value
	 */
	public function set_escape_values($escape)
	{
		$this->_escape_values = (bool)$escape;
	}

	/**
	 * @return boolean wether ".." should be removed from GET-values
	 */
	public function get_remove_dotdot()
	{
		return $this->_remove_dotdot;
	}

	/**
	 * Sets wether ".." should be removed from GET-values
	 * 
	 * @param boolean $remove the new value
	 */
	public function set_remove_dotdot($remove)
	{
		$this->_remove_dotdot = (bool)$remove;
	}

	/**
	 * @return bool wether htmlspecialchars() should be used for all values
	 */
	public function get_use_htmlspecialchars()
	{
		return $this->_use_htmlspecialchars;
	}

	/**
	 * Sets wether htmlspecialchars() should be used for all values
	 * 
	 * @param bool $use the new value
	 */
	public function set_use_htmlspecialchars($use)
	{
		$this->_use_htmlspecialchars = (bool)$use;
	}

	/**
	 * checks if the given variable exists
	 *
	 * @param string $name the name of the variable
	 * @param int|string $method the method from which you want to request the variable:
													 (get,post,cookie,server); -1 if this doesn't matter
	 * @return boolean true if a variable exists
	 */
	public function isset_var($name,$method = -1)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if($this->_inputs === null)
			$this->_collect_inputs();
		if($method == -1)
			$method = $this->_get_method($name);

		if($method == 'cookie' || $method == 'server')
			$this->_load_lazy_variable($method,$name);
		
		return isset($this->_inputs[$method][$name]);
	}
	
	/**
	 * Predefines the type of the given input-value from the given method.
	 * This gives you the opportunity to define the type of a value once and just request
	 * it without having to specify the type. Therefore this is more secure and less error-prone.
	 * Please use #get_predef() to get the value.
	 * <br>
	 * If you know all values that are allowed you can specify <var>$values</var>.
	 * This ensures that no other values are possible.
	 * 
	 * @param string $name the name of the value
	 * @param int|string $method the method for which you want to define the value:
	 * 											 (get,post,cookie,server); -1 if this doesn't matter
	 * @param int $type the type of the variable: INTEGER,STRING,INT_BOOL,BOOL,FLOAT,ID,...;
	 * 									-1 if you want to disable a check of the value
	 * @param array $values if you like you can specify all values that should be allowed
	 * @see get_predef()
	 */
	public function set_predef($name,$method = -1,$type = -1,$values = null)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if($values !== null)
			$this->_predef_values[$method.$name] = array($type,$values);
		else
			$this->_predef_values[$method.$name] = $type;
	}
	
	/**
	 * Returns the value of the predefined variable with given name and method.
	 * Will throw an error if the variable is not predefined!
	 * <br>
	 * If you grab a variable that has predefined values you can specify the default-value via
	 * <var>$default</var>. Otherwise you will get <var>null</var> if the value is invalid!
	 * 
	 * @param string $name the name of the value
	 * @param int|string $method the method for which you want to define the value:
	 * 											 (get,post,cookie,server); -1 if this doesn't matter
	 * @param mixed $default if you grab a variable that has predefined values you
	 * 											 can specify the default-value that you would like to get
	 * 											 if the value is invalid.
	 * @return mixed the value
	 * @see set_predef()
	 */
	public function get_predef($name,$method = -1,$default = null)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if(!isset($this->_predef_values[$method.$name]))
			FWS_Helper::error('"'.$name.'" does not exist for the method "'.$method.'"!');
		
		$type = $this->_predef_values[$method.$name];
		if(is_array($type))
			return $this->correct_var($name,$method,$type[0],$type[1],$default);
		
		return $this->get_var($name,$method,$this->_predef_values[$method.$name]);
	}
	
	/**
	 * Returns an array with all values of the given input-variables
	 *
	 * @param array $names an array with all names
	 * @param int|string $method the method for all variables:
	 * 											 (get,post,cookie,server); -1 if this doesn't matter
	 * @param int $type the type of all variables: INTEGER,STRING,INT_BOOL,BOOL,FLOAT,ID,...;
	 * 									-1 if this doesn't matter
	 * @return array an associative array with all values
	 */
	public function get_vars($names,$method = -1,$type = -1)
	{
		if(!is_array($names))
			FWS_Helper::def_error('array','names',$names);
		
		$vars = array();
		foreach($names as $name)
			$vars[$name] = $this->get_var($name,$method,$type);
		return $vars;
	}

	/**
	 * Returns the value of a variable and ensures that it has the expected type
	 * if the type is not correct, null will be returned.
	 *
	 * @param string $name the name of the variable
	 * @param int|string $method the method from which you want to request the variable:
	 * 											 (get,post,cookie,server); -1 if this doesn't matter
	 * @param int $type the type of the variable: INTEGER,STRING,INT_BOOL,BOOL,FLOAT,ID,...;
	 * 									-1 if this doesn't matter
	 * @return mixed the value of the variable or null if not found / invalid
	 */
	public function get_var($name,$method = -1,$type = -1)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if($this->_inputs === null)
			$this->_collect_inputs();
		if($method == -1)
			$method = $this->_get_method($name);

		if($method == 'cookie' || $method == 'server')
			$this->_load_lazy_variable($method,$name);
		
		if(isset($this->_inputs[$method][$name]))
		{
			$var = $this->_inputs[$method][$name];
			switch($type)
			{
				case -1:
					return $var;

				case FWS_Input::ID:
					if(is_scalar($var) && preg_match('/^[0-9]+$/',$var) && $var >= 1)
						return (int)$var;
					return null;

				case FWS_Input::INTEGER:
					if(is_scalar($var) && preg_match('/^-?[0-9]+$/',$var))
						return (int)$var;
					return null;

				case FWS_Input::ALPHA:
					if(is_scalar($var) && preg_match('/^[a-z]+$/i',$var))
						return $var;
					return null;

				case FWS_Input::ALPHA_NUM:
					if(is_scalar($var) && preg_match('/^[a-z0-9]+$/i',$var))
						return $var;
					return null;

				case FWS_Input::IDENTIFIER:
					if(is_scalar($var) && preg_match('/^[a-z0-9_]+$/i',$var))
						return $var;
					return null;

				case FWS_Input::HEX_32:
					if(is_scalar($var) && preg_match('/^[a-f0-9]{32}$/i',$var))
						return $var;
					return null;

				case FWS_Input::INT_BOOL:
					return ($var == 1) ? 1 : 0;

				case FWS_Input::FLOAT:
					$svar = strval($var);
					$fvar = strval((float)$var);
					if($svar == $fvar)
						return (float)$var;
					return null;

				case FWS_Input::STRING:
					return (string)$var;

				case FWS_Input::BOOL:
					$svar = strval($var);
					$fvar = strval((bool)$var);
					if($svar == $fvar)
						return (bool)$var;
					return null;
			}
		}

		return null;
	}
	
	/**
	 * Unescapes the given value of the given method. That means the method assumes that the value
	 * is been retrieved from this class and it undos everthing that has been done.
	 * Note that arrays are not supported! Additionally it doesn't matter what the value of
	 * magic_quotes_gpc is, the data will be unescaped in every case.
	 *
	 * @param mixed $value the value
	 * @param string $method the method (get,post,cookie,server)
	 * @return mixed the unescaped value
	 */
	public function unescape_value($value,$method)
	{
		if($this->_use_htmlspecialchars)
			$value = FWS_StringHelper::htmlspecialchars_back($value);
		if($this->_escape_values)
			$value = stripslashes($value);
		return $value;
	}

	/**
	 * returns all incoming data of the given method
	 * NOTE: will not take care of the type
	 *
	 * @param string $method the method (get,post,cookie,server)
	 * @return array an associative array with all variables of the the given method
	 */
	public function get_vars_from_method($method)
	{
		if($this->_inputs === null)
			$this->_collect_inputs();
		if($method == 'server' || $method == 'cookie')
			$this->_load_all_lazy($method);
		
		if(isset($this->_inputs[$method]))
			return $this->_inputs[$method];

		return array();
	}

	/**
	 * this method sets the given variable to the specified value (only in the intern array,
	 * not in the super-global GPC-arrays)
	 *
	 * @param string $name the name of the variable
	 * @param int|string $method the method from which you want to request the variable:
	 * 											 (get,post,cookie,server); -1 if you want to set it in all methods
	 * @param mixed $value the new value
	 * @return mixed the value
	 */
	public function set_var($name,$method,$value)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if($this->_inputs === null)
			$this->_collect_inputs();
		if($method == -1)
		{
			foreach(array('server','cookie','get','post') as $m)
				$this->_inputs[$m][$name] = $value;
			return $value;
		}

		return $this->_inputs[$method][$name] = $value;
	}

	/**
	 * removes the given variable from the input-array
	 *
	 * @param string $name the name of the variable
	 * @param int|string $method the method from which you want to request the variable:
	 * 											 (get,post,cookie,server); -1 if you want to unset it in all methods
	 */
	public function unset_var($name,$method)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if($this->_inputs === null)
			$this->_collect_inputs();
		if($method == -1)
		{
			foreach(array('server','cookie','get','post') as $m)
				unset($this->_inputs[$m][$name]);
		}
		else if(isset($this->_inputs[$method][$name]))
			unset($this->_inputs[$method][$name]);
	}

	/**
	 * Ensures that a variable has no unexpected value.
	 * Note that the default value will be set if <var>$set</var> is true <u>and</u>
	 * <var>$default</var> is not null!
	 *
	 * @param string $name the name of the variable
	 * @param int|string $method the method from which you want to request the variable:
	 * 	(get,post,cookie,server)
	 * @param int $type the type of the variable: INTEGER,STRING,INT_BOOL,BOOL,FLOAT,ID,...
	 * @param array $values an array with the allowed values
	 * @param mixed $default the default value which will be used if no of the allowed
	 * 	values matches
	 * @param boolean $set do you want to save the variable in the inputs, too?
	 * @return mixed the value
	 */
	public function correct_var($name,$method,$type,$values,$default,$set = true)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if(!is_array($values))
			FWS_Helper::def_error('array','values',$values);

		$var = $this->get_var($name,$method,$type);
		if($var === null)
			return $set && $default !== null ? $this->set_var($name,$method,$default) : $default;

		foreach($values as $value)
		{
			if($var === $value)
				return $value;
		}

		return $set && $default !== null ? $this->set_var($name,$method,$default) : $default;
	}
	
	/**
	 * Clears all collected values and reads them again from $_GET, $_POST, ...
	 */
	public function rescan_superglobals()
	{
		$this->_inputs = array();
		$this->_collect_inputs();
	}
	
	/**
	 * Loads the given server- or cookie-variable, if not already done
	 *
	 * @param string $method the method: cookie or server
	 * @param string $name the name of the variable
	 */
	private function _load_lazy_variable($method,$name)
	{
		if(isset($this->_inputs[$method][$name]))
			return;
		
		switch($method)
		{
			case 'cookie':
				if(isset($_COOKIE[$name]))
					$this->_inputs['cookie'][$name] = $this->_clean_default_value($_COOKIE[$name]);
				break;
			
			case 'server':
				if(isset($_SERVER[$name]))
					$this->_inputs['server'][$name] = $this->_clean_default_value($_SERVER[$name]);
				break;
		}
	}
	
	/**
	 * Loads all variables from the given method
	 *
	 * @param string $method the method: cookie or server
	 */
	private function _load_all_lazy($method)
	{
		switch($method)
		{
			case 'cookie':
				if(isset($_COOKIE) && is_array($_COOKIE))
				{
					foreach($_COOKIE as $key => $value)
						$this->_inputs['cookie'][$this->_clean_key($key)] = $this->_clean_default_value($value);
				}
				break;
			
			case 'server':
				if(isset($_SERVER) && is_array($_SERVER))
				{
					foreach($_SERVER as $key => $value)
						$this->_inputs['server'][$this->_clean_key($key)] = $this->_clean_default_value($value);
				}
				break;
		}
	}

	/**
	 * Saves all incoming data in the field $this->_inputs and cleans the values
	 */
	private function _collect_inputs()
	{
		$this->_inputs = array();
		if(isset($_POST) && is_array($_POST))
		{
			foreach($_POST as $key => $value)
				$this->_inputs['post'][$this->_clean_key($key)] = $this->_clean_post_value($value);
		}
		if(isset($_GET) && is_array($_GET))
		{
			foreach($_GET as $key => $value)
				$this->_inputs['get'][$this->_clean_key($key)] = $this->_clean_get_value($value);
		}
		
		// Note that we don't load $_SERVER and $_COOKIE here because this may be very much and
		// will be needed not really often
	}

	/**
	 * @param string $name the name of the variable
	 * @return string|int the method of a variable, if unknown -1
	 */
	private function _get_method($name)
	{
		foreach($this->_inputs as $method => $content)
		{
			foreach(array_keys($content) as $key)
			{
				if($key == $name)
					return $method;
			}
		}

		return -1;
	}

	/**
	 * cleans the key of input-data
	 *
	 * @param mixed $key the key to clean
	 * @return mixed the cleaned key
	 */
	private function _clean_key($key)
	{
		if(is_string($key))
		{
			$key = str_replace(array("\n","\r"),'',$key);
			$key = htmlspecialchars($key,ENT_QUOTES);
		}
		return $key;
	}

	/**
	 * delete not used and potentially dangerous characters
	 *
	 * @param string $value the value of the get-variable
	 * @return string the 'clean' value
	 */
	private function _clean_get_value($value)
	{
		if(is_array($value))
		{
			foreach($value as $k => $v)
				$value[$k] = $this->_clean_get_value($v);
		}
		else if(is_string($value))
		{
			$value = str_replace(array("\n","\r"),'',$value);
			if($this->_escape_values && !$this->_magic_quotes)
				$value = addslashes($value);
			else if(!$this->_escape_values && $this->_magic_quotes)
				$value = stripslashes($value);
			if($this->_use_htmlspecialchars)
				$value = htmlspecialchars($value,ENT_QUOTES);
			if($this->_remove_dotdot)
				$value = str_replace('..','',$value);
		}
		return $value;
	}

	/**
	 * escape incoming post-data and convert special-chars
	 *
	 * @param string $input the value of the post-variable
	 * @return string the 'clean' value
	 */
	private function _clean_post_value($input)
	{
		if(is_array($input))
		{
			foreach($input as $key => $val)
				$input[$key] = $this->_clean_post_value($val);
		}
		else if(is_string($input))
		{
			$input = str_replace(array("\r\n","\r"),"\n",$input);
			if($this->_escape_values && !$this->_magic_quotes)
				$input = addslashes($input);
			else if(!$this->_escape_values && $this->_magic_quotes)
				$input = stripslashes($input);
			if($this->_use_htmlspecialchars)
				$input = htmlspecialchars($input,ENT_QUOTES);
		}

		return $input;
	}

	/**
	 * Escape incoming data and convert special-chars
	 *
	 * @param string $input the value of the variable
	 * @return string the 'clean' value
	 */
	private function _clean_default_value($input)
	{
		if(is_array($input))
		{
			foreach($input as $key => $val)
				$input[$key] = $this->_clean_default_value($val);
		}
		else if(is_string($input))
		{
			if($this->_escape_values && !$this->_magic_quotes)
				$input = addslashes($input);
			else if(!$this->_escape_values && $this->_magic_quotes)
				$input = stripslashes($input);
			if($this->_use_htmlspecialchars)
				$input = htmlspecialchars($input,ENT_QUOTES);
		}

		return $input;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>