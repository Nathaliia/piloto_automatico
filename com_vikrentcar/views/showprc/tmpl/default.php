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



$tars=$this->tars;


$car=$this->car;
$pickup=$this->pickup;
$release=$this->release;
$place=$this->place;

$pattr=$this->pattr;
$city_ini=$this->city_ini;
$add_ini=$this->add_ini;
$city_fin=$this->city_fin;
$add_fin=$this->add_fin;
$add_vuelo=$this->add_vuelo;
$price= $this->price;
$coupon=$this->coupon;
$format=$this->format;



$ifcouponclient=$this->ifcouponclient;






$numpasangers=$this->numpasangers;
$isconvenio=$this->isconvenio;




$nametarifasmapa= vikrentcar::getformsmapazones();

$arraymapaform= json_decode($nametarifasmapa);

foreach ($arraymapaform as $key => $value) {

	if($value==vikrentcar::getPriceName($tars[0]['idprice'])){

		$timeconfigmapa=1;
	}else{

		$timeconfigmapa=0;
	}



	
}

$idpricesnotshowtime= vikrentcar::getidpricesnotshowtime();


echo vikrentcar:: getFullFrontTitle();
 



$preturnplace = JRequest :: getString('returnplace', '', 'request');
//$carats=vikrentcar::getCarCarat($car['idcarat']);
$carats = vikrentcar :: getCarCaratFly($car['idcarat']);
$currencysymb = vikrentcar :: getCurrencySymb();
//$urlli =  JRoute::_('index.php?option=com_vikrentcar');
if (!empty ($car['idopt'])) {
	$optionals = vikrentcar :: getCarOptionals($car['idopt']);
}
$discl = vikrentcar :: getDisclaimer();
?>		<div id="datahtml">
		<form id="formShowprc" action="<?php echo JRoute::_('index.php?option=com_vikrentcar'); ?>" method="post" >
		<div class="car_container">

			<?php

			
			if(array_key_exists('hours', $tars[0])) {
				?>
				<p class="car_title"><span class="vrhword"><?php echo $car['name']; ?>  <?php 
				
			
				if ($numpasangers!=0){


					
					echo (intval($numpasangers) == 1 ? "1 ".JText::_('VRCPASANGER') : $numpasangers." ".JText::_('VRCPASANGERS')); 
					

				}else{

					

					if(!(preg_match("/".$tar['idprice'].";/i", $idpricesnotshowtime))) {
					//if(vikrentcar::gettimeflagprice($tars[0]['idprice'])==1){

						echo (intval($tars[0]['hours']) == 1 ? "1 ".JText::_('VRCHOUR') : $tars[0]['hours']." ".JText::_('VRCHOURS')); 
					}else{


					}

					
					
				}
			
				?></span></p>
				<input type="hidden" name="hourly" id="hourly" value="1"/>
				
				<?php
			}else {
				?>
				<p class="car_title"><span class="vrhword"><?php echo JText::_('VRRENTAL'); ?> <?php echo $car['name']; ?>  <?php 
				//if (!empty ($numpasangers)){
 
				//echo (intval($numpasangers) == 1 ? "1 ".JText::_('VRCPASANGER') : $numpasangers." ".JText::_('VRCPASANGERs')); 
				
				//}else{
				//
				if($pattr=='Credito'){
						if(intval($tars[0]['days'])==2){

							echo '30 '.JText::_('VRCHOURS');

						}
						if(intval($tars[0]['days'])==3){

							echo '40 '.JText::_('VRCHOURS');

						}
					
					}else{

						if ($numpasangers==0){
							
							if(!(preg_match("/".$tar['idprice'].";/i", $idpricesnotshowtime))) {

							//if(vikrentcar::gettimeflagprice($tars[0]['idprice'])==1){

								echo JText::_('VRFOR'); 

								echo (intval($tars[0]['days']) == 1 ? "1 ".JText::_('VRDAY') : $tars[0]['days']." ".JText::_('VRDAYS').$numpasangers); 
						

					
					}else{


					}

						
						}
						//echo (intval($tars[0]['hours']) == 1 ? "1 ".JText::_('VRCHOUR') : $tars[0]['hours']." ".JText::_('VRCHOURS')); 
					}

				
				
				//}
				?>
				</span></p><input type="hidden" name="hourly" id="hourly" value="0"/>
				<?php
			}
			?>
			<div class="car_img_box ajust">
				<img style='height: 100%; width: 100%; object-fit: contain' alt="<?php echo $car['name']; ?>" src="<?php echo JURI::root(); ?>administrator/components/com_vikrentcar/resources/<?php echo $car['img']; ?>"/>
				<?php
				if(strlen($car['moreimgs']) > 0) {
					JHTML::_('behavior.modal');
					$moreimages = explode(';;', $car['moreimgs']);
					?>
					<div class="car_moreimages">
						<?php
						foreach($moreimages as $mimg) {
							if(!empty($mimg)) {
								
								?>
                                
								<a href="<?php echo JURI::root(); ?>administrator/components/com_vikrentcar/resources/big_<?php echo $mimg; ?>" target="_blank" class="modal" ><img src="<?php echo JURI::root(); ?>administrator/components/com_vikrentcar/resources/thumb_<?php echo $mimg; ?>"/></a>
								<?php
							}
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="car_description_box">
				<?php echo $car['info']; ?>
			</div>
			<?php if (!empty($carats)) { ?>
			<br clear="all"/>
			<div class="car_carats">
				<?php echo $carats; ?>
			</div>
			<?php } ?>
		</div>
		
		
		
		
		<div class="car_prices" >
			<span class="vrhword"><?php echo JText::_('VRPRICE'); ?>:</span>
			<table>
            
			<?php 
			//capturo el id del usuario 
			$user =& JFactory::getUser();
			//$coupon= vikrentcar:: getCodeCouponClient($user->id);
		
			
			foreach($tars as $k=>$t){ 
			
				if(array_key_exists('hours',$t)){
					
					



					$precioconDescuento = vikrentcar::sayCostWithCredito($t['idprice'], $t['idcar'],$t['hours'], $t['cost'] );
					//$precioconDescuento = vikrentcar::sayCostPlusIva($t['cost'], $t['idprice']);

					
					$horasCredito=vikrentcar ::getValueCreditUserHours(vikrentcar ::verPaqueteUser(),$t['idprice'],$t['idcar']) ;
				}else{
					$precioconDescuento = vikrentcar::sayCostPlusIva($t['cost'],$t['idprice'] );
					$horasCredito=vikrentcar ::getValueCreditUserHours(vikrentcar ::verPaqueteUser(), $t['idprice'],$t['idcar']) ;
				}

				if(vikrentcar::couponsEnabled()){
				
					if (!empty($coupon)){
						
						$totdue=$precioconDescuento;
						
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
									$totdue = $totdue * $minuscoupon / 100;?>
									<input type="hidden" id="tipoCoupon"  value="<?php echo $coupon['percentot'] ?>"/>
	                                 <input type="hidden" id="descCoupon"  value="<?php echo $coupon['value']?>"/>
	                                <?php
								}else {
									//total value
									$couponsave = $coupon['value'];
									$totdue = $totdue - $coupon['value'];
									?>
	                                <input type="hidden" id="tipoCoupon"  value="<?php echo $coupon['percentot'] ?>"/>
	                                <input type="hidden" id="descCoupon"  value="<?php echo $coupon['value'] ?>"/>
									<?php
								}
							}else {
								?>
								<input type="hidden" id="tipoCoupon"  value="0"/>
	                            <input type="hidden" id="descCoupon"  value="0"/>
	                            <?php
								JError :: raiseWarning('', JText::_('VRCCOUPONINVMINTOTORD'));
							}
						}
					}
				}
				//$horasCredito=vikrentcar ::getValueCreditUserHours(vikrentcar ::verPaqueteUser(), $t['idprice'],$t['idcar']) ;
				
				
				
				?>
                
                
			
				<tr>
                <td><label for="pid<?php echo $t['idprice']; ?>">
				<?php 
				if ($numpasangers!=0){

					$pattrdata= vikrentcar::mapzones($t['attrdata']);

				}else{

					$pattrdata=$t['attrdata'];
				}
				
				
				
				
				 echo  ''/*vikrentcar::getPriceName($t['idprice'])*/."".$currencysymb." ".number_format(vikrentcar::sayCostPlusIva($t['cost'], $t['idprice'])). (strlen($t['attrdata']) ? "</br>".vikrentcar::getPriceAttr($t['idprice']).": ".$pattrdata : "").(((vikrentcar ::verPaqueteUser()!=0) && array_key_exists('hours', $tars[0])) ?"</br> Usted tiene un credito de:".vikrentcar ::verPaqueteUser()." horas "."</br>Total con Credito aplicado es de:  ".$currencysymb." ".number_format($precioconDescuento,0):"")."". (vikrentcar::couponsEnabled() && $ifcouponclient && $coupontotok ? "</br><strong>".JText::_('VRCCOUPONLABEL').": ".$currencysymb." ".number_format($totdue,0)."</strong>" : "") ?>
                </label>
                <label>
                <?php

					if($isconvenio){

						$user =& JFactory::getUser();
						$infocoupons= vikrentcar:: getCodeCouponClient($user->id);


						echo '</br><strong>Convenio: '.$infocoupons['code'].'<strong>';

						?>

						<input type="hidden" name="convenio" value="<?php echo $infocoupons["code"]; ?>"/>

						<?php

					}

                ?>
                 </label>

                </td>
                <td><input type="radio" name="priceid" id="pid<?php echo $t['idprice']; ?>" value="<?php echo $t['idprice']; ?>"<?php echo ($k==0 ? " checked=\"checked\"" : ""); ?>/></td>
                </tr>
				<input type="hidden" id="precio<?php echo $t['idprice']; ?>"  value="<?php echo $precioconDescuento; ?>"/>
				<input type="hidden" id="precioCoupon"  value="<?php echo $totdue; ?>"/>
				<input type="hidden" id="idtar<?php echo $t['idprice']; ?>"  value="<?php echo $t['id']; ?>"/>
			
			<?php } ?>
			</table>
		</div>
		
		<?php

