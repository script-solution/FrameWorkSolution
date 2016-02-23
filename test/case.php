<?php
/**
 * Contains the testcase class
 * 
 * @package			FrameWorkSolution
 * @subpackage	test
 *
 * Copyright (C) 2003 - 2016 Nils Asmussen
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
 * The base-class for all test cases
 * 
 * @package			FrameWorkSolution
 * @subpackage	test
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Test_Case extends FWS_Object
{
	/**
	 * Checks whether both strings are equal.
	 *
	 * @param mixed $exp the expected value
	 * @param mixed $recv the received value
	 * @throws Exception if the values are not equal
	 */
	protected static function assert_equals($exp,$recv)
	{
		if($exp != $recv)
			throw new Exception('Values are not equal. Expected "'.$exp.'", got "'.$recv.'"');
	}
	/**
	 * Checks whether both strings are equal.
	 *
	 * @param mixed $exp the expected value
	 * @param mixed $recv the received value
	 * @throws Exception if the values are not equal
	 */
	protected static function assert_not_equals($exp,$recv)
	{
		if($exp == $recv)
			throw new Exception('Values are equal. Expected "'.$exp.'" != "'.$recv.'"');
	}
	
	/**
	 * Checks whether the string matches the given regular expression.
	 *
	 * @param string $pattern the regular expression
	 * @param string $string the received string
	 * @throws Exception if the string does not match the pattern
	 */
	protected static function assert_regex($pattern,$string)
	{
		if(!preg_match($pattern,$string))
			throw new Exception('String does not match pattern. Expected "'.$pattern.'", got "'.$string.'"');
	}
	
	/**
	 * Checks whether $val is true.
	 *
	 * @param bool $val the value
	 * @throws Exception if not true
	 */
	protected static function assert_true($val)
	{
		if($val !== true)
			throw new Exception($val.' is not true');
	}
	
	/**
	 * Checks whether $val is false.
	 *
	 * @param bool $val the value
	 * @throws Exception if not false
	 */
	protected static function assert_false($val)
	{
		if($val !== false)
			throw new Exception($val.' is not false');
	}
	
	/**
	 * Checks whether $val is null.
	 *
	 * @param mixed $val the value
	 * @throws Exception if not null
	 */
	protected static function assert_null($val)
	{
		if($val !== null)
			throw new Exception($val.' is not null');
	}
	
	/**
	 * Is called before every test in the testcase.
	 */
	public function set_up()
	{
	}
	
	/**
	 * Is called after every test in the testcase.
	 */
	public function tear_down()
	{
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>