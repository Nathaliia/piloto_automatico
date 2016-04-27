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

class VikrentcarViewOconfirm extends JView {
	function display($tpl = null) {
		
				
		$pcarid = JRequest :: getString('carid', '', 'request');
		$pdays = JRequest :: getString('days', '', 'request');
		$ppickup = JRequest :: getString('pickup', '', 'request');
		$prelease = JRequest :: getString('release', '', 'request');
		$ppriceid = JRequest :: getString('priceid', '', 'request');
		$pplace = JRequest :: getString('place', '', 'request');
		$preturnplace = JRequest :: getString('returnplace', '', 'request');
		



		//Nuevas Variables
		$pattr = JRequest :: getString('pattr', '', 'request');
		$city_ini = JRequest :: getString('city_ini', '', 'request');
		$add_ini = JRequest :: getString('add_ini', '', 'request');
		$city_fin = JRequest :: getString('city_fin', '', 'request');
		$add_fin = JRequest :: getString('add_fin', '', 'request');	
		$add_vuelo = JRequest :: getString('add_vuelo', '', 'request');	

		$numpasangers = JRequest :: getString('numpasangers', '', 'request');	
		$convenio = JRequest :: getString('convenio', '', 'request');	

		
		
		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_cars` WHERE `id`='" . $dbo->getEscaped($pcarid) . "'" . (!empty ($pplace) ? " AND `idplace` LIKE '%" . $dbo->getEscaped($pplace) . ";%'" : "") . ";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		
		if ($dbo->getNumRows() == 1) {
			$car = $dbo->loadAssocList();
			//vikrentcar 1.5
			$checkhourly = false;
			//vikrentcar 1.6
			$checkhourscharges = 0;
			$calcdays = $pdays;
			//
			$hoursdiff = 0;
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
			$groupdays = vikrentcar::getGroupDays($ppickup, $prelease, $daysdiff);
			$morehst = vikrentcar :: getHoursCarAvail() * 3600;
			$validtime = true;
			$check = "SELECT `id`,`ritiro`,`consegna` FROM `#__vikrentcar_busy` WHERE `idcar`='" . $dbo->getEscaped($car[0]['id']) . "';";
			$dbo->setQuery($check);
			$dbo->Query($check);
			if ($dbo->getNumRows() > 0) {
				$busy = $dbo->loadAssocList();
				foreach ($groupdays as $gday) {
					$bfound = 0;
					foreach ($busy as $bu) {
						if ($gday >= $bu['ritiro'] && $gday <= ($morehst + $bu['consegna'])) {
							$bfound++;
						}
					}
					if ($bfound >= $car[0]['units']) {
						$validtime = false;
						break;
					}
				}
			}
			//
			if ($validtime == true) {
				if (strlen($ppriceid)) {

					if (!empty ($pattr)){

						$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `idcar`='" . $dbo->getEscaped($car[0]['id']) . "' AND `attrdata`='" . $pattr . "' AND `idprice`='" . $dbo->getEscaped($ppriceid) . "';";
					}else{
						$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `idcar`='" . $dbo->getEscaped($car[0]['id']) . "' AND `days`='" . $dbo->getEscaped($pdays) . "' AND `idprice`='" . $dbo->getEscaped($ppriceid) . "';";
					}


					$dbo->setQuery($q);
					$dbo->Query($q);

					if ($dbo->getNumRows() > 0) {
						$price = $dbo->loadAssocList();
						//vikrentcar 1.5
						if($checkhourly) {
							$price = vikrentcar::applyHourlyPricesCar($price, $hoursdiff, $car[0]['id'], true,$pattr);
						}
						//
						//vikrentcar 1.6
						if($checkhourscharges > 0 && $aehourschbasp == true) {
							$ret = vikrentcar::applyExtraHoursChargesCar($price, $car[0]['id'], $checkhourscharges, $daysdiff, false, true, true);
							$price = $ret['return'];
							$calcdays = $ret['days'];
						}
						if($checkhourscharges > 0 && $aehourschbasp == false) {
							$price = vikrentcar::extraHoursSetPreviousFareCar($price, $car[0]['id'], $checkhourscharges, $daysdiff, true);
							$price = vikrentcar :: applySeasonsCar($price, $ppickup, $prelease, $pplace);
							$ret = vikrentcar::applyExtraHoursChargesCar($price, $car[0]['id'], $checkhourscharges, $daysdiff, true, true, true);
							$price = $ret['return'];
							$calcdays = $ret['days'];
						}else {
							$price = vikrentcar :: applySeasonsCar($price, $ppickup, $prelease, $pplace);
						}
						//set $pdays as the regular calculation for dayValidTs()
						if($checkhourscharges > 0) {
							$pdays = $daysdiff;
						}
						//
						$q = "SELECT * FROM `#__vikrentcar_optionals`;";
						$dbo->setQuery($q);
						$dbo->Query($q);
						if ($dbo->getNumRows() > 0) {
							$optionals = $dbo->loadAssocList();
							foreach ($optionals as $opt) {
								$tmpvar = JRequest :: getString('optid' . $opt['id'], '', 'request');
								if (!empty ($tmpvar)) {
									$opt['quan'] = $tmpvar;
									$selopt[] = $opt;
								}
							}
						}else {
							$selopt = "";
						}

						/*if((int)$pattr>=1  && (int)$pattr<=144 && $ppriceid==3 ){
							
							$pdays=1;
							$ppickup=$ppickup;
							//se establece a 3 horas traslado urbano
							$prelease= $ppickup+3600*3;

						}*/

						if($pattr=='Credito' && $ppriceid==5 ){
							$ppickup=$ppickup;
							
							if($pdays==2){
								$prelease= $ppickup+24*3600*2;

							}
							if($pdays==3){

								$prelease= $ppickup+24*3600*3;
							}
							
							//se establece a 3 horas traslado urbano
							

						}


						if (vikrentcar :: dayValidTs($pdays, $ppickup, $prelease)) {
							$ftitle = vikrentcar :: getFullFrontTitle();
							$q = "SELECT * FROM `#__vikrentcar_gpayments` WHERE `published`='1' ORDER BY `#__vikrentcar_gpayments`.`name` ASC;";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$payments = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";
							$q = "SELECT * FROM `#__vikrentcar_custfields` ORDER BY `#__vikrentcar_custfields`.`ordering` ASC;";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$cfields = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";
							//vikrentcar 1.6
							$pcouponcode = JRequest :: getString('couponcode', '', 'request');

							
							$coupon = "";
							if(strlen($pcouponcode) > 0) {

								$coupon = vikrentcar::getCouponInfo($pcouponcode);

								
								if(is_array($coupon)) {
									$coupondateok = true;
									if(strlen($coupon['datevalid']) > 0) {
										$dateparts = explode("-", $coupon['datevalid']);
										$pickinfo = getdate($ppickup);
										$dropinfo = getdate($prelease);
										$checkpick = mktime(0, 0, 0, $pickinfo['mon'], $pickinfo['mday'], $pickinfo['year']);
										$checkdrop = mktime(0, 0, 0, $dropinfo['mon'], $dropinfo['mday'], $dropinfo['year']);
										if(!($checkpick >= $dateparts[0] && $checkpick <= $dateparts[1] && $checkdrop >= $dateparts[0] && $checkdrop <= $dateparts[1])) {
											$coupondateok = false;
										}
									}
									if($coupondateok == true) {
										$couponcarok = true;
										if($coupon['allvehicles'] == 0) {
											if(!(preg_match("/;".$car[0]['id'].";/i", $coupon['idcars']))) {
												$couponcarok = false;
											}
										}
										if($couponcarok == true) {
											$this->assignRef('coupon', $coupon);
										}else {
											JError :: raiseWarning('', JText::_('VRCCOUPONINVCAR'));
										}
									}else {
										JError :: raiseWarning('', JText::_('VRCCOUPONINVDATES'));
									}
								}else {
									JError :: raiseWarning('', JText::_('VRCCOUPONNOTFOUND'));
								}
							}
							//
							$this->assignRef('car', $car[0]);
							$this->assignRef('price', $price[0]);
							$this->assignRef('selopt', $selopt);
							$this->assignRef('days', $pdays);
							//vikrentcar 1.6
							$this->assignRef('calcdays', $calcdays);
							//
							$this->assignRef('first', $ppickup);
							$this->assignRef('second', $prelease);
							$this->assignRef('ftitle', $ftitle);
							$this->assignRef('place', $pplace);
							$this->assignRef('returnplace', $preturnplace);
							$this->assignRef('payments', $payments);
							$this->assignRef('cfields', $cfields);

							//Nuevas Variables
							$this->assignRef('pattr', $pattr);
							$this->assignRef('city_ini', $city_ini);
							$this->assignRef('add_ini', $add_ini);
							$this->assignRef('city_fin', $city_fin);
							$this->assignRef('add_fin', $add_fin);
							$this->assignRef('add_vuelo', $add_vuelo);
							$this->assignRef('numpasangers', $numpasangers);
							$this->assignRef('convenio', $convenio);


							//theme
							$theme = vikrentcar::getTheme();
							if($theme != 'default') {
								$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'oconfirm';
								if(is_dir($thdir)) {
									$this->_setPath('template', $thdir.DS);
								}
							}
							//
							parent :: display($tpl);
						}else {
							showSelect (JText :: _('VRERRCALCTAR'));
						}
					}else {
						showSelect (JText :: _('VRTARNOTFOUND'));
					}
				}else {
					showSelect (JText :: _('VRNOTARSELECTED'));
				}
			}else {
				showSelect(JText :: _('VRCARNOTCONS') . " " . date($df . ' H:i', $ppickup) . " " . JText :: _('VRCARNOTCONSTO') . " " . date($df . ' H:i', $prelease));
			}
		}else {
			showSelect (JText :: _('VRCARNOTFND'));
		}
	}
}
?>