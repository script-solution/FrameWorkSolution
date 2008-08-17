<?php
/**
 * Contains the textarea-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a text-area:
 * <code>
 * 	<textarea name="...">...</textarea>
 * </code>
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_TextArea extends FWS_HTML_TextElement
{
	/**
	 * The number of rows of the textarea
	 *
	 * @var int
	 */
	private $_rows;
	
	/**
	 * Constructor
	 * 
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 * @param mixed $cols the cols of the element
	 * @param mixed $rows the number of rows
	 */
	public function __construct($name,$id = null,$value = null,$default = '',$cols = 60,
		$rows = 15)
	{
		parent::__construct($name,$id,$value,$default,$cols);
		
		$this->set_rows($rows);
	}

	/**
	 * @return int the number of rows
	 */
	public final function get_rows()
	{
		return $this->_rows;
	}

	/**
	 * Sets the number of rows of the textarea
	 * 
	 * @param int $rows the new value
	 */
	public final function set_rows($rows)
	{
		$this->_rows = $rows;
	}
	
	public function to_html()
	{
		$html = '<textarea'.$this->get_default_attr_html().' cols="'.$this->get_cols().'"';
		$html .= ' rows="'.$this->_rows.'">'.$this->get_used_value().'</textarea>';
		return $html;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>