<?php
/**
 * Contains the css-class-selector-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The class-selector
 *
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Selector_Class extends FWS_CSS_Selector_Type
{
	/**
	 * The class
	 *
	 * @var string
	 */
	private $_class;
	
	/**
	 * Constructor
	 *
	 * @param string $class the class
	 * @param string $tagname the tag-name (may be empty)
	 */
	public function __construct($class,$tagname = '')
	{
		parent::__construct($tagname == '' ? '*' : $tagname);
		
		if(!preg_match('/^[a-z\-_][a-z\-_0-9]*$/i',$class))
			FWS_Helper::error('The class has to be an identifier! (got "'.$class.'")');
		
		$this->_class = $class;
	}
	
	/**
	 * @return string the class
	 */
	public function get_class()
	{
		return $this->_class;
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
		$res .= '.'.$this->_class;
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