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

class VikrentcarViewOrdencarrito extends JView {
	
	
	
	function display($tpl = null) {
		
		
		$nameItemsCart = JRequest :: getString('itemsCart', '', 'request');
		$itemsPrice = JRequest :: getString('itemsPrice', '', 'request');
		$itemsQty = JRequest :: getString('itemsQty', '', 'request');
		$idcars = JRequest :: getString('idcars', '', 'request');
		$idtars = JRequest :: getString('tars', '', 'request');
		$hourly = JRequest :: getString('hourly', '', 'request');
		$idOrders = JRequest :: getString('idorders', '', 'request');
		
		
		
		//$itmesCart = JRequest :: getString('returnplace', '', 'request');
		//$ItemsCart = $this->itemsCart;
		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		
		
				
				
					
						
						
						
							//pregunta por cupones
							$pcouponcode = JRequest :: getString('couponcode', '', 'request');
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
							$this->assignRef('itemsCart', $nameItemsCart);
							$this->assignRef('itemsPrice', $itemsPrice);
							$this->assignRef('itemsQty', $itemsQty);
							$this->assignRef('idcars', $idcars);
							$this->assignRef('tars', $idtars);
							$this->assignRef('hourly', $hourly);
							$this->assignRef('idorders', $idOrders);
							
							
			
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