<?php
/**
 * Contains the css-import-block
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	css.block
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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
	 * Builds the string-representation of this ruleset. This will be valid CSS
	 *
	 * @param string $indent the indent for the string
	 * @return string the CSS-code
	 */
	public function __toString($indent = '')
	{
		$str = $indent.'@import "'.$this->_uri.'"';
		if(count($this->_media))
			$str .= ' '.implode(', ',$this->_media);
		$str .= ';';
		return $str;
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