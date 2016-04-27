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

jimport('joomla.application.component.view');

class VikrentcarViewCardetails extends JView {
	function display($tpl = null) {
		$pcarid = JRequest :: getString('carid', '', 'request');
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_cars` WHERE `id`='".$dbo->getEscaped($pcarid)."' AND `avail`='1';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() == 1) {
			$car=$dbo->loadAssocList();
			$q="SELECT `id`,`cost` FROM `#__vikrentcar_dispcost` WHERE `idcar`='".$dbo->getEscaped($car[0]['id'])."' AND `days`='1' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC LIMIT 1;";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if($dbo->getNumRows() == 1) {
				$tar=$dbo->loadAssocList();
				$car[0]['cost']=$tar[0]['cost'];
			}else {
				$q="SELECT `id`,`days`,`cost` FROM `#__vikrentcar_dispcost` WHERE `idcar`='".$dbo->getEscaped($car[0]['id'])."' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC LIMIT 1;";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if($dbo->getNumRows() == 1) {
					$tar=$dbo->loadAssocList();
					$car[0]['cost']=($tar[0]['cost'] / $tar[0]['days']);
				}else {
					$car[0]['cost']=0;
				}
			}
			$actnow=time();
			$q="SELECT * FROM `#__vikrentcar_busy` WHERE `idcar`='".$car[0]['id']."' AND (`ritiro`>=".$actnow." OR `consegna`>=".$actnow.");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$busy = $dbo->loadAssocList();
			}else {
				$busy="";
			}
			$this->assignRef('car', $car[0]);
			$this->assignRef('busy', $busy);
			//theme
			$theme = vikrentcar::getTheme();
			if($theme != 'default') {
				$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'cardetails';
				if(is_dir($thdir)) {
					$this->_setPath('template', $thdir.DS);
				}
			}
			//
			parent :: display($tpl);
		}else {
			$mainframe = & JFactory :: getApplication();
			$mainframe->redirect("index.php?option=com_vikrentcar&view=carslist");
		}
	}
}
?>