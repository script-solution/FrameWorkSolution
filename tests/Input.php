<?php
/**
 * Contains the FWS_Input test
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
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
 * FWS_Input test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_Input extends FWS_Test_Case
{
	/**
	 * @var FWS_Input
	 */
	private $_input;

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		$this->_input = FWS_Input::get_instance();
	}

	/**
	 * Prepares the environment before running a test.
	 */
	public function set_up()
	{
		$_GET = array(
			'int' => 1123,
			'float' => 0.4,
			'hex32' => md5('120'),
			'bool' => true,
			'intbool' => 1,
			'alpha' => 'abc',
			'alphanum' => 'abc123',
			'identifier' => 'AbCd123_',
			"#abc" => "a",
			"&g" => "b",
			"a\n\rb" => "c\n\rd",
			"sded" => "..//../.......///"
		);
		$_POST = array(
			"123#456" => "\r\r\r\r",
			"test" => "abc & def ' \""
		);
		$_SERVER['HTTP_USER_AGENT'] = 'Mein Useragent & mehr <script>alert("huhu");</script>';
		
		$this->_input->rescan_superglobals();
	}

	/**
	 * Tests FWS_Input->correct_var()
	 */
	public function testCorrect_var()
	{
		$res = $this->_input->correct_var('#abc','get',FWS_Input::STRING,array('a','b'),'c');
		self::assert_equals($res,'a');
		
		$res = $this->_input->correct_var('#abc','get',FWS_Input::STRING,array('x','y'),'c');
		$res2 = $this->_input->get_var('#abc','get',FWS_Input::STRING);
		self::assert_equals($res,'c');
		self::assert_equals($res2,'c');
	}

	/**
	 * Tests FWS_Input->get_predef()
	 */
	public function testGet_predef()
	{
		$this->_input->set_predef('int','get',FWS_Input::INTEGER);
		$res = $this->_input->get_predef('int','get');
		self::assert_equals($res,1123);
		
		$this->_input->set_predef('float','get',FWS_Input::FLOAT,array(1.0,0.4,0.2));
		$res = $this->_input->get_predef('float','get',1.0);
		self::assert_equals($res,0.4);
		
		$this->_input->set_predef('float','get',FWS_Input::FLOAT,array(1.0,0.3,0.2));
		$res = $this->_input->get_predef('float','get',1.0);
		self::assert_equals($res,1.0);
	}

	/**
	 * Tests FWS_Input->get_var()
	 */
	public function testGet_var()
	{
		$res = $this->_input->get_var('int','get',FWS_Input::INTEGER);
		self::assert_equals($res,1123);
		
		$res = $this->_input->get_var('int','get',FWS_Input::HEX_32);
		self::assert_null($res);
		
		$res = $this->_input->get_var('float','get',FWS_Input::FLOAT);
		self::assert_equals($res,0.4);
		
		$res = $this->_input->get_var('float','get',FWS_Input::INTEGER);
		self::assert_null($res);
		
		$res = $this->_input->get_var('hex32','get',FWS_Input::HEX_32);
		self::assert_equals($res,md5('120'));
		
		$res = $this->_input->get_var('hex32','get',FWS_Input::INTEGER);
		self::assert_null($res);
		
		$res = $this->_input->get_var('bool','get',FWS_Input::BOOL);
		self::assert_equals($res,true);
		
		$res = $this->_input->get_var('intbool','get',FWS_Input::INT_BOOL);
		self::assert_equals($res,1);
		
		$res = $this->_input->get_var('intbool','get',FWS_Input::BOOL);
		self::assert_equals($res,true);
		
		$res = $this->_input->get_var('alpha','get',FWS_Input::BOOL);
		self::assert_null($res);
		
		$res = $this->_input->get_var('alpha','get',FWS_Input::ALPHA);
		self::assert_equals($res,'abc');
		
		$res = $this->_input->get_var('alphanum','get',FWS_Input::ALPHA_NUM);
		self::assert_equals($res,'abc123');
		
		$res = $this->_input->get_var('identifier','get',FWS_Input::IDENTIFIER);
		self::assert_equals($res,'AbCd123_');
		
		$res = $this->_input->get_var('notexisting','get',FWS_Input::STRING);
		self::assert_null($res);
		
		$res = $this->_input->get_var('123#456','post',FWS_Input::STRING);
		self::assert_equals($res,"\n\n\n\n");
		
		$res = $this->_input->get_var("a\n\rb",'get');
		self::assert_null($res);
		
		$res = $this->_input->get_var('ab','get',FWS_Input::STRING);
		self::assert_equals($res,"cd");
		
		$res = $this->_input->get_var('123#456','post',FWS_Input::STRING);
		self::assert_equals($res,"\n\n\n\n");
		
		$res = $this->_input->get_var('test','post',FWS_Input::STRING);
		self::assert_equals($res,"abc &amp; def &#039; &quot;");
		
		$res = $this->_input->get_var('HTTP_USER_AGENT','server',FWS_Input::STRING);
		self::assert_equals(
			$res,'Mein Useragent &amp; mehr &lt;script&gt;alert(&quot;huhu&quot;);&lt;/script&gt;'
		);
	}

	/**
	 * Tests FWS_Input->isset_var()
	 */
	public function testIsset_var()
	{
		$res = $this->_input->isset_var('alpha','get');
		self::assert_true($res);
		
		$res = $this->_input->isset_var('notexisting','get');
		self::assert_false($res);
	}

	/**
	 * Tests FWS_Input->set_var()
	 */
	public function testSet_var()
	{
		$res = $this->_input->get_var('notexisting','get');
		self::assert_null($res);
		
		$this->_input->set_var('notexisting','get','123');
		$res = $this->_input->get_var('notexisting','get');
		self::assert_equals($res,'123');
	}

	/**
	 * Tests FWS_Input->unset_var()
	 */
	public function testUnset_var()
	{
		$res = $this->_input->isset_var('alpha','get');
		self::assert_true($res);
		
		$this->_input->unset_var('alpha','get');
		$res = $this->_input->isset_var('alpha','get');
		self::assert_false($res);
	}
}
