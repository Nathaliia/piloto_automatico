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




/*$car=$this->car;
$price=$this->price;
$selopt=$this->selopt;
$days=$this->days;
//vikrentcar 1.6
$calcdays=$this->calcdays;
if((int)$days != (int)$calcdays) {
	$origdays = $days;
	$days=$calcdays;
}
$coupon=$this->coupon;
//
$first=$this->first;
$second=$this->second;
$ftitle=$this->ftitle;
$place=$this->place;
$returnplace=$this->returnplace;
$payments=$this->payments;
$cfields=$this->cfields;*/






$imagenaccept = JURI::root()."images/ico/accept.png";
$imagenexlamation = JURI::root()."images/ico/exclamation.png";


$nameItemsCartJson=$this->itemsCart;
$itemsPriceJson=$this->itemsPrice;
$itemsQtyJson=$this->itemsQty;
$idcarsJson=$this->idcars;



$idtarsJson=$this->tars;
$hourlyJson=$this->hourly;
$idOrdersJson=$this->idorders;
$coupon=$this->coupon;
$cfields=$this->cfields;
$add_iniJson=$this->add_ini;

$numpasangersJson=$this->numpasangers;
$itmesValueCoupon=$this->itmesValueCoupon;

$itmespickup=$this->pickup;
$itmesrelease=$this->release;

$isconvenio=$this->isconvenio;

$itemsoptionals=$this->optionals;







/*$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
$dbo->setQuery($q);
$dbo->Query($q);
$sitelogo = $dbo->loadResult();*/






//captura de variables del carrito
/*
$session =& JFactory::getSession();
$nameItemsCart= $session->get('itemsCart','');
$itemsPrice= $session->get('itemsPrice','');
$itemsQty= $session->get('itemsQty','');


$idcars = $session->get('idcars','');
$idtars= $session->get('tars', '');
$hourly = $session->get('hourly', '');
$idOrders=$session->get('idorders', '');

*/




$nameItemsCart=json_decode($nameItemsCartJson, true);
$itemsPrice=json_decode($itemsPriceJson, true);
$itemsQty=json_decode($itemsQtyJson, true);
$idcars=json_decode($idcarsJson, true);
$idtars=json_decode($idtarsJson, true);
$hourly=json_decode($hourlyJson, true);
$idOrders=json_decode($idOrdersJson, true);
$numpasangers=json_decode($numpasangersJson, true);
$itmespickup=json_decode($itmespickup, true);
$itmesrelease=json_decode($itmesrelease, true);
$ValueCoupon=json_decode($itmesValueCoupon, true);
$optionalsdata=json_decode($itemsoptionals, true);

$poptionalsdata= json_decode($optionalsdata[0], true);




//$add_ini=json_decode($add_iniJson, true);


//date_default_timezone_set('America/Bogota');
$idpricesnotshowtime= vikrentcar::getidpricesnotshowtime();




if (@ is_array($cfields)) {
	foreach ($cfields as $cf) {
		if (!empty ($cf['poplink'])) {
			JHTML :: _('behavior.modal');
			break;
		}
	}
}
$currencysymb = vikrentcar :: getCurrencySymb();
if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
	$df = 'd/m/Y';
} else {
	$df = 'Y/m/d';
}
if (vikrentcar :: tokenForm()) {
	session_start();
	$vikt = uniqid(rand(17, 1717), true);
	$_SESSION['vikrtoken'] = $vikt;
	$tok = "<input type=\"hidden\" name=\"viktoken\" value=\"" . $vikt . "\"/>\n";
} else {
	$tok = "";
}

//echo $ftitle;


//se detecta si hay paquetes comprados por usuario



$saywithout = $imp;

$saywith = $totdue;

