<?php
/*
 * --------------------------------------------------------------------------------
   Weblogicx India  - K2 Store v 2.5
 * --------------------------------------------------------------------------------
 * @package		Joomla! 1.7x
 * @subpackage	K2 Store
 * @author    	Weblogicx India http://www.weblogicxindia.com
 * @copyright	Copyright (c) 2010 - 2015 Weblogicx India Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link		http://weblogicxindia.com
 * --------------------------------------------------------------------------------
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'utilities.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'prices.php');
JHTML::_('stylesheet', 'style.css', 'components/com_k2store/css/');
JHtml::_('behavior.framework');

//JHTML::_('behavior.mootools');

// Require specific controller if requested
$controller = JRequest::getWord('view', 'mycart');
$task = JRequest::getWord('task');

jimport('joomla.filesystem.file');
jimport('joomla.html.parameter');

if (JFile::exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	$classname = 'K2StoreController'.$controller;
	$controller = new $classname();
	$controller->execute($task);
	$controller->redirect();
}
else {
	JError::raiseError(404, JText::_('K2STORE_NOT_FOUND'));
}