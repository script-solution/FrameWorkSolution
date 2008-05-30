<?php
/**
 * Contains the textbox-class
 *
 * @version			$Id: textbox.php 736 2008-05-23 18:24:22Z nasmussen $
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a text-box:
 * <code>
 * 	<input type="text" name="..." value="..." />
 * </code>
 *
 * @package			PHPLib
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_HTML_TextBox extends PLIB_HTML_TextElement
{
	/**
	 * The maximum number of cols.
	 *
	 * @var int
	 */
	private $_maxlength;
	
	/**
	 * Constructor
	 * 
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 * @param mixed $cols the cols of the element
	 * @param mixed $maxlength the maximum length
	 */
	public function __construct($name,$id = null,$value = null,$default = '',$cols = 15,
		$maxlength = null)
	{
		parent::__construct($name,$id,$value,$default,$cols);
		
		$this->set_maxlength($maxlength);
	}

	/**
	 * @return int the maximum number of columns of the element
	 */
	public final function get_maxlength()
	{
		return $this->_maxlength;
	}

	/**
	 * Sets the maximum number of cols of the text-box. <var>null</var> indicates that the
	 * attribute will be ignored
	 * 
	 * @param int $maxlength the new value
	 */
	public final function set_maxlength($maxlength)
	{
		if($maxlength !== null && (!PLIB_Helper::is_integer($maxlength) || $maxlength <= 0))
			PLIB_Helper::def_error('intgt0','maxlength',$maxlength);
		
		$this->_maxlength = $maxlength;
	}
	
	public function to_html()
	{
		$html = '<input type="text"'.$this->_get_default_attr_html().' size="'.$this->get_cols().'"';
		if($this->_maxlength !== null)
			$html .= ' maxlength="'.$this->_maxlength.'"';
		$html .= ' value="'.$this->get_used_value().'"';
		$html .= ' />';
		return $html;
	}
	
	protected function _get_print_vars()
	{
		return array_merge(parent::_get_print_vars(),get_object_vars($this));
	}
}
?>