?>

			<div class="encabezadoorden"><img  height="auto" width="200px" alt="Piloto Automatico"  src="<?php  echo JURI::root(); ?>images/piloto-automatico.png">
			<h1 class="vrcrentalriepilogo"><?php echo JText::_('VRRIEPILOGORDT'); ?>:</h1>
			
				<div class="vrcrentforlocs">		
				
					<p class="vrcrentalfor">


						<div><p><?php echo JText::_('DATEA'); ?> <span class="vrcrentalfordate"><?php echo date($df.' H:i'); ?></span></p><p><span class="vrcrentalfordate"></span></p></div>
					</p>

				</div>

			</div>
		
			
				
		
		
		
		<div class='containertabla'>
		<div class="vrctableorderA">
		<!--<div class="headertable" >-->
		<?php

		//$fincabecera= '</div>';
		$descripcion='<div class="vrctableorderfrow header">'.JText::_("ORDDESCRIPTION").': </div>';
		$estado='<div class="vrctableorderfrow header" >'.JText::_("ORDNUM"). ': </div>';
		$htiempo='<div class="vrctableorderfrow header">'. JText::_("ORDCARRIDATE").': '.'</div>';
		$hcolsob='</div><div class="vrctableorderfrow header">&nbsp; </div>';
		$hprecio='<div class="vrctableorderfrow header" >'.JText::_("ORDNOTAX").': '.'</div>';
		$himpuesto='<div class="vrctableorderfrow header" >'.JText::_("ORDTAX").': '.'</div>';
		$hvaltotal='<div class="vrctableorderfrow header ">'. JText::_("ORDWITHTAX").': '.'</div>';
		$hsubtotal='<div class="vrctableorderfrow header ">'. JText::_("ORDSUBTOTAL").': '.'</div>';
		?>

			
		<?php 
		//Modificacion para que lea los items del carrito 
		$totalcoupones=0;
		if(!empty($nameItemsCart) && is_array($nameItemsCart)){
			$i=0;
			
			$totdueacum=0;
			$saywithoutacum=0;
			$dbo = & JFactory :: getDBO();
			foreach ($nameItemsCart as $val){
				$saywith=$itemsPrice[$i];
				$ppickup= $itmespickup[$i];
				$prelease= $itmesrelease[$i];
				$pidcars =$idcars[$i];
				if($hourly[$i]==1){

						if($idtars[$i]=='new'){

							$pidtar="";
						}else{
							$pidtar=$idtars[$i];

						}
				
					$q="SELECT * FROM  `#__vikrentcar_dispcosthours_master`	WHERE  `id` =".$pidtar.";";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$cprice = $dbo->loadAssocList();
					$saywith=$cprice[0]['cost'];

					
					$costoConCredito= vikrentcar ::sayCostWithCredito($cprice[0]['idprice'], $idcars[$i], $cprice[0]['hours'], $saywith);
					$saywithout = vikrentcar :: sayCostMinusIva($saywith, $cprice[0]['idprice']);
					$totdueacum =$totdueacum+$saywith;
					$saywithoutacum=$saywithoutacum+ $saywithout;
					$units='h';
					$imp=$saywithoutacum;

					if(!empty($nameItemsCart) && is_array($nameItemsCart)){
						$origtotdue= $totdueacum;
						$totdue=$totdueacum;
						
					}else{
					$origtotdue = $totdue;
					}
					$usedcoupon = false;


					if(vikrentcar::couponsEnabled()){

						

					if(is_array($coupon)) {
						
						//check min tot ord
						$coupontotok = true;
						$coupondateok = true;
						$couponsave=0;
						if(strlen($coupon['mintotord']) > 0) {
							if($totdue < $coupon['mintotord']) {
								$coupontotok = false;
								
							}
						}
						if($coupontotok == true) {
							$usedcoupon = true;
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

											if(!(preg_match("/;".$pidcars.";/i", $coupon['idcars']))) {

											//if(!(preg_match("/;".$car[0]['id'].";/i", $coupon['idcars']))) {
												$couponcarok = false;
											}
										}
										if($couponcarok == true) {
											 if($coupon['percentot'] == 1) {
												
												//percent value
												$minuscoupon = 100 - $coupon['value'];
												//$couponsave = $totdue * $coupon['value'] / 100;
												//
												$couponsave = $saywith * $coupon['value'] / 100;
												
												$totalcoupones= $totalcoupones +$couponsave;
												$totdue=$totdue-$totalcoupones;
												//$totdue = $totdue * $minuscoupon / 100;
											}else {
												//total value
												$couponsave = $coupon['value'];
												$totdue=$totdue-$couponsave;
												$totalcoupones= $totalcoupones +$couponsave;
												$totdue=$totdue-$totalcoupones;
												//$totdue = $totdue - $coupon['value'];
											}
										}
								}

							
						}else {
							echo ( JText::_('VRCCOUPONINVMINTOTORD'));
						}
					}
					}
					
					
					
				}
				else{

					$q="SELECT * FROM  `#__vikrentcar_dispcost_master`	WHERE  `id` =".$idtars[$i].";";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$cprice = $dbo->loadAssocList();
					$saywith=$cprice[0]['cost'];
					$costoConCredito= vikrentcar ::sayCostWithCredito($cprice[0]['idprice'],$idcars[$i], $cprice[0]['days'], $saywith);
					$saywithout = vikrentcar :: sayCostMinusIva($saywith, $cprice[0]['idprice']);
					
					$totdueacum =$totdueacum+$saywith;
					$saywithoutacum=$saywithoutacum+ $saywithout;
					
					$imp=$saywithoutacum;
					$units='d';

					if(!empty($nameItemsCart) && is_array($nameItemsCart)){
						$origtotdue= $totdueacum;
						$totdue=$totdueacum;
						
					}else{
					$origtotdue = $totdue;
					}
					$usedcoupon = false;
					if(vikrentcar::couponsEnabled()){

					if(is_array($coupon)) {
						
						//check min tot ord
						$coupontotok = true;
						$coupondateok = true;
						$couponsave=0;
						if(strlen($coupon['mintotord']) > 0) {
							if($totdue < $coupon['mintotord']) {
								$coupontotok = false;
								
							}
						}
						if($coupontotok == true) {
							$usedcoupon = true;
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
											if(!(preg_match("/;".$$pidcars.";/i", $coupon['idcars']))) {

											//if(!(preg_match("/;".$car[0]['id'].";/i", $coupon['idcars']))) {
												$couponcarok = false;
											}
										}
										if($couponcarok == true) {
											 if($coupon['percentot'] == 1) {
												
												//percent value
												$minuscoupon = 100 - $coupon['value'];
												$couponsave = $saywith * $coupon['value'] / 100;
												//$couponsave = $totdue * $coupon['value'] / 100;
												//$totdue = $totdue * $minuscoupon / 100;
												
												$totalcoupones= $totalcoupones +$couponsave;
												$totdue=$totdue-$totalcoupones;
											}else {
												//total value
												$couponsave = $coupon['value'];
												
												$totalcoupones= $totalcoupones +$couponsave;
												$totdue=$totdue-$totalcoupones;
												//$totdue = $totdue - $coupon['value'];
											}
										}
								}

							
						}else {
							echo ( JText::_('VRCCOUPONINVMINTOTORD'));
						}
					}
					}
				}

		if($idtars[$i]=='new'){

			$pidtar="";
		}else{
			$pidtar=$idtars[$i];

		}

				
		
		$q="SELECT * FROM  `#__vikrentcar_orders`	WHERE  `id` =".$pidtar.";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$infoOrder = $dbo->loadAssocList();
		
		?>
			<div class="datostable"><div class="dataorden"><?php   echo $descripcion; ?><div class="fila descripcion " align="left"><?php echo $val; ?><br/><?php echo ''/*vikrentcar::getPriceName($cprice[0]['idprice'])*/.'   '.(!empty($cprice[0]['attrdata']) ? "  ".vikrentcar::getPriceAttr($cprice[0]['idprice']).": ".$cprice[0]['attrdata'] : ""); ?></div></div>
			<div class="dataorden"><?php   echo $estado; ?> <div  class="fila descripcion" align="center" id="cstatus<?php  echo $i  ?>"> <?php  echo $i+1 ?></div></div><div class="dataorden"><?php   echo $htiempo; ?> <div  class="fila descripcion" align="center"><?php 
				
			/*	if($numpasangers[$i]!=0){ 
				echo ($numpasangers[$i]=='1')?$numpasangers[$i].' Pasajero':$numpasangers[$i].' Pasajeros';

				}else{
					if(!(preg_match("/".$cprice[0]['idprice'].";/i", $idpricesnotshowtime))){
					
						//echo (array_key_exists('hours', $cprice[0])?$cprice[0]['hours']." ".$units : $cprice[0]['days']." ".$units);
						//
						echo date('Y/m/d H:i:s', $ppickup);
					} 	
				}

				*/
			
			echo date('Y/m/d H:i:s', $ppickup);
			
			?></div></div>
			<?php

			if(false){

			?>
			<div class="dataorden"><?php   echo $hcolsob; ?> <div  class="fila descripcion" ><?php  echo vikrentcar :: getCreditoOrder($infoOrder[0]['id']);   ?></div></div>
			<?php
			}
			?>
			<div class="dataorden"><?php   echo $hprecio; ?> <div  class="fila descripcion numero" ><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywithout, 0); ?></span></div></div><div class="dataorden"><?php   echo $himpuesto; ?><div  class="fila descripcion numero" ><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith - $saywithout, 0); ?></span></div></div><div class="dataorden"><?php   echo $hvaltotal; ?><div  class="fila descripcion numero" align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith, 0); ?></span></div></div>
			<?php
			if($coupontotok){

			?>
				    <div class="">
				    	<div class="dataorden">
				    		<div  class="vrctableorderfrow header" align="center"><?php echo JText::_('VRCCOUPON'); ?>:   
				    			
				    		</div>

				   			 <div  class="fila descripcion"> <?php echo $coupon['code']; ?>

				   			 	<div  class="fila numerocoupon">
				    	
				    				- <?php echo $currencysymb; ?><?php echo number_format( $couponsave, 0); ?>
				    			</div> 
				    	      
				    		</div>
				    	</div>
				    </div>
		

		<?php }

		?>
		<div class="dataorden"><?php   echo $hsubtotal; ?> <div  class="fila descripcion numero subtotal" ><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith-$couponsave, 0); ?></span></div></div>

		</div>

		<?php



		$i++;}
		}else{
			//solo muestra ultimo producto que ordeno si el carrito no tiene valores ?>
		
		<?php 


		 }
		
		?>


		
		
		<?php

