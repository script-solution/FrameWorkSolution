<?php
/**
 * Contains the config-item-multi-enum class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "multi-enum". That means the user will get a multi-combobox
 * or multiple checkboxes.
 *
 * @package			PHPLib
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Config_Item_MultiEnum extends PLIB_Config_Item_Default
{
	public function get_control($form)
	{
		/* @var $form PLIB_HTML_Formular */
		$props = $this->_data->get_properties();
		$options = $this->_get_items($props);
		$vals = explode(',',$this->_data->get_value());
		
		if($props['type'] == 'combo')
		{
			$str = $form->get_combobox(
				$this->_data->get_name().'[]',$options,$vals,true,count($options)
			);
		}
		else
		{
			$str = '';
			$i = 0;
			$props = $this->_data->get_properties();
			$len = count($props);
			foreach($props as $key => $value)
			{
				if($key == 'type')
					continue;
				
				$str .= $form->get_checkbox(
					$this->_data->get_name().'['.$key.']',in_array($key,$vals),1,$this->locale->lang($value)
				);
				if($i < $len - 1)
					$str .= '<br />';
			}
		}
		
		$str .= $this->_get_suffix();
		return $str;
	}
	
	/**
	 * Builds the items for the combobox or radioboxes
	 *
	 * @param array $props the properties of the item
	 * @return array all items
	 */
	protected function _get_items($props)
	{
		$options = array();
		foreach($props as $k => $v)
		{
			if($k === 'type')
				continue;
			$options[$k] = $this->locale->lang($v,false);
		}
		return $options;
	}

	public function get_value()
	{
		$props = $this->_data->get_properties();
		$vals = $this->input->get_var($this->_data->get_name(),'post');
		if(!is_array($vals))
			$vals = array();
		
		if($props['type'] == 'combo')
			return implode(',',$vals);
		
		return implode(',',array_keys($vals));
	}
}
?>