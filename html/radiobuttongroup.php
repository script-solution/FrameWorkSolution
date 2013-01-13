<?php
/**
 * Contains the radiobutton-group-class
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
 * Represents a radio-button-group.
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_RadioButtonGroup extends FWS_HTML_List
{
	/**
	 * The separator for the radiobuttons
	 *
	 * @var string
	 */
	private $_separator;
	
	/**
	 * Constructor
	 *
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $value the value of the element (null = default)
	 * @param mixed $default the default value
	 * @param string $separator the separator for the radiobuttons
	 */
	public function __construct($name,$id = null,$value = null,$default = '',$separator = '<br />')
	{
		parent::__construct($name,$id,$value,$default);
		
		$this->set_separator($separator);
	}

	/**
	 * @return string the separator for the radio-buttons
	 */
	public final function get_separator()
	{
		return $this->_separator;
	}

	/**
	 * Sets the separator for the radio-buttons
	 * 
	 * @param string $separator the new value
	 */
	public final function set_separator($separator)
	{
		$this->_separator = $separator;
	}
	
	public function to_html()
	{
		$i = 0;
		$id = $this->get_id();
		$len = count($this->get_options());
		$selected = $this->get_used_value();
		
		$html = '';
		foreach($this->get_options() as $k => $v)
		{
			// set temp id for this radiobutton
			$this->set_id($id.'_'.$k);
			
			$html .= '<input type="radio"'.$this->get_default_attr_html().' value="'.$k.'"';
			if($k == $selected)
				$html .= ' checked="checked"';
			$html .= '/>&nbsp;<label for="'.$this->get_id().'">'.$v.'</label>';
			
			// append sep?
			if($i < $len - 1)
				$html .= $this->_separator;
			$i++;
		}
		
		// reset id
		$this->set_id($id);
		return $html;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>