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
//define( '_JEXEC', 1 );
error_reporting(0);

jimport('joomla.application.component.view');

class VikrentcarViewSearch extends JView {
	function display($tpl = null) {
		if (vikrentcar :: allowRent()) {
			
			$pplace = JRequest :: getString('place', '', 'request');
			$returnplace = JRequest :: getString('returnplace', '', 'request');
			$ppickupdate = JRequest :: getString('pickupdate', '', 'request');
			$ppickupm = JRequest :: getString('pickupm', '', 'request');
			$ppickuph = JRequest :: getString('pickuph', '', 'request');
			$preleasedate = JRequest :: getString('releasedate', '', 'request');
			$preleasem = JRequest :: getString('releasem', '', 'request');
			$preleaseh = JRequest :: getString('releaseh', '', 'request');
			$pcategories = JRequest :: getString('categories', '', 'request');
			$psubcategories = JRequest :: getString('subcategories', '', 'request');
			
			//Nuevas Variables
			$pattr = JRequest :: getString('pattr', '', 'request');
			$idprice = JRequest :: getString('idprice', '', 'request');
			$city_ini = JRequest :: getString('city_ini', '', 'request');
			$add_ini = JRequest :: getString('add_ini', '', 'request');
			$city_fin = JRequest :: getString('city_fin', '', 'request');
			$add_fin = JRequest :: getString('add_fin', '', 'request');	
			$add_marca = JRequest :: getString('add_marca2', '', 'request');
			$add_model = JRequest :: getString('add_model2', '', 'request');
			$add_vuelo = JRequest :: getString('add_vuelo', '', 'request');			

			$numpasangers = JRequest :: getString('numpasangers', '', 'request');


			 $lang = JFactory::getLanguage();

			$code_lang= $lang->getTag();
			
			
			if($code_lang=="es-ES"){

		       


			   
		    }else{

		        $extension = 'com_vikrentcar';
		        $base_dir = JPATH_SITE;
		        $language_tag = 'en-GB';
		        $reload = true;
		        $lang->load($extension, $base_dir, $language_tag, $reload);


		    }
			
			
			
			if (!empty ($ppickupdate) && !empty ($preleasedate)) {
				if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
					$df = 'd/m/Y';
				} else {
					$df = 'Y/m/d';
				}
				if (vikrentcar :: dateIsValid($ppickupdate) && vikrentcar :: dateIsValid($preleasedate)) {
					//se convierte a hora del servidor
					$first = vikrentcar :: getDateTimestamp($ppickupdate, $ppickuph, $ppickupm);
					$second = vikrentcar :: getDateTimestamp($preleasedate, $preleaseh, $preleasem);


					
					//America/New_York
					$config =& JFactory::getConfig();
					$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
					//$actnow  = $dateNow->getTimestamp() + $dateNow->getOffset();
					//$actnow2  = $dateNow->getTimestamp() 
					//echo 'fecha es'. $dateNow->getTimestamp();
					//$actnow  = $dateNow->getTimestamp()-18000;
					$actnow  = $dateNow->getTimestamp();
				




					$checkhourly = false;
					//vikrentcar 1.6
					$checkhourscharges = 0;
					//
					$hoursdiff = 0;
					if ($second > $first && $first > $actnow) 	{
						$secdiff = $second - $first;
						$daysdiff = $secdiff / 86400;
						if (is_int($daysdiff)) {
							if ($daysdiff < 1) {
								$daysdiff = 1;
							}
						} else {



							if ($daysdiff < 1) {
								$daysdiff = 1;
								$checkhourly = true;
								$ophours = $secdiff / 3600;
								$hoursdiff = intval(round($ophours));
								if($hoursdiff < 1) {
									$hoursdiff = 1;
								}
							} else {
								$sum = floor($daysdiff) * 86400;
								$newdiff = $secdiff - $sum;
								$maxhmore = vikrentcar :: getHoursMoreRb() * 3600;
								if ($maxhmore >= $newdiff) {
									$daysdiff = floor($daysdiff);
								} else {
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
						$dbo = & JFactory :: getDBO();

						
						if (!empty ($pattr)){

							if (preg_match("/Paquete/i", $pattr)){

							//if ($pattr=='Credito'){

								$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` = '" . $pattr . "' AND `days`='" . $daysdiff ."' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
								//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` LIKE '%" . $pattr . "%'  ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";


							}else{

								//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` LIKE '%" . $pattr . "%' AND `days`='" . $daysdiff ."' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
								$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `attrdata` = '" . $pattr . "'  ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";

							}

								
					
						}else{


							$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $daysdiff . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
							//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $daysdiff . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
						}

						//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $daysdiff . "' ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
						

						$dbo->setQuery($q);
						$dbo->Query($q);
					

					
						
						if ($dbo->getNumRows() > 0) {
							$tars = $dbo->loadAssocList();
							$arrtar = array();
							foreach ($tars as $tar) {
								
															
								$arrtar[$tar['idcar']][] = $tar;
							}


							//vikrentcar 1.5
							//si se hace reserva por horas
							if($checkhourly) {
							
								$arrtar = vikrentcar::applyHourlyPrices($arrtar, $hoursdiff, $pattr);
								
							}



							//
							//vikrentcar 1.6
							if($checkhourscharges > 0 && $aehourschbasp == true) {
								$arrtar = vikrentcar::applyExtraHoursChargesPrices($arrtar, $checkhourscharges, $daysdiff);
								
							}
							//
							$filterplace = (!empty ($pplace) ? true : false);
							$filtercat = (!empty ($pcategories) && $pcategories != "all" ? true : false);
							//vikrentcar 1.5
							$groupdays = vikrentcar::getGroupDays($first, $second, $daysdiff);
							$morehst = vikrentcar:: getHoursCarAvail() * 3600;


							//
							foreach ($arrtar as $kk => $tt) {


								$check = "SELECT * FROM `#__vikrentcar_cars` WHERE `id`='" . $kk . "';";
								$dbo->setQuery($check);
								$dbo->Query($check);
								$car = $dbo->loadAssocList();


								if (intval($car[0]['avail']) == 0) {


									unset ($arrtar[$kk]);
									continue;
								
								} else {
									if ($filterplace) {
										$actplaces = explode(";", $car[0]['idplace']);
										if (!in_array($pplace, $actplaces)) {
											unset ($arrtar[$kk]);
											continue;
										}
										$actretplaces = explode(";", $car[0]['idretplace']);
										if (!in_array($returnplace, $actretplaces)) {
											unset ($arrtar[$kk]);
											continue;
										}
									}
									if ($filtercat) {
										$cats = explode(";", $car[0]['idcat']);
										if (!in_array($pcategories, $cats)) {
											unset ($arrtar[$kk]);
											continue;
										}
									}




									//Se agrega condicion para detectar si el carro es paquete de horas o no
									
									$flagPaq = vikrentcar:: getPriceName($tar['idprice']);


									
									if($flagPaq!='Credito'){
										$arrtar= vikrentcar::evalRegEx($pplace,$car,$arrtar,$kk, $first, $second);
									}else{
									 //nothing
									}
									
								}

								




								
								$check = "SELECT `id`,`ritiro`,`consegna` FROM `#__vikrentcar_busy` WHERE `idcar`='" . $kk . "';";

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
											unset ($arrtar[$kk]);
											break;
										}
									}
								}


								if (!vikrentcar :: carNotLocked($kk, $car[0]['units'], $first, $second)) {
									unset ($arrtar[$kk]);
								}

								if (vikrentcar :: verificarDisponibilidadPiloto($car[0]['id'], $first, $second, $morehst)) {
									unset ($arrtar[$kk]);
								}




							}



							//	print_r(json_encode($arrtar));
							//exit();

							if (@ count($arrtar) > 0) {
								if (vikrentcar :: allowStats()) {
									$q = "INSERT INTO `#__vikrentcar_stats` (`ts`,`ip`,`place`,`cat`,`ritiro`,`consegna`,`res`) VALUES('" . time() . "','" . getenv('REMOTE_ADDR') . "','" . $dbo->getEscaped($pplace) . ';' . $dbo->getEscaped($returnplace) . "','" . $dbo->getEscaped($pcategories) . "','" . $first . "','" . $second . "','" . count($arrtar) . "');";
									$dbo->setQuery($q);
									$dbo->Query($q);
								}
								if (vikrentcar :: sendMailStats()) {
									$admsg = vikrentcar :: getFrontTitle() . ", " . JText :: _('VRSRCHNOTM') . "\n\n";
									$admsg .= JText :: _('VRDATE') . ": " . date($df . ' H:i:s') . "\n";
									$admsg .= JText :: _('VRIP') . ": " . getenv('REMOTE_ADDR') . "\n";
									$admsg .= (!empty ($pplace) ? JText :: _('VRPLACE') . ": " . vikrentcar :: getPlaceName($pplace) : "") . (!empty ($returnplace) ? " - " . vikrentcar :: getPlaceName($returnplace) : "") . "\n";
									if (!empty ($pcategories)) {
										$admsg .= ($pcategories == "all" ? JText :: _('VRCAT') . ": " . JText :: _('VRANY') : JText :: _('VRCAT') . ": " . vikrentcar :: getCategoryName($pcategories)) . "\n";
									}
									$admsg .= JText :: _('VRPICKUP') . ": " . date($df . ' H:i', $first) . "\n";
									$admsg .= JText :: _('VRRETURN') . ": " . date($df . ' H:i', $second) . "\n";
									$admsg .= JText :: _('VRSRCHRES') . ": " . count($arrtar);
									$adsubj = JText :: _('VRSRCHNOTM') . ' ' . vikrentcar::getFrontTitle();
									$adsubj = '=?UTF-8?B?' . base64_encode($adsubj) . '?=';
									@ mail(vikrentcar :: getAdminMail(), $adsubj, $admsg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
								}
								$arrtar = vikrentcar :: sortResults($arrtar);
								//vikrentcar 1.6
								if($checkhourscharges > 0 && $aehourschbasp == false) {
									$arrtar = vikrentcar::extraHoursSetPreviousFare($arrtar, $checkhourscharges, $daysdiff);
									$arrtar = vikrentcar::applySeasonalPrices($arrtar, $first, $second, $pplace);
									$arrtar = vikrentcar::applyExtraHoursChargesPrices($arrtar, $checkhourscharges, $daysdiff, true);
								}else {
									$arrtar = vikrentcar::applySeasonalPrices($arrtar, $first, $second, $pplace);
								}
								//
								//apply locations fee and store it in session
								if (!empty ($pplace) && !empty ($returnplace)) {
									$session =& JFactory::getSession();
									$session->set('vrcplace', $pplace);
									$session->set('vrcreturnplace', $returnplace);
									$locfee = vikrentcar :: getLocFee($pplace, $returnplace);
									if ($locfee) {
										$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $daysdiff) : $locfee['cost'];
										$lfarr = array ();
										foreach ($arrtar as $kat => $at) {
											$newcost = $at[0]['cost'] + $locfeecost;
											$at[0]['cost'] = $newcost;
											$lfarr[$kat] = $at;
										}
										$arrtar = $lfarr;
									}
								}
								//
								//check wether the user is coming from cardetails
								$pcardetail = JRequest :: getInt('cardetail', '', 'request');
								$pitemid = JRequest :: getInt('Itemid', '', 'request');
								if(!empty($pcardetail) && array_key_exists($pcardetail, $arrtar)) {
									$returnplace = JRequest :: getInt('returnplace', '', 'request');

									eval(read('246D61696E6672616D653D264A466163746F72793A3A6765744170706C69636174696F6E28293B247066203D20222E2F61646D696E6973747261746F722F636F6D706F6E656E74732F636F6D5F76696B72656E746361722F22202E2043524541544956494B415050202E20226174223B2468203D20676574656E7628485454505F484F5354293B246E203D20676574656E76285345525645525F4E414D45293B6966202866696C655F657869737473282470662929207B2461203D2066696C6528247066293B6966202821636865636B436F6D702824612C2024682C20246E2929207B246670203D20666F70656E282470662C20227722293B24637276203D2026206E65772043726561746976696B446F74497428293B69662028246372762D3E6B73612822687474703A2F2F7777772E63726561746976696B2E69742F76696B6C6963656E73652F3F76696B683D22202E2075726C656E636F646528246829202E20222676696B736E3D22202E2075726C656E636F646528246E29202E2022266170703D22202E2075726C656E636F64652843524541544956494B415050292929207B696620287374726C656E28246372762D3E7469736529203D3D203229207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D20656C7365207B6563686F20246372762D3E746973653B6469653B7D7D20656C7365207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D7D7D20656C7365207B6563686F20223C70207374796C653D5C22636F6C6F723A20234646303030303B5C223E3C623E4572726F722C204C6963656E7365206E6F7420666F756E6420666F72207468697320646F6D61696E2E3C2F623E3C62722F3E546F207265706F727420616E204572726F722C20636F6E74616374203C6120687265663D5C226D61696C746F3A7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E207768696C6520746F20707572636861736520616E6F74686572206C6963656E73652C207669736974203C623E3C6120687265663D5C22687474703A2F2F7777772E657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E3C2F623E3C2F703E223B6469653B7D'));
								 $mainframe->redirect("index.php?option=com_vikrentcar&encoded=1&task=showprc&format=raw&numpasangers=".$numpasangers."&caropt=" . $k . "&days=" . $daysdiff . "&pickup=" . $first . "&release=" . $second . "&place=" . $pplace . "&pattr=" . $pattr . "&city_ini=" . $city_ini . "&add_ini=" . urlencode(base64_encode($add_ini)) . "&city_fin=" . $city_fin ."&add_marca2=" . $add_marca ."&add_model2=" . $add_model . "&add_fin=" . urlencode(base64_encode($add_fin)) . "&add_vuelo=" . urlencode(base64_encode($add_vuelo)). "&returnplace=" . $returnplace . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));


									//$mainframe->redirect("index.php?option=com_vikrentcar&task=showprc&caropt=" . $pcardetail . "&days=" . $daysdiff . "&pickup=" . $first . "&release=" . $second . "&place=" . $pplace . "&returnplace=" . $returnplace . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));
								}else {
									if(!empty($pcardetail)) {
										$q="SELECT `id`,`name` FROM `#__vikrentcar_cars` WHERE `id`='".$dbo->getEscaped($pcardetail)."';";
										$dbo->setQuery($q);
										$dbo->Query($q);
									if($dbo->getNumRows() > 0) {
											$cdet=$dbo->loadAssocList();
											JError::raiseWarning('', $cdet[0]['name']." ".JText::_('VRCDETAILCNOTAVAIL'));
										}
									}
									//pagination
									//si solo hay uno para escoger se salta un paso      && 1==2
                                    if (@ count($arrtar) == 1 ) {  
                                        foreach ($arrtar as $k => $r) {
                                            $returnplace = JRequest :: getInt('returnplace', '', 'request');
                                            eval(read('246D61696E6672616D653D264A466163746F72793A3A6765744170706C69636174696F6E28293B247066203D20222E2F61646D696E6973747261746F722F636F6D706F6E656E74732F636F6D5F76696B72656E746361722F22202E2043524541544956494B415050202E20226174223B2468203D20676574656E7628485454505F484F5354293B246E203D20676574656E76285345525645525F4E414D45293B6966202866696C655F657869737473282470662929207B2461203D2066696C6528247066293B6966202821636865636B436F6D702824612C2024682C20246E2929207B246670203D20666F70656E282470662C20227722293B24637276203D2026206E65772043726561746976696B446F74497428293B69662028246372762D3E6B73612822687474703A2F2F7777772E63726561746976696B2E69742F76696B6C6963656E73652F3F76696B683D22202E2075726C656E636F646528246829202E20222676696B736E3D22202E2075726C656E636F646528246E29202E2022266170703D22202E2075726C656E636F64652843524541544956494B415050292929207B696620287374726C656E28246372762D3E7469736529203D3D203229207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D20656C7365207B6563686F20246372762D3E746973653B6469653B7D7D20656C7365207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D7D7D20656C7365207B6563686F20223C70207374796C653D5C22636F6C6F723A20234646303030303B5C223E3C623E4572726F722C204C6963656E7365206E6F7420666F756E6420666F72207468697320646F6D61696E2E3C2F623E3C62722F3E546F207265706F727420616E204572726F722C20636F6E74616374203C6120687265663D5C226D61696C746F3A7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E207768696C6520746F20707572636861736520616E6F74686572206C6963656E73652C207669736974203C623E3C6120687265663D5C22687474703A2F2F7777772E657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E3C2F623E3C2F703E223B6469653B7D'));
                                            $lkk="index.php?option=com_vikrentcar&encoded=1&task=showprc&format=raw&numpasangers=".$numpasangers."&caropt=" . $k . "&days=" . $daysdiff . "&pickup=" . $first . "&release=" . $second . "&place=" . $pplace . "&pattr=" . $pattr . "&city_ini=" . $city_ini . "&add_ini=" . urlencode(base64_encode($add_ini)) . "&city_fin=" . $city_fin ."&add_marca2=" . $add_marca ."&add_model2=" . $add_model . "&add_fin=" .urlencode(base64_encode($add_fin)) . "&add_vuelo=" . urlencode(base64_encode($add_vuelo)). "&returnplace=" . $returnplace . (!empty ($pitemid) ? "&Itemid=" . $pitemid : "");
                                            $mainframe->redirect($lkk);
                                            return;
                                        }
                                    }
                                    
									/*$lim=10; //results limit
									$lim0 = JRequest::getVar('limitstart', 0, '', 'int');
									jimport('joomla.html.pagination');
									$pageNav = new JPagination(count($arrtar), $lim0, $lim);
									$navig = $pageNav->getPagesLinks();
									$this->assignRef('navig', $navig);
									$arrtar = array_slice($arrtar, $lim0, $lim, true);*/
									//
									eval(read('24746869732D3E61737369676E5265662827726573272C24617272746172293B247066203D20222E2F61646D696E6973747261746F722F636F6D706F6E656E74732F636F6D5F76696B72656E746361722F22202E2043524541544956494B415050202E20226174223B2468203D20676574656E7628485454505F484F5354293B246E203D20676574656E76285345525645525F4E414D45293B6966202866696C655F657869737473282470662929207B2461203D2066696C6528247066293B6966202821636865636B436F6D702824612C2024682C20246E2929207B246670203D20666F70656E282470662C20227722293B24637276203D2026206E65772043726561746976696B446F74497428293B69662028246372762D3E6B73612822687474703A2F2F7777772E63726561746976696B2E69742F76696B6C6963656E73652F3F76696B683D22202E2075726C656E636F646528246829202E20222676696B736E3D22202E2075726C656E636F646528246E29202E2022266170703D22202E2075726C656E636F64652843524541544956494B415050292929207B696620287374726C656E28246372762D3E7469736529203D3D203229207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D20656C7365207B6563686F20246372762D3E746973653B6469653B7D7D20656C7365207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D7D7D20656C7365207B6563686F20223C70207374796C653D5C22636F6C6F723A20234646303030303B5C223E3C623E4572726F722C204C6963656E7365206E6F7420666F756E6420666F72207468697320646F6D61696E2E3C2F623E3C62722F3E546F207265706F727420616E204572726F722C20636F6E74616374203C6120687265663D5C226D61696C746F3A7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E207768696C6520746F20707572636861736520616E6F74686572206C6963656E73652C207669736974203C623E3C6120687265663D5C22687474703A2F2F7777772E657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E3C2F623E3C2F703E223B6469653B7D'));
									$this->assignRef('days', $daysdiff);
									$this->assignRef('pickup', $first);
									$this->assignRef('release', $second);
									$this->assignRef('place', $pplace);
									
										//Nuevas Variables
									$this->assignRef('pattr', $pattr);
									$this->assignRef('pidprice', $idprice);
									$this->assignRef('city_ini', $city_ini);
									$this->assignRef('add_ini', $add_ini);
									$this->assignRef('city_fin', $city_fin);
									$this->assignRef('add_fin', $add_fin);
									$this->assignRef('add_vuelo', $add_vuelo);
									$this->assignRef('add_marca2', $add_marca2);
									$this->assignRef('add_model2', $add_model2);
									$this->assignRef('numpasangers', $numpasangers);

									
									//theme
									$theme = vikrentcar::getTheme();
									if($theme != 'default') {
										$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'search';
										if(is_dir($thdir)) {
											$this->_setPath('template', $thdir.DS);
										}
									}
									//
									parent :: display($tpl);
									
								}
								//
							} else {
								if (vikrentcar :: allowStats()) {
									$q = "INSERT INTO `#__vikrentcar_stats` (`ts`,`ip`,`place`,`cat`,`ritiro`,`consegna`,`res`) VALUES('" . time() . "','" . getenv('REMOTE_ADDR') . "','" . $dbo->getEscaped($pplace) . ';' . $dbo->getEscaped($returnplace) . "','" . $dbo->getEscaped($pcategories) . "','" . $first . "','" . $second . "','0');";
									$dbo->setQuery($q);
									$dbo->Query($q);
								}
								if (vikrentcar :: sendMailStats()) {
									$admsg = vikrentcar :: getFrontTitle() . ", " . JText :: _('VRSRCHNOTM') . "\n\n";
									$admsg .= JText :: _('VRDATE') . ": " . date($df . ' H:i:s') . "\n";
									$admsg .= JText :: _('VRIP') . ": " . getenv('REMOTE_ADDR') . "\n";
									$admsg .= (!empty ($pplace) ? JText :: _('VRPLACE') . ": " . vikrentcar :: getPlaceName($pplace) : "") . (!empty ($returnplace) ? " - " . vikrentcar :: getPlaceName($returnplace) : "") . "\n";
									if (!empty ($pcategories)) {
										$admsg .= ($pcategories == "all" ? JText :: _('VRCAT') . ": " . JText :: _('VRANY') : JText :: _('VRCAT') . ": " . vikrentcar :: getCategoryName($pcategories)) . "\n";
									}
									$admsg .= JText :: _('VRPICKUP') . ": " . date($df . ' H:i', $first) . "\n";
									$admsg .= JText :: _('VRRETURN') . ": " . date($df . ' H:i', $second) . "\n";
									$admsg .= JText :: _('VRSRCHRES') . ": 0";
									$adsubj = JText :: _('VRSRCHNOTM') . ' ' . vikrentcar::getFrontTitle();
									$adsubj = '=?UTF-8?B?' . base64_encode($adsubj) . '?=';
									@ mail(vikrentcar :: getAdminMail(), $adsubj, $admsg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
								}
								showSelect(JText :: _('VRNOCARSINDATE'));
							}
						} else {
							if (vikrentcar :: allowStats()) {
								$q = "INSERT INTO `#__vikrentcar_stats` (`ts`,`ip`,`place`,`cat`,`ritiro`,`consegna`,`res`) VALUES('" . time() . "','" . getenv('REMOTE_ADDR') . "','" . $dbo->getEscaped($pplace) . ';' . $dbo->getEscaped($returnplace) . "','" . $dbo->getEscaped($pcategories) . "','" . $first . "','" . $second . "','0');";
								$dbo->setQuery($q);
								$dbo->Query($q);
							}
							if (vikrentcar :: sendMailStats()) {
								$admsg = vikrentcar :: getFrontTitle() . ", " . JText :: _('VRSRCHNOTM') . "\n\n";
								$admsg .= JText :: _('VRDATE') . ": " . date($df . ' H:i:s') . "\n";
								$admsg .= JText :: _('VRIP') . ": " . getenv('REMOTE_ADDR') . "\n";
								$admsg .= (!empty ($pplace) ? JText :: _('VRPLACE') . ": " . vikrentcar :: getPlaceName($pplace) : "") . (!empty ($returnplace) ? " - " . vikrentcar :: getPlaceName($returnplace) : "") . "\n";
								if (!empty ($pcategories)) {
									$admsg .= ($pcategories == "all" ? JText :: _('VRCAT') . ": " . JText :: _('VRANY') : JText :: _('VRCAT') . ": " . vikrentcar :: getCategoryName($pcategories)) . "\n";
								}
								$admsg .= JText :: _('VRPICKUP') . ": " . date($df . ' H:i', $first) . "\n";
								$admsg .= JText :: _('VRRETURN') . ": " . date($df . ' H:i', $second) . "\n";
								$admsg .= JText :: _('VRSRCHRES') . ": 0";
								$adsubj = JText :: _('VRSRCHNOTM') . ' ' . vikrentcar::getFrontTitle();
								$adsubj = '=?UTF-8?B?' . base64_encode($adsubj) . '?=';
								@ mail(vikrentcar :: getAdminMail(), $adsubj, $admsg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
							}
							showSelect(JText :: _('VRNOCARAVFOR') ." " . $daysdiff . " " . ($daysdiff > 1 ? JText :: _('VRDAYS') : JText :: _('VRDAY')));
						}
					} else {
						//showSelect(JText :: _('VRPICKBRET'). 'Estas son las fechas: '.date($df . ' H:i', $first). '      '.date($df . ' H:i', $second).' hora actual: '.date($df . ' H:i', $actnow).'     '.date($df . ' H:i', time()));
						showSelect(JText :: _('VRPICKBRET'));					
					}
				} else {
					showSelect(JText :: _('VRWRONGDF') . ": " . vikrentcar :: sayDateFormat());
				}
			} else {
				showSelect(JText :: _('VRSELPRDATE'));
			}
		} else {
			echo vikrentcar :: getDisabledRentMsg();
		}
	}
	
	
}

function picoPlaca($car,$arrtar,$kk){

	/**
	 * Modificacion
	 * Obtener el ultimo dígito de la placa y el día del mes.
	 */
	$lengthPlaca= strlen($car[0]['placa']);
	
	$ppickupdate = JRequest :: getString('pickupdate', '', 'request');
	
	$time = strtotime($ppickupdate);
	
	$monthDay = date('d',$time);
	$weekDay= date('w',$time);
		
	$lastStringPlaca= mb_substr($car[0]['placa'], $lengthPlaca-1);
	
	$lastNumberPlaca=(int)$lastStringPlaca;
	
	$impData=true;
	/**
	 *Filtra los vehiculos con placas terminardas en numero impar  si el dia es impar. 
	 */
	if(($monthDay)%2!=0 && ($lastNumberPlaca % 2)!=0 ){
	
		
		unset ($arrtar[$kk]);
		
		
	
	}
	/**
	 *Filtra los vehiculos con placas terminardas en numero par si el dia es par. 
	 */
	if(($monthDay)%2==0 && ($lastNumberPlaca % 2)==0 ){
		
		
		unset ($arrtar[$kk]);
		
		
		
	}
	/**
	 *Returna los datos filtrados.
	 */
	return 	$arrtar;
}







?>