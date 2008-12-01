<?php
/**
 * Contains the data-object-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	objects
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The abstract data object which should be the base-class for all data-objects
 * you create.
 * A data object may create, update and delete itself.
 * Additionally it may store errors for later usage.
 * 
 * @package			FrameWorkSolution
 * @subpackage	objects
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Objects_Data extends FWS_Object
{
	/**
	 * The table-name in the database this object belongs to
	 *
	 * @var string
	 */
	protected $_table = null;
	
	/**
	 * An array with the collected errors
	 *
	 * @var array
	 */
	protected $_errors = array();
	
	/**
	 * Constructor
	 * 
	 * @param string $table the table the object belongs to
	 */
	public function __construct($table)
	{
		parent::__construct();
		
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);
		
		$this->_table = $table;
	}
	
	/**
	 * The table has to have the field "id" which is the primary key of the table.
	 * 
	 * @return int the id of this object
	 */
	public abstract function get_id();
	
	/**
	 * @return string the name of the database-table
	 */
	public final function table()
	{
		return $this->_table;
	}
	
	/**
	 * @return array the collected error-messages
	 */
	public final function errors()
	{
		return $this->_errors;
	}
	
	/**
	 * Clears all collected errors
	 */
	public final function clear_errors()
	{
		$this->_errors = array();
	}
	
	/**
	 * Checks all values for errors and stores them to the error-field.
	 * Fields that contain errors will be set back to null (or lets better say, that would
	 * make sense).
	 * 
	 * @param string $type the type of action to perform: create, update or delete
	 * @return boolean true if everything is ok
	 */
	public abstract function check($type = 'create');
	
	/**
	 * Should create this object in the database. You should update fields that are known after
	 * the database-query. So for example if the id was not set before you've created the object
	 * in the database you should set it afterwards in this object.
	 */
	public function create()
	{
		$db = FWS_Props::get()->db();

		$fields = $this->_get_fields();
		
		// no fields are not allowed
		if(count($fields) == 0)
			throw new FWS_Exceptions_MissingData('Please set at least one field first!');
		
		$db->sql_insert($this->table(),$fields);
		
		// assign the id if not already done
		if($this->get_id() === null)
			$this->set_id($db->get_last_insert_id());
	}
	
	/**
	 * Should update this object to the database.
	 */
	public function update()
	{
		$db = FWS_Props::get()->db();

		if($this->get_id() === null)
			throw new FWS_Exceptions_MissingData('The id is missing');
		
		$fields = $this->_get_fields();
		
		if(count($fields) == 0)
			throw new FWS_Exceptions_MissingData('Please set at least one field first!');
		
		$db->sql_update($this->table(),'WHERE id = '.$this->get_id(),$fields);
	}
	
	/**
	 * Should delete this object from the database.
	 */
	public function delete()
	{
		$db = FWS_Props::get()->db();

		if($this->get_id() === null)
			throw new FWS_Exceptions_MissingData('The id is missing');
		
		$db->sql_qry('DELETE FROM '.$this->table().' WHERE id = '.$this->get_id());
	}
	
	/**
	 * This method checks a field for a given type. By default the check will only be performed
	 * if the value is not NULL. You can force the check by setting <var>$is_required</var> to true.
	 * The possible values for $for are:
	 * <ul>
	 * 	<li>timestamp		=> uses {@link FWS_Date::is_valid_timestamp()}</li>
	 * 	<li>empty				=> has to be empty</li>
	 * 	<li>notempty		=> has to be not-empty</li>
	 * 	<li>id					=> has to be a positive integer
	 * 	<li>numeric			=> uses <var>is_numeric()</var></li>
	 * 	<li>enum				=> requires <var>$values</var> and uses <var>in_array()</var></li>
	 * </ul>
	 * Will add a default-error message if invalid
	 * 
	 * @param string $field the field-name
	 * @param string $for the type to check for. See the list above!
	 * @param boolean $is_required force the check?
	 * @param array $values possible values for the field. empty if it should not be used
	 * @return boolean true if the value is ok
	 */
	protected function check_field_for($field,$for = 'timestamp',$is_required = false,
		$values = array())
	{
		$errors = 0;
		$method = 'get_'.$field;
		$val = $this->$method();
		if($val !== null || $is_required)
		{
			$error = false;
			switch($for)
			{
				case 'empty':
					$error = !empty($val);
					break;
				
				case 'notempty':
					$error = empty($val);
					break;
				
				case 'timestamp':
					$error = !FWS_Date::is_valid_timestamp($val);
					break;
				
				case 'id':
					$error = !FWS_Helper::is_integer($val) || $val <= 0;
					break;
				
				case 'numeric':
					$error = !is_numeric($val);
					break;
				
				case 'enum':
					if(!is_array($values) || count($values) == 0)
						FWS_Helper::def_error('array>0','values',$values);
					
					$error = !in_array($val,$values);
					break;
			}
			
			if($error)
			{
				if($is_required && empty($val))
					$this->add_error($this->missing_field_msg($field),$field);
				else
					$this->add_error($this->invalid_field_msg($field),$field);
				$errors++;
			}
		}
		
		return $errors == 0;
	}
	
	/**
	 * A convenience-method to add a default invalid-field error
	 * 
	 * @param string $field the name of the field
	 */
	protected function add_invalid_field_error($field)
	{
		$this->add_error($this->invalid_field_msg($field),$field);
	}
	
	/**
	 * A convenience-method to add a default missing-field error
	 * 
	 * @param string $field the name of the field
	 */
	protected function add_missing_field_error($field)
	{
		$this->add_error($this->missing_field_msg($field),$field);
	}
	
	/**
	 * Adds an error to the error list and sets the given field to null (if not empty)
	 * 
	 * @param string $message the message to add
	 * @param string $field the name of the field
	 */
	protected function add_error($message,$field = '')
	{
		$this->_errors[] = $message;
		if($field)
		{
			$method = 'set_'.$field;
			$this->$method(null);
		}
	}
	
	/**
	 * Builds an invalid-field message for the given field
	 * 
	 * @param string $field the field-name
	 * @return string the message
	 */
	protected function invalid_field_msg($field)
	{
		$method = 'get_'.$field;
		return sprintf(
			'The value "%s" for the field "%s" is invalid',$this->$method(),$this->field_name($field)
		);
	}
	
	/**
	 * Builds a missing-field message for the given field
	 * 
	 * @param string $field the field-name
	 * @return string the message
	 */
	protected function missing_field_msg($field)
	{
		return sprintf('The value for the field "%s" is missing!',$this->field_name($field));
	}
	
	/**
	 * Returns the name to use for display of the given field.
	 * By default it returns just the name of the field. You may overwrite this method
	 * to change the behaviour!
	 * 
	 * @param string $field the name of the field
	 * @return string the name to display
	 */
	protected function field_name($field)
	{
		return $field;
	}
	
	/**
	 * Collects all fields to update/insert
	 * 
	 * @return array the fields
	 */
	private function _get_fields()
	{
		$fields = array();
		foreach(get_class_methods(get_class($this)) as $var)
		{
			// TODO that's no good solution. but what is one? :/
			if(FWS_String::substr($var,0,4) == 'get_' && $var != 'get_id' && $var != 'get_object_id' &&
				$var != 'get_dump_vars' && $var != 'get_dump')
			{
				$value = $this->$var();
				if($value !== null)
					$fields[FWS_String::substr($var,4)] = $value;
			}
		}
		return $fields;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>
