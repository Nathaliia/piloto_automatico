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

$ord = $this->ord;
$tar = $this->tar;
$payment = $this->payment;
$format = $this->format;
$id_transaccion = $this->id_transaccion;
$saldo = $this->saldo;

$estarifazona=false;
if(is_numeric($tar['attrdata'])){

	$estarifazona=true;
}


//vikrentcar 1.6
$calcdays = $this->calcdays;
if(strlen($calcdays) > 0) {
	$origdays = $ord['days'];
	$ord['days'] = $calcdays;
}
//

$currencysymb = vikrentcar :: getCurrencySymb();
if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
	$df = 'd/m/Y';
} else {
	$df = 'Y/m/d';
}
$dbo = & JFactory :: getDBO();
$carinfo = vikrentcar :: getCarInfo($ord['idcar']);

if($tar['cost']!=$ord['totpaid']){

	$imp = vikrentcar :: sayCostMinusIva($ord['totpaid'], $tar['idprice']);
	$isdue = vikrentcar :: sayCostPlusIva($ord['totpaid'], $tar['idprice']);

}else{

	$imp = vikrentcar :: sayCostMinusIva($tar['cost'], $tar['idprice']);
	$isdue = vikrentcar :: sayCostPlusIva($tar['cost'], $tar['idprice']);
}




//$isdue = vikrentcar :: sayCostPlusIva($tar['cost'], $tar['idprice']);
if (!empty ($ord['optionals'])) {
	$stepo = explode(";", $ord['optionals']);
	foreach ($stepo as $one) {
		if (!empty ($one)) {
			$stept = explode(":", $one);
			$q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($stept[0]) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$actopt = $dbo->loadAssocList();
				$realcost = (intval($actopt[0]['perday']) == 1 ? ($actopt[0]['cost'] * $ord['days'] * $stept[1]) : ($actopt[0]['cost'] * $stept[1]));
				if (!empty ($actopt[0]['maxprice']) && $actopt[0]['maxprice'] > 0 && $realcost > $actopt[0]['maxprice']) {
					$realcost = $actopt[0]['maxprice'];
					if(intval($actopt[0]['hmany']) == 1 && intval($stept[1]) > 1) {
						$realcost = $actopt[0]['maxprice'] * $stept[1];
					}
				}
				$imp += vikrentcar :: sayOptionalsMinusIva($realcost, $actopt[0]['idiva']);
				$tmpopr = vikrentcar :: sayOptionalsPlusIva($realcost, $actopt[0]['idiva']);
				$isdue += $tmpopr;
				$optbought .= ($stept[1] > 1 ? $stept[1] . " " : "") . $actopt[0]['name'] . ": " . $currencysymb . " " . $tmpopr . "<br/>";
			}
		}
	}
}
if (!empty ($ord['idplace']) && !empty ($ord['idreturnplace'])) {
	$locfee = vikrentcar :: getLocFee($ord['idplace'], $ord['idreturnplace']);
	if ($locfee) {
		$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $ord['days']) : $locfee['cost'];
		$locfeewithout = vikrentcar :: sayLocFeeMinusIva($locfeecost, $locfee['idiva']);
		$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
		$imp += $locfeewithout;
		$isdue += $locfeewith;
	}
}
$tax = $isdue - $imp;

//vikrentcar 1.6 coupon
$usedcoupon = false;
$origisdue = $isdue;
if(strlen($ord['coupon']) > 0) {
	$usedcoupon = true;
	$expcoupon = explode(";", $ord['coupon']);
	$isdue = $isdue - $expcoupon[1];
}
//

//echo vikrentcar :: getFullFrontTitle();

