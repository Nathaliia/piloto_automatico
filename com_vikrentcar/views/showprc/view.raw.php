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

class VikrentcarViewShowprc extends JView {

	function display($tpl = null) {

		$pcaropt = JRequest :: getString('caropt', '', 'request');
		$pdays = JRequest :: getString('days', '', 'request');
		$ppickup = JRequest :: getString('pickup', '', 'request');
		$prelease = JRequest :: getString('release', '', 'request');
		$pplace = JRequest :: getString('place', '', 'request');
		$preturnplace = JRequest :: getString('returnplace', '', 'request');

		//Nuevas Variables
		$pattr = JRequest :: getString('pattr', '', 'request');
		$city_ini = JRequest :: getString('city_ini', '', 'request');
		$add_ini = JRequest :: getString('add_ini', '', 'request');

		$encoded = JRequest :: getString('encoded', '', 'request');


		//$add_ini =  JRequest::getVar('add_ini', '', 'default', 'BASE64');
		$city_fin = JRequest :: getString('city_fin', '', 'request');
		$add_fin = JRequest :: getString('add_fin', '', 'request');	
		$format = JRequest :: getString('format', '', 'request');
		$add_marca = JRequest :: getString('add_marca2', '', 'request');	
		$add_model = JRequest :: getString('add_model2', '', 'request');
		$add_vuelo = JRequest :: getString('add_vuelo', '', 'request');	

		if($encoded=='1'){

			$add_ini =base64_decode($add_ini);	
			$add_fin =base64_decode($add_fin);	
			$add_vuelo =base64_decode($add_vuelo);	



		}

		
		

		$numpasangers = JRequest :: getString('numpasangers', '', 'request');	

		$isconvenio=false;
		

		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `units` FROM `#__vikrentcar_cars` WHERE `id`='" . $dbo->getEscaped($pcaropt) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$units = $dbo->loadResult();
		//vikrentcar 1.5
		$checkhourly = false;
		//vikrentcar 1.6
		$checkhourscharges = 0;
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
		$goonunits = true;
		$check = "SELECT `id`,`ritiro`,`consegna` FROM `#__vikrentcar_busy` WHERE `idcar`='" . $dbo->getEscaped($pcaropt) . "';";
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
				if ($bfound >= $units) {
					$goonunits = false;
					break;
				}
			}
		}
		//
		if ($goonunits) {
			
			if (!empty ($pattr)){
				//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` LIKE '%" . $pattr ."%' AND `days`='" . $dbo->getEscaped($pdays) . "' AND `idcar`='" . $dbo->getEscaped($pcaropt) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
				$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` = '" . $pattr ."'  AND `idcar`='" . $dbo->getEscaped($pcaropt) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";

				/*
				switch ($pattr) {

			    case 'Aeropuerto':
			        $q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `idprice`='6'";
			        break;
			   
			    
			  


			 
			    case 'Paquete':
			        $q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `idprice`='5'";
			        break;
			    default:
			    	$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata`='" . $pattr ."' AND `days`='" . $dbo->getEscaped($pdays) . "' AND `idcar`='" . $dbo->getEscaped($pcaropt) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
			    	//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $dbo->getEscaped($pdays) . "' AND `idcar`='" . $dbo->getEscaped($pcaropt) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC;";


				}
				*/

			}else{
				$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $dbo->getEscaped($pdays) . "' AND `idcar`='" . $dbo->getEscaped($pcaropt) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC;";
			}

			
			$dbo->setQuery($q);
			$dbo->Query($q);

			if ($dbo->getNumRows() == 0) {
			
			$oi = "SELECT `idcat` FROM `#__vikrentcar_cars` WHERE `id`='".$pcaropt."'";
			$dbo->setQuery($oi);
			$dbo->Query($oi);
			
				if ($dbo->getNumRows() > 0) {
					$res = $dbo->loadAssocList();
					$cat= substr($res[0]['idcat'], 0,-1);

					if (!empty ($pattr)){

					
							
							//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` LIKE '%" . $pattr ."%' AND `days`='" . $dbo->getEscaped($pdays) . "' AND `idcar`='" . $dbo->getEscaped($cat) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
							$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` = '" . $pattr ."'  AND `idcar`='" . $dbo->getEscaped($cat) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
						
					}else{
							$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $dbo->getEscaped($pdays) . "'AND  `attrdata`='' AND `idcar`='" . $dbo->getEscaped($cat) . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC;";
					}

					//$q = "SELECT * FROM `#__vikrentcar_dispcost_master` WHERE  `days`='" . $dbo->getEscaped($pdays) . "' AND `idcar`='" . $dbo->getEscaped($cat) . "' ORDER BY `#__vikrentcar_dispcost_master`.`cost` ASC, `#__vikrentcar_dispcost_master`.`idcar` ASC;";

					$dbo->setQuery($q);
					$dbo->Query($q);

				}
			}

			
		
            
			if ($dbo->getNumRows() > 0) {


				$tars = $dbo->loadAssocList();



				//vikrentcar 1.5
				
				if($checkhourly) {
					$tars = vikrentcar::applyHourlyPricesCar($tars, $hoursdiff, $pcaropt,false, $pattr);
					
				}
				//
				//vikrentcar 1.6
				if($checkhourscharges > 0 && $aehourschbasp == true) {
					$tars = vikrentcar::applyExtraHoursChargesCar($tars, $pcaropt, $checkhourscharges, $daysdiff);
				}
				//
				
				$q = "SELECT * FROM `#__vikrentcar_cars` WHERE `id`='" . $dbo->getEscaped($pcaropt) . "'" . (!empty ($pplace) ? "  AND `idplace` LIKE '%" . $dbo->getEscaped($pplace) . ";%'" : "") . ";";
				$dbo->setQuery($q);
				$dbo->Query($q);

				if ($dbo->getNumRows() == 1) {
					$car = $dbo->loadAssocList();
					if (intval($car[0]['avail']) == 1) {
						if (vikrentcar :: dayValidTs($pdays, $ppickup, $prelease)) {
							//vikrentcar 1.6
							if($checkhourscharges > 0 && $aehourschbasp == false) {
								$tars = vikrentcar::extraHoursSetPreviousFareCar($tars, $pcaropt, $checkhourscharges, $daysdiff);
								$tars = vikrentcar :: applySeasonsCar($tars, $ppickup, $prelease, $pplace);
								$tars = vikrentcar::applyExtraHoursChargesCar($tars, $pcaropt, $checkhourscharges, $daysdiff, true);
							}else {
								$tars = vikrentcar :: applySeasonsCar($tars, $ppickup, $prelease, $pplace);

							}
							//
							//apply locations fee
							
							if (!empty ($pplace) && !empty ($preturnplace)) {
								$locfee = vikrentcar :: getLocFee($pplace, $preturnplace);
								if ($locfee) {
									$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $pdays) : $locfee['cost'];
									$lfarr = array ();
									foreach ($tars as $kat => $at) {
										$newcost = $at['cost'] + $locfeecost;
										$at['cost'] = $newcost;
										$lfarr[$kat] = $at;
									}
									$tars = $lfarr;
								}
							}
								//recibe cupon
							//$pcouponcode = JRequest :: getString('couponcode', '', 'request');
							$user =& JFactory::getUser();
							$coupons= vikrentcar:: getCodeCouponClient($user->id);
							$ifcouponclient=false;
							if(is_array($coupons)) {
								$ifcouponclient=true;
							}
							

							$pcouponcode= $coupons['code'];
							$pcoupontype =$coupons['type'];
							
							//detecta si es convenio

							//if((preg_match("/CONV-/i", $pcouponcode))) {
							if((preg_match("/3/", $pcoupontype))) {


								

								$isconvenio = true;

								
								


								

							}
							//sino encuentra cupones busca cupones o convenios ingresados por el usuario
							/*if($coupons==""){
								$coupons = 	vikrentcar::getDataClientFromProfile($user->id);
								$pcouponcode= $coupons['convenio'];
							}*/
							//$coupon = "";
							//
							
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
											
											
											JError :: raiseWarning('',$coupon['code'].": ". JText::_('VRCCOUPONINVCAR'));
										}
									}else {
										JError :: raiseWarning('', $coupon['code'].": ". JText::_('VRCCOUPONINVDATES'));
									}
								}else {
									JError :: raiseWarning('', $coupon['code'].": ". JText::_('VRCCOUPONNOTFOUND'));
								}
							}
							//
							
							$this->assignRef('tars', $tars);
							$this->assignRef('car', $car[0]);
							$this->assignRef('pickup', $ppickup);
							$this->assignRef('release', $prelease);
							$this->assignRef('place', $pplace);

							//Nuevas Variables
							$this->assignRef('pattr', $pattr);
							$this->assignRef('city_ini', $city_ini);
							$this->assignRef('add_ini', $add_ini);
							$this->assignRef('city_fin', $city_fin);
							$this->assignRef('add_fin', $add_fin);
							$this->assignRef('add_vuelo', $add_vuelo);
							$this->assignRef('add_marca', $add_marca);
							$this->assignRef('add_model', $add_model);
							$this->assignRef('format', $format);
							$this->assignRef('numpasangers', $numpasangers);
							$this->assignRef('ifcouponclient', $ifcouponclient);
							$this->assignRef('isconvenio', $isconvenio);

							

							
							

							//theme
							$theme = vikrentcar::getTheme();
							if($theme != 'default') {
								$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'showprc';
								if(is_dir($thdir)) {
									$this->_setPath('template', $thdir.DS);
								}
							}
							//
							parent :: display($tpl);
						}else {
							showSelect(JText :: _('VRERRCALCTAR'));
						}
					}else {
						showSelect(JText :: _('VRCARNOTAV'));
					}
				}else {
					showSelect(JText :: _('VRCARNOTFND'));
				}
			}else {
				showSelect(JText :: _('VRNOTARFNDSELO'));


			}
		}else {
			showSelect(JText :: _('VRCARNOTRIT') . " " . date($df . ' H:i', $ppickup) . " " . JText :: _('VRCARNOTCONSTO') . " " . date($df . ' H:i', $prelease));
		}
	}
}
?>