$sf = 2;
if (is_array($selopt)) {
	foreach ($selopt as $aop) {
		if (intval($aop['perday']) == 1) {
			$thisoptcost = ($aop['cost'] * $aop['quan']) * $days;
		} else {
			$thisoptcost = $aop['cost'] * $aop['quan'];
		}
		if (!empty ($aop['maxprice']) && $aop['maxprice'] > 0 && $thisoptcost > $aop['maxprice']) {
			$thisoptcost = $aop['maxprice'];
			if(intval($aop['hmany']) == 1 && intval($aop['quan']) > 1) {
				$thisoptcost = $aop['maxprice'] * $aop['quan'];
			}
		}
		$optwithout = (intval($aop['perday']) == 1 ? vikrentcar :: sayOptionalsMinusIva($thisoptcost, $aop['idiva']) : vikrentcar :: sayOptionalsMinusIva($thisoptcost, $aop['idiva']));
		$optwith = (intval($aop['perday']) == 1 ? vikrentcar :: sayOptionalsPlusIva($thisoptcost, $aop['idiva']) : vikrentcar :: sayOptionalsPlusIva($thisoptcost, $aop['idiva']));
		$opttax = number_format($optwith - $optwithout, 0);
		?>
			<div<?php echo (($sf % 2) == 0 ? " class=\"vrcordrowtwo\"" : " class=\"vrcordrowone\""); ?>><div><?php echo $aop['name'].($aop['quan'] > 1 ? " <small>(x ".$aop['quan'].")</small>" : ""); ?></div><div></div><div align="center"><?php echo (array_key_exists('hours', $price) ? $price['hours'] : $days); ?></div><div align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($optwithout, 0); ?></span></div><div align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($opttax, 0); ?></span></div><div align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($optwith, 0); ?></span></div></div>
		<?php

		$sf++;
	}
}
if (!empty ($place) && !empty ($returnplace)) {

	$locfee = vikrentcar :: getLocFee($place, $returnplace);
	if ($locfee) {

		$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $days) : $locfee['cost'];
		$locfeewithout = vikrentcar :: sayLocFeeMinusIva($locfeecost, $locfee['idiva']);
		$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
		$locfeetax = number_format($locfeewith - $locfeewithout, 0);
		$imp += $locfeewithout;
		$totdue += $locfeewith;
		
		?>
		
		<div<?php echo (($sf % 2) == 0 ? " class=\"vrcordrowtwo\"" : " class=\"vrcordrowone\"");?>>
			<div><?php echo JText::_('VRLOCFEETOPAY'); ?></div>
			<div  class="fila" align="center"><?php echo (array_key_exists('hours', $price) ? $price['hours'] : $days); ?></div>
			<div class="fila"  align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeewithout, 0); ?></span></div>
			<div  class="fila" align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeetax, 0); ?></span></div>
			<div  class="fila" align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeewith, 0); ?></span></div>
		</div>
		
		<?php

	}
}

