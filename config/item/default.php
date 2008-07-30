<?php
/**
 * Contains the default-item-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The default-implementation for the config-item
 * 
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_Config_Item_Default extends FWS_Object implements FWS_Config_Item
{
	/**
	 * The data of the item
	 *
	 * @var FWS_Config_Data
	 */
	protected $_data;
	
	/**
	 * Constructor
	 *
	 * @param FWS_Config_Data $data the data of the item
	 */
	public function __construct($data)
	{
		parent::__construct();
		
		if(!($data instanceof FWS_Config_Data))
			FWS_Helper::def_error('instance','data','FWS_Config_Data',$data);
		
		$this->_data = $data;
	}
	
	public function get_data()
	{
		return $this->_data;
	}
	
	public function has_changed()
	{
		return $this->_data->get_value() != $this->get_value();
	}
	
	/**
	 * @return string the suffix or an empty string for no suffix
	 */
	protected function get_suffix()
	{
		if(($suffix = $this->_data->get_suffix()))
			return ' '.preg_replace('/%([a-z0-9_]+)/ei','FWS_Props::get()->locale()->lang("\\1")',$suffix);
		return '';
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>