?>
		<p class="warn"><?php echo JText::_('VRORDEREDON'); ?> <?php echo date($df.' H:i', $ord['ts']); ?> <?php echo JText::_('VRWAITINGPAYM'); ?></p> 
		
		<div class="vrcvordudata">
			<p><span class="vrcvordudatatitle"><?php echo JText::_('VRPERSDETS'); ?>:</span> <?php echo nl2br($ord['custdata']); ?></p>		
		</div>
		
		<div class="vrcvordcarinfo">
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRCARRENTED'); ?>:</span> <?php echo $carinfo['name']; ?></p>
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRDAL'); ?></span> <?php echo date($df.' H:i', $ord['ritiro']); ?> - <span class="vrcvordcarinfotitle"><?php $estarifazona?  '' : JText::_('VRAL'); ?></span> <?php  
			$idpricesnotshowtime= vikrentcar::getidpricesnotshowtime();

			if((preg_match("/".$tar['idprice'].";/i", $idpricesnotshowtime))) {
			//if(vikrentcar::gettimeflagprice($tar['idprice'])==1){

			  
			}else{
 				echo date($df.' H:i', $ord['consegna']); 
				
			}

			 ?></p>
			<?php if(!empty($ord['idplace'])) { ?>
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRRITIROCAR'); ?>:</span> <?php echo vikrentcar::getPlaceName($ord['idplace']); ?></p>
			<?php } ?>
			<?php if(!empty($ord['idreturnplace'])) { ?>
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRRETURNCARORD'); ?>:</span> <?php echo vikrentcar::getPlaceName($ord['idreturnplace']); ?></p>
			<?php } ?>
		</div>
		
		<div class="vrcvordcosts" <?php ($format=='raw')?"style='display:none'":''  ?>>
			<p><span class="vrcvordcoststitle"><?php echo vikrentcar::getPriceName($tar['idprice']); ?>:</span> <?php echo $currencysymb; ?> <?php if ($format=='raw'){echo vikrentcar::sayCostPlusIva($tar['cost'], $tar['idprice']);}else{echo $ord['totpaid'];} ?></p>
			<?php if(strlen($optbought)){ ?>
			<p><span class="vrcvordcoststitle"><?php echo JText::_('VROPTS'); ?>:</span><div class="vrcvordcostsoptionals"><?php echo $optbought; ?></div></p>
			<?php } ?>
			<?php if($locfeewith) { ?>
			<p><span class="vrcvordcoststitle"><?php echo JText::_('VRLOCFEETOPAY'); ?>:</span> <?php echo $currencysymb; ?> <?php echo $locfeewith; ?></p>
			<?php } ?>
			<?php if($usedcoupon == true) { ?>
			<p><span class="vrcvordcoststitle"><?php echo JText::_('VRCCOUPON').' '.$expcoupon[2]; ?>:</span> - <?php echo $currencysymb; ?> <?php echo number_format($expcoupon[1], 0); ?></p>
			<?php } ?>
			<p class="vrcvordcoststot"><span class="vrcvordcoststitle"><?php echo JText::_('VRTOTAL'); ?>:</span> <?php echo $currencysymb; ?> <?php if ($format=='raw'){echo number_format($isdue, 0);}else{echo number_format($ord['totpaid'], 0);} ?></p>
		</div>
		
		<?php

