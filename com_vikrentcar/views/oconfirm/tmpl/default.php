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

$car=$this->car;
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
$cfields=$this->cfields;

$pattr=$this->pattr;
$city_ini=$this->city_ini;
$add_ini=$this->add_ini;
$city_fin=$this->city_fin;
$add_fin=$this->add_fin;

$add_vuelo=$this->add_vuelo;

//$add_fin=$this->lugarReco;

$numpasangers=$this->numpasangers;

$convenio=$this->convenio;

//captura de variables del carrito

$session =& JFactory::getSession();
$nameItemsCart= $session->get('itemsCart','');
$itemsPrice= $session->get('itemsPrice','');
$itemsQty= $session->get('itemsQty','');


$idcars = $session->get('idcars','');
$idtars= $session->get('tars', '');
$hourly = $session->get('hourly', '');
$idOrders=$session->get('idorders', '');



/*$session->clear('itemsCart','');
$session->clear('itemsPrice','');
$session->clear('itemsQty','');
$session->clear('idcars','');
$session->clear('tars','');
$session->clear('hourly','');*/


/*$carid= $session->set('carid', $carid,'carrito');
$dias= $session->set('days', $days,'carrito');
$tpriceid =$session->set('tpriceid', $tpriceid,'carrito');*/

//$session->clear()

/*$nameItemsCart= $session->get('itemsCart');
$itemsPrice= $session->get('itemsPrice');
$itemsQty= $session->get('itemsQty');
$idItems= $session->get('idItemas');
*/
//$resumeCart= $session->get('resumeCart','carrito');


$nameItemsCart=json_decode($nameItemsCart, true);
$itemsPrice=json_decode($itemsPrice, true);
$itemsQty=json_decode($itemsQty, true);
$idcars=json_decode($idcars, true);
$idtars=json_decode($idtars, true);
$hourly=json_decode($hourly, true);
$idOrders=json_decode($idOrders, true);





/*echo '<br>';
print_r($salida1);
echo '<br>';
print_r($salida2);*/

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

$carats = vikrentcar :: getCarCarat($car['idcarat']);
$imp = vikrentcar :: sayCostMinusIva($price['cost'], $price['idprice']);
$totdue = vikrentcar :: sayCostPlusIva($price['cost'], $price['idprice']);
	/*if (array_key_exists("hours",$price)){
		$costoConCredito= vikrentcar ::sayCostWithCredito($price['idprice'], $price['idcar'], $price['hours'], $totdue);
	}
	
	else {
		$costoConCredito= vikrentcar ::sayCostWithCreditoDay($price['idprice'], $price['idcar'], $price['days'], $totdue);
	}*/
$hD =(array_key_exists('hours', $price)? $price['hours'] : $days);
$horas =0;

//$idcar=$car['id'];
//se detecta si carro es  paquete

if(vikrentcar:: getPriceName( $price['idprice'])!='Credito'){
	
	if(vikrentcar ::verPaqueteUser()!=0){
		$horasPaquete =vikrentcar ::verPaqueteUser();
		//$tothD=$hD-$costoConCredito;
		$imp = vikrentcar :: sayCostMinusIva($costoConCredito, $price['idprice']);
		$totdue=$costoConCredito;
	
	}	
	
}else{
	
		$horasPaquete =vikrentcar ::verPaqueteUser();
		$totdue=$costoConCredito;
}
//se detecta si hay paquetes comprados por usuario



