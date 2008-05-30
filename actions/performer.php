<?php
/**
 * Contains the action-performer for the {@link PLIB_Actions_Base}-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	actions
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Will be used to determine which action has to be performed, if any.
 * And, of course, the action will also be started and finished from here.
 *
 * @package			PHPLib
 * @subpackage	actions
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Actions_Performer extends PLIB_FullObject
{
	/**
	 * An associative array with all announced actions:
	 * <code>
	 * 	array(
	 * 		<actionID> => <action>
	 * 	)
	 * </code>
	 *
	 * @var array
	 */
	private $_actions = array();
	
	/**
	 * An associative array with additional parameters for actions:
	 * <code>
	 * 	array(
	 * 		<actionID> => array(<param1>,...,<paramN>)
	 * 	)
	 * </code>
	 *
	 * @var array
	 */
	private $_add_params = array();

	/**
	 * The prefix for the action-classes
	 *
	 * @var string
	 */
	private $_prefix = 'PLIB_Action_';
	
	/**
	 * The folder which contains the modules
	 *
	 * @var string
	 */
	private $_mod_folder = 'modules/';

	/**
	 * @return string the prefix of the action-classes
	 */
	public final function get_prefix()
	{
		return $this->_prefix;
	}

	/**
	 * Sets the prefix for the actions. This will be the first part of the class-name:
	 * <pre>&lt;prefix&gt;&lt;actionName&gt;</pre>
	 *
	 * @param string $prefix the new value
	 */
	public final function set_prefix($prefix)
	{
		$this->_prefix = $prefix;
	}

	/**
	 * @return string the folder which contains the modules
	 */
	public final function get_modules_folder()
	{
		return $this->_mod_folder;
	}

	/**
	 * Sets the folder which contains the modules
	 * 
	 * @param string $folder the new value (with trailing slash and starting at
	 * 	{@link PLIB_Path::inner()})
	 */
	public final function set_mod_folder($folder)
	{
		if(!is_dir(PLIB_Path::inner().$folder))
			PLIB_Helper::error('"'.PLIB_Path::inner().$folder.'" is no folder!');
		
		$this->_mod_folder = PLIB_FileUtils::ensure_trailing_slash($folder);
	}

	/**
	 * Adds the given action to the container. Note that the action has
	 * to be an instance of an inherited class of {@link PLIB_Actions_Base}!
	 *
	 * @param PLIB_Actions_Base $action an instance of an inherited class of {@link PLIB_Actions_Base}
	 */
	public final function add_action($action)
	{
		if(!($action instanceof PLIB_Actions_Base))
			PLIB_Helper::def_error('instance','action','PLIB_Actions_Base',$action);

		$this->_actions[$action->get_action_id()] = $action;
	}

	/**
	 * Adds the given actions. The parameter has to have the following form:
	 * <code>
	 * 	array(
	 * 		<actionID> => <actionName>
	 * 		...
	 * 	)
	 * </code>
	 * The method assumes that the action-file will be:
	 * <code>modules/<moduleName>/action_<actionName>.php</code>
	 * and the action-class:
	 * <code><prefix><moduleName>_<actionName></code>
	 *
	 * @param string $module_name the name of the module
	 * @param array $actions the actions to add
	 */
	public final function add_actions($module_name,$actions)
	{
		if(empty($module_name))
			PLIB_Helper::def_error('notempty','module_name',$module_name);

		if(!is_array($actions))
			PLIB_Helper::def_error('array','actions',$actions);

		foreach($actions as $id => $name)
		{
			if(!PLIB_Helper::is_integer($id))
				PLIB_Helper::def_error('integer','actions[id]',$id);
			
			if(empty($name))
				PLIB_Helper::def_error('notempty','actions['.$id.']',$name);
			
			// Are there additional parameters?
			if(is_array($name))
			{
				$this->_add_params[$id] = array();
				for($i = 1,$len = count($name);$i < $len;$i++)
					$this->_add_params[$id][] = $name[$i];
				$name = $name[0];
			}
			
			$filename = PLIB_Path::inner().$this->_mod_folder.$module_name.'/action_'.$name.'.php';
			if(!is_file($filename))
				PLIB_Helper::error('The file "'.$filename.'" does not exist!');
			
			include_once($filename);
			$classname = $this->_prefix.$module_name.'_'.$name;
			if(!class_exists($classname))
				PLIB_Helper::error('The class "'.$classname.'" does not exist!');
			
			$c = new $classname($id);
			$this->_actions[$id] = $c;
		}
	}

	/**
	 * Determines the action-type to perform.
	 * You may overwrite this method to change the behaviour.
	 *
	 * By default the method looks for <var>$_POST['action_type']</var> and
	 * <var>$_GET['at']</var>.
	 *
	 * @return int the action-type or null if nothing is to do
	 */
	public function get_action_type()
	{
		$action_type = $this->input->get_var('action_type','post',PLIB_Input::INTEGER);
		if($action_type === null)
			$action_type = $this->input->get_var('at','get',PLIB_Input::INTEGER);

		return $action_type;
	}

	/**
	 * Performs an requested action.
	 * And displays the error-message if any occurred or the status-page if enabled
	 *
	 * @return int the result of this function:
	 * <pre>
	 * 	-1 = error,
	 * 	0 = success / nothing done,
	 * 	1 = success + status-page
	 * </pre>
	 */
	public final function perform_actions()
	{
		$action_type = $this->get_action_type();
		if($action_type === null)
			return 0;

		// action unknown?
		if(!isset($this->_actions[$action_type]))
			return 0;

		// perform the action
		$c = $this->_actions[$action_type];
		/* @var $c PLIB_Actions_Base */
		
		$this->_before_action_performed($action_type,$c);
		
		if(isset($this->_add_params[$action_type]))
			$message = call_user_func_array(array($c,'perform_action'),$this->_add_params[$action_type]);
		else
			$message = $c->perform_action();
		
		$this->_after_action_performed($action_type,$c,$message);

		// has an error occurred?
		if($message)
		{
			if(!is_array($message))
				$message = array($message);
			
			foreach($message as $mline)
			{
				if($this->locale->contains_lang('error_'.$mline))
					$this->msgs->add_error($this->locale->lang('error_'.$mline));
				else if($this->locale->contains_lang($mline))
					$this->msgs->add_error($this->locale->lang($mline));
				else
					$this->msgs->add_error($mline);
			}
			
			return $c->get_error_return_val();
		}
		else
		{
			// nothing has been done
			if(!$c->get_action_performed())
				return 0;

			// do we have to display a status-page?
			if($c->show_status_page())
			{
				if($c->get_success_msg() != '')
					$success_msg = $c->get_success_msg();
				else
					$success_msg = $this->locale->lang('success_'.$action_type);

				foreach($c->get_links() as $name => $url)
					$this->msgs->add_link($name,$url);
				
				$this->msgs->add_notice($success_msg);
				if($c->get_redirect())
					$this->doc->request_redirect($c->get_redirect_url(),$c->get_redirect_time());
				
				return 1;
			}
			// redirect the user immediatly?
			else if($c->get_redirect())
			{
				$url = $c->get_redirect_url();
				$url = str_replace('&amp;','&',$url);
				$this->doc->redirect($url);
			}
		}

		return 0;
	}
	
	/**
	 * This method will be called before the action has been performed.
	 * You may overwrite this to do something before the action will be performed
	 * 
	 * @param int $id the action-id
	 * @param PLIB_Actions_Base $action the action-instance
	 */
	protected function _before_action_performed($id,$action)
	{
		// by default we do nothing
	}
	
	/**
	 * This method will be called after the action has been performed.
	 * You may overwrite this to do something after the action will be performed.
	 * You may also change the message that should be displayed
	 *
	 * @param int $id the action-id
	 * @param PLIB_Actions_Base $action the action-instance
	 * @param string $message the message that has been returned from the action
	 */
	protected function _after_action_performed($id,$action,&$message)
	{
		// be default we do nothing
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>