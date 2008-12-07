<?php
/**
 * Contains the css-type-selector-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The type-selector. Selects all tags with a specific name (or all in case of '*')
 *
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_CSS_Selector_Type extends FWS_Object implements FWS_CSS_Selector
{
	/**
	 * The tagname (may be * or an identifier)
	 *
	 * @var string
	 */
	private $_tagname;
	
	/**
	 * Constructor
	 *
	 * @param string $tagname the tagname
	 */
	public function __construct($tagname)
	{
		if(!preg_match('/^\*|([a-z\-_][a-z\-_0-9]*)$/i',$tagname))
			FWS_Helper::error('The tag-name has to be an identifier or "*"! (got "'.$tagname.'")');
		
		$this->_tagname = $tagname;
	}
	
	/**
	 * @return string the tag-name
	 */
	public final function get_tagname()
	{
		return $this->_tagname;
	}
	
	/**
	 * @see FWS_CSS_Selector::__toString()
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->_tagname;
	}
	
	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>