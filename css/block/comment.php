<?php
/**
 * Contains the css-comment-block-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * A comment-block
 *
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Block_Comment extends FWS_Object implements FWS_CSS_Block
{
	/**
	 * The content
	 *
	 * @var string
	 */
	private $_content;
	
	/**
	 * Constructur
	 *
	 * @param string $content
	 */
	public function __construct($content)
	{
		parent::__construct();
		$this->_content = $content;
	}
	
	/**
	 * @return string the content
	 */
	public function get_content()
	{
		return $this->_content;
	}
	
	/**
	 * @see FWS_CSS_Block::get_type()
	 *
	 * @return int
	 */
	public function get_type()
	{
		return self::COMMENT;
	}
	
	/**
	 * Builds the string-representation of this ruleset. This will be valid CSS
	 *
	 * @param string $indent the indent for the string
	 * @return string the CSS-code
	 */
	public function __toString($indent = '')
	{
		return $indent.$this->_content;
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