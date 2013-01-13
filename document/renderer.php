<?php
/**
 * Contains the document-renderer-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	document
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
 * The interface for all document-renderer
 *
 * @package			FrameWorkSolution
 * @subpackage	document
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Document_Renderer
{
	/**
	 * Should render the given document and return the result
	 *
	 * @param FWS_Document $doc the document
	 * @return string the result
	 */
	public function render($doc);
}
?>