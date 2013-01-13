<?php
/**
 * Contains the css-list-selector-class
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
 * The connector-selector. For example:
 * <pre>p > b</pre>
 * <var>p</var> and <var>b</var> would be implementations of FWS_CSS_Selector and
 * '>' and '+' would be connectors (represented as strings).
 *
 * @package			FrameWorkSolution
 * @subpackage	css.selector
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Selector_Connector extends FWS_Object implements FWS_CSS_Selector
{
	/**
	 * The connector that matches an element in the children-hierarchie
	 */
	const CON_ANY_CHILD		= ' ';
	/**
	 * The connector the matches an elements in the children
	 */
	const CON_NEXT_CHILD	= '>';
	/**
	 * The connector that matches the next element that has the same parent
	 */
	const CON_NEXT_SIB		= '+';
	
	/**
	 * The left selector
	 *
	 * @var FWS_CSS_Selector
	 */
	private $_lsel;
	
	/**
	 * The connector
	 *
	 * @var string
	 */
	private $_con;
	
	/**
	 * The right selector
	 *
	 * @var FWS_CSS_Selector
	 */
	private $_rsel;
	
	/**
	 * Constructor
	 *
	 * @param FWS_CSS_Selector $lsel the selector to decorate
	 * @param string $con the connector: CON_ANY_CHILD, CON_NEXT_CHILD or CON_NEXT_SIB
	 * @param FWS_CSS_Selector $rsel the right selector
	 */
	public function __construct($lsel,$con,$rsel)
	{
		parent::__construct();
		
		$this->set_left_selector($lsel);
		$this->set_connector($con);
		$this->set_right_selector($rsel);
	}
	
	/**
	 * @return FWS_CSS_Selector the left selector
	 */
	public function get_left_selector()
	{
		return $this->_lsel;
	}
	
	/**
	 * Sets the left selector
	 *
	 * @param FWS_CSS_Selector $selector the selector
	 */
	public function set_left_selector($selector)
	{
		if(!($selector instanceof FWS_CSS_Selector))
			FWS_Helper::def_error('instance','selector','FWS_CSS_Selector',$selector);
		
		$this->_lsel = $selector;
	}
	
	/**
	 * @return string the connector
	 */
	public function get_connector()
	{
		return $this->_con;
	}
	
	/**
	 * Sets the connector
	 *
	 * @param string $con the connector: CON_ANY_CHILD, CON_NEXT_CHILD or CON_NEXT_SIB
	 */
	public function set_connector($con)
	{
		if(!in_array($con,array(self::CON_ANY_CHILD,self::CON_NEXT_CHILD,self::CON_NEXT_SIB)))
		{
			FWS_Helper::def_error('inarray','con',
				array(self::CON_ANY_CHILD,self::CON_NEXT_CHILD,self::CON_NEXT_SIB),$con);
		}
		
		$this->_con = $con;
	}
	
	/**
	 * @return FWS_CSS_Selector the right selector
	 */
	public function get_right_selector()
	{
		return $this->_rsel;
	}
	
	/**
	 * Sets the right selector
	 *
	 * @param FWS_CSS_Selector $selector the selector
	 */
	public function set_right_selector($selector)
	{
		if(!($selector instanceof FWS_CSS_Selector))
			FWS_Helper::def_error('instance','selector','FWS_CSS_Selector',$selector);
		
		$this->_rsel = $selector;
	}

	/**
	 * @see FWS_CSS_Selector::to_css()
	 *
	 * @return string
	 */
	public function to_css()
	{
		if($this->_con == self::CON_ANY_CHILD)
			return $this->_lsel.$this->_con.$this->_rsel;
		return $this->_lsel.' '.$this->_con.' '.$this->_rsel;
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