//store Order Total in session for modules
$session =& JFactory::getSession();
$session->set('vikrentcar_ordertotal', $totdue);
//

//vikrentcar 1.6



//

?>
		
		
		
			<div class="totalordenes">
				
				<?php

				if(vikrentcar::verPaqueteUser()!=0){

					?>
				
				<div class="" align="center"><?php  echo vikrentcar::verPaqueteUser();?></div>

				<?php 

					}

				?>
				<div class="dataorden">
					<div class="vrctableorderfrow header" ><span class=""><?php echo JText::_('VRTOTAL'); ?>: </span></div><div class="fila descripcion numero formattotalneto" ><span class="vrccurrency "><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($imp, 0); ?></span></div >

				</div>
				<div class="dataorden">
				<div class="vrctableorderfrow header" ><span class=""><?php echo JText::_('ORDTAXTOT'); ?>: </span></div><div class="fila descripcion numero formattotalimp " ><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format(($origtotdue - $imp), 0); ?></span></div>
				</div>
				<div class="dataorden">
				<div  class="vrctableorderfrow header" ><span class=" "><?php echo JText::_('ORDSUBOT'); ?>:</span></div><div class="fila descripcion numero formatsubtotal  " > <span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class=""><?php echo number_format($origtotdue, 0); ?></span></div>
				</div>

		

		<?php
		if($usedcoupon == true) {
			
			?>
		
					<div class="dataorden">
					<div class="vrctableorderfrow header">Total 
					<?php echo  JText::_('VRCCOUPON'); ?>: 
					</div>
					<div  class="fila descripcion"> <?php echo $coupon['code']; ?>
					<div  class="fila numerocoupon formattotalcoupon"><span class="vrccurrency">- <?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($totalcoupones, 0); ?></span></div>
				</div>
				


			
				
			
			   
			
			
			</div>
			<div class="dataorden">
			<div class="vrctableorderfrow header"><?php echo JText::_('VRCNEWTOTAL'); ?>: </div><div  class="fila descripcion numero formattotalpagar"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($origtotdue-$totalcoupones, 0); ?></span></div>
			</div>

			<?php
		}
		?>
		
		</div>
		</div>
		</div>		

