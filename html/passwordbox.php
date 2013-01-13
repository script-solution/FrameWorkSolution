<?php
/**
 * Contains the password-box-class
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
 * Represents a password-box:
 * <code>
 * 	<input type="password" name="..." />
 * </code>
 *
 * @package			FrameWorkSolution
 * @subpackage	html
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_HTML_PasswordBox extends FWS_HTML_TextBox
{
	/**
	 * Constructor
	 * 
	 * @param string $name the name of the control
	 * @param mixed $id the id of the element (null = none)
	 * @param mixed $default the default value
	 * @param mixed $cols the cols of the element
	 * @param mixed $maxlength the maximum length
	 */
	public function __construct($name,$id = null,$default = '',$cols = 15,$maxlength = null)
	{
		parent::__construct($name,$id,null,$default,$cols,$maxlength);
	}
	
	public function to_html()
	{
		$html = '<input type="password"'.$this->get_default_attr_html();
		$html .= ' size="'.$this->get_cols().'" value="'.$this->get_used_value().'"';
		if($this->get_maxlength() !== null)
			$html .= ' maxlength="'.$this->get_maxlength().'"';
		$html .= ' />';
		return $html;
	}
	
	protected function get_dump_vars()
	{
		return array_merge(parent::get_dump_vars(),get_object_vars($this));
	}
}
?>