$saywithout = $imp;
$saywith = $totdue;
if (is_array($selopt)) {
	foreach ($selopt as $selo) {
		$wop .= $selo['id'] . ":" . $selo['quan'] . ";";
		$realcost = (intval($selo['perday']) == 1 ? ($selo['cost'] * $days * $selo['quan']) : ($selo['cost'] * $selo['quan']));
		if (!empty ($selo['maxprice']) && $selo['maxprice'] > 0 && $realcost > $selo['maxprice']) {
			$realcost = $selo['maxprice'];
			if(intval($selo['hmany']) == 1 && intval($selo['quan']) > 1) {
				$realcost = $selo['maxprice'] * $selo['quan'];
			}
		}
		$imp += vikrentcar :: sayOptionalsMinusIva($realcost, $selo['idiva']);
		$totdue += vikrentcar :: sayOptionalsPlusIva($realcost, $selo['idiva']);
	}
} else {
	$wop = "";
}
?>
		<p class="vrcrentalriepilogo"><?php echo JText::_('VRRIEPILOGOORD'); ?>:</p>
		
		<div class="vrcrentforlocs">
		
		<?php
		if(array_key_exists('hours', $price)) {
			?>
			<p class="vrcrentalfor">
				<span class="vrcrentalforone"><?php echo JText::_('VRRENTAL'); ?> <?php echo $car['name']; ?> <?php echo JText::_('VRFOR'); ?> <?php echo (intval($price['hours']) == 1 ? "1 ".JText::_('VRCHOUR') : $price['hours']." ".JText::_('VRCHOURS')); ?></span>
				<div class="vrcrentalfortwo"><p><?php echo JText::_('VRDAL'); ?> <span class="vrcrentalfordate"><?php echo date($df.' H:i', $first); ?></span></p><p><?php echo JText::_('VRAL'); ?> <span class="vrcrentalfordate"><?php echo date('H:i', $second); ?></span></p></div>
			</p>
			<?php
		}else {
			?>
			<p class="vrcrentalfor">
				<span class="vrcrentalforone"><?php echo JText::_('VRRENTAL'); ?> <?php echo $car['name']; ?> <?php echo JText::_('VRFOR'); ?> <?php echo (intval($days)==1 ? "1 ".JText::_('VRDAY') : $days." ".JText::_('VRDAYS')); ?></span>
				<div class="vrcrentalfortwo"><p><?php echo JText::_('VRDAL'); ?> <span class="vrcrentalfordate"><?php echo date($df.' H:i', $first); ?></span></p><p><?php echo JText::_('VRAL'); ?> <span class="vrcrentalfordate"><?php echo date($df.' H:i', $second); ?></span></p></div>
			</p>
			<?php
		}
		?>
		
		<div class="vrclocsboxsum">
		<?php if(!empty($place)) { ?>
		<p class="vrcpickuploc"><?php echo JText::_('VRRITIROCAR'); ?>: <span class="vrcpickuplocname"><?php echo vikrentcar::getPlaceName($place); ?></span></p>
		<?php } ?>
		
		<?php if(!empty($returnplace)) { ?>
		<p class="vrcdropoffloc"><?php echo JText::_('VRRETURNCARORD'); ?>: <span class="vrcdropofflocname"><?php echo vikrentcar::getPlaceName($returnplace); ?></span></p>
		<?php } ?>
		</div>
		
		</div>
		
		<table class="vrctableorder">
		<tr class="vrctableorderfrow"><td>&nbsp;</td><td align="center"><?php echo (array_key_exists('hours', $price) ? JText::_('VRCHOURS'): JText::_('VRCDAYS')); ?></td><td align="center"><?php echo JText::_('ORDCREDIT')?></td><td align="center"><?php echo JText::_('ORDNOTAX'); ?></td><td align="center"><?php echo JText::_('ORDTAX'); ?></td><td align="center"><?php echo JText::_('ORDWITHTAX'); ?></td></tr>
	
		
		
	
		
			
			<tr><td align="left"><?php echo $car['name']; ?><br/><?php echo vikrentcar::getPriceName($price['idprice']).(!empty($price['attrdata']) ? "<br/>".vikrentcar::getPriceAttr($price['idprice']).": ".$price['attrdata'] : ""); ?></td>
			<td align="center"><?php echo (array_key_exists('hours', $price)? $price['hours'] : $days); ?></td><td align="center"><?php  echo vikrentcar :: verPaqueteUser();   ?></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywithout, 2); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith - $saywithout, 2); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($saywith, 2); ?></span></td></tr>
		
	
		
		
		<?php
//aqui seleccion de opciones adicionales
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
		$opttax = number_format($optwith - $optwithout, 2);
		?>
			<tr<?php echo (($sf % 2) == 0 ? " class=\"vrcordrowtwo\"" : " class=\"vrcordrowone\""); ?>><td><?php echo $aop['name'].($aop['quan'] > 1 ? " <small>(x ".$aop['quan'].")</small>" : ""); ?></td><td align="center"><?php echo (array_key_exists('hours', $price) ? $price['hours'] : $days); ?></td><td></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($optwithout, 2); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($opttax, 2); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($optwith, 2); ?></span></td></tr>
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
		$locfeetax = number_format($locfeewith - $locfeewithout, 2);
		$imp += $locfeewithout;
		$totdue += $locfeewith;
		
		?>
		
		<tr<?php echo (($sf % 2) == 0 ? " class=\"vrcordrowtwo\"" : " class=\"vrcordrowone\"");?>>
			<td><?php echo JText::_('VRLOCFEETOPAY'); ?></td>
			<td align="center"><?php echo (array_key_exists('hours', $price) ? $price['hours'] : $days); ?></td>
			<td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeewithout, 2); ?></span></td>
			<td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeetax, 2); ?></span></td>
			<td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($locfeewith, 2); ?></span></td>
		</tr>
		
		<?php

	}
}

//store Order Total in session for modules
$session =& JFactory::getSession();
$session->set('vikrentcar_ordertotal', $totdue);
//

//vikrentcar 1.6



$origtotdue = $totdue;

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
		
		?>
        <input type="hidden" id="tipoCoupon"  value="0"/>
        <input type="hidden" id="descCoupon"  value="0"/>
        <?php
		JError :: raiseWarning('', JText::_('VRCCOUPONINVMINTOTORD'));
	}
}
//

