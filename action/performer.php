<?php
/**
 * Contains the action-performer for the {@link FWS_Action_Base}-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	action
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Will be used to determine which action has to be performed, if any.
 * And, of course, the action will also be started and finished from here.
 *
 * @package			FrameWorkSolution
 * @subpackage	action
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Action_Performer extends FWS_Object
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
	private $_prefix = 'FWS_Action_';
	
	/**
	 * The folder which contains the modules
	 *
	 * @var string
	 */
	private $_mod_folder = 'modules/';
	
	/**
	 * The action-listener
	 *
	 * @var FWS_Action_Listener
	 */
	private $_listener = null;
	
	/**
	 * Sets the action-listener
	 *
	 * @param FWS_Action_Listener $listener the listener
	 */
	public final function set_listener($listener)
	{
		if(!($listener instanceof FWS_Action_Listener))
			FWS_Helper::def_error('instance','listener','FWS_Action_Listener',$listener);
		
		$this->_listener = $listener;
	}

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
	 * 	{@link FWS_Path::server_app()})
	 */
	public final function set_mod_folder($folder)
	{
		if(!is_dir(FWS_Path::server_app().$folder))
			FWS_Helper::error('"'.FWS_Path::server_app().$folder.'" is no folder!');
		
		$this->_mod_folder = FWS_FileUtils::ensure_trailing_slash($folder);
	}

	/**
	 * Adds the given action to the container. Note that the action has
	 * to be an instance of an inherited class of {@link FWS_Action_Base}!
	 *
	 * @param FWS_Action_Base $action an instance of an inherited class of {@link FWS_Action_Base}
	 */
	public final function add_action($action)
	{
		if(!($action instanceof FWS_Action_Base))
			FWS_Helper::def_error('instance','action','FWS_Action_Base',$action);

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
	 * <code><modfolder>/<moduleName>/action_<actionName>.php</code>
	 * and the action-class:
	 * <code><prefix><moduleName>_<actionName></code>
	 *
	 * @param string $module_name the name of the module
	 * @param array $actions the actions to add
	 */
	public final function add_actions($module_name,$actions)
	{
		if(empty($module_name))
			FWS_Helper::def_error('notempty','module_name',$module_name);

		if(!is_array($actions))
			FWS_Helper::def_error('array','actions',$actions);

		foreach($actions as $id => $name)
		{
			if(!FWS_Helper::is_integer($id))
				FWS_Helper::def_error('integer','actions[id]',$id);
			
			if(empty($name))
				FWS_Helper::def_error('notempty','actions['.$id.']',$name);
			
			// Are there additional parameters?
			if(is_array($name))
			{
				$this->_add_params[$id] = array();
				for($i = 1,$len = count($name);$i < $len;$i++)
					$this->_add_params[$id][] = $name[$i];
				$name = $name[0];
			}
			
			$filename = FWS_Path::server_app().$this->_mod_folder.$module_name.'/action_'.$name.'.php';
			if(!is_file($filename))
				FWS_Helper::error('The file "'.$filename.'" does not exist!');
			
			include_once($filename);
			$classname = $this->_prefix.$module_name.'_'.$name;
			if(!class_exists($classname))
				FWS_Helper::error('The class "'.$classname.'" does not exist!');
			
			$c = new $classname($id);
			$this->_actions[$id] = $c;
		}
	}

	/**
	 * Determines the id of the action to perform.
	 * You may overwrite this method to change the behaviour.
	 *
	 * By default the method looks for <var>$_POST['aid']</var> and
	 * <var>$_GET['aid']</var>.
	 *
	 * @return int the action-type or null if nothing is to do
	 */
	protected function get_action_id()
	{
		$input = FWS_Props::get()->input();

		$action_type = $input->get_var('aid','post',FWS_Input::INTEGER);
		if($action_type === null)
			$action_type = $input->get_var('aid','get',FWS_Input::INTEGER);

		return $action_type;
	}

	/**
	 * Performs an requested action.
	 * And displays the error-message if any occurred or the status-page if enabled
	 *
	 * @return int the result of the action:
	 * <pre>
	 * 	-1 = error,
	 * 	0 = success / nothing done,
	 * 	1 = success + status-page
	 * </pre>
	 */
	public final function perform_action()
	{
		$id = $this->get_action_id();
		if($id === null)
			return 0;
		
		return $this->perform_action_by_id($id);
	}
	
	/**
	 * Performs the action with given id, if available
	 *
	 * @param mixed $id the action-id
	 * @return int the result of the action:
	 * <pre>
	 * 	-1 = error,
	 * 	0 = success / nothing done,
	 * 	1 = success + status-page
	 * </pre>
	 */
	public final function perform_action_by_id($id)
	{
		$locale = FWS_Props::get()->locale();
		$msgs = FWS_Props::get()->msgs();
		$doc = FWS_Props::get()->doc();
		
		if($id === null)
			FWS_Helper::def_error('notnull','id',$id);
		
		// action unknown?
		if(!isset($this->_actions[$id]))
			return 0;
		
		// perform the action
		$c = $this->_actions[$id];
		/* @var $c FWS_Action_Base */
		
		if($this->_listener !== null)
			$this->_listener->before_action_performed($id,$c);
		
		if(isset($this->_add_params[$id]))
			$message = call_user_func_array(array($c,'perform_action'),$this->_add_params[$id]);
		else
			$message = $c->perform_action();
		
		if($this->_listener !== null)
			$this->_listener->after_action_performed($id,$c,$message);

		// has an error occurred?
		if($message)
		{
			if(!is_array($message))
				$message = array($message);
			
			foreach($message as $mline)
			{
				if($locale->contains_lang('error_'.$mline))
					$msgs->add_error($locale->lang('error_'.$mline));
				else if($locale->contains_lang($mline))
					$msgs->add_error($locale->lang($mline));
				else
					$msgs->add_error($mline);
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
					$success_msg = $locale->lang('success_'.$id);

				foreach($c->get_links() as $name => $url)
				{
					if($url instanceof FWS_URL)
						$msgs->add_link($name,$url->to_url());
					else
						$msgs->add_link($name,$url);
				}
				
				$msgs->add_notice($success_msg);
				if($c->get_redirect())
					$doc->request_redirect($c->get_redirect_url(),$c->get_redirect_time());
				
				return 1;
			}
			// redirect the user immediatly?
			else if($c->get_redirect())
			{
				$url = $c->get_redirect_url();
				$doc->redirect($url);
			}
		}

		return 0;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>