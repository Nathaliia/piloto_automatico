
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
//$add_ini=json_decode($add_iniJson, true);



date_default_timezone_set('America/Bogota');



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
		<p class="vrcrentalriepilogo"><?php echo JText::_('VRRIEPILOGORDT'); ?>:</p>
		
		<div class="vrcrentforlocs">		
		
			<p class="vrcrentalfor">
				<div class="vrcrentalfortwo"><p><?php echo JText::_('DATEA'); ?> <span class="vrcrentalfordate"><?php echo date($df.' H:i'); ?></span></p><p><span class="vrcrentalfordate"></span></p></div>
			</p>
		
			
				
		
		
		</div>
		
		<table class="vrctableorder">
		<tr class="vrctableorderfrow"><td>&nbsp;</td><td align="center"><?php echo  JText::_(VRSTATUS);?></td><td align="center"><?php echo (array_key_exists('hours', $price) ? JText::_('VRCHOURS').'/'.JText::_('ORDDD') : JText::_('VRCHOURS').'/'.JText::_('ORDDD')); ?></td><td align="center"><?php echo JText::_('ORDCREDIT')?></td><td align="center"><?php echo JText::_('ORDNOTAX'); ?></td><td align="center"><?php echo JText::_('ORDTAX'); ?></td><td align="center"><?php echo JText::_('ORDWITHTAX'); ?></td></tr>
		<?php 
		//Modificacion para que lea los items del carrito 
		
		if(!empty($nameItemsCart) && is_array($nameItemsCart)){
			$i=0;
			
			$totdueacum=0;
			$saywithoutacum=0;
			$dbo = & JFactory :: getDBO();
			foreach ($nameItemsCart as $val){
				$saywith=$itemsPrice[$i];
				if($hourly[$i]==1){
				
					$q="SELECT * FROM  `#__vikrentcar_dispcosthours`	WHERE  `id` =".$idtars[$i].";";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$cprice = $dbo->loadAssocList();

					
					$costoConCredito= vikrentcar ::sayCostWithCredito($cprice[0]['idprice'], $idcars[$i], $cprice[0]['hours'], $itemsPrice[$i]);
					$saywithout = vikrentcar :: sayCostMinusIva($itemsPrice[$i], $cprice[0]['idprice']);
					$totdueacum =$totdueacum+$saywith;
					$saywithoutacum=$saywithoutacum+ $saywithout;
					$units='h';
					$imp=$saywithoutacum;
					
					
					
				}
				else{

					$q="SELECT * FROM  `#__vikrentcar_dispcost`	WHERE  `id` =".$idtars[$i].";";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$cprice = $dbo->loadAssocList();
					$costoConCredito= vikrentcar ::sayCostWithCredito($cprice[0]['idprice'],$idcars[$i], $cprice[0]['days'], $itemsPrice[$i]);
					$saywithout = vikrentcar :: sayCostMinusIva($itemsPrice[$i], $cprice[0]['idprice']);
					
					$totdueacum =$totdueacum+$saywith;
					$saywithoutacum=$saywithoutacum+ $saywithout;
					
					$imp=$saywithoutacum;
					$units='d';
				}

				
		
		$q="SELECT * FROM  `#__vikrentcar_orders`	WHERE  `id` =".$idOrders[$i].";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$infoOrder = $dbo->loadAssocList();
		
		?>
			<tr><td align="left"><a id="link<?php  echo $i  ?>" href="<?php  echo JURI::root().'?index.php&option=com_vikrentcar&format=raw&task=vieworder&sid='.$infoOrder[0]['sid'].'&ts='.$infoOrder[0]['ts']; ?>" title="Ver Detalle Orden: <?php echo $infoOrder[0]['id'];?>" target="_blank" class="modal">	<?php echo $val; ?></a><br/><?php echo vikrentcar::getPriceName($cprice[0]['idprice']).'   '.(!empty($cprice[0]['attrdata']) ? "  ".vikrentcar::getPriceAttr($cprice[0]['idprice']).": ".$cprice[0]['attrdata'] : ""); ?></td>
			<td align="center" id="cstatus<?php  echo $i  ?>"><?php echo ($infoOrder[0]['status']=='confirmed')? '<p><img src='.$imagenaccept.' title="'.JText::_('VRSTATUSCONFIRMED').'"/></p>': '<p><img src='.$imagenexlamation.' title="'.JText::_('VRSTATUSPENDIENTE').'"/></p>'?></td><td align="center"><?php 
				if($numpasangers[$i]!=null){ 
				echo ($numpasangers[$i]=='1')?$numpasangers[$i].' Pasajero':$numpasangers[$i].' Pasajeros';

				}else{
				
				echo (array_key_exists('hours', $cprice[0])?$cprice[0]['hours']." ".$units : $cprice[0]['days']." ".$units);  	
				}
			
			?></td><td align="center"><?php  echo vikrentcar :: getCreditoOrder($infoOrder[0]['id']);   ?></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywithout, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith - $saywithout, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith, 0); ?></span></td></tr>
			
		<?php $i++;}
		}else{
			//solo muestra ultimo producto que ordeno si el carrito no tiene valores ?>
			<tr><td align="left"><?php echo $car['name']; ?><br/><?php echo vikrentcar::getPriceName($cprice[0]['idprice']).(!empty($cprice[0]['attrdata']) ? "  ".vikrentcar::getPriceAttr($cprice[0]['idprice']).": ".$cprice['attrdata'] : ""); ?></td>
			<td align="center"><?php 

			if($numpasangers[$i]!=null){
				echo ($numpasangers[$i]=='1')?$numpasangers[$i].' Pasajero':$numpasangers[$i].' Pasajeros';	

			}else{
				echo (array_key_exists('hours', $price)? $price['hours'] : $days); 	
			}
			?></td><td align="center"><?php  echo vikrentcar :: getCreditoOrder($infoOrder[0]['id']);   ?></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywithout, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith - $saywithout, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith, 0); ?></span></td></tr>
		<?php }
		
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
			<tr<?php echo (($sf % 2) == 0 ? " class=\"vrcordrowtwo\"" : " class=\"vrcordrowone\""); ?>><td><?php echo $aop['name'].($aop['quan'] > 1 ? " <small>(x ".$aop['quan'].")</small>" : ""); ?></td><td></td><td align="center"><?php echo (array_key_exists('hours', $price) ? $price['hours'] : $days); ?></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($optwithout, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($opttax, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($optwith, 0); ?></span></td></tr>
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
		
		<tr<?php echo (($sf % 2) == 0 ? " class=\"vrcordrowtwo\"" : " class=\"vrcordrowone\"");?>>
			<td><?php echo JText::_('VRLOCFEETOPAY'); ?></td>
			<td align="center"><?php echo (array_key_exists('hours', $price) ? $price['hours'] : $days); ?></td>
			<td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeewithout, 0); ?></span></td>
			<td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeetax, 0); ?></span></td>
			<td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeewith, 0); ?></span></td>
		</tr>
		
		<?php

	}
}