?>
		
		<tr height="20px"><td colspan="5" height="20px">&nbsp;</td></tr>
		<tr class="vrcordrowtotal"><td><?php echo JText::_('VRTOTAL'); ?></td><td align="center"><?php  //echo (vikrentcar::isPaquete($car['id'])==0)? ($hD-$horasPaquete): ($horasPaquete+$price['hours']);?></td><td align="center">&nbsp;</td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($imp, 2); ?></span></td><td align="center"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format(($origtotdue - $imp), 2); ?></span></td><td align="center" class="vrctotalord"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($origtotdue, 2); ?></span></td></tr>
		<?php
		if($usedcoupon == true) {
			?>
			<tr class="vrcordrowtotal"><td><?php echo JText::_('VRCCOUPON'); ?> <?php echo $coupon['code']; ?></td><td align="center">&nbsp;</td><td align="center">&nbsp;</td><td align="center">&nbsp;</td><td align="center" class="vrctotalord"><span class="vrccurrency">- <?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($couponsave, 2); ?></span></td></tr>
			<tr class="vrcordrowtotal"><td><?php echo JText::_('VRCNEWTOTAL'); ?></td><td align="center">&nbsp;</td><td align="center">&nbsp;</td><td align="center" class="vrctotalord"><span class="vrccurrency"><?php echo $currencysymb; ?></span> <span class="vrcprice"><?php echo number_format($totdue, 2); ?></span></td></tr>
			<?php
		}
		?>
		</table>		

<?php
//vikrentcar 1.6
//se desabilita formulario de cupones
$formcouponenable=false;
if($formcouponenable){
	if(vikrentcar::couponsEnabled() && !is_array($coupon)) {
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
			<input type="hidden" name="pattr" value="<?php echo $pattr; ?>"/>
			<input type="hidden" name="city_ini" value="<?php echo $city_ini; ?>"/>
			<input type="hidden" name="add_ini" value="<?php echo $add_ini; ?>"/>
			<input type="hidden" name="city_fin" value="<?php echo $city_fin; ?>"/>
			<input type="hidden" name="add_fin" value="<?php echo $add_fin; ?>"/>
			<input type="hidden" name="add_vuelo" value="<?php echo $add_vuelo; ?>"/>
			<?php
			if (is_array($selopt)) {
				foreach ($selopt as $aop) {
					echo '<input type="hidden" name="optid'.$aop['id'].'" value="'.$aop['quan'].'"/>'."\n";
				}
			}
			?>
			<input type="hidden"  name="task" value="oconfirm"/>
		</form>
		<?php
	}

}//
?>
		
		<script language="JavaScript" type="text/javascript">
  		function checkvrcFields(){
  			var vrvar = document.vrc;
			<?php

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
		<?php

if (@ is_array($cfields)) {
	?>
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
		<?php

}
?>
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
		<input type="hidden" name="pattr" value="<?php echo $pattr; ?>"/>
		<input type="hidden" name="city_ini" value="<?php echo $city_ini; ?>"/>
		<input type="hidden" name="add_ini" value="<?php echo $add_ini; ?>"/>
		<input type="hidden" name="city_fin" value="<?php echo $city_fin; ?>"/>
		<input type="hidden" name="add_fin" value="<?php echo $add_fin; ?>"/>
		<input type="hidden" name="add_vuelo" value="<?php echo $add_vuelo; ?>"/>
		<input type="hidden" name="valorPaquete" value="<?php echo $price['attrdata']; ?>"/>
		<input type="hidden" name="valorPaqueteUsuario" value="<?php echo $horasPaquete; ?>"/>
		<input type="hidden" name="CostCredit" value="<?php echo $costoConCredito; ?>"/>
        <input type="hidden" name="nameCar" id="nameCar" value="<?php echo $car['name']; ?>"/>
        <input type="hidden" name="idtar" value="<?php echo $price['id']; ?>"/>
         <input type="hidden" name="format" value="raw"/>
        <input type="hidden" name="horasPaqueteUsadas" value="<?php echo $horasPaquete; ?>"/>
        <input type="hidden" id="numpasangers" name="numpasangers" value="<?php echo $numpasangers; ?>"/>
<?php

		$user =& JFactory::getUser();
		$infocoupons= vikrentcar:: getCodeCouponClient($user->id);
        if(preg_match("/3/", $infocoupons['type'])) {
?>

		<input type="hidden" name="convenio" value="<?php echo $convenio; ?>"/>

<?php 

}
?>
		
		
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
        <input type="hidden" name="ishourly" value="1"/>	
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
		<input type="hidden" id="nextTask" name="task" value="saveorder"/>
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
				$okchargedisc = number_format($pay['charge'], 2);
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
		<div class="goback">
			<a href="javascript: void(0);" onclick="javascript: window.history.back();"><?php echo JText::_('VRBACK'); ?></a>
		</div>
		<?php



?>