<?php
/**
 * Contains the css-charset-block-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A charset-block
 *
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Block_Charset extends FWS_Object implements FWS_CSS_Block
{
	/**
	 * The charset
	 *
	 * @var string
	 */
	private $_charset;
	
	/**
	 * Constructur
	 *
	 * @param string $charset the charset
	 */
	public function __construct($charset)
	{
		parent::__construct();
		$this->_charset = $charset;
	}
	
	/**
	 * @return string the charset
	 */
	public function get_charset()
	{
		return $this->_charset;
	}
	
	/**
	 * @see FWS_CSS_Block::get_type()
	 *
	 * @return int
	 */
	public function get_type()
	{
		return self::CHARSET;
	}
	
	/**
	 * @see FWS_CSS_Block::to_css()
	 *
	 * @param string $indent
	 * @return string
	 */
	public function to_css($indent = '')
	{
		return $indent.'@charset "'.$this->_charset.'";';
	}
	
	/**
	 * @return string the string-representation
	 */
	public function __toString()
	{
		return $this->to_css();
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