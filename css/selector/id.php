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
final class FWS_CSS_Selector_ID extends FWS_CSS_Selector_Type
{
	/**
	 * The id
	 *
	 * @var string
	 */
	private $_id;
	
	/**
	 * Constructor
	 *
	 * @param string $id the id
	 * @param string $tagname the tag-name (may be empty)
	 */
	public function __construct($id,$tagname = '')
	{
		parent::__construct($tagname == '' ? '*' : $tagname);
		
		if(!preg_match('/^[a-z\-_][a-z\-_0-9]*$/i',$id))
			FWS_Helper::error('The id has to be an identifier! (got "'.$id.'")');
		
		$this->_id = $id;
	}
	
	/**
	 * @return string the id
	 */
	public function get_id()
	{
		return $this->_id;
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
		$res .= '#'.$this->_id;
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