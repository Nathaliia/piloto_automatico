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

class VikrentcarViewConfirmedorder extends JView {
	function display($tpl = null) {
		$sid = JRequest :: getString('sid', '', 'request');
		$ts = JRequest :: getString('ts', '', 'request');
        $format = JRequest :: getString('format', '', 'request');
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_orders` WHERE `sid`='" . $dbo->getEscaped($sid) . "' AND `ts`='" . $dbo->getEscaped($ts) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$order = $dbo->loadAssocList();
		//vikrentcar 1.5
		if($order[0]['hourly'] == 1) {
			$q = "SELECT * FROM `#__vikrentcar_dispcosthours_master` WHERE `id`='" . $order[0]['idtar'] . "';";
		}else {
			$q = "SELECT * FROM `#__vikrentcar_dispcost_master` WHERE `id`='" . $order[0]['idtar'] . "';";
		}
		//
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$tar = $dbo->loadAssocList();
			//vikrentcar 1.5
			if($order[0]['hourly'] == 1) {
				foreach($tar as $kt => $vt) {
					$tar[$kt]['days'] = 1;
				}
			}
			//
			//vikrentcar 1.6
			$checkhourscharges = 0;
			$hoursdiff = 0;
			$ppickup = $order[0]['ritiro'];
			$prelease = $order[0]['consegna'];
			$secdiff = $prelease - $ppickup;
			$daysdiff = $secdiff / 86400;
			if (is_int($daysdiff)) {
				if ($daysdiff < 1) {
					$daysdiff = 1;
				}
			}else {
				if ($daysdiff < 1) {
					$daysdiff = 1;
					$checkhourly = true;
					$ophours = $secdiff / 3600;
					$hoursdiff = intval(round($ophours));
					if($hoursdiff < 1) {
						$hoursdiff = 1;
					}
				}else {
					$sum = floor($daysdiff) * 86400;
					$newdiff = $secdiff - $sum;
					$maxhmore = vikrentcar :: getHoursMoreRb() * 3600;
					if ($maxhmore >= $newdiff) {
						$daysdiff = floor($daysdiff);
					}else {
						$daysdiff = ceil($daysdiff);
						//vikrentcar 1.6
						$ehours = intval(round(($newdiff - $maxhmore) / 3600));
						$checkhourscharges = $ehours;
						if($checkhourscharges > 0) {
							$aehourschbasp = vikrentcar::applyExtraHoursChargesBasp();
						}
						//
					}
				}
			}
			if($checkhourscharges > 0 && $aehourschbasp == true) {
				$ret = vikrentcar::applyExtraHoursChargesCar($tar, $order[0]['idcar'], $checkhourscharges, $daysdiff, false, true, true);
				$tar = $ret['return'];
				$calcdays = $ret['days'];
			}
			if($checkhourscharges > 0 && $aehourschbasp == false) {
				$tar = vikrentcar::extraHoursSetPreviousFareCar($tar, $order[0]['idcar'], $checkhourscharges, $daysdiff, true);
				$tar = vikrentcar :: applySeasonsCar($tar, $order[0]['ritiro'], $order[0]['consegna'], $order[0]['idplace']);
				$ret = vikrentcar::applyExtraHoursChargesCar($tar, $order[0]['idcar'], $checkhourscharges, $daysdiff, true, true, true);
				$tar = $ret['return'];
				$calcdays = $ret['days'];
			}else {
				$tar = vikrentcar :: applySeasonsCar($tar, $order[0]['ritiro'], $order[0]['consegna'], $order[0]['idplace']);
			}
			//
		} else {
			$tar[0] = "";
		}
		$payment = "";
		if (!empty ($order[0]['idpayment'])) {
			$exppay = explode('=', $order[0]['idpayment']);
			$payment = vikrentcar :: getPayment($exppay[0]);
		}
		//vikrentcar 1.6
		if($checkhourscharges > 0) {
			$this->assignRef('calcdays', $calcdays);
		}
		//
		
		
		$this->assignRef('ord', $order[0]);
		$this->assignRef('tar', $tar[0]);
		$this->assignRef('payment', $payment);
		$this->assignRef('format', $format);
		//theme
		$theme = vikrentcar::getTheme();
		if($theme != 'default') {
			$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'confirmedorder';
			if(is_dir($thdir)) {
				$this->_setPath('template', $thdir.DS);
			}
		}
		//
		parent :: display($tpl);
	}
}
?>