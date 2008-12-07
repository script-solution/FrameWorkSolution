<?php
/**
 * Contains the css-pseudo-selector
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The pseudo-selector (implemented as decorator)
 *
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Selector_Pseudo extends FWS_Object implements FWS_CSS_Selector
{
	/**
	 * The selector to decorate
	 *
	 * @var FWS_CSS_Selector
	 */
	private $_selector;
	
	/**
	 * The pseudo-name
	 *
	 * @var string
	 */
	private $_pseudo;
	
	/**
	 * Constructor
	 *
	 * @param FWS_CSS_Selector $selector the selector. Note that it makes no sense to use a
	 * 	connector-selector here.
	 * @param string $pseudo the pseudo-name
	 */
	public function __construct($selector,$pseudo)
	{
		parent::__construct();
		
		$this->_selector = $selector;
		$this->_pseudo = $pseudo;
	}
	
	/**
	 * @return FWS_CSS_Selector the selector
	 */
	public function get_selector()
	{
		return $this->_selector;
	}
	
	/**
	 * @return string the pseudo-name
	 */
	public function get_pseudo()
	{
		return $this->_pseudo;
	}

	/**
	 * @see FWS_CSS_Selector::__toString()
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->_selector.':'.$this->_pseudo;
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