if (!empty ($car['idopt']) && is_array($optionals)) {
?>
		<div class="car_options">
			<span class="vrhword"><?php echo JText::_('VRACCOPZ'); ?>:</span>
			<table cellspacing="0" cellpadding="0" width="90%">
			<?php

	foreach ($optionals as $k => $o) {
		$optcost = intval($o['perday']) == 1 ? ($o['cost'] * $tars[0]['days']) : $o['cost'];
		if (!empty ($o['maxprice']) && $o['maxprice'] > 0 && $optcost > $o['maxprice']) {
			$optcost = $o['maxprice'];
		}
		$optcost = $optcost * 1;
		//vikrentcar 1.6
		if(intval($o['forcesel']) == 1) {
			$forcedquan = 1;
			$forceperday = false;
			if(strlen($o['forceval']) > 0) {
				$forceparts = explode("-", $o['forceval']);
				$forcedquan = intval($forceparts[0]);
				$forceperday = intval($forceparts[1]) == 1 ? true : false;
			}
			$setoptquan = $forceperday == true ? $forcedquan * $tars[0]['days'] : $forcedquan;
			if(intval($o['hmany']) == 1) {
				$optquaninp = "<input type=\"hidden\" name=\"optid".$o['id']."\" value=\"".$setoptquan."\"/><span class=\"vrcoptionforcequant\"><small>x</small> ".$setoptquan."</span>";
			}else {
				$optquaninp = "<input type=\"hidden\" name=\"optid".$o['id']."\" value=\"".$setoptquan."\"/><span class=\"vrcoptionforcequant\"><small>x</small> ".$setoptquan."</span>";
			}
		}else {
			if(intval($o['hmany']) == 1) {
				$optquaninp = "<input class='inputopt' type=\"text\" name=\"optid".$o['id']."\" value=\"0\" size=\"1\"/>";
				$precioOpcion= vikrentcar::sayOptionalsPlusIva($optcost, $o['idiva']);
			}else {


				//se guarda precio de la opccion
				$precioOpcion= vikrentcar::sayOptionalsPlusIva($optcost, $o['idiva']);
				$optquaninp = "<input  type=\"checkbox\" name=\"optid".$o['id']."\" value=\"1\"/>"."<input type=\"hidden\" id=\"optid".$o['id']."\" value=\".$precioOpcion.\"  checked=false\"/>";
			    
			}
		}
		//
		//se guarda el precio de la opcion
		
		
		
		?>
		<input type="hidden" class='precioOptionales' id="precioOpcion<?php echo $o['id']; ?>" value="<?php echo $precioOpcion; ?>"/>
			
			<tr height="30px"><td><?php echo (!empty($o['img']) ? "<img class=\"maxthirty\" src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/".$o['img']."\" align=\"middle\" />" : "") ?></td><td><strong><?php echo $o['name']; ?></strong></td><td><strong><?php echo ($precioOpcion==0)?'':$currencysymb; ?> <?php echo(vikrentcar::sayOptionalsPlusIva($optcost, $o['idiva'])==0)?'':vikrentcar::sayOptionalsPlusIva($optcost, $o['idiva']); ?></strong></td><td align="center"><?php echo $optquaninp; ?></td></tr>
				<?php if(strlen(strip_tags(str_replace("&#160;", "", trim($o['descr']))))){ ?>
				<!--<tr><td colspan="4"><div class="vrcoptionaldescr"><?php echo $o['descr']; ?></div></td></tr>-->
				
				
				<?php } ?>
			<?php

	}
	?>
			</table>
		</div>
		<?php

}
?>
		<input type="hidden" name="place" value="<?php echo $place; ?>"/>
		<input type="hidden" name="returnplace" value="<?php echo $preturnplace; ?>"/>
        <input type="hidden" name="carid" value="<?php echo $car['id']; ?>"/>
		<input type="hidden" name="horas" value="<?php echo intval($tars[0]['hours']); ?>"/>
		
		<!-- <input type="hidden" name="days" value="<?php //echo (empty($pattr))?$tars[0]['days']:1; ?>"/>-->
  		<input type="hidden" name="days" value="<?php echo $tars[0]['days'];?>"/>
  		<input type="hidden" name="pickup" value="<?php echo $pickup; ?>"/>
  		<input type="hidden" name="release" value="<?php echo $release; ?>"/>
  		<input type="hidden" name="task" value="oconfirm"/>
		<input type="hidden" name="pattr" value="<?php echo $pattr; ?>"/>
		<input type="hidden" name="city_ini" value="<?php echo $city_ini; ?>"/>
		<input type="hidden" name="add_ini" value="<?php echo $add_ini; ?>"/>
		<input type="hidden" name="city_fin" value="<?php echo $city_fin; ?>"/>
		<input type="hidden" name="add_fin" value="<?php echo $add_fin; ?>"/>
		<input type="hidden" name="add_vuelo" value="<?php echo $add_vuelo; ?>"/>
        <input type="hidden" name="categoria" value="<?php echo $car['idcat']; ?>"/>
  		<input type="hidden" id="nextTask" name="nextTask" value="oconfirm"/>
  		<input type="hidden" id="numpasangers" name="numpasangers" value="<?php echo $numpasangers; ?>"/>
  		
  		<!-- se guarda el nombre del carro actual -->
  		<input type="hidden" name="nameCar" id="nameCar" value="<?php echo $car['name']; ?>"/>
  		
  		<?php