<?php
//vikrentcar 1.6

//se deshabilita si el usuario tiene cupones 

/*if(vikrentcar::couponsEnabled() && !is_array($coupon)) {
  //if(false) {	
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_vikrentcar'); ?>" method="post" >
		<div class="vrcentercoupon">
		<span class="vrchaveacoupon"><?php echo JText::_('VRCHAVEACOUPON'); ?></span><input type="text" name="couponcode" value="" size="20" class="vrcinputcoupon"/><input type="submit" class="vrcsubmitcoupon" name="applyacoupon" value="<?php echo JText::_('VRCSUBMITCOUPON'); ?>"/>
		</div>
		<input type="hidden" name="priceid" value="<?php echo $price['idprice']; ?>"/>
		<input type="hidden" name="place" value="<?php echo $place; ?>"/>
		<input type="hidden" name="returnplace" value="<?php echo $returnplace; ?>"/>
		<input type="hidden" name="carid" value="<?php echo $car['id']; ?>"/>
  		<input type="hidden" name="days" value="<?php echo $days; ?>"/>
  		<input type="hidden" name="pickup" value="<?php echo $first; ?>"/>
  		<input type="hidden" name="release" value="<?php echo $second; ?>"/>
  		<?php
  		if (is_array($selopt)) {
			foreach ($selopt as $aop) {
				echo '<input type="hidden" name="optid'.$aop['id'].'" value="'.$aop['quan'].'"/>'."\n";
			}
  		}
  		?>
  		<input type="hidden" name="task" value="oconfirm"/>
	</form>
	<?php
}*/
//
?>
		
		<script language="JavaScript" type="text/javascript">
  		function checkvrcFields(){
  			var vrvar = document.vrc;
			<?php

$currentUser = JFactory::getUser();
	
$dbo = & JFactory :: getDBO();
$q = "SELECT * FROM `#__vikrentcar_profiles` WHERE `user_id`='" .$currentUser->id. "';";
$dbo->setQuery($q);
$dbo->Query($q);
$datosCustomer = $dbo->loadAssocList();

if (@ is_array($cfields)) {
	foreach ($cfields as $cf) {
		if (intval($cf['required']) == 1) {
			if ($cf['type'] == "text" || $cf['type'] == "textarea") {
			?>
			if(!vrvar.vrcf<?php echo $cf['id']; ?>.value.match(/\S/)) {
				document.getElementById('vrcf<?php echo $cf['id']; ?>').style.color='#ff0000';
				return false;
			}else {
				document.getElementById('vrcf<?php echo $cf['id']; ?>').style.color='';
			}
			<?php

			}elseif ($cf['type'] == "select") {
			?>
			if(!vrvar.vrcf<?php echo $cf['id']; ?>.value.match(/\S/)) {
				document.getElementById('vrcf<?php echo $cf['id']; ?>').style.color='#ff0000';
				return false;
			}else {
				document.getElementById('vrcf<?php echo $cf['id']; ?>').style.color='';
			}
			<?php

			} elseif ($cf['type'] == "checkbox") {
				//checkbox
			?>
			if(vrvar.vrcf<?php echo $cf['id']; ?>.checked) {
				document.getElementById('vrcf<?php echo $cf['id']; ?>').style.color='';
			}else {
				document.getElementById('vrcf<?php echo $cf['id']; ?>').style.color='#ff0000';
				return false;
			}
			<?php

			}
		}
	}
}
?>
  			return true;
  		}
		</script>
	<div class="formuserfinal">
	<form action="<?php echo JRoute::_('index.php?option=com_vikrentcar'); ?>" name="vrc" method="post" onsubmit="javascript: return checkvrcFields();">
	

	<table class="vrccustomfields">
	<?php
	
	$currentUser = JFactory::getUser();
	
	$dbo = & JFactory :: getDBO();
	$q = "SELECT * FROM `#__vikrentcar_profiles` WHERE `user_id`='" .$currentUser->id. "';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	$datosCustomer = $dbo->loadAssocList();
	
	FB::log($currentUser);
	$juseremail = !empty($currentUser->email) ? $currentUser->email : "";
	$jname = !empty($currentUser->name) ? $currentUser->name : "";
	
	foreach ($cfields as $cf) {
		
		if (intval($cf['required']) == 1) {
			$isreq = "<span class=\"vrcrequired\"><sup>*</sup></span> ";
		} else {
			$isreq = "";
		}
		if (!empty ($cf['poplink'])) {
			$fname = "<a href=\"" . $cf['poplink'] . "\" id=\"vrcf" . $cf['id'] . "\" rel=\"{handler: 'iframe', size: {x: 750, y: 600}}\" target=\"_blank\" class=\"modal\">" . JText :: _($cf['name']) . "</a>";
		} else {
			$fname = "<span id=\"vrcf" . $cf['id'] . "\">" . JText :: _($cf['name']) . "</span>";
		}
		if ($cf['type'] == "text") {
			if($cf['isemail'] == 1){
				$textmailval=$juseremail;
				
			}
			if($cf['name'] == 'ORDER_NAME'){
				$textmailval=$jname;
				
			}
			

			if($cf['name'] == 'ORDER_LNAME'){
				$textmailval=$datosCustomer[0]['lname'];
				
			}
			if($cf['name'] == 'ORDER_PHONE'){
				$textmailval=$datosCustomer[0]['phone'];
				
			}
			if($cf['name'] == 'ORDER_ADDRESS'){
				$textmailval=$datosCustomer[0]['address'];
				
			}
			if($cf['name'] == 'ORDER_CITY'){
				$textmailval=$datosCustomer[0]['city'];
				
			}
			
		
			//$textmailval = intval($cf['isemail']) == 1 ? $juseremail : (($cf['name'] == 'ORDER_NAME')? $jname : '');
		
		if($cf['id']==3 && $datosCustomer[0]['usertype']==1 ){


		//nada
		?>
					
		<?php
		}else{?>

			<tr><td align="right"><?php echo $isreq; ?><?php echo $fname; ?> </td><td><input type="text" name="vrcf<?php echo $cf['id']; ?>" value="<?php  echo $textmailval; ?>" size="40" class="vrcinput"/></td></tr>
		<?php }
		}elseif ($cf['type'] == "textarea") {
		?>
					<tr><td valign="top" align="right"><?php echo $isreq; ?><?php echo $fname; ?> </td><td><textarea name="vrcf<?php echo $cf['id']; ?>" rows="5" cols="30" class="vrctextarea"></textarea></td></tr>
		<?php

		}elseif ($cf['type'] == "select") {
			$answ = explode(";;__;;", $cf['choose']);
			$wcfsel = "<select name=\"vrcf" . $cf['id'] . "\">\n";
			foreach ($answ as $aw) {
				if (!empty ($aw)) {
					$wcfsel .= "<option value=\"" . $aw . "\">" . $aw . "</option>\n";
				}
			}
			$wcfsel .= "</select>\n";
		?>
					<tr><td align="right"><?php echo $isreq; ?><?php echo $fname; ?> </td><td><?php echo $wcfsel; ?></td></tr>
		<?php

		}elseif ($cf['type'] == "separator") {
			$cfsepclass = strlen(JText::_($cf['name'])) > 30 ? "vrcseparatorcflong" : "vrcseparatorcf";
		?>
					<tr><td colspan="2" class="<?php echo $cfsepclass; ?>"><?php echo JText::_($cf['name']); ?></td></tr>
		<?php
		}else {
		?>
					<tr><td align="right"><?php echo $isreq; ?><?php echo $fname; ?> </td><td><input type="checkbox" name="vrcf<?php echo $cf['id']; ?>" value="1"/></td></tr>
		<?php

		}
	}
