<?php
/**
 * Contains the css-type-selector-class
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
		parent::__construct();
		
		$this->set_tagname($tagname);
	}
	
	/**
	 * @return string the tag-name
	 */
	public final function get_tagname()
	{
		return $this->_tagname;
	}
	
	/**
	 * Sets the tagname
	 *
	 * @param string $tagname the new value
	 */
	public final function set_tagname($tagname)
	{
		if($tagname != '' && !preg_match('/^\*|([a-z\-_][a-z\-_0-9]*)$/i',$tagname))
			FWS_Helper::error('The tag-name has to be an identifier or "*" or empty! (got "'.$tagname.'")');
		
		$this->_tagname = $tagname;
	}
	
	/**
	 * @see FWS_CSS_Selector::to_css()
	 *
	 * @return string
	 */
	public function to_css()
	{
		return $this->_tagname;
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