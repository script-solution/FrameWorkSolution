<?php
/**
 * Contains the config-item-multi-enum class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The implementation of the config-item "multi-enum". That means the user will get a multi-combobox
 * or multiple checkboxes.
 *
 * @package			FrameWorkSolution
 * @subpackage	config.item
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Item_MultiEnum extends FWS_Config_Item_Default
{
	public function get_control($form)
	{
		$locale = FWS_Props::get()->locale();

		/* @var $form FWS_HTML_Formular */
		$props = $this->_data->get_properties();
		$options = $this->get_items($props);
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
					$this->_data->get_name().'['.$key.']',in_array($key,$vals),'1',$locale->lang($value)
				);
				if($i < $len - 1)
					$str .= '<br />';
			}
		}
		
		$str .= $this->get_suffix();
		return $str;
	}
	
	/**
	 * Builds the items for the combobox or radioboxes
	 *
	 * @param array $props the properties of the item
	 * @return array all items
	 */
	protected function get_items($props)
	{
		$locale = FWS_Props::get()->locale();

		$options = array();
		foreach($props as $k => $v)
		{
			if($k === 'type')
				continue;
			$options[$k] = $locale->lang($v,false);
		}
		return $options;
	}

	public function get_value()
	{
		$input = FWS_Props::get()->input();

		$props = $this->_data->get_properties();
		$vals = $input->get_var($this->_data->get_name(),'post');
		if(!is_array($vals))
			$vals = array();
		
		if($props['type'] == 'combo')
			return implode(',',$vals);
		
		return implode(',',array_keys($vals));
	}
}
?>