?>
		</table>

		<input type="hidden" name="days" value="<?php echo $days; ?>"/>
		<?php
		//vikrentcar 1.6
		if($origdays) {
			?>
			<input type="hidden" name="origdays" value="<?php echo $origdays; ?>"/>
			<?php
		}
		//
		?>
		<input type="hidden" name="pickup" value="<?php echo $first; ?>"/>
		<input type="hidden" name="release" value="<?php echo $second; ?>"/>
		<input type="hidden" name="car" value="<?php echo $car['id']; ?>"/>
		<input type="hidden" name="prtar" value="<?php echo $price['id']; ?>"/>
		<input type="hidden" name="priceid" value="<?php echo $price['idprice']; ?>"/>
		<input type="hidden" name="optionals" value="<?php echo $wop; ?>"/>
		<input type="hidden" name="totdue" value="<?php echo $totdue; ?>"/>
		<input type="hidden" name="place" value="<?php echo $place; ?>"/>
		<input type="hidden" name="returnplace" value="<?php echo $returnplace; ?>"/>
		<input type="hidden" name="valorPaquete" value="<?php echo $price['attrdata']; ?>"/>
		<input type="hidden" name="valorPaqueteUsuario" value="<?php echo $horasPaquete; ?>"/>
		<input type="hidden" name="CostCredit" value="<?php echo $costoConCredito; ?>"/>
		
		
		<?php
		if(vikrentcar:: getPriceName( $price['idprice'])=='Credito'){
		$paq=true
		?>
		<input type="hidden" name="isPaquete" value='yes'/>
		<?php
		}else{
		?>
		
		<input type="hidden" name="isPaquete" value='no'/>
		<?php
		$paq=false; 
		}
		?>
		<?php

		if(array_key_exists('hours', $price)) {
			?>
		<input type="hidden" name="hourly" value="<?php echo $price['hours']; ?>"/>	
			<?php
		}
		if($usedcoupon == true && is_array($coupon)) {
			?>
		<input type="hidden" name="couponcode" value="<?php echo $coupon['code']; ?>"/>
			<?php
		}
		?>
		<?php echo $tok; ?>
		<input type="hidden" name="task" value="saveorder"/>
		<br/>
		<?php

