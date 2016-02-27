<?php
/**
 * Contains the base-object for all classes
 * 
 * @package			FrameWorkSolution
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
 * This is the base object for all class which can be instantiated in the framework.
 * It offers us a way to affect all classes in the framework. So for example to print
 * an object or do other things.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Object
{
	/**
	 * The next id that should be used
	 *
	 * @var unknown_type
	 */
	private static $_next_id = 1;
	
	/**
	 * The id of this object
	 *
	 * @var int
	 */
	protected $_object_id;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_object_id = self::$_next_id++;
	}
	
	public function __clone()
	{
		// we have to assign a new id to cloned objects
		$this->_object_id = self::$_next_id++;
	}
	
	/**
	 * @return int the id of this object
	 */
	public final function get_object_id()
	{
		return $this->_object_id;
	}
	
	/**
	 * Prints the dump of this object. The method does the following:
	 * <code>
	 * echo $this->get_dump($use_html);
	 * </code>
	 * 
	 * @param boolean $use_html print the object as HTML?
	 */
	public final function dump($use_html = true)
	{
		echo $this->get_dump($use_html);
	}
	
	/**
	 * Builds the dump of this object. It uses {@link get_dump_vars} and formats this via
	 * {@link FWS_Printer}.
	 *
	 * @param boolean $use_html whether HTML should be used
	 * @return string the dump
	 */
	public final function get_dump($use_html = true)
	{
		$str = '';
		if($use_html)
			$str .= '<b>'.get_class($this).'</b>';
		else
			$str .= get_class($this);
		$str .= FWS_Printer::to_string($this->get_dump_vars(),$use_html);
		return $str;
	}
	
	/**
	 * Should return an array with all variables of the class that should be printed
	 * by dump() and get_dump(). If you overwrite this method you may use:
	 * <code>array_merge(parent::get_dump_vars(),get_object_vars($this))</code>
	 * By default you should return:
	 * <code>get_object_vars($this)</code>
	 *
	 * @return array an associative array with all variables
	 * @see get_object_vars()
	 */
	protected abstract function get_dump_vars();
}
?>