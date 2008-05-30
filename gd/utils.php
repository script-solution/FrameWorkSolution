<?php
/**
 * Contains the gd-utils-class
 *
 * @version			$Id: utils.php 672 2008-05-05 21:58:06Z nasmussen $
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains some helper-methods for the gd-package
 *
 * @package			PHPLib
 * @subpackage	gd
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_GD_Utils extends PLIB_UtilBase
{
	/**
	 * Adds the given padding to the bounds
	 *
	 * @param array $bounds the bounds
	 * @param PLIB_GD_Padding $padding the padding to add
	 * @param int $angle the rotation-angle
	 */
	public static function add_padding(&$bounds,$padding,$angle)
	{
		if(!($padding instanceof PLIB_GD_Padding))
			PLIB_Helper::def_error('instance','padding','PLIB_GD_Padding',$padding);
		
		$pl = $padding->get_left();
		$pb = $padding->get_bottom();
		$pr = $padding->get_right();
		$pt = $padding->get_top();
		self::add_padding_custom($bounds,$pt,$pr,$pb,$pl,$angle);
	}
	
	/**
	 * Adds the given padding to the bounds
	 *
	 * @param array $bounds the bounds
	 * @param int $pt the top-padding
	 * @param int $pr the right-padding
	 * @param int $pb the bottom-padding
	 * @param int $pl the left-padding
	 * @param int $angle the rotation-angle
	 */
	public static function add_padding_custom(&$bounds,$pt,$pr,$pb,$pl,$angle)
	{
		if(!is_array($bounds) || count($bounds) != 8)
			PLIB_Helper::error('Invalid bounds-array: '.PLIB_PrintUtils::to_string($bounds,true,false));
		
		// nothing to do?
		if($pt == 0 && $pr == 0 && $pb == 0 && $pl == 0)
			return;
		
		$a = deg2rad($angle);
		$sina = sin($a);
		$sinp2a = sin(M_PI_2 - $a);
		$cosa = cos($a);
		$cosp2a = cos(M_PI_2 - $a);
		
		// -- top
		$ptx = $sina * $pt;
		$pty = $sinp2a * $pt;
		
		// top left
		$bounds[6] -= $ptx;
		$bounds[7] -= $pty;
		
		// top right
		$bounds[4] -= $ptx;
		$bounds[5] -= $pty;
		
		// -- bottom
		$pbx = $sina * $pb;
		$pby = $sinp2a * $pb;
		
		// bottom left
		$bounds[0] += $pbx;
		$bounds[1] += $pby;
		
		// bottom right
		$bounds[2] += $pbx;
		$bounds[3] += $pby;
		
		// -- left
		$plx = $cosa * $pl;
		$ply = $cosp2a * $pl;
		
		// bottom left
		$bounds[0] -= $plx;
		$bounds[1] += $ply;
		
		// top left
		$bounds[6] -= $plx;
		$bounds[7] += $ply;
		
		// -- right
		$plx = $cosa * $pr;
		$ply = $cosp2a * $pr;
		
		// top right
		$bounds[4] += $plx;
		$bounds[5] -= $ply;
		
		// bottom right
		$bounds[2] += $plx;
		$bounds[3] -= $ply;
	}
}
?>