if (@ is_array($payments)) {
	?>
	<p class="vrcchoosepayment"><?php echo JText::_('VRCHOOSEPAYMENT'); ?></p>
	<ul style="list-style-type: none;">
	<?php
	foreach ($payments as $pk => $pay) {
		$rcheck = $pk == 0 ? " checked=\"checked\"" : "";
		$saypcharge = "";
		if ($pay['charge'] > 0.00) {
			$decimals = $pay['charge'] - (int)$pay['charge'];
			if($decimals > 0.00) {
				$okchargedisc = number_format($pay['charge'], 0);
			}else {
				$okchargedisc = number_format($pay['charge'], 0);
			}
			$saypcharge .= " (".($pay['ch_disc'] == 1 ? "+" : "-");
			$saypcharge .= "<span class=\"vrcprice\">" . $okchargedisc . "</span> <span class=\"vrccurrency\">" . ($pay['val_pcent'] == 1 ? $currencysymb : "%") . "</span>";
			$saypcharge .= ")";
		}
	?>
		<li><input type="radio" name="gpayid" value="<?php echo $pay['id']; ?>" id="gpay<?php echo $pay['id']; ?>"<?php echo $rcheck; ?>/> <label for="gpay<?php echo $pay['id']; ?>"><?php echo $pay['name'].$saypcharge; ?></label></li>
	<?php
	}
	?>
		</ul>
		<br/>
	<?php
}


