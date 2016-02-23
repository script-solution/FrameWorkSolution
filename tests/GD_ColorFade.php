<?php
/**
 * Contains the FWS_GD_ColorFade test
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
 * FWS_GD_ColorFade test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tests_GD_ColorFade extends FWS_Test_Case
{
	/**
	 * @var FWS_GD_ColorFade
	 */
	private $_cf;

	/**
	 * Prepares the environment before running a test.
	 */
	public function set_up()
	{
		$colors = array(
			array(255,0,0),
			array(0,0,255),
			array(0,255,0),
			array(255,0,255)
		);
		$this->_cf = new FWS_GD_ColorFade(360,120,$colors);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	public function tear_down()
	{
		$this->_cf = null;
	}

	/**
	 * Tests FWS_GD_ColorFade->get_color_at()
	 */
	public function testGet_color_at()
	{
		$c = $this->_cf->get_color_at(0);
		$c = $this->_cf->get_color_at(1);
		$c = $this->_cf->get_color_at(2);
		$c = $this->_cf->get_color_at(41);
	}
}
