<?php
/**
 * Contains the css-charset-block-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	css.block
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
 * A charset-block
 *
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Block_Charset extends FWS_Object implements FWS_CSS_Block
{
	/**
	 * The charset
	 *
	 * @var string
	 */
	private $_charset;
	
	/**
	 * Constructur
	 *
	 * @param string $charset the charset
	 */
	public function __construct($charset)
	{
		parent::__construct();
		$this->_charset = $charset;
	}
	
	/**
	 * @return string the charset
	 */
	public function get_charset()
	{
		return $this->_charset;
	}
	
	/**
	 * @see FWS_CSS_Block::get_type()
	 *
	 * @return int
	 */
	public function get_type()
	{
		return self::CHARSET;
	}
	
	/**
	 * @see FWS_CSS_Block::to_css()
	 *
	 * @param string $indent
	 * @return string
	 */
	public function to_css($indent = '')
	{
		return $indent.'@charset "'.$this->_charset.'";';
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