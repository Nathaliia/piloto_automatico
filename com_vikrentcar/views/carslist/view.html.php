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

class VikrentcarViewCarslist extends JView {
	function display($tpl = null) {
		$dbo = & JFactory :: getDBO();
		$pcategory_id = JRequest :: getInt('category_id', '', 'request');
		$category = "";
		if($pcategory_id > 0) {
			$q="SELECT * FROM `#__vikrentcar_categories` WHERE `id`='".$pcategory_id."';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if($dbo->getNumRows() == 1) {
				$category = $dbo->loadAssocList();
				$category = $category[0];
			}
		}
		if(is_array($category)) {
			$q = "SELECT `id`,`name`,`img`,`idcat`,`idcarat`,`info` FROM `#__vikrentcar_cars` WHERE `avail`='1' AND `idcat` LIKE '%".$category['id'].";%' AND `idcat` NOT LIKE '%".$category['id'].$category['id'].";%';";
		}else {
			$q = "SELECT `id`,`name`,`img`,`idcat`,`idcarat`,`info` FROM `#__vikrentcar_cars` WHERE `avail`='1';";
		}
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() > 0) {
			$cars=$dbo->loadAssocList();
			foreach($cars as $k=>$c) {
				$q="SELECT `id`,`cost` FROM `#__vikrentcar_dispcost` WHERE `idcar`='".$dbo->getEscaped($c['id'])."' AND `days`='1' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC LIMIT 1;";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if($dbo->getNumRows() == 1) {
					$tar=$dbo->loadAssocList();
					$cars[$k]['cost']=$tar[0]['cost'];
				}else {
					$q="SELECT `id`,`days`,`cost` FROM `#__vikrentcar_dispcost` WHERE `idcar`='".$dbo->getEscaped($c['id'])."' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC LIMIT 1;";
					$dbo->setQuery($q);
					$dbo->Query($q);
					if($dbo->getNumRows() == 1) {
						$tar=$dbo->loadAssocList();
						$cars[$k]['cost']=($tar[0]['cost'] / $tar[0]['days']);
					}else {
						$cars[$k]['cost']=0;
					}
				}
			}
			$cars = vikrentcar :: sortCarPrices($cars);
			//pagination
			$lim=20; //results limit
			$lim0 = JRequest::getVar('limitstart', 0, '', 'int');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(count($cars), $lim0, $lim);
			$navig = $pageNav->getPagesLinks();
			$this->assignRef('navig', $navig);
			$cars = array_slice($cars, $lim0, $lim, true);
			//
			
			$this->assignRef('cars', $cars);
			$this->assignRef('category', $category);
			//theme
			$theme = vikrentcar::getTheme();
			if($theme != 'default') {
				$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'carslist';
				if(is_dir($thdir)) {
					$this->_setPath('template', $thdir.DS);
				}
			}
			//
			parent :: display($tpl);
		}
	}
}
?>