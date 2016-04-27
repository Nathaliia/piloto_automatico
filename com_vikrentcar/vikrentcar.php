<?php
/**
 * Copyright (c) Extensionsforjoomla.com - E4J - Alessio <tech@extensionsforjoomla.com>
 * 
 * You should have received a copy of the License
 * along with this program.  If not, see <http://www.extensionsforjoomla.com/>.
 * 
 * For any bug, error please contact <tech@extensionsforjoomla.com>
 * We will try to fix it.
 * 
 * Extensionsforjoomla.com - All Rights Reserved
 * 
 */

defined('_JEXEC') OR die('Restricted Area');
error_reporting(0);

$document = & JFactory :: getDocument();
$document->addStyleSheet(JURI::root().'administrator/components/com_vikrentcar/vikrentcar.css');
$document->addStyleSheet(JURI::root().'components/com_vikrentcar/vikrentcar_styles.css');

$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/style.css');

$document->addScript(JURI::root().'components/com_vikrentcar/resources/redeban.js');



//se agrega comportamiento modal para que siempre se cargue con el componente
JHTML::_('behavior.modal');

require_once(JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."lib.vikrentcar.php");

if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_acymailing'.DS.'helpers'.DS.'helper.php')){
echo 'This code can not work without the AcyMailing Component';
return false;
}

$listMailClass = acymailing_get('class.listmail');

$listas= $listMailClass->getLists();


foreach ($listas as $key => $val) {

			
			define($val->name, $val->listid);

    		

    		
}



if(defined('JVERSION') && version_compare(JVERSION, '1.6.0') < 0) {
	//joomla 1.5
	require_once( JPATH_COMPONENT.DS.'controller.php' );
	$classname = 'VikrentcarController';
	$controller   = new $classname( );
	$controller->execute( JRequest::getVar( 'task' ) );
	$controller->redirect();
}else {
	//joomla > 1.5
	jimport('joomla.application.component.controller');
	$controller = JController::getInstance('Vikrentcar');
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
}




?>
