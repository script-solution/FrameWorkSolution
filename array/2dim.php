<?php
/**
 * Contains the 2-dimensional cache
 *
 * @version			$Id: 2dim.php 768 2008-05-25 08:36:14Z nasmussen $
 * @package			PHPLib
 * @subpackage	array
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A container for 2 dimensional arrays. That means that The structure should be the following:
 * <code>
 * 	array(<key> => array('field1' => <value>,...), ...)
 * </code>
 * Additionally to the 1-dim-cache this class provides methods to search for elements
 * that match a given condition.
 *
 * @package			PHPLib
 * @subpackage	array
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Array_2Dim extends PLIB_Array_1Dim
{
	/**
	 * Represents an AND-link which means that all conditions have to match
	 */
	const LINK_AND		= 0;
	
	/**
	 * Represents an OR-link which means that at least one condition has to match
	 */
	const LINK_OR			= 1;
	
	/**
	 * Changes the given field in the element with given key. The method has just an
	 * effect for a 2-dimensional array!
	 * Note that $value has to be scalar!
	 *
	 * @param mixed $key the key of the element
	 * @param string $field the name of the field you want to change
	 * @param mixed $value the new value of the field
	 */
	public final function set_element_field($key,$field,$value)
	{
		if(!is_string($field))
			PLIB_Helper::def_error('string','field',$field);
		if($value !== null && !is_scalar($value))
			PLIB_Helper::def_error('scalar','value',$value);
		
		if(isset($this->_cache_content[$key]) && is_array($this->_cache_content[$key]))
			$this->_cache_content[$key][$field] = $value;
	}
	
	/**
	 * Checks wether an element exists with the specified content.
	 * The condition is an associative array with the keys and values that the
	 * element should have and the link with which the elements in the condition-array
	 * should be connected. You can choose between self::LINK_AND and self::LINK_OR.
	 * That means that either all key-value-pairs have to match or just one.
	 *
	 * @param array $values an associative array with the conditions:
	 * 	<code>array(<key> => <value>[, ...])</code>
	 * @param int $link the link of the conditions: self::LINK_AND,self::LINK_OR
	 * @return boolean true if the element has been found
	 */
	public final function element_exists_with($values,$link = self::LINK_AND)
	{
		return $this->get_key_with($values,$link) !== null;
	}
	
	/**
	 * Returns an array with the values of the given field for every element that has a key
	 * in the given key-array.
	 *
	 * @param array $keys the key-array
	 * @param string $field the field-name
	 * @return array the found field-values
	 */
	public final function get_field_vals_of_keys($keys,$field)
	{
		$elements = array();
		foreach($keys as $key)
		{
			$el = $this->get_element($key);
			if($el !== null && isset($el[$field]))
				$elements[] = $el[$field];
		}
		return $elements;
	}

	/**
	 * Searches for the first element that matches the condition.
	 * The condition is an associative array with the keys and values that the
	 * element should have and the link with which the elements in the condition-array
	 * should be connected. You can choose between self::LINK_AND and self::LINK_OR.
	 * That means that either all key-value-pairs have to match or just one.
	 *
	 * @param array $values an associative array with the conditions:
	 * 	<code>array(<key> => <value>[, ...])</code>
	 * @param int $link the link of the conditions: self::LINK_AND,self::LINK_OR
	 * @return mixed the element or null if not found
	 */
	public final function get_element_with($values,$link = self::LINK_AND)
	{
		$key = $this->get_key_with($values,$link);
		if($key !== null)
			return $this->_cache_content[$key];

		return null;
	}

	/**
	 * Returns an array with all elements which fullfill the given condition.
	 * The condition is an associative array with the keys and values that the
	 * element should have and the link with which the elements in the condition-array
	 * should be connected. You can choose between self::LINK_AND and self::LINK_OR.
	 * That means that either all key-value-pairs have to match or just one.
	 *
	 * @param array $values an associative array with the conditions:
	 * 	<code>array(<key> => <value>[, ...])</code>
	 * @param int $link the link of the conditions: self::LINK_AND,self::LINK_OR
	 * @return array an associative array of the form:
	 * 	<code>array(<key> => <element>)</code>
	 */
	public final function get_elements_with($values,$link = self::LINK_AND)
	{
		if(!is_array($values))
			PLIB_Helper::def_error('array','values',$values);
		if(!in_array($link,array(self::LINK_OR,self::LINK_AND)))
			PLIB_Helper::def_error('inarray','link',array(self::LINK_OR,self::LINK_AND),$link);
		
		$result = array();
		foreach($this->_cache_content as $key => $data)
		{
			if($this->_element_matches($values,$link,$data))
				$result[$key] = $data;
		}

		return $result;
	}

	/**
	 * Determines the key of the first element which matches the given condition.
	 * The condition is an associative array with the keys and values that the
	 * element should have and the link with which the elements in the condition-array
	 * should be connected. You can choose between self::LINK_AND and self::LINK_OR.
	 * That means that either all key-value-pairs have to match or just one.
	 *
	 * @param array $values an associative array with the conditions:
	 * 	<code>array(<key> => <value>[, ...])</code>
	 * @param int $link the link of the conditions: self::LINK_AND,self::LINK_OR
	 * @return mixed the key of the element or null if not found
	 */
	public final function get_key_with($values,$link = self::LINK_AND)
	{
		if(!is_array($values))
			PLIB_Helper::def_error('array','values',$values);
		if(!in_array($link,array(self::LINK_OR,self::LINK_AND)))
			PLIB_Helper::def_error('inarray','link',array(self::LINK_OR,self::LINK_AND),$link);
		
		// Note that the order of the elements is important here. Therefore we
		// loop through the keys (which are sorted) instead of the elements
		foreach($this->_cache_content_keys as $key)
		{
			$data = $this->_cache_content[$key];
			if($this->_element_matches($values,$link,$data))
				return $key;
		}

		return null;
	}
	
	/**
	 * Determines wether the given element matches the condition
	 *
	 * @param array $values the conditions
	 * @param int $link the link of the conditions
	 * @param array $element the element to check
	 * @return boolean true if the element matches
	 */
	private function _element_matches($values,$link,$element)
	{
		$matches = $link == self::LINK_AND;
		if($matches)
		{
			foreach($values as $k => $v)
			{
				if(!isset($element[$k]) || $element[$k] != $v)
				{
					$matches = false;
					break;
				}
			}
		}
		else
		{
			foreach($values as $k => $v)
			{
				if(isset($element[$k]) && $element[$k] == $v)
				{
					$matches = true;
					break;
				}
			}
		}
		
		return $matches;
	}
}
?>