?>
		<input type="submit" id="saveorder"  name="saveorder" value="<?php echo JText::_('VRORDCONFIRM'); ?>" class="booknow"/>
		<?php

$pitemid = JRequest :: getInt('Itemid', '', 'request');

if($isconvenio){

	$user =& JFactory::getUser();
	$infocoupons= vikrentcar:: getCodeCouponClient($user->id);

	?>

	<input type="hidden" name="convenio" value="<?php echo $infocoupons['code']; ?>"/>
<?php

}
if (!empty ($pitemid)) {
?>
			<input type="hidden" name="Itemid" value="<?php echo $pitemid; ?>"/>
			<?php

}
?>
		</form>
	</div>
		
		<?php
//vikrentcar 1.6
//
//formulario anterior para ingreso de codigo
/*
if(vikrentcar::couponsEnabled() && !is_array($coupon)) {
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_vikrentcar'); ?>" method="post" >
		<div class="vrcentercoupon">
		<span class="vrchaveacoupon"><?php echo JText::_('VRCHAVEACOUPON'); ?></span><input type="text" name="couponcode" value="" size="20" class="vrcinputcoupon"  /><input type="submit" class="vrcsubmitcoupon" name="applyacoupon" value="<?php echo JText::_('VRCSUBMITCOUPON'); ?>"/>
		</div>
        
        
		<input type="hidden" name="priceid" value="<?php echo $price['idprice']; ?>"/>
		<input type="hidden" name="place" value="<?php echo $place; ?>"/>
		<input type="hidden" name="returnplace" value="<?php echo $returnplace; ?>"/>
		<input type="hidden" name="carid" value="<?php echo $car['id']; ?>"/>
  		<input type="hidden" name="days" value="<?php echo $days; ?>"/>
  		<input type="hidden" name="pickup" value="<?php echo $first; ?>"/>
  		<input type="hidden" name="release" value="<?php echo $second; ?>"/>
		<input type="hidden" name="pattr" value="<?php echo $pattr; ?>"/>
		<input type="hidden" name="city_ini" value="<?php echo $city_ini; ?>"/>
		<input type="hidden" name="add_ini" value="<?php echo $add_ini; ?>"/>
		<input type="hidden" name="city_fin" value="<?php echo $city_fin; ?>"/>
		<input type="hidden" name="add_fin" value="<?php echo $add_fin; ?>"/>
        <input type="hidden" name="format" value="raw"/>
        
        
        <input type="hidden" name="itemsCart" value='<?php echo $nameItemsCartJson; ?>'/>
        <input type="hidden" name="itemsPrice" value="<?php echo $itemsPriceJson; ?>"/>
        <input type="hidden" name="itemsQty" value="<?php echo $itemsQtyJson; ?>"/>
        <input type="hidden" name="idcars" value="<?php echo $idcarsJson; ?>"/>
        <input type="hidden" name="tars" value="<?php echo $idtarsJson; ?>"/>
        <input type="hidden" name="hourly" value="<?php echo $hourlyJson; ?>"/>
        <input type="hidden" name="idorders" value="<?php echo $idOrdersJson; ?>"/>
      	
        
  		<?php
		
	

  		if (is_array($selopt)) {
			foreach ($selopt as $aop) {
				echo '<input type="hidden" name="optid'.$aop['id'].'" value="'.$aop['quan'].'"/>'."\n";
			}
  		}
  		?>
  		<input type="hidden"  name="task" value="ordencarrito"/>
	</form>
	<?php
}

*/
//
?>

	

