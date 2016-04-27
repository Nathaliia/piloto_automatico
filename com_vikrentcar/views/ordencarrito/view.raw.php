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
jimport( 'joomla.methods' );

class VikrentcarViewOrdencarrito extends JView {
	
	
	
	function display($tpl = null) {

		
		
		$nameItemsCart = JRequest :: getString('itemsCart', '', 'request');
		$itemsPrice = JRequest :: getString('itemsPrice', '', 'request');
		$itemsQty = JRequest :: getString('itemsQty', '', 'request');
		$idcars = JRequest :: getString('idcars', '', 'request');
		$idtars = JRequest :: getString('tars', '', 'request');
		$hourly = JRequest :: getString('hourly', '', 'request');
		$idOrders = JRequest :: getString('idOrders', '', 'request');

		$ppickup = JRequest :: getString('pickup', '', 'request');
		$prelease = JRequest :: getString('release', '', 'request');
		$ValueCoupon = JRequest :: getString('ValueCoupon', '', 'request');
		$optionals = JRequest :: getString('optionals', '', 'request');
		$pidcars=json_decode($idcars, true);
		$pppickup= $ppickup;
		$pprelease= $prelease;
		$itemsppickup=json_decode($ppickup, true);
		$itmesprelease=json_decode($prelease, true);



		$itmesValueCoupon=json_decode($ValueCoupon, true);

		$itmesOptionals=json_decode($optionals, true);


		
		
		
		//Nuevas Variables
		$pattr = JRequest :: getString('pattr', '', 'request');
		$city_ini = JRequest :: getString('city_ini', '', 'request');
		$add_ini = JRequest :: getString('padd_ini', '', 'request');
		$city_fin = JRequest :: getString('city_fin', '', 'request');
		$add_fin = JRequest :: getString('padd_fin', '', 'request');	
       // $add_ini = JRequest :: getString('lugarReco', '', 'request');
		
		$numpasangers = JRequest :: getString('pasajeros', '', 'request');	
		
		//$itmesCart = JRequest :: getString('returnplace', '', 'request');
		//$ItemsCart = $this->itemsCart;
		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		
							$dbo = & JFactory :: getDBO();
							$q = "SELECT * FROM `#__vikrentcar_custfields` ORDER BY `#__vikrentcar_custfields`.`ordering` ASC;";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$cfields = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";
							//vikrentcar 1.6
				
				
					
						
						
						
							//pregunta por cupones
						if(vikrentcar::couponsEnabled()){
							$pcouponcode = JRequest :: getString('couponcode', '', 'request');
							

							if(strlen($pcouponcode)==0){
								$user =& JFactory::getUser();

								$coupon = vikrentcar::getCodeCouponClient($user->id);


								$pcouponcode =$coupon['code'];

								$pcoupontype =$coupon['type'];

								if((preg_match("/3/", $pcoupontype))) {

								

									$isconvenio = true;

								
									

									$this->assignRef('isconvenio', $isconvenio);
								

								}
								

							}
							$i=0;
							$arraycoupons;
							
							if(strlen($pcouponcode) > 0) {

								
								$coupon = vikrentcar::getCouponInfo($pcouponcode);
								foreach ($itemsppickup as $ppickup){
									$prelease= $itmesprelease[$i];
									$ppidcars=$pidcars[$i];
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
											if(!(preg_match("/;".$ppidcars.";/i", $coupon['idcars']))) {

											//if(!(preg_match("/;".$car[0]['id'].";/i", $coupon['idcars']))) {
												$couponcarok = false;
											}
										}
										if($couponcarok == true) {


											$this->assignRef('coupon', $coupon);

											
										}else {
											$nameCar  = vikrentcar::getCarInfo($ppidcars);
											showSelect (JText::sprintf('VRCCOUPONINVCAR',$coupon['code'], $nameCar['name']) );
										}
									}else {
										$nameCar  = vikrentcar::getCarInfo($ppidcars);

										showSelect (JText::sprintf('VRCCOUPONINVDATES',$coupon['code'], date($df, $ppickup). ' - '.date($df, $prelease), $nameCar['name'] , date($df,$dateparts[0]), date($df,$dateparts[1]) ) );
										
										
									}
								}else {
									showSelect (JText::_('VRCCOUPONNOTFOUND'));
								}

								$i++;


								}

								
							}

							}
							//
							$this->assignRef('itemsCart', $nameItemsCart);
							$this->assignRef('itemsPrice', $itemsPrice);
							$this->assignRef('itemsQty', $itemsQty);
							$this->assignRef('idcars', $idcars);
							$this->assignRef('tars', $idtars);
							$this->assignRef('hourly', $hourly);
							$this->assignRef('idorders', $idOrders);
							$this->assignRef('cfields', $cfields);

							$this->assignRef('pickup', $pppickup);
							$this->assignRef('release', $pprelease);
							$this->assignRef('optionals', $optionals);

							
							
							
							
							//Nuevas Variables
							$this->assignRef('pattr', $pattr);
							$this->assignRef('city_ini', $city_ini);
							$this->assignRef('add_ini', $add_ini);
							$this->assignRef('city_fin', $city_fin);
							$this->assignRef('add_fin', $add_fin);
							$this->assignRef('numpasangers', $numpasangers);


							//$this->assignRef('lugarReco', $lugarReco);
			
							//theme
							$theme = vikrentcar::getTheme();
							if($theme != 'default') {
								$thdir = JPATH_SITE.DS.'components'.DS.'com_vikrentcar'.DS.'themes'.DS.$theme.DS.'ordencarrito';
								if(is_dir($thdir)) {
									$this->_setPath('template', $thdir.DS);
								}
							}

							
							//
							parent :: display($tpl);
						
							
			
		
	}
}
?>