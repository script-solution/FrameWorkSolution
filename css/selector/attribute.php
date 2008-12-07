<?php
/**
 * Contains the css-id-selector-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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
		
		if(!preg_match('/^[a-z\-_][a-z\-_0-9]*$/i',$attrname))
			FWS_Helper::error('The id has to be an identifier! (got "'.$attrname.'")');
		if(!in_array($attrop,array(self::OP_EXIST,self::OP_EQ,self::OP_IN_SET,self::OP_IN_HSET)))
		{
			FWS_Helper::def_error('inarray','attrop',
				array(self::OP_EXIST,self::OP_EQ,self::OP_IN_SET,self::OP_IN_HSET),$attrop);
		}
		if($attrop != self::OP_EXIST && $attrval === null)
			FWS_Helper::error('$attrval has to be non-null if you don\'t use OP_EXIST!');
		
		$this->_attrname = $attrname;
		$this->_attrop = $attrop;
		$this->_attrval = $attrop == self::OP_EXIST ? null : $attrval;
	}
	
	/**
	 * @return string the attribute-name
	 */
	public function get_attribute_name()
	{
		return $this->_attrname;
	}
	
	/**
	 * @return string the operator: OP_EXISTS, OP_EQ, OP_IN_SET or OP_IN_HSET
	 */
	public function get_attribute_op()
	{
		return $this->_attrop;
	}
	
	/**
	 * @return string the attribute-value (null if op = OP_EXIST)
	 */
	public function get_attribute_value()
	{
		return $this->_attrval;
	}

	/**
	 * @see FWS_CSS_Selector_Type::__toString()
	 *
	 * @return string
	 */
	public function __toString()
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