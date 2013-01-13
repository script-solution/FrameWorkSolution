<?php
/**
 * Contains the css-import-block
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
 * An import-block
 *
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_CSS_Block_Import extends FWS_Object implements FWS_CSS_Block
{
	/**
	 * The file URI
	 *
	 * @var string
	 */
	private $_uri;
	
	/**
	 * An array of medias
	 *
	 * @var array
	 */
	private $_media;
	
	/**
	 * Constructur
	 *
	 * @param string $uri the file-URI
	 * @param array $media a list of media-types for this import
	 */
	public function __construct($uri,$media = array())
	{
		parent::__construct();
		
		$this->_uri = $uri;
		$this->_media = $media;
	}
	
	/**
	 * @return string the file URI
	 */
	public function get_uri()
	{
		return $this->_uri;
	}
	
	/**
	 * Sets the file URI
	 *
	 * @param string $uri the new value
	 */
	public function set_uri($uri)
	{
		if(empty($uri))
			FWS_Helper::def_error('notempty','uri',$uri);
		
		$this->_uri = $uri;
	}
	
	/**
	 * @return array the media-types
	 */
	public function get_media()
	{
		return $this->_media;
	}
	
	/**
	 * Sets the media-types
	 *
	 * @param array $media the media-types
	 */
	public function set_media($media)
	{
		if(!is_array($media))
			FWS_Helper::def_error('array','media',$media);
		
		$this->_media = $media;
	}
	
	/**
	 * @see FWS_CSS_Block::get_type()
	 *
	 * @return int
	 */
	public function get_type()
	{
		return self::IMPORT;
	}
	
	/**
	 * @see FWS_CSS_Block::to_css()
	 *
	 * @param string $indent
	 * @return string
	 */
	public function to_css($indent = '')
	{
		$str = $indent.'@import "'.$this->_uri.'"';
		if(count($this->_media))
			$str .= ' '.implode(', ',$this->_media);
		$str .= ';';
		return $str;
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