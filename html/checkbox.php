<?php
/**
 * Contains the checkbox-class
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
 * Represents a checkbox
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_Checkbox extends FWS_HTML_FormElement
{
	/**
	 * The text of the checkbox
	 *
	 * @var string
	 */
	protected $_text;
	
	/**
	 * The value of the checkbox. That is the value that the element will have
	 * if it has been selected
	 *
	 * @var mixed
	 */
	protected $_checked_value;
	
	/**
	 * Constructor
	 *
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 * @param string $text the text of the checkbox
	 * @param string $checked_value the value of the combobox (not the select-state!)
	 */
	public function __construct($name,$id = null,$value = null,$default = '',$text = '',
		$checked_value = '')
	{
		parent::__construct($name,$id,$value,$default);
		
		$this->set_text($text);
		$this->set_checked_value($checked_value);
	}

	/**
	 * @return string the text of the checkbox
	 */
	public final function get_text()
	{
		return $this->_text;
	}

	/**
	 * Sets the text of the checkbox
	 * 
	 * @param string $text the new value
	 */
	public final function set_text($text)
	{
		$this->_text = $text;
	}

	/**
	 * @return mixed the value of the checkbox (not the select-state!)
	 */
	public final function get_checked_value()
	{
		return $this->_checked_value;
	}

	/**
	 * Sets the value of the checkbox (not the select-state!).
	 * 
	 * @param mixed $checked_value the new value
	 */
	public final function set_checked_value($checked_value)
	{
		$this->_checked_value = $checked_value;
	}
	
	public function to_html()
	{
		$html = '<input type="checkbox"'.$this->get_default_attr_html();
		if($this->get_used_value())
			$html .= ' checked="checked"';
		$html .= ' value="'.$this->_checked_value.'" />';
		$html .= '&nbsp;<label for="'.$this->get_id().'">'.$this->_text.'</label>';
		return $html;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>