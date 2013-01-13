<?php
/**
 * Contains the css-pseudo-selector
 * 
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
	 * @see FWS_CSS_Selector::to_css()
	 *
	 * @return string
	 */
	public function to_css()
	{
		return $this->_selector.':'.$this->_pseudo;
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