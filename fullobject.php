<?php
/**
 * Contains the full-object-class
 *
 * @version			$Id: fullobject.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class is an example of the class {@link PLIB_FullObject} which is used
 * of all class in the library which require some properties.
 * It is just used to have code-completion (otherwise some IDEs don't know the
 * type of the properties).
 * <br>
 * But of course the class is required because many classes inherit from it.
 * But the contents of the class doesn't matter.
 * <br>
 * So this class is intended to be copied and renamed to {@link PLIB_FullObject}.
 * It contains the default fields with the default types. Of course you may change that.
 * Note that the fields are commented out because they are not required to use the
 * library and we don't want to blow every class and its objects by useless fields :)
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class PLIB_FullObject extends PLIB_Object
{
	/**
	 * The db-connection class
	 *
	 * @var PLIB_MySQL
	 */
	//private $db;

	/**
	 * The input-class
	 *
	 * @var PLIB_Input
	 */
	//private $input;

	/**
	 * The cookie-handling object
	 *
	 * @var PLIB_Cookies
	 */
	//private $cookies;

	/**
	 * The locale-object
	 *
	 * @var PLIB_Locale
	 */
	//private $locale;

	/**
	 * The template-object
	 *
	 * @var PLIB_Template_Handler
	 */
	//private $tpl;

	/**
	 * The session-manager-object
	 *
	 * @var PLIB_Session_Manager
	 */
	//private $sessions;
	
	/**
	 * The current user
	 *
	 * @var PLIB_User_Current
	 */
	//private $user;

	/**
	 * The object for the URL-creation
	 *
	 * @var PLIB_URL
	 */
	//private $url;
	
	/**
	 * The document
	 *
	 * @var PLIB_Document
	 */
	//private $doc;
	
	/**
	 * The messages-object
	 *
	 * @var PLIB_Messages
	 */
	//private $msgs;
}
?>