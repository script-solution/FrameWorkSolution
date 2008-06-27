<?php
/**
 * Contains the ellipse-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents an ellipse for drawing with GD
 *
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_GD_Ellipse extends PLIB_FullObject
{
	/**
	 * The center-point
	 *
	 * @var PLIB_GD_Point
	 */
	protected $_center;
	
	/**
	 * The size of the ellipse
	 *
	 * @var PLIB_GD_Dimension
	 */
	protected $_size;

	/**
	 * Constructor
	 *
	 * @param PLIB_GD_Point $center the center-point
	 * @param PLIB_GD_Dimension $size the size of the ellipse
	 */
	public function __construct($center,$size)
	{
		parent::__construct();
		
		if(!($center instanceof PLIB_GD_Point))
			PLIB_Helper::def_error('instance','center','PLIB_GD_Point',$center);
		if(!($size instanceof PLIB_GD_Dimension))
			PLIB_Helper::def_error('instance','size','PLIB_GD_Dimension',$size);
		
		$this->_center = $center;
		$this->_size = $size;
	}
	
	/**
	 * @return PLIB_GD_Point the center-point
	 */
	public function get_center()
	{
		return $this->_center;
	}
	
	/**
	 * @return PLIB_GD_Dimension the size
	 */
	public function get_size()
	{
		return $this->_size;
	}
	
	/**
	 * Moves the ellipse by the given amount
	 *
	 * @param int $x the amount in x-direction
	 * @param int $y the amount in y-direction
	 */
	public function translate($x,$y)
	{
		$this->_center->translate($x,$y);
	}
	
	/**
	 * Increases the size of the ellipse by the given amounts
	 *
	 * @param int $w the amount to add to the width
	 * @param int $h the amount to add to the height
	 */
	public function grow($w,$h)
	{
		$this->_size->increase($w,$h);
	}
	
	/**
	 * Decreases the size of the ellipse by the given amounts
	 *
	 * @param int $w the amount to substract from the width
	 * @param int $h the amount to substract from the height
	 */
	public function shrink($w,$h)
	{
		$this->_size->decrease($w,$h);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>