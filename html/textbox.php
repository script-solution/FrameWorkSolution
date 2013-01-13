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
 * Represents a text-box:
 * <code>
 * 	<input type="text" name="..." value="..." />
 * </code>
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_TextBox extends FWS_HTML_TextElement
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
		if($maxlength !== null && (!FWS_Helper::is_integer($maxlength) || $maxlength <= 0))
			FWS_Helper::def_error('intgt0','maxlength',$maxlength);
		
		$this->_maxlength = $maxlength;
	}
	
	public function to_html()
	{
		$html = '<input type="text"'.$this->get_default_attr_html().' size="'.$this->get_cols().'"';
		if($this->_maxlength !== null)
			$html .= ' maxlength="'.$this->_maxlength.'"';
		$html .= ' value="'.$this->get_used_value().'"';
		$html .= ' />';
		return $html;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>