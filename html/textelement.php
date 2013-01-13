<?php
/**
 * Contains the textbox-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	html
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
 * The abstract base-class for all text-elements
 * (&lt;input type="text"../&gt;,&lt;textarea&gt;,...)
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_HTML_TextElement extends FWS_HTML_FormElement
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
		if(!FWS_Helper::is_integer($cols) || $cols <= 0)
			FWS_Helper::def_error('intgt0','cols',$cols);
		
		$this->_cols = $cols;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>