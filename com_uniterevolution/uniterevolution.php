<?php
/**
 * @package Unite Revolution Slider for Joomla 1.7-2.5
 * @version 1.0
 * @author UniteCMS.net
 * @copyright (C) 2012- Unite CMS
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


require_once JPATH_ADMINISTRATOR."/components/com_uniterevolution/includes.php";

//error_reporting(E_ALL); // debug

// Include dependancies
jimport('joomla.application.component.controller');

if(UniteFunctionJoomlaRev::isJoomla3())
	$controller	= JControllerLegacy::getInstance(GlobalsUniteRev::EXTENSION_NAME);
else	
	$controller	= JController::getInstance(GlobalsUniteRev::EXTENSION_NAME);


// Perform the Request task
//$task = JRequest::getCmd('task');
$task = JFactory::getApplication()->input->get('task');

$controller->execute($task);
$controller->redirect();

?>