//store Order Total in session for modules
$session =& JFactory::getSession();
$session->set('vikrentcar_ordertotal', $totdue);
//

//vikrentcar 1.6


if(!empty($nameItemsCart) && is_array($nameItemsCart)){
	$origtotdue= $totdueacum;
	$totdue=$totdueacum;
	
}else{
$origtotdue = $totdue;
}
$usedcoupon = false;

if(is_array($coupon)) {
	
	//check min tot ord
	$coupontotok = true;
	if(strlen($coupon['mintotord']) > 0) {
		if($totdue < $coupon['mintotord']) {
			$coupontotok = false;
			
		}
	}
	if($coupontotok == true) {
		$usedcoupon = true;
		if($coupon['percentot'] == 1) {
			
			//percent value
			$minuscoupon = 100 - $coupon['value'];
			$couponsave = $totdue * $coupon['value'] / 100;
			$totdue = $totdue * $minuscoupon / 100;
		}else {
			//total value
			$couponsave = $coupon['value'];
			$totdue = $totdue - $coupon['value'];
		}
	}else {
		echo ( JText::_('VRCCOUPONINVMINTOTORD'));
	}
}
//

?>
		
		<tr height="20px"><td colspan="5" height="20px">&nbsp;</td></tr>
		<tr class="vrcordrowtotal"><td><?php echo JText::_('VRTOTAL'); ?></td><td></td><td align="center"></td><td align="center"><?php  echo vikrentcar::verPaqueteUser();?></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($imp, 0); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format(($origtotdue - $imp), 0); ?></span></td><td align="center" class="vrctotalord"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($origtotdue, 0); ?></span></td></tr>
		<?php
		if($usedcoupon == true) {
			
			?>
			<tr class="vrcordrowtotal"><td><?php echo JText::_('VRCCOUPON'); ?> <?php echo $coupon['code']; ?></td><td align="center">&nbsp;</td><td align="center">&nbsp;</td><td align="center">&nbsp;</td><td align="center" class="vrctotalord"><span class="vrccurrency">- <?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($couponsave, 0); ?></span></td></tr>
			<tr class="vrcordrowtotal"><td><?php echo JText::_('VRCNEWTOTAL'); ?></td><td align="center">&nbsp;</td><td align="center">&nbsp;</td><td align="center" class="vrctotalord"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($totdue, 0); ?></span></td></tr>
			<?php
		}
		?>
		</table>		

<?php
//vikrentcar 1.6

//se deshabilita si el usuario tiene cupones 

//if(vikrentcar::couponsEnabled() && !is_array($coupon)) {
  if(false) {	
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
}
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
		?>
					<tr><td align="right"><?php echo $isreq; ?><?php echo $fname; ?> </td><td><input type="text" name="vrcf<?php echo $cf['id']; ?>" value="<?php echo $textmailval; ?>" size="40" class="vrcinput"/></td></tr>
		<?php

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
if (!empty ($pitemid)) {
?>
			<input type="hidden" name="Itemid" value="<?php echo $pitemid; ?>"/>
			<?php

}
?>
		</form>
		
		<?php
//vikrentcar 1.6
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
//
?>

	