if (is_array($payment)) {
	require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_vikrentcar". DS . "payments" . DS . $payment['file']);
	$return_url = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $ord['sid'] . "&ts=" . $ord['ts'];
	$error_url = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $ord['sid'] . "&ts=" . $ord['ts'];
	$notify_url = JURI :: root() . "index.php?option=com_vikrentcar&task=notifypayment&sid=" . $ord['sid'] . "&ts=" . $ord['ts']."&tmpl=component";
	$transaction_name = vikrentcar :: getPaymentName();
	$leave_deposit = 0;
	$percentdeposit = "";
	$array_order = array ();
	$array_order['id'] =$ord['id'];
	$array_order['sid'] =$ord['sid'];
	$array_order['ts'] =$ord['ts'];
	$array_order['id_factura'] =$ord['id_factura'];
	$array_order['id_transaccion'] =$id_transaccion;

	$array_order['account_name'] = vikrentcar :: getPaypalAcc();
	$array_order['transaction_currency'] = vikrentcar :: getCurrencyCodePp();
	$array_order['vehicle_name'] = $carinfo['name'];
	$array_order['transaction_name'] = !empty ($transaction_name) ? $transaction_name : $carinfo['name'];
	//

	
	$array_order['currency_symb'] = $currencysymb;
	$array_order['net_price'] = $imp;
	$array_order['tax'] = $tax;
	$array_order['return_url'] = $return_url;
	$array_order['error_url'] = $error_url;
	$array_order['notify_url'] = $notify_url;
	$calcilo= $ord['order_total'];

	
	$array_order['total_to_pay'] = $ord['totpaid'];

		
	$array_order['order_total'] = $ord['totpaid'];
	
	//$array_order['total_to_pay'] = $isdue;
	//$array_order['total_to_pay'] = $ord['totpaid'];
	$array_order['total_net_price'] = $imp;
	$array_order['total_tax'] = $tax;
	$totalchanged = false;
	if ($payment['charge'] > 0.00) {
		$totalchanged = true;
		if($payment['ch_disc'] == 1) {
			//charge
			if($payment['val_pcent'] == 1) {
				//fixed value
				$array_order['total_net_price'] += $payment['charge'];
				$array_order['total_tax'] += $payment['charge'];
				$array_order['total_to_pay'] += $payment['charge'];
				$newtotaltopay = $array_order['total_to_pay'];
			}else {
				//percent value
				$percent_net = $array_order['total_net_price'] * $payment['charge'] / 100;
				$percent_tax = $array_order['total_tax'] * $payment['charge'] / 100;
				$percent_to_pay = $array_order['total_to_pay'] * $payment['charge'] / 100;
				$array_order['total_net_price'] += $percent_net;
				$array_order['total_tax'] += $percent_tax;
				$array_order['total_to_pay'] += $percent_to_pay;
				$newtotaltopay = $array_order['total_to_pay'];
			}
		}else {
			//discount
			if($payment['val_pcent'] == 1) {
				//fixed value
				$array_order['total_net_price'] -= $payment['charge'];
				$array_order['total_tax'] -= $payment['charge'];
				$array_order['total_to_pay'] -= $payment['charge'];
				$newtotaltopay = $array_order['total_to_pay'];
			}else {
				//percent value
				$percent_net = $array_order['total_net_price'] * $payment['charge'] / 100;
				$percent_tax = $array_order['total_tax'] * $payment['charge'] / 100;
				$percent_to_pay = $array_order['total_to_pay'] * $payment['charge'] / 100;
				$array_order['total_net_price'] -= $percent_net;
				$array_order['total_tax'] -= $percent_tax;
				$array_order['total_to_pay'] -= $percent_to_pay;
				$newtotaltopay = $array_order['total_to_pay'];
			}
		}
	}
	if (!vikrentcar :: payTotal()) {
		$percentdeposit = vikrentcar :: getAccPerCent();
		if ($percentdeposit > 0) {
			$leave_deposit = 1;
			$array_order['total_to_pay'] = $array_order['total_to_pay'] * $percentdeposit / 100;
			$array_order['total_net_price'] = $array_order['total_net_price'] * $percentdeposit / 100;
			//$array_order['total_tax'] = $tax * $percentdeposit / 100;
			$array_order['total_tax'] = ($array_order['total_to_pay'] - $array_order['total_net_price']);
		}
	}
	$array_order['leave_deposit'] = $leave_deposit;
	$array_order['percentdeposit'] = $percentdeposit;
	$array_order['payment_info'] = $payment;
	
	?>
	<div class="vrcvordpaybutton" >
	<?php	
	if($totalchanged) {
		$chdecimals = $payment['charge'] - (int)$payment['charge'];
		?>
		<p class="vrcpaymentchangetot" <?php ($format=='raw')?"style='display:none'":''  ?> >
		<?php echo $payment['name']; ?> 
		(<?php echo ($payment['ch_disc'] == 1 ? "+" : "-").($chdecimals > 0.00 ? $payment['charge'] : number_format($payment['charge'], 0))." ".($payment['val_pcent'] == 1 ? $currencysymb : "%"); ?>) 
		<span class="vrcorddiffpayment" <?php ($format=='raw')?"style='display:none'":''  ?>><?php echo $currencysymb; ?> <?php echo number_format($newtotaltopay, 0); ?></span>
		</p>
		<input type="hidden" name="newpaytotal" value="<?php echo $newtotaltopay;?>" />
		<input type="hidden" name="namePayment" value="<?php echo $payment['name'];;?>" />
		<input type="hidden" name="chargePayment" value="(<?php echo ($payment['ch_disc'] == 1 ? "+" : "-").($chdecimals > 0.00 ? $payment['charge'] : number_format($payment['charge'], 0))." ".($payment['val_pcent'] == 1 ? $currencysymb : "%"); ?>) " />
		
		<?php
	}
	$obj = new vikRentCarPayment($array_order);
	$obj->showPayment();
	?>
	</div>
	<?php
}
?>