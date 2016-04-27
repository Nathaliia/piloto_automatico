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
if (is_array($tar)) {
	$prname = vikrentcar :: getPriceName($tar['idprice']);
	$isdue = vikrentcar :: sayCostPlusIva($tar['cost'], $tar['idprice']);
} else {
	$prname = "";
	$isdue = 0;
}
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
		$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
		$isdue += $locfeewith;
	}
}

//vikrentcar 1.6 coupon
$usedcoupon = false;
$origisdue = $isdue;
if(strlen($ord['coupon']) > 0) {
	$usedcoupon = true;
	$expcoupon = explode(";", $ord['coupon']);
	$isdue = $isdue - $expcoupon[1];
}
//

echo vikrentcar :: getFullFrontTitle();
?>
		<p class="successmade"><?php echo JText::_('VRORDEREDON'); ?> <?php echo date($df.' H:i', $ord['ts']); ?></p> 
				
		<div class="vrcvordudata">
			<p><span class="vrcvordudatatitle"><?php echo JText::_('VRPERSDETS'); ?>:</span> <?php echo nl2br($ord['custdata']); ?></p>		
		</div>
		
		<div class="vrcvordcarinfo">
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRCARRENTED'); ?>:</span> <?php echo $carinfo['name']; ?></p>
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRDAL'); ?></span> <?php echo date($df.' H:i', $ord['ritiro']); ?>  <?php

			$idpricesnotshowtime= vikrentcar::getidpricesnotshowtime();

			if((preg_match("/".$tar['idprice'].";/i", $idpricesnotshowtime))) {

			  
			}else{

				?>- <span class="vrcvordcarinfotitle"><?php echo JText::_('VRAL'); ?></span><?php echo date($df.' H:i', $ord['consegna']); 
			}
			?></p>
			<?php if(!empty($ord['idplace'])) { ?>
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRRITIROCAR'); ?>:</span> <?php echo vikrentcar::getPlaceName($ord['idplace']); ?></p>
			<?php } ?>
			<?php if(!empty($ord['idreturnplace'])) { ?>
			<p><span class="vrcvordcarinfotitle"><?php echo JText::_('VRRETURNCARORD'); ?>:</span> <?php echo vikrentcar::getPlaceName($ord['idreturnplace']); ?></p>
			<?php } ?>
		</div>
		
		<div class="vrcvordcosts">
			<?php 
			if(is_array($tar)){
			?>
			<p><span class="vrcvordcoststitle"><?php echo $prname; ?>:</span> <?php echo $currencysymb; ?> <?php echo vikrentcar::sayCostPlusIva($tar['cost'], $tar['idprice']); ?></p>
			<?php } ?>
			<?php if(strlen($optbought)){ ?>
			<p><span class="vrcvordcoststitle"><?php echo JText::_('VROPTS'); ?>:</span><div class="vrcvordcostsoptionals"><?php echo $optbought; ?></div></p>
			<?php } ?>
			<?php if($locfeewith) { ?>
			<p><span class="vrcvordcoststitle"><?php echo JText::_('VRLOCFEETOPAY'); ?>:</span> <?php echo $currencysymb; ?> <?php echo $locfeewith; ?></p>
			<?php } ?>
			<?php if($usedcoupon == true) { ?>
			<p><span class="vrcvordcoststitle"><?php echo JText::_('VRCCOUPON').' '.$expcoupon[2]; ?>:</span> - <?php echo $currencysymb; ?> <?php echo number_format($expcoupon[1], 2); ?></p>
			<?php } ?>
			<p class="vrcvordcoststot"><span class="vrcvordcoststitle"><?php echo JText::_('VRTOTAL'); ?>:</span> <?php echo $currencysymb; ?> <?php echo number_format($isdue, 2); ?></p>
		</div>
		<?php

if (@ is_array($payment) && intval($payment['shownotealw']) == 1) {
	if(strlen($payment['note']) > 0) {
		?>
		<div class="vrcvordpaynote">
		<?php
	}
	echo $payment['note'];
	if(strlen($payment['note']) > 0) {
		?>
		</div>
		<?php
	}
}
?>