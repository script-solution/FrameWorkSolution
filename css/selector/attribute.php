<?php
/**
 * Contains the css-id-selector-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	css.selector
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
 * The id-selector
 *
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Selector_Attribute extends FWS_CSS_Selector_Type
{
	/**
	 * The operator that matches if an attribute exists
	 */
	const OP_EXIST		= '';
	/**
	 * The operator that matches an attribute-value exactly
	 */
	const OP_EQ				= '=';
	/**
	 * The operator that matches if the attribute-value exists in a list separated by whitespace
	 */
	const OP_IN_SET		= '~=';
	/**
	 * The operator that matches if a list seperated by '-' contains a value that starts with
	 * the attribute value
	 */
	const OP_IN_HSET	= '|=';
	
	/**
	 * The attribute-name
	 *
	 * @var string
	 */
	private $_attrname;
	
	/**
	 * The attribute-operator: OP_EXISTS, OP_EQ, OP_IN_SET or OP_IN_HSET
	 *
	 * @var string
	 */
	private $_attrop;
	
	/**
	 * The value of the attribute (may be empty)
	 *
	 * @var string
	 */
	private $_attrval = null;
	
	/**
	 * Constructor
	 *
	 * @param string $attrname the name of the attribute
	 * @param string $attrop the operator: OP_EXISTS, OP_EQ, OP_IN_SET or OP_IN_HSET
	 * @param string $attrval the value of the attribute (may be null for OP_EXISTS)
	 * @param string $tagname the tag-name (may be empty)
	 */
	public function __construct($attrname,$attrop = self::OP_EXISTS,$attrval = null,$tagname = '')
	{
		parent::__construct($tagname == '' ? '*' : $tagname);
		
		$this->set_attribute_name($attrname);
		$this->set_attribute_op($attrop);
		$this->set_attribute_value($attrop == self::OP_EXIST ? null : $attrval);
	}
	
	/**
	 * @return string the attribute-name
	 */
	public function get_attribute_name()
	{
		return $this->_attrname;
	}
	
	/**
	 * Sets the attribute-name
	 * 
	 * @param string $name the new value
	 */
	public function set_attribute_name($name)
	{
		if(!preg_match('/^[a-z\-_][a-z\-_0-9]*$/i',$name))
			FWS_Helper::error('The name has to be an identifier! (got "'.$name.'")');
		
		$this->_attrname = $name;
	}
	
	/**
	 * @return string the operator: OP_EXISTS, OP_EQ, OP_IN_SET or OP_IN_HSET
	 */
	public function get_attribute_op()
	{
		return $this->_attrop;
	}
	
	/**
	 * Sets the attribute-operator
	 * 
	 * @param string $op the operator: OP_EXISTS, OP_EQ, OP_IN_SET or OP_IN_HSET
	 */
	public function set_attribute_op($op)
	{
		if(!in_array($op,array(self::OP_EXIST,self::OP_EQ,self::OP_IN_SET,self::OP_IN_HSET)))
		{
			FWS_Helper::def_error('inarray','op',
				array(self::OP_EXIST,self::OP_EQ,self::OP_IN_SET,self::OP_IN_HSET),$op);
		}
		
		$this->_attrop = $op;
	}
	
	/**
	 * @return string the attribute-value (null if op = OP_EXIST)
	 */
	public function get_attribute_value()
	{
		return $this->_attrval;
	}
	
	/**
	 * Sets the attribute-value
	 * 
	 * @param mixed $value the new value. Should be null for operator OP_EXIST!
	 */
	public function set_attribute_value($value)
	{
		$this->_attrval = $value;
	}

	/**
	 * @see FWS_CSS_Selector_Type::to_css()
	 *
	 * @return string
	 */
	public function to_css()
	{
		$res = '';
		if($this->get_tagname() != '*')
			$res .= $this->get_tagname();
		$res .= '['.$this->_attrname;
		if($this->_attrop != self::OP_EXIST)
			$res .= ' '.$this->_attrop.' "'.$this->_attrval.'"';
		$res .= ']';
		return $res;
	}
	
	/**
	 * @return string the string-representation
	 */
	public function __toString()
	{
		return $this->to_css();
	}

	/**
	 * @see FWS_CSS_Selector_Type::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>