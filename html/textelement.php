<?php
/**
 * Contains the textbox-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The abstract base-class for all text-elements
 * (&lt;input type="text"../&gt;,&lt;textarea&gt;,...)
 *
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_HTML_TextElement extends PLIB_HTML_FormElement
{
	/**
	 * The number of cols for the text-element
	 *
	 * @var int
	 */
	private $_cols;
	
	/**
	 * Constructor
	 * 
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 * @param mixed $cols the cols of the element
	 */
	public function __construct($name,$id = null,$value = null,$default = '',$cols = 15)
	{
		parent::__construct($name,$id,$value,$default);
		
		$this->set_cols($cols);
	}

	/**
	 * @return int the number of cols of the element
	 */
	public final function get_cols()
	{
		return $this->_cols;
	}

	/**
	 * Sets the number of cols of the text-element
	 * 
	 * @param int $cols the new value
	 */
	public final function set_cols($cols)
	{
		if(!PLIB_Helper::is_integer($cols) || $cols <= 0)
			PLIB_Helper::def_error('intgt0','cols',$cols);
		
		$this->_cols = $cols;
	}
}
?>