$pitemid = JRequest :: getInt('Itemid', '', 'request');
if (!empty ($pitemid)) {
?>
			<input type="hidden" name="Itemid" value="<?php echo $pitemid; ?>"/>
			<?php

}

				// chequea si el usuario tiene descuento para ser aplicados directamente
				//$user =& JFactory::getUser();
				//$coupon= vikrentcar:: getCodeCouponClient($user->id);
				
			if(vikrentcar::couponsEnabled()){
				if(!empty($coupon['code'])){?>
					
					<input type="hidden" name="couponcode" value="<?php echo $coupon['code']; ?>"/>
				<?php	
				} 
			}
		


?>
		<br clear="all">
		
  		<?php if(strlen($discl)){ ?>
			<div class="car_disclaimer"><?php echo $discl; ?></div>
		<?php } ?>
		
		<div class="car_buttons_box">
			<input type="submit" name="goon" id="goonRes" value="<?php echo JText::_('VRBOOKNOW'); ?>" class="booknow"/>
			            <?php
			//if($format!='raw'){
			?>
               <div id="previous" class="goback">

				<a href="javascript: void(0);" ><?php echo JText::_('VRBACK'); ?></a>
			
			   </div>
			<?php
			//}else{
			?>
             
			
			   
            <?php
			
			//}
			
			?>
            
           
		</div>
		
		</form>
        </div>
 <script>
     jQuery(document).ready(function() {

     	var mentooltip= "<?php echo JText::_('VRMSTOOLTIPOPT'); ?>"

         jQuery(".inputopt").tooltipster({
            theme: 'tooltipster-punk',
            position:'bottom',
            content: mentooltip
                                 
         });

     });

  </script>


		
		

