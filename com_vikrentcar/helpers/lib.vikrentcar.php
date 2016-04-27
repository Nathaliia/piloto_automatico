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
defined('_JEXEC') or die('Restricted access');
error_reporting(0);


/*if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_acymailing'.DS.'helpers'.DS.'helper.php')){
echo 'This code can not work without the AcyMailing Component';
return false;
}

$listMailClass = acymailing_get('class.listmail');

$listas= $listMailClass->getLists();


foreach ($listas as $key => $val) {

			
			define($val->name, $val->listid);

    		

    		
}*/

if (!function_exists('showSelect')) {
	function showSelect($err) {

		if (strlen($err)) {
				echo "<p class=\"err\">" . $err . "</p>";
		}

			//$app = JFactory::getApplication();
			//$app->redirect(JURI::root().'index.php?#setform',$err, 'error');
			
		
	
	}


}

class vikrentcar {


function formsearch(){


		if (vikrentcar :: allowRent()) {
			$dbo = & JFactory :: getDBO();
			//vikrentcar 1.5
			$calendartype = vikrentcar::calendarType();
			$document = & JFactory :: getDocument();
			//load jQuery lib e jQuery UI
			if(vikrentcar::loadJquery()) {
				$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery-1.8.2.min.js');
			}
			if($calendartype == "jqueryui") {
				$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/jquery-ui-1.9.0.custom.css');
				//load jQuery UI
				$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery-ui-1.9.0.custom.min.js');
			}
			//
			$ppickup = JRequest :: getInt('pickup', '', 'request');
			$preturn = JRequest :: getInt('return', '', 'request');
			$pitemid = JRequest :: getInt('Itemid', '', 'request');
			$pval = "";
			$rval = "";
			$vrcdateformat = vikrentcar::getDateFormat();
			if ($vrcdateformat == "%d/%m/%Y") {
				$df = 'd/m/Y';
			} else {
				$df = 'Y/m/d';
			}
			if (!empty ($ppickup)) {
				$dp = date($df, $ppickup);
				if (vikrentcar :: dateIsValid($dp)) {
					$pval = $dp;
				}
			}
			if (!empty ($preturn)) {
				$dr = date($df, $preturn);
				if (vikrentcar :: dateIsValid($dr)) {
					$rval = $dr;
				}
			}
			$coordsplaces = array();
			$selform = "<div class=\"vrcdivsearch\"><form action=\"".JRoute::_('index.php?option=com_vikrentcar')."\" method=\"get\"><table class=\"vrctsearch\">\n";
			$selform .= "<input type=\"hidden\" name=\"option\" value=\"com_vikrentcar\"/>\n";
			$selform .= "<input type=\"hidden\" name=\"task\" value=\"search\"/>\n";
			$diffopentime = false;
			if (vikrentcar :: showPlacesFront()) {
				$q = "SELECT * FROM `#__vikrentcar_places` ORDER BY `#__vikrentcar_places`.`name` ASC;";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() > 0) {
					$places = $dbo->loadAssocList();
					//check if some place has a different opening time (1.6)
					foreach ($places as $pla) {
						if(!empty($pla['opentime'])) {
							$diffopentime = true;
							break;
						}
					}
					$onchangeplaces = $diffopentime == true ? " onchange=\"javascript: vrcSetLocOpenTime(this.value, 'pickup');\"" : "";
					$onchangeplacesdrop = $diffopentime == true ? " onchange=\"javascript: vrcSetLocOpenTime(this.value, 'dropoff');\"" : "";
					if($diffopentime == true) {
						$onchangedecl = '
jQuery.noConflict();
function vrcSetLocOpenTime(loc, where) {
	jQuery.ajax({
		type: "POST",
		url: "'.JRoute::_('index.php?option=com_vikrentcar&task=ajaxlocopentime&tmpl=component').'",
		data: { idloc: loc, pickdrop: where }
	}).done(function(res) {
		var vrcobj = jQuery.parseJSON(res);
		if(where == "pickup") {
			jQuery("#vrccomselph").html(vrcobj.hours);
			jQuery("#vrccomselpm").html(vrcobj.minutes);
		}else {
			jQuery("#vrccomseldh").html(vrcobj.hours);
			jQuery("#vrccomseldm").html(vrcobj.minutes);
		}
	});
}';
						$document->addScriptDeclaration($onchangedecl);
					}
					//end check if some place has a different opningtime (1.6)
					$selform .= "<tr><td>&bull; " . JText :: _('VRPPLACE') . ": </td><td><select name=\"place\" id=\"place\"".$onchangeplaces.">";
					foreach ($places as $pla) {
						$selform .= "<option value=\"" . $pla['id'] . "\" id=\"place".$pla['id']."\">" . $pla['name'] . "</option>\n";
						if(!empty($pla['lat']) && !empty($pla['lng'])) {
							$coordsplaces[] = $pla;
						}
					}
					$selform .= "</select></td></tr>\n";
				}
			}
			
			if($diffopentime == true && is_array($places) && strlen($places[0]['opentime']) > 0) {
				$parts = explode("-", $places[0]['opentime']);
				if (is_array($parts) && $parts[0] != $parts[1]) {
					$opent = vikrentcar :: getHoursMinutes($parts[0]);
					$closet = vikrentcar :: getHoursMinutes($parts[1]);
					$i = $opent[0];
					$imin = $opent[1];
					$j = $closet[0];
				} else {
					$i = 0;
					$imin = 0;
					$j = 23;
				}
			}else {
				$timeopst = vikrentcar :: getTimeOpenStore();
				if (is_array($timeopst) && $timeopst[0] != $timeopst[1]) {
					$opent = vikrentcar :: getHoursMinutes($timeopst[0]);
					$closet = vikrentcar :: getHoursMinutes($timeopst[1]);
					$i = $opent[0];
					$imin = $opent[1];
					$j = $closet[0];
				} else {
					$i = 0;
					$imin = 0;
					$j = 23;
				}
			}
			$hours = "";
			while ($i <= $j) {
				if ($i < 10) {
					$i = "0" . $i;
				} else {
					$i = $i;
				}
				$hours .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
				$i++;
			}
			$minutes = "";
			for ($i = 0; $i < 60; $i += 15) {
				if ($i < 10) {
					$i = "0" . $i;
				} else {
					$i = $i;
				}
				$minutes .= "<option value=\"" . $i . "\"".((int)$i == $imin ? " selected=\"selected\"" : "").">" . $i . "</option>\n";
			}
			
			//vikrentcar 1.5
			if($calendartype == "jqueryui") {
				if ($vrcdateformat == "%d/%m/%Y") {
					$juidf = 'dd/mm/yy';
				}else {
					$juidf = 'yy/mm/dd';
				}
				//lang for jQuery UI Calendar
				$ldecl = '
jQuery(function($){'."\n".'
	$.datepicker.regional["vikrentcar"] = {'."\n".'
		closeText: "'.JText::_('VRCJQCALDONE').'",'."\n".'
		prevText: "'.JText::_('VRCJQCALPREV').'",'."\n".'
		nextText: "'.JText::_('VRCJQCALNEXT').'",'."\n".'
		currentText: "'.JText::_('VRCJQCALTODAY').'",'."\n".'
		monthNames: ["'.JText::_('VRMONTHONE').'","'.JText::_('VRMONTHTWO').'","'.JText::_('VRMONTHTHREE').'","'.JText::_('VRMONTHFOUR').'","'.JText::_('VRMONTHFIVE').'","'.JText::_('VRMONTHSIX').'","'.JText::_('VRMONTHSEVEN').'","'.JText::_('VRMONTHEIGHT').'","'.JText::_('VRMONTHNINE').'","'.JText::_('VRMONTHTEN').'","'.JText::_('VRMONTHELEVEN').'","'.JText::_('VRMONTHTWELVE').'"],'."\n".'
		monthNamesShort: ["'.substr(JText::_('VRMONTHONE'), 0, 3).'","'.substr(JText::_('VRMONTHTWO'), 0, 3).'","'.substr(JText::_('VRMONTHTHREE'), 0, 3).'","'.substr(JText::_('VRMONTHFOUR'), 0, 3).'","'.substr(JText::_('VRMONTHFIVE'), 0, 3).'","'.substr(JText::_('VRMONTHSIX'), 0, 3).'","'.substr(JText::_('VRMONTHSEVEN'), 0, 3).'","'.substr(JText::_('VRMONTHEIGHT'), 0, 3).'","'.substr(JText::_('VRMONTHNINE'), 0, 3).'","'.substr(JText::_('VRMONTHTEN'), 0, 3).'","'.substr(JText::_('VRMONTHELEVEN'), 0, 3).'","'.substr(JText::_('VRMONTHTWELVE'), 0, 3).'"],'."\n".'
		dayNames: ["'.JText::_('VRCJQCALSUN').'", "'.JText::_('VRCJQCALMON').'", "'.JText::_('VRCJQCALTUE').'", "'.JText::_('VRCJQCALWED').'", "'.JText::_('VRCJQCALTHU').'", "'.JText::_('VRCJQCALFRI').'", "'.JText::_('VRCJQCALSAT').'"],'."\n".'
		dayNamesShort: ["'.substr(JText::_('VRCJQCALSUN'), 0, 3).'", "'.substr(JText::_('VRCJQCALMON'), 0, 3).'", "'.substr(JText::_('VRCJQCALTUE'), 0, 3).'", "'.substr(JText::_('VRCJQCALWED'), 0, 3).'", "'.substr(JText::_('VRCJQCALTHU'), 0, 3).'", "'.substr(JText::_('VRCJQCALFRI'), 0, 3).'", "'.substr(JText::_('VRCJQCALSAT'), 0, 3).'"],'."\n".'
		dayNamesMin: ["'.substr(JText::_('VRCJQCALSUN'), 0, 2).'", "'.substr(JText::_('VRCJQCALMON'), 0, 2).'", "'.substr(JText::_('VRCJQCALTUE'), 0, 2).'", "'.substr(JText::_('VRCJQCALWED'), 0, 2).'", "'.substr(JText::_('VRCJQCALTHU'), 0, 2).'", "'.substr(JText::_('VRCJQCALFRI'), 0, 2).'", "'.substr(JText::_('VRCJQCALSAT'), 0, 2).'"],'."\n".'
		weekHeader: "'.JText::_('VRCJQCALWKHEADER').'",'."\n".'
		dateFormat: "'.$juidf.'",'."\n".'
		firstDay: 1,'."\n".'
		isRTL: false,'."\n".'
		showMonthAfterYear: false,'."\n".'
		yearSuffix: ""'."\n".'
	};'."\n".'
	$.datepicker.setDefaults($.datepicker.regional["vikrentcar"]);'."\n".'
});';
				$document->addScriptDeclaration($ldecl);
				//
				$sdecl = "
jQuery.noConflict();
jQuery(function(){
	jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ '' ] );
	jQuery('#pickupdate').datepicker({
		showOn: 'both',
		buttonImage: '".JURI::root()."components/com_vikrentcar/resources/images/calendar.png',
		buttonImageOnly: true,
		onSelect: function( selectedDate ) {
			jQuery('#releasedate').datepicker( 'option', 'minDate', selectedDate );
		}
	});
	jQuery('#pickupdate').datepicker( 'option', 'dateFormat', '".$juidf."');
	jQuery('#pickupdate').datepicker( 'option', 'minDate', '0d');
	jQuery('#releasedate').datepicker({
		showOn: 'both',
		buttonImage: '".JURI::root()."components/com_vikrentcar/resources/images/calendar.png',
		buttonImageOnly: true,
		onSelect: function( selectedDate ) {
			jQuery('#pickupdate').datepicker( 'option', 'maxDate', selectedDate );
		}
	});
	jQuery('#releasedate').datepicker( 'option', 'dateFormat', '".$juidf."');
	jQuery('#releasedate').datepicker( 'option', 'minDate', '0d');
	jQuery('#pickupdate').datepicker( 'option', jQuery.datepicker.regional[ 'vikrentcar' ] );
	jQuery('#releasedate').datepicker( 'option', jQuery.datepicker.regional[ 'vikrentcar' ] );
});";
				$document->addScriptDeclaration($sdecl);
				$selform .= "<tr><td>&bull; " . JText :: _('VRPICKUPCAR') . ": </td><td><input type=\"text\" name=\"pickupdate\" id=\"pickupdate\" size=\"10\"/> " . JText :: _('VRALLE') . " <span id=\"vrccomselph\"><select name=\"pickuph\">" . $hours . "</select></span> : <span id=\"vrccomselpm\"><select name=\"pickupm\">" . $minutes . "</select></span></td></tr>\n";
				$selform .= "<tr><td>&bull; " . JText :: _('VRRETURNCAR') . ": </td><td><input type=\"text\" name=\"releasedate\" id=\"releasedate\" size=\"10\"/> " . JText :: _('VRALLE') . " <span id=\"vrccomseldh\"><select name=\"releaseh\">" . $hours . "</select></span> : <span id=\"vrccomseldm\"><select name=\"releasem\">" . $minutes . "</select></span></td></tr>";
			}else {
				//default Joomla Calendar
				JHTML :: _('behavior.calendar');
				$selform .= "<tr><td>&bull; " . JText :: _('VRPICKUPCAR') . ": </td><td>" . JHTML :: _('calendar', '', 'pickupdate', 'pickupdate', $vrcdateformat, array (
					'class' => '',
					'size' => '10',
					'maxlength' => '19'
				)) . " " . JText :: _('VRALLE') . " <span id=\"vrccomselph\"><select name=\"pickuph\">" . $hours . "</select></span> : <span id=\"vrccomselpm\"><select name=\"pickupm\">" . $minutes . "</select></span></td></tr>\n";
				$selform .= "<tr><td>&bull; " . JText :: _('VRRETURNCAR') . ": </td><td>" . JHTML :: _('calendar', '', 'releasedate', 'releasedate', $vrcdateformat, array (
					'class' => '',
					'size' => '10',
					'maxlength' => '19'
				)) . " " . JText :: _('VRALLE') . " <span id=\"vrccomseldh\"><select name=\"releaseh\">" . $hours . "</select></span> : <span id=\"vrccomseldm\"><select name=\"releasem\">" . $minutes . "</select></span></td></tr>";
			}
			//
			if (@ is_array($places)) {
				$selform .= "<tr><td>&bull; " . JText :: _('VRRETURNCARORD') . ": </td><td><select name=\"returnplace\" id=\"returnplace\"".(strlen($onchangeplacesdrop) > 0 ? $onchangeplacesdrop : "").">";
				foreach ($places as $pla) {
					$selform .= "<option value=\"" . $pla['id'] . "\" id=\"returnplace".$pla['id']."\">" . $pla['name'] . "</option>\n";
				}
				$selform .= "</select></td></tr>\n";
			}
			if (vikrentcar :: showCategoriesFront()) {
				$q = "SELECT * FROM `#__vikrentcar_categories` ORDER BY `#__vikrentcar_categories`.`name` ASC;";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() > 0) {
					$categories = $dbo->loadAssocList();
					$selform .= "<tr><td>&bull; " . JText :: _('VRCARCAT') . ": </td><td><select name=\"categories\">";
					$selform .= "<option value=\"all\">" . JText :: _('VRALLCAT') . "</option>\n";
					foreach ($categories as $cat) {
						$selform .= "<option value=\"" . $cat['id'] . "\">" . $cat['name'] . "</option>\n";
					}
					$selform .= "</select></td></tr>\n";
				}
			}
			$selform .= "<tr><td></td><td><input type=\"submit\" name=\"search\" value=\"" . vikrentcar :: getSubmitName() . "\"" . (strlen(vikrentcar :: getSubmitClass()) ? " class=\"" . vikrentcar :: getSubmitClass() . "\"" : "") . "/></td></tr>\n";
			$selform .= "</table>\n";
			$selform .= (!empty ($pitemid) ? "<input type=\"hidden\" name=\"Itemid\" value=\"" . $pitemid . "\"/>" : "") . "</form></div>";
			//locations on google map
			if(count($coordsplaces) > 0) {
				JHTML::_('behavior.modal');
				$selform = '<div class="vrclocationsbox"><div class="vrclocationsmapdiv"><a href="'.JURI::root().'index.php?option=com_vikrentcar&view=locationsmap&tmpl=component" rel="{handler: \'iframe\', size: {x: 750, y: 600}}" class="modal" target="_blank">'.JText::_('VRCLOCATIONSMAP').'</a></div></div>'.$selform;
			}
			//
			echo vikrentcar :: getFullFrontTitle();
			echo vikrentcar :: getIntroMain();
			if (strlen($err)) {
				echo "<p class=\"err\">" . $err . "</p>";
			}
			echo $selform;
			echo vikrentcar :: getClosingMain();
			//echo javascript to fill the date values
			if (!empty ($pval) && !empty ($rval)) {
				if($calendartype == "jqueryui") {
					?>
					<script language="JavaScript" type="text/javascript">
					jQuery.noConflict();
					jQuery(function(){
						jQuery('#pickupdate').val('<?php echo $pval; ?>');
						jQuery('#releasedate').val('<?php echo $rval; ?>');
					});
					</script>
					<?php
				}else {
					?>
					<script language="JavaScript" type="text/javascript">
					document.getElementById('pickupdate').value='<?php echo $pval; ?>';
					document.getElementById('releasedate').value='<?php echo $rval; ?>';
					</script>
					<?php
				}
			}
		
		} else {
			echo vikrentcar :: getDisabledRentMsg();
		}




}


function saveSaldo($newSaldo, $idUser, $savesaldo=true){

	$dbo= JFactory::getDBO();

	if($savesaldo){

		if($newSaldo<0){

			$newSaldo=0;

		}

	$q="UPDATE  `#__vikrentcar_profiles` SET  `saldo`  ='".$newSaldo."' WHERE `user_id`='". $idUser."';";  
	$dbo->setQuery($q);
	$dbo->Query($q);
	}else{

	$q="UPDATE  `#__vikrentcar_profiles` SET  `saldo_paq`  ='".$newSaldo."' WHERE `user_id`='". $idUser."';";  
	$dbo->setQuery($q);
	$dbo->Query($q);


	}


}


function savePaqueteHoras($order, $hours, $valorequivalenteMoney){

	$dbo= JFactory::getDBO();

	$config =& JFactory::getConfig();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
	$nowts  = $dateNow->getTimestamp();

	$valorsegSemana=604800;



	$until=$order[0]['ritiro'] +$valorsegSemana;


	

	$q="INSERT INTO #__vikrentcar_paquetes( id_user , id_order, valor_paquete ,fecha_ini, fecha_final ,until, horas) VALUES(".$order[0]['ujid']." , ".$order[0]['id']."  ,". $valorequivalenteMoney."  ,".$order[0]['ritiro']."  ,".$order[0]['consegna'].", ".$until.", ".$hours." );";
	$dbo->setQuery($q);
	$dbo->Query($q);
	

	if($dbo->getAffectedRows() !=0){

		return true;


	}else{

		return false;

	}





}


function getSaldoUser($userid, $getsaldo=true){

	$dbo= JFactory::getDBO();

	//$dato =vikrentcar::evalDisponibilidad();
	if($getsaldo){
	
			$q = "SELECT `saldo` FROM `#__vikrentcar_profiles` WHERE `user_id`='" . $dbo->getEscaped($userid) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$saldo = $dbo->loadResult();


			return $saldo;
	}else{

			$q = "SELECT `saldo_paq` FROM `#__vikrentcar_profiles` WHERE `user_id`='" . $dbo->getEscaped($userid) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$saldo = $dbo->loadResult();

			return $saldo;



	}


}

function borrarPaquetesvencidos($id_user){



	$dbo= JFactory::getDBO();
	$config =& JFactory::getConfig();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
	$nowts  = $dateNow->getTimestamp();



	//SELECCIONO EL SALDO TOTAL Y EL SALDO DEL USAURIO EN  PAQUETES QUE ESTA VENCIDO
	//este select obtiene la suma del saldo vencido

	$q="SELECT  SUM(p.valor_paquete) as total , pro.saldo_paq as saldo_paquete ,  SUM(p.val_gastado) as total_gastado, p.id as id FROM `#__vikrentcar_paquetes` as p INNER JOIN `#__vikrentcar_profiles` as pro ON p.id_user=pro.user_id WHERE p.id_user=".$id_user. " AND p.until<".$nowts;
	$dbo->setQuery($q);
	$dbo->Query($q);

	



	if($dbo->getNumRows() > 0){

		$datos = $dbo->loadAssocList();

	

		if($datos[0]['total']==NULL && $datos[0]['total_gastado']==NULL && $datos[0]['saldo_paquete']==NULL){

			return false;


		}



	
		$subSaldo= $datos[0]['total']- $datos[0]['total_gastado'];

		$newSaldo=$datos[0]['saldo_paquete']- $subSaldo;

		if($newSaldo<0){

			$newSaldo=0;
		}

		//se guardan paquetes vencidos en tabla de paquetes vencidos

		$q="INSERT #__vikrentcar_paquetes_vencidos(`id`,`id_user`,`id_order`,`valor_paquete`,`fecha_ini`,`fecha_final`,`until`,`horas`,`val_gastado`)  SELECT `id`,`id_user`,`id_order`,`valor_paquete`,`fecha_ini`,`fecha_final`,`until`,`horas`,`val_gastado` FROM #__vikrentcar_paquetes WHERE id_user=".$id_user. " AND until<".$nowts;;
	   
		
	    $dbo->setQuery($q);
		$dbo->Query($q);
		
		

		$q="UPDATE  `#__vikrentcar_profiles` SET  `saldo_paq`  ='".$newSaldo."' WHERE `user_id`='". $id_user."';";  



		$dbo->setQuery($q);
		$dbo->Query($q);




		

		//if($dbo->getAffectedRows() !=0){

			$q="DELETE   FROM `#__vikrentcar_paquetes`   WHERE id_user=".$id_user. " AND until<".$nowts;



			$dbo->setQuery($q);
			$dbo->Query($q);

			if($dbo->getAffectedRows() !=0){

			return true;

		

			}

		//}

		

	}else{

		return false;
	}




}


function getSaldoPaquetes($id_user, $cat){


	self:: borrarPaquetesvencidos($id_user);



	$dbo= JFactory::getDBO();
	$config =& JFactory::getConfig();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
	$nowts  = $dateNow->getTimestamp();
    $q="SELECT  SUM( p.valor_paquete ) as total, p.horas as horas, p.id as id FROM `#__vikrentcar_paquetes` as p  INNER JOIN `#__vikrentcar_orders` as o ON p.id_order=o.id INNER JOIN `#__vikrentcar_cars` as c ON c.id=o.idcar WHERE p.id_user='".$id_user."' AND p.until>=".$nowts." AND p.fecha_ini<".$nowts."  AND c.idcat=".$cat;

		
	
		
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() > 0) {

			$rows = $dbo->loadAssocList();
	
			return $rows[0]['id'];

		}else{

			
			return 0;
		}



}

function aplycableSaldo($id_user, $cat){



	$dbo= JFactory::getDBO();
	$config =& JFactory::getConfig();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
	$nowts  = $dateNow->getTimestamp();
    $q="SELECT  SUM( p.valor_paquete ) as total, p.horas as horas FROM `#__vikrentcar_paquetes` as p  INNER JOIN `#__vikrentcar_orders` as o ON p.id_order=o.id INNER JOIN `#__vikrentcar_cars` as c ON c.id=o.idcar WHERE p.id_user='".$id_user."' AND p.until>=".$nowts." AND p.ritiro<".$nowts."  AND c.idcat=".$cat;

		
	
		
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {

			$rows = $dbo->loadAssocList();
	
			return $rows[0]['horas'];

		}

	


		if ($dbo->getNumRows() ==0) {

			
			
			return 0;
		}



}

function getMoneyEquivalenteHoras($hours, $idcar, $nombreattr){

	$dbo= JFactory::getDBO();

	//extraigo numero de horas guardado en la cadena
	$numerohoras = ereg_replace("[^0-9]", "", $nombreattr);



		

		$q = "SELECT * FROM `#__vikrentcar_dispcosthours_master` WHERE `idcar`='" .$idcar . "' AND hours=1;";


	

	$dbo->setQuery($q);
	$dbo->Query($q);
	$tar = $dbo->loadAssocList();

	

	return $tar[0]['cost']*$numerohoras;



}


function convertMoneyToHours($money, $idcar, $nombreattr){

	$dbo= JFactory::getDBO();

	//extraigo numero de horas guardado en la cadena
	$numerohoras = ereg_replace("[^0-9]", "", $nombreattr);



		

		$q = "SELECT * FROM `#__vikrentcar_dispcosthours_master` WHERE `idcar`='" .$idcar . "' AND hours=1;";


	

	$dbo->setQuery($q);
	$dbo->Query($q);
	$tar = $dbo->loadAssocList();

	

	return $money/$tar[0]['cost'];



}

function saveCredito($idorder, $userid, $concepto, $ifSaveCredito=true){

	$config =& JFactory::getConfig();
	$dbo= JFactory::getDBO();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

	$mysqlDateTime = $dateNow->format(DateTime::ISO8601);

	if($ifSaveCredito){


	$q="SELECT * FROM `#__vikrentcar_orders` WHERE `id`='".$idorder."';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	if ($dbo->getNumRows() == 1) {

		$rows = $dbo->loadAssocList();

		$q="INSERT INTO `#__vikrentcar_payments_users` (`fecha`,`concepto`,`credito`,`id_user`) VALUES('".$mysqlDateTime."','".$concepto."','".$rows[0]['totpaid']."','".$userid."');";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$lid = $dbo->insertid();



	}

	

	}else{


		$q="SELECT SUM( p.valor_paquete ) as total FROM `#__vikrentcar_paquetes` as p WHERE `id_order`='".$idorder."' AND id_user=".$userid.";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {

			$rows = $dbo->loadAssocList();

			$q="INSERT INTO `#__vikrentcar_payments_users` (`fecha`,`concepto`,`credito`,`id_user`) VALUES('".$mysqlDateTime."','".$concepto."','".$rows[0]['total']."','".$userid."');";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$lid = $dbo->insertid();

		}

	


	}

	
	

	return $lid;



}

function saveDebito($idorder, $userid, $concepto, $saveDebitoPaq=false, $idpaquete=null){

	$config =& JFactory::getConfig();
	$dbo= JFactory::getDBO();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

	$mysqlDateTime = $dateNow->format(DateTime::ISO8601);

	

	 



	$q="SELECT * FROM `#__vikrentcar_orders` WHERE `id`='".$idorder."';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	if ($dbo->getNumRows() == 1) {

		$rows = $dbo->loadAssocList();

	}

	
	$q="INSERT INTO `#__vikrentcar_payments_users` (`fecha`,`concepto`,`debito`,`id_user`) VALUES('".$mysqlDateTime."','".$concepto."','".$rows[0]['totpaid']."','".$userid."');";
	$dbo->setQuery($q);
	$dbo->Query($q);
	$lid = $dbo->insertid();

	if($saveDebitoPaq){

		$q="SELECT  `val_gastado` FROM `#__vikrentcar_paquetes`  WHERE `id`='". $idpaquete."';";  
		$dbo->setQuery($q);
		$dbo->Query($q);
		$gastado = $dbo->loadResult();

		$gastado =$gastado +$rows[0]['totpaid'];

		$q="UPDATE  `#__vikrentcar_paquetes` SET  `val_gastado`  ='".$gastado."' WHERE `id`='". $idpaquete."';";  
		$dbo->setQuery($q);
		$dbo->Query($q);

		$q="UPDATE  `#__vikrentcar_orders` SET  `id_paq`  ='".$idpaquete."' WHERE `id`='". $idorder."';";  
		$dbo->setQuery($q);
		$dbo->Query($q);


	}

	return $lid;






	



}

function calcularNuevoSaldo($idorder, $userid, $lid, $ifGetSaldo=true){

	$dbo= JFactory::getDBO();
	//obtiene saldo actul
	
		$saldo= self::getSaldoUser($userid, $ifGetSaldo);
	

	$q="SELECT * FROM `#__vikrentcar_payments_users` WHERE `id`='".$lid."';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	if ($dbo->getNumRows() == 1) {

		$dpay = $dbo->loadAssocList();

	}

	$newSaldo= $saldo+$dpay[0]['credito']-$dpay[0]['debito'];

	return $newSaldo;

}


	function sincronizarusers(){




		
	}
	
	function addJoomlaUser($name, $username, $email, $password) {
		jimport('joomla.version');
		$version = new JVersion();
		$jv=$version->getShortVersion();
		if(version_compare($jv, '1.6.0') < 0) {
			//Joomla 1.5
			jimport('joomla.user.helper');
			$user = clone(JFactory::getUser(0));
			$config=& JFactory::getConfig();
			$authorize=& JFactory::getACL();
			$document =& JFactory::getDocument();
			$newUsertype = 'Registered';
			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword($password, $salt);
			$password = $crypt.':'.$salt;
			$user->set('id', null);
			$user->set('name',$name);
			$user->set('username',$username);
			$user->set('password',$password);
			$user->set('password2',$password);
			$user->set('email',$email);
			$user->set('usertype',$newUsertype);
			$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
			$date =& JFactory::getDate();
			$user->set('registerDate', $date->toMySQL());
			if ( !$user->save() ){
				JError::raiseWarning('', JText::_( $user->getError()));
				return false;
			}
			$user =& JFactory::getUser($username);
			return $user->get('id') ;
		}else {
			//Joomla 1.6 or 1.7 or 2.5
			$data=array();
			$dbo = & JFactory :: getDBO();
			//se cambia para que el registro rapido quede en el grupo de clientes regulares
			//$q="SELECT `id` FROM `#__usergroups` WHERE `title` LIKE '%registered%' OR `title` LIKE '%clientes regulares%';";
			$q="SELECT `id` FROM `#__usergroups` WHERE `title` LIKE '%clientes regulares%';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$getgroup=$dbo->loadAssocList();
			foreach ($getgroup as $gtg) {
				$data['groups'][]=$gtg['id'];
			}
			$data['name']=$name;
			$data['username']=$username;
			$data['email']=$email;
			$data['password']=$password;
			$user = new JUser;
			if ($user->bind($data)) {
				if ( $user->save() ){
					$newuserid=$user->id;
					return $newuserid;
				}else {
					return false;
				}
			}else {
				return false;
			}
		}
	}
	
	function userIsLogged () {
		$user =& JFactory::getUser();
		if ($user->guest) {
			return false;
		}else {
			return true;
		}
	}
	
	function getTheme () {
		$dbo = & JFactory :: getDBO();
		$q="SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='theme';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s=$dbo->loadAssocList();
		return $s[0]['setting'];
	}
	
	function getFooterOrdMail() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='footerordmail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}
	
	function requireLogin() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='requirelogin';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return intval($s[0]['setting']) == 1 ? true : false;
	}
	
	function isPaquete($kk){
		$dbo = & JFactory :: getDBO();
		$queryPaq = "SELECT `paq` FROM `#__vikrentcar_cars` WHERE `id`='" . $kk . "';";
		$dbo->setQuery($queryPaq);
		$dbo->Query($queryPaq);
		$flagPaq = $dbo->loadAssocList();
		return $flagPaq[0]['paq'];
	}
	function couponsEnabled() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='enablecoupons';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return intval($s[0]['setting']) == 1 ? true : false;
	}
	
	
	function applyExtraHoursChargesBasp() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ehourschbasp';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		//true is before special prices, false is after
		return intval($s[0]['setting']) == 1 ? true : false;
	}
	
function getCreditoOrder($order){

	if(is_array($order)){


	}

		$dbo = & JFactory :: getDBO();
		$q = "SELECT `credito` FROM `#__vikrentcar_creditos` WHERE `idorder`='".$order. "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		//true is before special prices, false is after
		return $s[0]['credito'];


}

	function verPaqueteUser(){
		//jimport('joomla.version');
	
		$user =& JFactory::getUser();
	
		if (!$user->guest) {
	
			$usuario=  $user->id;
		}
	
		$creditValue = vikrentcar :: getCreditoUser($usuario);
	
		if(isset($creditValue)){
	
			return $creditValue;
		}else{
			return 0;
		}
	
	
	}
	
	function loadJquery($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='loadjquery';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$s = $dbo->loadAssocList();
			return intval($s[0]['setting']) == 1 ? true : false;
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('loadJquery', '');
			if(!empty($sval)) {
				return intval($sval) == 1 ? true : false;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='loadjquery';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$s = $dbo->loadAssocList();
				$session->set('loadJquery', $s[0]['setting']);
				return intval($s[0]['setting']) == 1 ? true : false;
			}
		}
	}
	
	function calendarType($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='calendar';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$s = $dbo->loadAssocList();
			return $s[0]['setting'];
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('calendarType', '');
			if(!empty($sval)) {
				return $sval;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='calendar';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$s = $dbo->loadAssocList();
				$session->set('calendarType', $s[0]['setting']);
				return $s[0]['setting'];
			}
		}
	}
	
	function getSiteLogo() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}
	
	
	
	function getCodeCouponClient($juser){
		
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_coupons` WHERE `juser`="."'".$juser."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		
		if ($dbo->getNumRows() > 0) {
			$n = $dbo->loadAssocList();
			return $n[0];
		}else{
		
		return "";
		}
		
		
	}
	
	function getDataClientFromProfile($juser){
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_profiles` WHERE `user_id`="."'".$juser."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		
		if ($dbo->getNumRows() > 0) {
			$n = $dbo->loadAssocList();
			return $n[0];
		}else{
		
		return "";
		}
		
		
	}
	
	function numCalendars() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='numcalendars';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}
	
	function showPartlyReserved() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='showpartlyreserved';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return intval($s[0]['setting']) == 1 ? true : false;
	}

	function getDisclaimer() {

		$dbo = & JFactory :: getDBO();

		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='disclaimer';";

		$dbo->setQuery($q);

		$dbo->Query($q);

		$ft = $dbo->loadAssocList();

		$lang = JFactory::getLanguage();

		$code_lang= $lang->getTag();

		$porciones = explode("$$", $ft[0]['setting']);

		
		if(sizeof($porciones)>1){

			if($code_lang=='es-ES'){
				$avisolegal= $porciones[0];

			}else{
				$avisolegal= $porciones[1];

			}
		}else{

			$avisolegal=$ft[0]['setting'];
		}

		

		return $avisolegal;

	}

	function showFooter() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='showfooter';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$s = $dbo->loadAssocList();
			return (intval($s[0]['setting']) == 1 ? true : false);
		} else {
			return false;
		}
	}

	function getPriceName($idp) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`name` FROM `#__vikrentcar_prices` WHERE `id`='" . $idp . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$n = $dbo->loadAssocList();
			return $n[0]['name'];
		}
		return "";
	}
	
	function getTarInfo($idp,$hourly) {
		$dbo = & JFactory :: getDBO();
		if($hourly=='1'){
		$q = "SELECT *FROM `#__vikrentcar_dispcosthours_master` WHERE `id`='" . $idp . "';";
		}else{
		$q = "SELECT *FROM `#__vikrentcar_dispcost_master` WHERE `id`='" . $idp . "';";	
		}
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$n = $dbo->loadAssocList();
			return $n;
		}
		return "";
	}

	function getPriceAttr($idp) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`attr` FROM `#__vikrentcar_prices` WHERE `id`='" . $idp . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$n = $dbo->loadAssocList();
			return $n[0]['attr'];
		}
		return "";
	}
	
	function getIdPrice($idtar, $idorder) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `idtar`,`hourly` FROM `#__vikrentcar_orders` WHERE `idtar`='" . $idtar . "' AND `id`='" . $idorder."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		if($n[0]['hourly']==0){
			
			$q = "SELECT `idprice` FROM `#__vikrentcar_dispcost` WHERE `id`='" . $idtar . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
			$n = $dbo->loadAssocList();
			return $n[0]['idprice'];
			}
		}
				
		else{
			$q = "SELECT `idprice` FROM `#__vikrentcar_dispcosthours` WHERE `id`='" . $idtar . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
			$n = $dbo->loadAssocList();
			return $n[0]['idprice'];
			}
		}
		
		return "";
	}
	
	
	function getCatName($idc) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_categories` WHERE `id`='" . $idc . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		return $n[0]['name'];
	
	}

	function getCatPlace($idc) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_categories` WHERE `id`='" . $idc . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		return $n[0]['idplace'];
	
	}

	function getCategories($idc) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_categories` order by name ASC;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		return $n;
	
	}

	
	function getAliq($idal) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $idal . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		return $n[0]['aliq'];
	}

	function getTimeOpenStore($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='timeopenstore';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$n = $dbo->loadAssocList();
			if (empty ($n[0]['setting']) && $n[0]['setting'] != "0") {
				return false;
			} else {
				$x = explode("-", $n[0]['setting']);
				if (!empty ($x[1]) && $x[1] != "0") {
					return $x;
				}
			}
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('getTimeOpenStore', '');
			if(!empty($sval)) {
				return $sval;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='timeopenstore';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$n = $dbo->loadAssocList();
				if (empty ($n[0]['setting']) && $n[0]['setting'] != "0") {
					return false;
				} else {
					$x = explode("-", $n[0]['setting']);
					if (!empty ($x[1]) && $x[1] != "0") {
						$session->set('getTimeOpenStore', $x);
						return $x;
					}
				}
			}
		}
		return false;
	}

	function getHoursMinutes($secs) {
		if ($secs >= 3600) {
			$op = $secs / 3600;
			$hours = floor($op);
			$less = $hours * 3600;
			$newsec = $secs - $less;
			$optwo = $newsec / 60;
			$minutes = floor($optwo);
		} else {
			$hours = "0";
			$optwo = $secs / 60;
			$minutes = floor($optwo);
		}
		$x[] = $hours;
		$x[] = $minutes;
		return $x;
	}

	function showPlacesFront($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='placesfront';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$s = $dbo->loadAssocList();
				return (intval($s[0]['setting']) == 1 ? true : false);
			} else {
				return false;
			}
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('showPlacesFront', '');
			if(strlen($sval) > 0) {
				return (intval($sval) == 1 ? true : false);
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='placesfront';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() == 1) {
					$s = $dbo->loadAssocList();
					$session->set('showPlacesFront', $s[0]['setting']);
					return (intval($s[0]['setting']) == 1 ? true : false);
				} else {
					return false;
				}
			}
		}
	}

	function showCategoriesFront($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='showcategories';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$s = $dbo->loadAssocList();
				return (intval($s[0]['setting']) == 1 ? true : false);
			} else {
				return false;
			}
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('showCategoriesFront', '');
			if(strlen($sval) > 0) {
				return (intval($sval) == 1 ? true : false);
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='showcategories';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() == 1) {
					$s = $dbo->loadAssocList();
					$session->set('showCategoriesFront', $s[0]['setting']);
					return (intval($s[0]['setting']) == 1 ? true : false);
				} else {
					return false;
				}
			}
		}
	}

	function allowRent() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='allowrent';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$s = $dbo->loadAssocList();
			return (intval($s[0]['setting']) == 1 ? true : false);
		} else {
			return false;
		}
	}

	function getDisabledRentMsg() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='disabledrentmsg';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getDateFormat($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$s = $dbo->loadAssocList();
			return $s[0]['setting'];
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('getDateFormat', '');
			if(!empty($sval)) {
				return $sval;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$s = $dbo->loadAssocList();
				$session->set('getDateFormat', $s[0]['setting']);
				return $s[0]['setting'];
			}
		}
	}

	function getHoursMoreRb() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='hoursmorerentback';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getHoursCarAvail() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='hoursmorecaravail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getSMSAvail() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sms';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getSMSUser() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='USUARIO_SMS';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getSMSPassword() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='PASSWORD_SMS';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getSMSUrl() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='URL_SERVICE_SMS';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}


		function getsmsrealsend() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='smsrealsend';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}


	

	function getAcymailingAvail() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='acymailing';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getFrontTitle() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='fronttitle';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getformsconstructor() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='formsconstructor';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getdataTour($id){

		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_tours_info` WHERE `id_tour`=".$id;
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		
		return $s[0];
		
	}

	function saveformsconstructor($val) {



		$dbo = & JFactory :: getDBO();
		$q = "UPDATE  `#__vikrentcar_config` SET `setting` = ".$dbo->quote($val)."  WHERE `param`='formsconstructor';";

	
		$dbo->setQuery($q);
		$dbo->Query($q);

		

				
	}

	function getformsconstructorIngles() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='formconstructorIngles';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getConfigSlides() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='configslides';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getformsmapazones() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='formsmapazones';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();

		if($s[0]['setting']==null || $s[0]['setting']=='' ){

			return 0;
		}

		$arrayidprices= explode(";", $s[0]['setting']);

		foreach ($arrayidprices as $key => $value) {

			

			$nameprice[]= self::getPriceName($value);
			
		}
	   
		
		 
		//$arraynames= explode(";", $nameprice);
		
		return json_encode($nameprice);
	}

	function deleteformconstructor() {
		$dbo = & JFactory :: getDBO();
		$q = "DELETE FROM `#__vikrentcar_config`  WHERE `param`='formsconstructor';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getAffectedRows() !=0){
			return true;
		}else{

			return false;
		}

	}

	function gettimeflagprice($idprice) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `view` FROM `#__vikrentcar_prices` WHERE `id`='".$idprice."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['view'];
	}

	function gettimeflagpriceAll() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`, `view` FROM `#__vikrentcar_prices`;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft;
	}

	function getFrontTitleTag() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='fronttitletag';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getFrontTitleTagClass() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='fronttitletagclass';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getCurrencyName() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencyname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getCurrencySymb($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencysymb';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$ft = $dbo->loadAssocList();
			return $ft[0]['setting'];
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('getCurrencySymb', '');
			if(!empty($sval)) {
				return $sval;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencysymb';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$ft = $dbo->loadAssocList();
				$session->set('getCurrencySymb', $ft[0]['setting']);
				return $ft[0]['setting'];
			}
		}
	}

	function getCurrencyCodePp() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencycodepp';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getSubmitName($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='searchbtnval';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$ft = $dbo->loadAssocList();
			if (!empty ($ft[0]['setting'])) {
				return $ft[0]['setting'];
			} else {
				return "";
			}
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('getSubmitName', '');
			if(!empty($sval)) {
				return $sval;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='searchbtnval';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$ft = $dbo->loadAssocList();
				if (!empty ($ft[0]['setting'])) {
					return $ft[0]['setting'];
				} else {
					return "";
				}
			}
		}
	}

	function getSubmitClass($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='searchbtnclass';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$ft = $dbo->loadAssocList();
			return $ft[0]['setting'];
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('getSubmitClass', '');
			if(!empty($sval)) {
				return $sval;
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='searchbtnclass';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$ft = $dbo->loadAssocList();
				$session->set('getSubmitClass', $ft[0]['setting']);
				return $ft[0]['setting'];
			}
		}
	}

	function getIntroMain() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='intromain';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getClosingMain() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='closingmain';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		return $ft[0]['setting'];
	}

	function getFullFrontTitle() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='fronttitle';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='fronttitletag';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$fttag = $dbo->loadAssocList();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='fronttitletagclass';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$fttagclass = $dbo->loadAssocList();
		if (empty ($ft[0]['setting'])) {
			return "";
		} else {
			if (empty ($fttag[0]['setting'])) {
				return $ft[0]['setting'] . "<br/>\n";
			} else {
				$tag = str_replace("<", "", $fttag[0]['setting']);
				$tag = str_replace(">", "", $tag);
				$tag = str_replace("/", "", $tag);
				$tag = trim($tag);
				return "<" . $tag . "" . (!empty ($fttagclass) ? " class=\"" . $fttagclass[0]['setting'] . "\"" : "") . ">" . $ft[0]['setting'] . "</" . $tag . ">";
			}
		}
	}

	function dateIsValid($date) {
		$df = self::getDateFormat();
		if (strlen($date) != "10") {
			return false;
		}
		$x = explode("/", $date);
		if ($df == "%d/%m/%Y") {
			if (strlen($x[0]) != "2" || $x[0] > 31 || strlen($x[1]) != "2" || $x[1] > 12 || strlen($x[2]) != "4") {
				return false;
			}
		} else {
			if (strlen($x[2]) != "2" || $x[2] > 31 || strlen($x[1]) != "2" || $x[1] > 12 || strlen($x[0]) != "4") {
				return false;
			}
		}
		return true;
	}

	function sayDateFormat() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		if ($s[0]['setting'] == "%d/%m/%Y") {
			return JText :: _('VRCONFIGONETWELVE');
		} else {
			return JText :: _('VRCONFIGONETENTHREE');
		}
	}

	function getDateTimestamp($date, $h, $m) {
		$df = self::getDateFormat();
	
		$x = explode("/", $date);
		if ($df == "%d/%m/%Y") {
			$dts = strtotime($x[1] . "/" . $x[0] . "/" . $x[2]);
		} else {
			$dts = strtotime($x[1] . "/" . $x[2] . "/" . $x[0]);
		}
		
		$hts = 3600 * $h;
		$mts = 60 * $m;
		return ($dts + $hts + $mts);
	}

	function ivaInclusa($skipsession = false) {
		if($skipsession) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$s = $dbo->loadAssocList();
			return (intval($s[0]['setting']) == 1 ? true : false);
		}else {
			$session =& JFactory::getSession();
			$sval = $session->get('ivaInclusa', '');
			if(strlen($sval) > 0) {
				return (intval($sval) == 1 ? true : false);
			}else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$s = $dbo->loadAssocList();
				$session->set('ivaInclusa', $s[0]['setting']);
				return (intval($s[0]['setting']) == 1 ? true : false);
			}
		}
	}

	function tokenForm() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='tokenform';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return (intval($s[0]['setting']) == 1 ? true : false);
	}

	function getPaypalAcc() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ccpaypal';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getAccPerCent() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='payaccpercent';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getAdminMail() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='adminemail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getPaymentName() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='paymentname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s[0]['setting'];
	}

	function getMinutesLock($conv = false) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='minuteslock';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		if ($conv) {
			$op = $s[0]['setting'] * 60;
			return (time() + $op);
		} else {
			return $s[0]['setting'];
		}
	}

	function carNotLocked($idcar, $units, $first, $second) {
		$dbo = & JFactory :: getDBO();
		$actnow = time();
		$booked = array ();
		$q = "DELETE FROM `#__vikrentcar_tmplock` WHERE `until`<" . $dbo->getEscaped($actnow) . ";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		//vikrentcar 1.5
		$secdiff = $second - $first;
		$daysdiff = $secdiff / 86400;
		if (is_int($daysdiff)) {
			if ($daysdiff < 1) {
				$daysdiff = 1;
			}
		}else {
			if ($daysdiff < 1) {
				$daysdiff = 1;
			}else {
				$sum = floor($daysdiff) * 86400;
				$newdiff = $secdiff - $sum;
				$maxhmore = self :: getHoursMoreRb() * 3600;
				if ($maxhmore >= $newdiff) {
					$daysdiff = floor($daysdiff);
				}else {
					$daysdiff = ceil($daysdiff);
				}
			}
		}
		$groupdays = self::getGroupDays($first, $second, $daysdiff);
		$check = "SELECT `id`,`ritiro`,`realback` FROM `#__vikrentcar_tmplock` WHERE `idcar`='" . $dbo->getEscaped($idcar) . "' AND `until`>=" . $dbo->getEscaped($actnow) . ";";
		$dbo->setQuery($check);
		$dbo->Query($check);
		if ($dbo->getNumRows() > 0) {
			$busy = $dbo->loadAssocList();
			foreach ($groupdays as $gday) {
				$bfound = 0;
				foreach ($busy as $bu) {
					if ($gday >= $bu['ritiro'] && $gday <= $bu['realback']) {
						$bfound++;
					}
				}
				if ($bfound >= $units) {
					return false;
				}
			}
		}
		//
		return true;
	}
	
	function getGroupDays($first, $second, $daysdiff) {
		$ret = array();
		$ret[] = $first;
		if($daysdiff > 1) {
			$start = getdate($first);
			$end = getdate($second);
			$endcheck = mktime(0, 0, 0, $end['mon'], $end['mday'], $end['year']);
			for($i = 1; $i < $daysdiff; $i++) {
				$checkday = $start['mday'] + $i;
				$dayts = mktime(0, 0, 0, $start['mon'], $checkday, $start['year']);
				if($dayts != $endcheck) {				
					$ret[] = $dayts;
				}
			}
		}
		$ret[] = $second;
		return $ret;
	}
	
	function carBookable($idcar, $units, $first, $second) {
		$dbo = & JFactory :: getDBO();
		//vikrentcar 1.5
		$secdiff = $second - $first;
		$daysdiff = $secdiff / 86400;
		if (is_int($daysdiff)) {
			if ($daysdiff < 1) {
				$daysdiff = 1;
			}
		}else {
			if ($daysdiff < 1) {
				$daysdiff = 1;
			}else {
				$sum = floor($daysdiff) * 86400;
				$newdiff = $secdiff - $sum;
				$maxhmore = self :: getHoursMoreRb() * 3600;
				if ($maxhmore >= $newdiff) {
					$daysdiff = floor($daysdiff);
				}else {
					$daysdiff = ceil($daysdiff);
				}
			}
		}
		$groupdays = self::getGroupDays($first, $second, $daysdiff);
		$check = "SELECT `id`,`ritiro`,`realback` FROM `#__vikrentcar_busy` WHERE `idcar`='" . $dbo->getEscaped($idcar) . "';";
		$dbo->setQuery($check);
		$dbo->Query($check);
		if ($dbo->getNumRows() > 0) {
			$busy = $dbo->loadAssocList();
			foreach ($groupdays as $gday) {
				$bfound = 0;
				foreach ($busy as $bu) {
					if ($gday >= $bu['ritiro'] && $gday <= $bu['realback']) {
						$bfound++;
					}
				}
				if ($bfound >= $units) {
					return false;
				}
			}
		}
		//
		return true;
	}

	function payTotal() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='paytotal';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return (intval($s[0]['setting']) == 1 ? true : false);
	}

	function getmaxZones() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='maxzones';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() == 1) {
			$c = $dbo->loadResult();
			return $c;
		}else {
			return "";
		}
	}
	
	function getCouponInfo($code) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_coupons` WHERE `code`='".$dbo->getEscaped($code)."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() == 1) {
			$c = $dbo->loadAssocList();
			return $c[0];
		}else {
			return "";
		}
	}

	function getInfoAdicionalUsuario($idUser){

		$language = JFactory::getLanguage();
	    $language->load('com_vikrentcar');

		$dbo = & JFactory :: getDBO();
		$datosSalida='';

		$q= "SELECT  p.movil as movil, p.phone as phone, p.Email as email  FROM `#__vikrentcar_profiles` AS p WHERE p.user_id='".$idUser."';";
		
		
			$dbo->setQuery($q);
			$dbo->Query($q);

			$dbo->Query($q);
			if($dbo->getNumRows() > 0) {

				$datosUsuarios=$dbo->loadAssocList();  

				$datosSalida.= '<br/>'.JText::_('VRMSEMAIL').': '.$datosUsuarios[0]['email'].'<br/>';

				$datosSalida.= JText::_('VRMSTELEFONO').': '.$datosUsuarios[0]['phone'].'<br/>';
			  
			    $datosSalida.= JText::_('VRMSMOVIL').': '.$datosUsuarios[0]['email'].'<br/>';

			}


			return $datosSalida;





	}


	function getInfoAdicionalOrdenEsp($idcar, $pidCond, $idOrder){

		$dbo = & JFactory :: getDBO();
		$datosSalida='';

		



		if($pidCond!=0 ){



			//$q = "SELECT `id`,`name`,`placa`,`idCond`, `img`,`idcat`,`idcarat`,`info`  FROM `#__vikrentcar_cars` WHERE `id`='" . $idcar . "';";
			$q= "SELECT  cvc.id as id, cvc.name as name, cves.placa as placa, cves.id_Cond as idCond , cvc.img as img, cvc.idcat as idcat, cvc.idcarat as idcarat, cves.infoCar as info  FROM `#__vikrentcar_orders` o INNER JOIN `#__vikrentcar_cars` cvc ON cvc.id=o.idcar LEFT JOIN `#__vikrentcar_esp_services` cves ON cves.id_Order=o.id WHERE cves.id_Order='".$idOrder."';";

		
			$dbo->setQuery($q);
			$dbo->Query($q);

			$dbo->Query($q);
			if($dbo->getNumRows() > 0) {
				$s = $dbo->loadAssocList();

				if(!empty($s[0]['placa'])){

					//$datosSalida.='<br/><strong>Placa: </strong>'.$s[0]['placa'].'<br/>';
				}

				

					$datosSalida.='<br/>'.$s[0]['info'];
				

				
			}else{

				//nothing
			}



		}

		return $datosSalida;

	

		



	}




	

	function getplacaconIdcond($idcar, $pidCond, $idOrder){

		$dbo = & JFactory :: getDBO();

		$q = "SELECT `id`,`name`,`placa`,`idCond`, `img`,`idcat`,`idcarat`,`info`  FROM `#__vikrentcar_cars` WHERE `id`='" . $idcar . "';";

	
		
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();


			if($pidCond!=0 ){



			//$q = "SELECT `id`,`name`,`placa`,`idCond`, `img`,`idcat`,`idcarat`,`info`  FROM `#__vikrentcar_cars` WHERE `id`='" . $idcar . "';";
			$q= "SELECT  cvc.id as id, cvc.name as name, cves.placa as placa, cves.id_Cond as idCond , cvc.img as img, cvc.idcat as idcat, cvc.idcarat as idcarat, cves.infoCar as info  FROM `#__vikrentcar_orders` o INNER JOIN `#__vikrentcar_cars` cvc ON cvc.id=o.idcar LEFT JOIN `#__vikrentcar_esp_services` cves ON cves.id_Order=o.id WHERE cves.id_Order='".$idOrder."';";

		
			$dbo->setQuery($q);
			$dbo->Query($q);

			$dbo->Query($q);
			if($dbo->getNumRows() > 0) {
				$s = $dbo->loadAssocList();
			}else{

				//nothing
			}



		}

		$gt=$s[0];

		return $s[0];


	}
	
	function getCarInfo2($idcar, $idorder=0) {
		$dbo = & JFactory :: getDBO();




		//Modificacion 1: ingresa consulta de la placa
		$q = "SELECT `id`,`name`,`placa`,`idCond`, `img`,`idcat`,`idcarat`,`info`  FROM `#__vikrentcar_cars` WHERE `id`='" . $idcar . "';";

	
		
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();


		/*$datosnovehi= vikrentcar::getcatnotvehiculo();

		foreach ($datosnovehi as $key => $value) {


			if((preg_match("/".$value.";/i", $s[0]['idcat']))) {

			   
			     $isnotveh=true;

			     break;

			}else{

				 $isnotveh=false;

			     
			}


		
			
		}*/

		if($idorder!=0 ){



			//$q = "SELECT `id`,`name`,`placa`,`idCond`, `img`,`idcat`,`idcarat`,`info`  FROM `#__vikrentcar_cars` WHERE `id`='" . $idcar . "';";
			$q= "SELECT  cvc.id as id, cvc.name as name, cves.placa as placa, cves.id_Cond as idCond , cvc.img as img, cvc.idcat as idcat, cvc.idcarat as idcarat, cves.infoCar as info  FROM `#__vikrentcar_orders` o INNER JOIN `#__vikrentcar_cars` cvc ON cvc.id=o.idcar LEFT JOIN `#__vikrentcar_esp_services` cves ON cves.id_Order=o.id WHERE cves.id_Order='".$idorder."';";

		
			$dbo->setQuery($q);
			$dbo->Query($q);

			$dbo->Query($q);
			if($dbo->getNumRows() > 0) {
				$s = $dbo->loadAssocList();
			}else{

				//nothing
			}



		}

		

		return $s[0];
	}

	function getCarInfo($idcar) {
		$dbo = & JFactory :: getDBO();




		//Modificacion 1: ingresa consulta de la placa
		$q = "SELECT `id`,`name`,`placa`,`idCond`, `img`,`idcat`,`idcarat`,`info`  FROM `#__vikrentcar_cars` WHERE `id`='" . $idcar . "';";


		
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();



		return $s[0];
	}

	function getMaxValMapZones($idprice){

		


		$dbo = & JFactory :: getDBO();
		$q = "SELECT MAX(days) FROM `#__vikrentcar_dispcost` WHERE `idprice`='" . $dbo->getEscaped($idprice) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() == 1) {
			$c = $dbo->loadResult();
			return $c;
		}else {
			return "";
		}



	}

	function mapzones($valorbuscar){


		$cantzones=self::getmaxZones();



		$x= sqrt($cantzones);
		$y=$x;



		for ($i=1; $i <= $x ; $i++) { 

			for ($j=1; $j <=$y ; $j++) { 

			   $cell=(($i-1)*$x) + $j;

				$arrayzones[$i][$j] = $cell;

				if($cell==$valorbuscar){

			 	   
			 	    return   $i.'-'.$j;

			    }

			
			
			}

		}


	}

	function getNamesCondNew($idCond){

		$dbo = & JFactory :: getDBO();

		$q="SELECT p.name as `Nombre`, p.lname as `Apellido`, p.user_id as user_id FROM `#__vikrentcar_profiles` AS p  WHERE p.user_id=".$idCond; 
		
	
			$dbo->setQuery($q);

			$dbo->Query($q);
			$s = $dbo->loadAssocList();

		return $s[0]['Nombre'].' '.$s[0]['Apellido'];

	}


	function getNamesCond($idCond, $pidCond=0){

		$dbo = & JFactory :: getDBO();

		//$dat= explode('E-', $pidCond);


		

					

		if($pidCond==0 || empty($pidCond) ){



		
			$q="SELECT p.name as `Nombre`, p.lname as `Apellido`, p.user_id as user_id FROM `#__vikrentcar_profiles` AS p  WHERE p.user_id=".$idCond; 
		
	
			$dbo->setQuery($q);

			$dbo->Query($q);
			$s = $dbo->loadAssocList();

			return $s[0];

		}else{



			$q="SELECT p.name as `Nombre`, p.lname as `Apellido`, p.user_id as user_id FROM `#__vikrentcar_profiles` AS p INNER JOIN `#__vikrentcar_esp_services` as esp ON esp.id_Cond=p.user_id WHERE esp.id=".$pidCond; 
			

			$dbo->setQuery($q);
			$dbo->Query($q);
			$s = $dbo->loadAssocList();



			return $s[0];

		}

		




	}

	function reemplazarnumDocxIdCond(){

		$db = &JFactory::getDBO();
		$query = "SELECT  cars.idCond FROM  #__vikrentcar_cars  as cars";
		$db->setQuery($query);
		$db->Query($query);
		$idconds = $db->loadAssocList();

		foreach ($idconds as $key => $value) {

			$query = "SELECT  p.user_id FROM  #__vikrentcar_profiles  as p WHERE p.num_doc=".$value;
			$db->setQuery($query);
			$db->Query($query);

			$idUser = $db->loadResult();

			echo $idUser;


			
		}




	}

	function detectarNumDocExist($documento, $idUser=null){

		$app		= JFactory::getApplication();
		$num=0;
		$db = &JFactory::getDBO();
		$query = "SELECT  COUNT(p.pro_num_doc) as total, p.userid as userid FROM  #__profiler_users  as p  WHERE p.pro_num_doc='".$documento."'";
		$db->setQuery($query);
		$db->Query($query);
		$num = $db->loadAssocList();

	
		$dat= $num[0]['total'];
		$dat2= $num[0]['userid'];
		print_r($idUser);
		if($num[0]['total']>0){

			if($num[0]['userid']==$idUser){

				return true;
				
			}else{

				return false;

			}

			
		


		}else{

			return true;
			
		}


	}

	function actualizar_user($iduser){

		$dbo = &JFactory::getDBO();

		$query= "SELECT * FROM #__vikrentcar_profiles WHERE user_id='".$iduser."'";
		$dbo->setQuery($query);
		$dbo->query();
		if ($dbo->getNumRows() > 0) {

			$profiles = $dbo->loadAssocList();

			$q="UPDATE #__profiler_users SET  email='".$profiles[0]['Email']."', name='".$profiles[0]['name'].' '.$profiles[0]['lname']."', lastname='".$profiles[0]['lname']."', firstname='".$profiles[0]['name']."', pro_city='".$profiles[0]['city']."', pro_address='".$profiles[0]['address']."', pro_movil='".$profiles[0]['movil']."', pro_phone='".$profiles[0]['phone']."', pro_doc_type='".$profiles[0]['doc_type']."', pro_num_doc='".$profiles[0]['num_doc']."', pro_convenio='".$profiles[0]['convenio']."' WHERE userid='".$iduser."'";
			$dbo->setQuery($q);
			$dbo->query();


			/*$q="UPDATE #__users SET  password='".$profiles[0]['Password']."',  name='".$profiles[0]['name'].' '.$profiles[0]['lname']."', email='".$profiles[0]['Email']."' WHERE id='".$iduser."'";
			$dbo->setQuery($q);
			$dbo->query();*/

		}
		

		



	}

	function sincronizar_users(){



		
		//ProfilerControllerUser::synchronize();
		//
		$app		= JFactory::getApplication();
		$db = &JFactory::getDBO();
		$query = "SELECT u.id, u.name, u.email FROM  #__users  AS u WHERE NOT EXISTS (SELECT pu.userid FROM #__profiler_users AS pu WHERE pu.userid =u.id)";
		//$query = "SELECT u.id, u.name FROM #__users AS u WHERE NOT EXISTS (SELECT pu.id FROM #__profiler_users AS pu WHERE pu.id=u.id)";
		$db->setQuery($query);
		$db->query();




		if ($db->getNumRows() > 0) {

			$users = $db->loadAssocList();

			foreach ($users as $user) {

			

				$query= "SELECT * FROM #__vikrentcar_profiles WHERE user_id='".$user['id']."'";
				$db->setQuery($query);
				$db->query();
				$profiles = $db->loadAssocList();

				


				$query = "INSERT INTO #__profiler_users (userid , name, firstname, lastname ,email) VALUES ('".$user['id']."','".$profiles[0]['name'].' '.$profiles[0]['lname']."', '".$profiles[0]['name']."', '".$profiles[0]['lname']."', '".$user['email']."');";
				$db->setQuery($query);
				$db->query();
				
				
				
				$q="UPDATE #__profiler_users SET pro_city='".$profiles[0]['city']."', pro_address='".$profiles[0]['address']."', pro_movil='".$profiles[0]['movil']."', pro_phone='".$profiles[0]['phone']."', pro_doc_type='".$profiles[0]['doc_type']."', pro_num_doc='".$profiles[0]['num_doc']."', pro_convenio='".$profiles[0]['convenio']."' WHERE userid='".$user['id']."'";
				$db->setQuery($q);
				$db->query();
				
				//$app->enqueueMessage($e);
				echo 'Sincronizacion completada';
	
			}
				
		}else{

			//se detecta usuarios que  estan en vikrentcar pero no en profiler table  se borran 

			$query= "SELECT  IFNULL(cpu.userid,'NN') AS 'idsuser', cvp.user_id as usernoexist FROM `#__vikrentcar_profiles` cvp   left JOIN `#__profiler_users` cpu ON cvp.user_id=cpu.userid ";
			$db->setQuery($query);
			$db->query();
			$usersnoestan = $db->loadAssocList();

			//si busca usuarios en la tabla de vikrentcar y no loes encuentra en la tabla principal de usuarios profiler borra los que sobran
			$borrar=false;
			foreach ($usersnoestan as $key => $value) {



				if($value['idsuser']=='NN'){

					$borrar=true;

					$q="DELETE FROM `#__vikrentcar_profiles` WHERE user_id=".$value['usernoexist'];
					$db->setQuery($q);
					$db->query();

					

				}


				
			}

			if($borrar){

				echo 'Sincronizacion completada';

			}
			
		
			

		}

		

	}
	
		


	function sayCategory($ids) {
		$dbo = & JFactory :: getDBO();
		$split = explode(";", $ids);
		$say = "";
		foreach ($split as $k => $s) {
			if (strlen($s)) {
				$q = "SELECT `id`,`name` FROM `#__vikrentcar_categories` WHERE `id`='" . $s . "';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$nam = $dbo->loadAssocList();
				$say .= $nam[0]['name'];
				$say .= (strlen($split[($k +1)]) && end($split) != $s ? ", " : "");
			}
		}
		return $say;
	}
	


	function getCarCarat($idc) {
		$dbo = & JFactory :: getDBO();
		$split = explode(";", $idc);
		$carat = "";
		$dbo = & JFactory :: getDBO();
		$arr = array ();
		$where = array();
		foreach ($split as $s) {
			if (!empty ($s)) {
				$where[]=$s;
			}
		}
		if (count($where) > 0) {
			$q = "SELECT `id`,`name`,`icon`,`align`,`textimg` FROM `#__vikrentcar_caratteristiche` WHERE `id` IN (".implode(",", $where).");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$arr = $dbo->loadAssocList();
			}
		}
		if (@ count($arr) > 0) {
			$carat .= "<table class=\"vrcsearchcaratt\">";
			foreach ($arr as $a) {
				if (!empty ($a['textimg'])) {
					if ($a['align'] == "left") {
						$carat .= "<tr><td align=\"center\">" . $a['textimg'] . "</td>" . (!empty ($a['icon']) ? "<td align=\"center\"><img src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/></td></tr>" : "</tr>");
					}
					elseif ($a['align'] == "center") {
						$carat .= "<tr><td align=\"center\">" . (!empty ($a['icon']) ? "<img src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/><br/>" : "") . $a['textimg'] . "</td></tr>";
					} else {
						$carat .= "<tr>" . (!empty ($a['icon']) ? "<td align=\"center\"><img src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/></td>" : "") . "<td align=\"center\">" . $a['textimg'] . "</td></tr>";
					}
				} else {
					$carat .= (!empty ($a['icon']) ? "<tr><td align=\"center\"><img src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\" alt=\"" . $a['name'] . "\" title=\"" . $a['name'] . "\"/></td></tr>" : "");
				}
			}
			$carat .= "</table>\n";
		}
		return $carat;
	}
	function getOptInfo($poptionals, $pdays, $isdue){
	  $dbo = & JFactory :: getDBO();	

	  $stepo = explode(";", $poptionals);
	  foreach ($stepo as $oo) {
		  if (!empty ($oo)) {
			  $stept = explode(":", $oo);
			  $q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($stept[0]) . "';";
			  $dbo->setQuery($q);
			  $dbo->Query($q);
			  if ($dbo->getNumRows() == 1) {
				  $actopt = $dbo->loadAssocList();
				  $realcost = (intval($actopt[0]['perday']) == 1 ? ($actopt[0]['cost'] * $pdays * $stept[1]) : ($actopt[0]['cost'] * $stept[1]));
				  if (!empty($actopt[0]['maxprice']) && $actopt[0]['maxprice'] > 0 && $realcost > $actopt[0]['maxprice']) {
					  $realcost = $actopt[0]['maxprice'];
					  if(intval($actopt[0]['hmany']) == 1 && intval($stept[1]) > 1) {
						  $realcost = $actopt[0]['maxprice'] * $stept[1];
					  }
				  }
				  $tmpopr = self::sayOptionalsPlusIva($realcost, $actopt[0]['idiva']);
				 // $tmpopr = 0;
				  $isdue += $tmpopr;
				  $optstr .= ($stept[1] > 1 ? $stept[1] . " " : "") . $actopt[0]['name'] . ": " . $tmpopr . " " . $currencyname . "\n";
			  }
		  }
	  }
	  
	  return  $optstr;
							
		
	}

	function getCarCaratFly($idc) {
		$dbo = & JFactory :: getDBO();
		$split = explode(";", $idc);
		$carat = "";
		$dbo = & JFactory :: getDBO();
		$arr = array ();
		$where = array();
		foreach ($split as $s) {
			if (!empty ($s)) {
				$where[]=$s;
			}
		}
		if (count($where) > 0) {
			$q = "SELECT `id`,`name`,`icon`,`align`,`textimg` FROM `#__vikrentcar_caratteristiche` WHERE `id` IN (".implode(",", $where).");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$arr = $dbo->loadAssocList();
			}
		}
		if (@ count($arr) > 0) {
			$carat .= "<table><tr>";
			$cont=1;
			foreach ($arr as $a) {
				if (!empty ($a['textimg'])) {
					if ($a['align'] == "left") {
						$carat .= "<td valign=\"top\">" . $a['textimg'] . (!empty ($a['icon']) ? " <img id=caract_".$cont. " src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/></td>" : "</td>");
					}
					elseif ($a['align'] == "center") {
						$carat .= "<td align=\"center\" valign=\"top\">" . (!empty ($a['icon']) ? "<img id=caract_".$cont. " src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/><br/>" : "") . $a['textimg'] . "</td>";
					} else {
						$carat .= "<td valign=\"top\">" . (!empty ($a['icon']) ? "<img id=caract_".$cont. "  src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/> " : "") . $a['textimg'] . "</td>";
					}
				} else {
					$carat .= (!empty ($a['icon']) ? "<td valign=\"top\"><img id=caract_".$cont. "  src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\" alt=\"" . $a['name'] . "\" title=\"" . $a['name'] . "\"/></td>" : "");
				}
				$cont++;
			}
			$carat .= "</tr></table>\n";
		}
		return $carat;
	}



	function buildforms(){

		//error_reporting(-1);


			$dbo = & JFactory :: getDBO();
			$q="SELECT id FROM `#__vikrentcar_categories`as cat ORDER BY id ASC ";
			$dbo->setQuery($q);
			$dbo->Query($q);

			if ($dbo->getNumRows() > 0) {

				$categorias = $dbo->loadAssocList();

			}






			$arraycat = array();
			$datosformularios=array();

			foreach ($categorias as  $cate) {

				$formularios=array();

			

				$jsonsoptions= self::crearformulariopaso3($cate['id']);

				$arrayoptions= json_decode(($jsonsoptions));


				
				foreach ($arrayoptions as $val) {


					$valoresdat=  self::datosCrearformuPaso3($cate['id'] ,  $val->idprice);



					$arrayvaloresdat= json_decode(($valoresdat));

					$datosformularios[]=$arrayvaloresdat;

				}

					$arraycat[] = $formularios;

					

			}


			



				$jsonformsdata =json_encode($datosformularios);


				

			//$jsonformsdata =json_encode($datosformularios);

			

			

	
			
	      
		

				$q="SELECT id FROM `#__vikrentcar_config` WHERE `param`='formsconstructor'";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$sett = $dbo->loadResult();


				if ($dbo->getNumRows()>0) {

					
					$q = "UPDATE `#__vikrentcar_config` SET `setting`='".$jsonformsdata." WHERE `param`='formsconstructor';";
					$dbo->setQuery($q);
					$dbo->Query($q);	

					

						


				}else{

					$q="INSERT INTO `#__vikrentcar_config` (`param`, `setting`) VALUES('formsconstructor', '".$jsonformsdata."');";
					$dbo->setQuery($q);
					$dbo->Query($q);

							



				}



		

		
	

	


		



}


function buildformsjson($jsonform){

		$arrayformesturcture = json_decode($jsonform);

		return $arrayformesturcture;

}

	function datosCrearformuPaso3($idcat, $idprice){

		//$idcat = JRequest :: getInt('idcat', '', 'request');
		//$attrdata = JRequest :: getString('attrdata', '', 'request');

		


			$dbo = & JFactory :: getDBO();
			$q="SELECT MAX(cost_d.days) as maximo ,  MIN(cost_d.days) as minimo ,  pr.name as nameCod,  cat.id as catid , (0) as 'horas', pr.id as 'idprice'  FROM `chun4_vikrentcar_dispcost`as cost_d INNER JOIN `chun4_vikrentcar_cars` as cars ON cost_d.idcar=cars.id INNER JOIN `chun4_vikrentcar_categories` AS cat ON cars.idcat=CONCAT(cat.id,';')  INNER JOIN  `chun4_vikrentcar_prices` AS pr ON cost_d.idprice = pr.id WHERE cat.id=".$idcat." AND pr.id=".$idprice." UNION SELECT MAX(cost_d.hours) as maximo ,  MIN(cost_d.hours) as maximo ,  pr.name as nameCod,  cat.id as catid, (1) as 'horas' ,pr.id as 'idprice'   FROM `chun4_vikrentcar_dispcosthours`as cost_d INNER JOIN `chun4_vikrentcar_cars` as cars ON cost_d.idcar=cars.id INNER JOIN `chun4_vikrentcar_categories` AS cat ON cars.idcat=CONCAT(cat.id,';')  INNER JOIN  `chun4_vikrentcar_prices` AS pr ON cost_d.idprice = pr.id WHERE cat.id=".$idcat." AND pr.id=".$idprice;
			

			$dbo->setQuery($q);
			$dbo->Query($q);


			$datostarifa = $dbo->loadAssocList();

			

		

		return json_encode($datostarifa);



}

	function crearformulariopaso3($idcat){

		//$idcat = JRequest :: getInt('idcat', '', 'request');

		$dbo = & JFactory :: getDBO();
		$q="SELECT DISTINCT pr.id as 'idprice', pr.name as 'codePrice' FROM `#__vikrentcar_dispcosthours`as cost_h INNER JOIN `#__vikrentcar_cars` as cars ON cost_h.idcar=cars.id INNER JOIN `#__vikrentcar_categories` AS cat ON cars.idcat=CONCAT(cat.id,';')  INNER JOIN  `#__vikrentcar_prices` AS pr ON cost_h.idprice = pr.id WHERE cat.id=".$idcat." UNION  SELECT DISTINCT pr.id as 'idprice', pr.name as 'codePrice' FROM `#__vikrentcar_dispcost`as cost_d INNER JOIN `#__vikrentcar_cars` as cars ON cost_d.idcar=cars.id INNER JOIN `#__vikrentcar_categories` AS cat ON cars.idcat=CONCAT(cat.id,';')  INNER JOIN  `#__vikrentcar_prices` AS pr ON cost_d.idprice = pr.id WHERE cat.id=".$idcat;
		$dbo->setQuery($q);
		$dbo->Query($q);

		

			

		


		if ($dbo->getNumRows() > 0) {
			$constructorSel = $dbo->loadAssocList();
			       
		}

		
			return (json_encode($constructorSel));
		    

		


	}



	function getCarCaratOriz($idc) {
		$dbo = & JFactory :: getDBO();
		$split = explode(";", $idc);
		$carat = "";
		$dbo = & JFactory :: getDBO();
		$arr = array ();
		$where = array();
		foreach ($split as $s) {
			if (!empty($s)) {
				$where[]=$s;
			}
		}
		if (count($where) > 0) {
			$q = "SELECT `id`,`name`,`icon`,`align`,`textimg` FROM `#__vikrentcar_caratteristiche` WHERE `id` IN (".implode(",", $where).");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$arr = $dbo->loadAssocList();
			}
		}
		if (count($arr) > 0) {
			$carat .= "<table><tr>";
			$cont=1;
			foreach ($arr as $a) {
				if (!empty ($a['textimg'])) {
					if ($a['align'] == "left") {
						$carat .= "<td>" . $a['textimg'] . (!empty ($a['icon']) ? "<img src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/></td>" : "</td>");
					}
					elseif ($a['align'] == "center") {
						$carat .= "<td align=\"center\">" . (!empty ($a['icon']) ? "<img id=caract_".$cont. "  src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/><br/>" : "") . $a['textimg'] . "</td>";
					} else {
						$carat .= "<td>" . (!empty ($a['icon']) ? "<img  id=caract_".$cont. "  src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/>" : "") . $a['textimg'] . "</td>";
					}
				} else {
					$carat .= (!empty ($a['icon']) ? "<td><img  id=caract_".$cont. "  src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\" alt=\"" . $a['name'] . "\" title=\"" . $a['name'] . "\"/></td>" : "");
				}
			$cont++;
			}
			$carat .= "</tr></table>\n";
		}
		return $carat;
	}

	function getCarCaratOriz2($idc) {
		$dbo = & JFactory :: getDBO();
		$split = explode(";", $idc);
		$carat = "";
		$dbo = & JFactory :: getDBO();
		$arr = array ();
		$where = array();
		foreach ($split as $s) {
			if (!empty($s)) {
				$where[]=$s;
			}
		}
		if (count($where) > 0) {
			$q = "SELECT `id`,`name`,`icon`,`align`,`textimg` FROM `#__vikrentcar_caratteristiche` WHERE `id` IN (".implode(",", $where).");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$arr = $dbo->loadAssocList();
			}
		}
		if (count($arr) > 0) {
			$carat .= "<div><div class='ordenar carcat'>";
			$cont=1;
			foreach ($arr as $a) {
				if (!empty ($a['textimg'])) {
					if ($a['align'] == "left") {
						$carat .= "<div class='ordenar'>" . $a['textimg'] . (!empty ($a['icon']) ? "<img src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/></div>" : "</div>");
					}
					elseif ($a['align'] == "center") {
						$carat .= "<div class='ordenar' align=\"center\">" . (!empty ($a['icon']) ? "<img id=caract_".$cont. "  src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/><br/>" : "") . $a['textimg'] . "</div>";
					} else {
						$carat .= "<div class='ordenar'>" . (!empty ($a['icon']) ? "<img  id=caract_".$cont. "  src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\"/>" : "") . $a['textimg'] . "</div>";
					}
				} else {
					$carat .= (!empty ($a['icon']) ? "<div class='ordenar'><img  id=caract_".$cont. "  src=\"".JURI::root()."administrator/components/com_vikrentcar/resources/" . $a['icon'] . "\" alt=\"" . $a['name'] . "\" title=\"" . $a['name'] . "\"/></div>" : "");
				}
			$cont++;
			}
			$carat .= "</div></div>\n";
		}
		return $carat;
	}


	function getCarOptionals($idopts) {
		$split = explode(";", $idopts);
		$dbo = & JFactory :: getDBO();
		$arr = array ();
		foreach ($split as $s) {
			if (!empty ($s)) {
				$q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($s) . "';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() == 1) {
					$arr = array_merge($arr, $dbo->loadAssocList());
				}
			}
		}
		if (@ count($arr) > 0) {
			return $arr;
		}
		return "";
	}

	function movimientostransaccion($neworderid){

		$dbo = & JFactory :: getDBO();

		$q="SELECT * FROM #__vikrentcar_orders WHERE id=".$neworderid;
	    $dbo->setQuery($q);
		$dbo->Query($q);
		$rowsNewOrder = $dbo->loadAssocList();



			$saldo= vikrentcar :: getSaldoUser($rowsNewOrder[0]['ujid']);

			


			if((int)$saldo>=(int)$rowsNewOrder[0]['totpaid'] ){
				$saldoFavor=true;
				$payment['setconfirmed']=1;

			}else{

				if($saldo==0){

				}else{

					//si saldo es menor al valor aplico saldo y queda un pendiente de pago

					$isdue= $isdue-$saldo;
					vikrentcar::saveSaldo(0, $rowsNewOrder['ujid']);
					$payment['setconfirmed'] = 0;

					$q = "UPDATE `#__vikrentcar_orders` SET `status`='standby', totpaid =".$isdue." WHERE `id`='" . $neworderid . "';";
					$dbo->setQuery($q);
					$dbo->Query($q);

				  
				}
			}

			if($saldoFavor){

				$concepto='Reasignacion piloto: '.$neworderid;


			}else{

				$concepto='Reasignacion piloto: '.$neworderid;


			}

			//se guarda el saldo que le queda

				$userId = $rowsNewOrder[0]['ujid'];
				$idorder=$neworderid;


				

				$lid= vikrentcar::saveDebito($idorder,$userId, $concepto);

				$newSaldo = vikrentcar::calcularNuevoSaldo($idorder, $userId, $lid);

				if($newSaldo<0){

				$newSaldo=0;
				}

				
										

				vikrentcar::saveSaldo($newSaldo, $userId);

				return $newSaldo;


	}

	function dayValidTs($days, $first, $second) {
		
		
		
		
		$secdiff = $second - $first;
		//86400 es la cantidad de segundos que hay en un dia
		$daysdiff = $secdiff / 86400;
		if (is_int($daysdiff)) {
			if ($daysdiff < 1) {
				$daysdiff = 1;
			}
		} else {
			if ($daysdiff < 1) {
				$daysdiff = 1;
			} else {
				$dbo = & JFactory :: getDBO();
				$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='hoursmorerentback';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$s = $dbo->loadAssocList();
				$sum = floor($daysdiff) * 86400;
				$newdiff = $secdiff - $sum;
				$maxhmore = $s[0]['setting'] * 3600;
				if ($maxhmore >= $newdiff) {
					$daysdiff = floor($daysdiff);
				} else {
					$daysdiff = ceil($daysdiff);
				}
			}
		}
		return ($daysdiff == $days ? true : false);
	}

	function sayCostPlusIva($cost, $idprice) {
		$dbo = & JFactory :: getDBO();
		$session =& JFactory::getSession();
		$sval = $session->get('ivaInclusa', '');
		if(strlen($sval) > 0) {
			$ivainclusa = $sval;
		}else {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$iva = $dbo->loadAssocList();
			$session->set('ivaInclusa', $iva[0]['setting']);
			$ivainclusa = $iva[0]['setting'];
		}
		if (intval($ivainclusa) == 0) {
			$q = "SELECT `idiva` FROM `#__vikrentcar_prices` WHERE `id`='" . $idprice . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$pidiva = $dbo->loadAssocList();
				$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $pidiva[0]['idiva'] . "';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() == 1) {
					$paliq = $dbo->loadAssocList();
					$subt = 100 + $paliq[0]['aliq'];
					$op = ($cost * $subt / 100);
					//$op=money_format('%.2n', $op);
					//$op=number_format($op, 2);
					return $op;
				}
			}
		}
		return $cost;
	}


	function sayCostWithCredito( $idprice, $idcar ,$hours, $precio) {
		//revisa el credito del usuario
		$credito= vikrentcar ::verPaqueteUser();
		//se hace la diferencia para buscar el costo real
		if($credito!=0 || !isset($credito) || !empty($credito) ){
		$hoursdiff=$hours-$credito;
		//Compara si los creditos cubren todo el valor  si es asi el total a pagar ser� cero
		if($hoursdiff<=0){
			return 0;
		}
		$dbo = & JFactory :: getDBO();
		$q=  "SELECT `cost` FROM  `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' AND   `idprice`='".$idprice."'"." AND   `idcar`='".$idcar."'";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$cost = $dbo->loadAssocList();
		
		
	
		return $cost[0]['cost'];

		}else{

		return  $precio;

		}
	}
	
	function sayCostWithCreditoDay( $idprice, $idcar ,$daysdiff) {
		//revisa el credito del usuario
		$credito= vikrentcar ::verPaqueteUser();
		//se hace la diferencia para buscar el costo real
		//$hoursdiff=$hours-$credito;
		//Compara si los creditos cubren todo el valor  si es asi el total a pagar ser� cero
		if($daysdiff<=0){
			return 0;
		}
		$dbo = & JFactory :: getDBO();
		$q=  "SELECT `cost` FROM  `#__vikrentcar_dispcost` WHERE `days`='" . $daysdiff . "' AND   `idprice`='".$idprice."'"." AND   `idcar`='".$idcar."'";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$cost = $dbo->loadAssocList();
	
	
	
		return $cost[0]['cost'];
	}

	function reversarSaldoPaquete( $orderinfo,$userId,$nowts ){

		$dbo = & JFactory :: getDBO();		


		$q="SELECT  p.val_gastado as val_gastado, pro.saldo_paq as saldo_paq FROM `#__vikrentcar_paquetes` as p  INNER JOIN `#__vikrentcar_profiles` as pro ON pro.user_id=p.id_user WHERE p.id =".$orderinfo[0]['id_paq']. " AND p.until>=".$nowts." AND p.fecha_ini<".$nowts;;
		$dbo->setQuery($q);
		$dbo->Query($q);

	

			

			//si el paquete no ha vencido se revierte el proceso agregando el saldo al saldo pquete del usuario

			if ($dbo->getNumRows() > 0) {

					$datosPaquete = $dbo->loadAssocList();
					//actualizo valor gastado del paquete
					//
				

					$newvalgastado= $datosPaquete[0]['val_gastado']-$orderinfo[0]['totpaid'];


				 
					$q="UPDATE  `#__vikrentcar_paquetes` SET  `val_gastado`  ='".$newvalgastado."' WHERE `id`='". $orderinfo[0]['id_paq']."';";  
					$dbo->setQuery($q);
					$dbo->Query($q);

					

					//actualizo saldo paquete del usuario

					$newsaltopaqtotal=$datosPaquete[0]['saldo_paq']+$orderinfo[0]['totpaid'];


					$q="UPDATE  `#__vikrentcar_profiles` SET  `saldo_paq`  ='".$newsaltopaqtotal."' WHERE `user_id`='". $userId."';";  
					$dbo->setQuery($q);
					$dbo->Query($q);

					return true;



			}else{



				    return false;




			}







	}
	
	
	function sayCostMinusIva($cost, $idprice) {
		$dbo = & JFactory :: getDBO();
		$session =& JFactory::getSession();
		$sval = $session->get('ivaInclusa', '');
		if(strlen($sval) > 0) {
			$ivainclusa = $sval;
		}else {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$iva = $dbo->loadAssocList();
			$session->set('ivaInclusa', $iva[0]['setting']);
			$ivainclusa = $iva[0]['setting'];
		}
		if (intval($ivainclusa) == 1) {
			$q = "SELECT `idiva` FROM `#__vikrentcar_prices` WHERE `id`='" . $idprice . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$pidiva = $dbo->loadAssocList();
				$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $pidiva[0]['idiva'] . "';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() == 1) {
					$paliq = $dbo->loadAssocList();
					$subt = 100 - $paliq[0]['aliq'];
					$op = ($cost * $subt/100);
					//calculo anterior
					//$subt = 100 + $paliq[0]['aliq'];
					//$op = ($cost * 100 / $subt);
					//$op=money_format('%.2n', $op);
					//					$op=number_format($op, 2);
					return $op;
				}
			}
		}
		return $cost;
	}

	function sayOptionalsPlusIva($cost, $idiva) {
		$dbo = & JFactory :: getDBO();
		$session =& JFactory::getSession();
		$sval = $session->get('ivaInclusa', '');
		if(strlen($sval) > 0) {
			$ivainclusa = $sval;
		}else {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$iva = $dbo->loadAssocList();
			$session->set('ivaInclusa', $iva[0]['setting']);
			$ivainclusa = $iva[0]['setting'];
		}
		if (intval($ivainclusa) == 0) {
			$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $idiva . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$piva = $dbo->loadAssocList();
				$subt = 100 + $piva[0]['aliq'];
				$op = ($cost * $subt / 100);
				//				$op=number_format($op, 2);
				return $op;
			}
		}
		return $cost;
	}

	function sayOptionalsMinusIva($cost, $idiva) {
		$dbo = & JFactory :: getDBO();
		$session =& JFactory::getSession();
		$sval = $session->get('ivaInclusa', '');
		if(strlen($sval) > 0) {
			$ivainclusa = $sval;
		}else {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$iva = $dbo->loadAssocList();
			$session->set('ivaInclusa', $iva[0]['setting']);
			$ivainclusa = $iva[0]['setting'];
		}
		if (intval($ivainclusa) == 1) {
			$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $idiva . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$piva = $dbo->loadAssocList();
				$subt = 100 + $piva[0]['aliq'];
				$op = ($cost * 100 / $subt);
				//				$op=number_format($op, 2);
				return $op;
			}
		}
		return $cost;
	}

	function getSecretLink() {
		$sid = mt_rand();
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `sid` FROM `#__vikrentcar_orders`;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if (@ $dbo->getNumRows() > 0) {
			$all = $dbo->loadAssocList();
			foreach ($all as $s) {
				$arr[] = $s['sid'];
			}
			if (in_array($sid, $arr)) {
				while (in_array($sid, $arr)) {
					$sid++;
				}
			}
		}
		return $sid;
	}

	function buildCustData($arr, $sep) {
		$cdata = "";
		foreach ($arr as $k => $e) {
			if (strlen($e)) {
				$cdata .= (strlen($k) > 0 ? $k . ": " : "") . $e . $sep;
			}
		}
		return $cdata;
	}

	function estaCancelada($idorder) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id` FROM `#__vikrentcar_orders_canceled` WHERE `id`=".$idorder.";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() > 0) {

			return true;

		}else{

			return false;
		}
		
	}

	function sendAdminMail($to, $subject, $ftitle, $ts, $custdata, $carname, $first, $second, $pricestr, $optstr, $tot, $status, $place = "", $returnplace = "", $maillocfee = "", $payname = "", $couponstr = "") {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencyname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$currencyname = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$formdate = $dbo->loadResult();
		if ($formdate == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$msg = $ftitle . "\n\n";
		$msg .= JText :: _('VRLIBONE') . " " . date($df . ' H:i', $ts) . "\n";
		$msg .= JText :: _('VRLIBTWO') . ":\n" . $custdata . "\n";
		$msg .= JText :: _('VRLIBTHREE') . ": " . $carname . "\n";
		$msg .= JText :: _('VRLIBFOUR') . " " . date($df . ' H:i', $first) . "\n";
		$msg .= JText :: _('VRLIBFIVE') . " " . date($df . ' H:i', $second) . "\n";
		$msg .= (!empty ($place) ? JText :: _('VRRITIROCAR') . ": " . $place . "\n" : "");
		$msg .= (!empty ($returnplace) ? JText :: _('VRRETURNCARORD') . ": " . $returnplace . "\n" : "");
		$msg .= $pricestr . "\n";
		$msg .= $optstr . "\n";
		if (!empty ($maillocfee) && $maillocfee > 0) {
			$msg .= JText :: _('VRLOCFEETOPAY') . ": " . number_format($maillocfee, 2) . " " . $currencyname . "\n\n";
		}
		//vikrentcar 1.6 coupon
		if(strlen($couponstr) > 0) {
			$expcoupon = explode(";", $couponstr);
			$msg .= JText :: _('VRCCOUPON')." ".$expcoupon[2].": -" . $expcoupon[1] . " " . $currencyname . "\n\n";
		}
		//
		$msg .= JText :: _('VRLIBSIX') . ": " . $tot . " " . $currencyname . "\n\n";
		if (!empty ($payname)) {
			$msg .= JText :: _('VRLIBPAYNAME') . ": " . $payname . "\n\n";
		}
		$msg .= JText :: _('VRLIBSEVEN') . ": " . $status;
		
//		$msg = utf8_decode($msg);
//		$msg = mb_convert_encoding($msg, 'KOI8-R', 'UTF-8');

		$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
		if (@ mail($to, $subject, $msg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8")) {
			return true;
		}
		return false;
	}
	
	function loadEmailTemplate ($tipo,  $mostrar) {
		define('_VIKRENTCAREXEC', '1');
		ob_start();
		if($tipo!='piloto'){

			if($mostrar=='standby'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_standby.php";
			}

			if($mostrar=='confirmed'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_confirmed.php";

			}


			if($mostrar=='canceled'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_cancelacion.php";


			}

			if($mostrar=='error'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_error.php";

			}


		}else{

			if($mostrar=='standby'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_pilot_standby.php";
			}

			if($mostrar=='confirmed'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_pilot_confirmed.php";

			}


			if($mostrar=='canceled'){

				include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_cancelacion_piloto.php";

			}

			




		}
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	function loadtemplateaspirante(){

		define('_VIKRENTCAREXEC', '1');
		ob_start();

		include JPATH_SITE . DS ."components". DS ."com_vikrentcar". DS . "helpers" . DS ."email_tmpl_alerta_aspirante.php";

		$content = ob_get_contents();
		ob_end_clean();
		return $content;



	}

	function parseEmailTemplateasyPiloto($tmpl, $logo, $fecha, $mensaje, $link){

		$parsed = $tmpl;
		$parsed = str_replace("{logo}",  $logo, $parsed);
		$parsed = str_replace("{fecha}", $fecha, $parsed);
		$parsed = str_replace("{mensaje}", $mensaje, $parsed);
		$parsed = str_replace("{enlace}", $link, $parsed);

		return $parsed;



	}

	function testnamescat(){ 

		$idcat='14';

		print_r(JText::_('VRNAMECAT'.$idcat));

	}

	function getcatnotvehiculo(){


		 $formsdata= self:: getformsconstructor();
		 $formsdata =json_decode($formsdata, true);

		 foreach ($formsdata as $key => $value) {

		 	foreach ($value as  $value2) {
		 		if($value2['notvehic']=='1'){

		 			$catidnotvehicules[]= ($value2['catid']);


		 		}

		 	}
		 	
		 }

		 $catidnotvehicules= array_unique($catidnotvehicules);

		 return $catidnotvehicules;


	}

	function getidpricesnotshowtime(){


		 $formsdata= self:: getformsconstructor();
		 $formsdata =json_decode($formsdata, true);

		 foreach ($formsdata as $key => $value) {

		 	foreach ($value as  $value2) {
		 		if($value2['showtime']=='0'){

		 			$nosthowtime[]= (int)$value2['idprice'];


		 		}

		 	}
		 	
		 }

		$nosthowtime= array_unique($nosthowtime);

		$data= implode(";",$nosthowtime);

		return $data;


	}
	
	function parseEmailTemplate ($tmpl, $orderid, $currencyname, $status, $tlogo, $tcname, $todate, $tcustdata, $tiname, $tpickupdate, $tdropdate, $tpickupplace, $tdropplace, $tprices, $topts, $tlocfee, $ttot, $tlink, $tfootm, $couponstr, $datosConductor, $datosCancelacion, $saldo,  $linkinfopersonal=null, $df='', $idprice=null) {
		
		
		$datosnovehi= vikrentcar::getcatnotvehiculo();

		$dbo = & JFactory :: getDBO();
		$q = "SELECT c.idcat,  o.idCond FROM `#__vikrentcar_orders` o INNER JOIN `#__vikrentcar_cars` c  ON c.id=o.idcar  WHERE o.id='".$orderid."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$datoscat = $dbo->loadAssocList();

		//detecta si carro es un servicio especial que no se le asigna un auto como tal


		//$namecat= self:: getCatName(trim($datoscat[0]['idcat'],';'));

        

		$arraycusdata= self::convertCustdataToArray($tcustdata);


		$tpickupplace =$tpickupplace."\n". $arraycusdata['Dirección Inicial'];

		$tdropplace = $arraycusdata[JText :: _('ORDER_ADDRESS')];

		$quiencancelo= $datosCancelacion[0]['nameQuienCancelo'];
		$motivocancelacion= $datosCancelacion[0]['motivosCancel'];

		
		$tpickuptime = date('g:i a', strtotime($tpickupdate));
		$tdroptime = date('g:i a', strtotime($tdropdate));

		$tpickupdate = date($df, strtotime($tpickupdate));
		$tdropdate = date($df, strtotime($tdropdate));

		$idcat= trim($datoscat[0]['idcat'],';');
		$isnotveh=false;

		foreach ($datosnovehi as $key => $value) {


			if((preg_match("/".$value.";/i", $datoscat[0]['idcat']))) {

			     $titlelPilotdata=  '<h3 class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0 0 10px; padding: 0;">'.JText::_('VRNAMECAT'.$idcat).'</h3>';
			  	 $placadata='';
			  	 $isnotveh=true;

			     break;

			}else{

			     $titlelPilotdata=  '<h3 class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0 0 10px; padding: 0;">'.JText::_('VRLIBNINEPILOT').'</h3>';
				 $placadata=' <p >'. JText::_('VRLIBNINEPLACAVEHI').': '.$datosConductor[0]['placa'].'</p>';
			}


		
			# code...
		}

		$parsed = $tmpl;
		$parsed = str_replace("{logo}", $tlogo, $parsed);
		$parsed = str_replace("{company_name}", $tcname, $parsed);
		$parsed = str_replace("{order_id}", $orderid, $parsed);
		$statusclass = $status == JText :: _('VRCOMPLETED') ? "confirmed" : "standby";
		$parsed = str_replace("{order_status_class}", $statusclass, $parsed);
		$parsed = str_replace("{order_status}", $status, $parsed);
		$parsed = str_replace("{order_date}", $todate, $parsed);
		$parsed = str_replace("{customer_info}", $tcustdata, $parsed);
		$parsed = str_replace("{item_name}", $tiname, $parsed);
		$parsed = str_replace("{pickup_date}", $tpickupdate, $parsed);
		$parsed = str_replace("{tpickuptime}", $tpickuptime, $parsed);
		$parsed = str_replace("{pickup_location}", $tpickupplace, $parsed);
		$parsed = str_replace("{title_datapilot}", $titlelPilotdata, $parsed);

		
		

		

		$q = "SELECT  o.id, cves.placa, cves.infoCar, pro.name, pro.lname, pro.movil, pro.num_doc FROM `#__vikrentcar_orders` o INNER JOIN `#__vikrentcar_cars` cvc ON cvc.id=o.idcar LEFT JOIN `#__vikrentcar_esp_services` cves ON cves.id_Order=o.id INNER JOIN  `#__vikrentcar_profiles` pro ON pro.user_id=cves.id_Cond WHERE cves.id_Order='".$orderid."';";

		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() >0) {

			$datoscaresp = $dbo->loadAssocList();

			$parsed = str_replace("{carInfo}", $datoscaresp[0]['infoCar'], $parsed);
			if($datoscaresp[0]['placa']==''){

				$placadata='';
				$parsed = str_replace("{placa_Cond}", $placadata, $parsed);


			}else{

				$placadata=' <p >'. JText::_('VRLIBNINEPLACAVEHI').': '.$datoscaresp[0]['placa'].'</p>';
				$parsed = str_replace("{placa_Cond}", $placadata, $parsed);


			}


			

			
			$parsed = str_replace("{name_Cond}", $datoscaresp[0]['name'].' '. $datoscaresp[0]['lname'] , $parsed);
			$parsed = str_replace("{CelularCond}", $datoscaresp[0]['movil'], $parsed);
			$parsed = str_replace("{CedulaCond}", $datoscaresp[0]['num_doc'], $parsed);

		}else{

			if(($datoscat[0]['idCond']==0 || $datoscat[0]['idCond']=='' || $datoscat[0]['idCond']==null )&& $isnotveh){

				$msgemail= JText::sprintf( 'VRNAMSGEMAIL', JText::_('VRNAMECAT'.$idcat) );

				$parsed = str_replace("{CedulaCond}",$msgemail, $parsed);
			//
			}

			$parsed = str_replace("{placa_Cond}", $placadata, $parsed);

			$parsed = str_replace("{carInfo}", $datosConductor[0]['carInfo'], $parsed);
			$parsed = str_replace("{name_Cond}", $datosConductor[0]['nameCond'], $parsed);
			$parsed = str_replace("{CelularCond}", $datosConductor[0]['CelularCond'], $parsed);
			$parsed = str_replace("{CedulaCond}", $datosConductor[0]['CedulaCond'], $parsed);

		}

			

		

		

	

			
			
			
			



		

		

		


		$divsdropoff_date='  <p style="margin: 0px 0 5px; padding: 0;"><span class="Stile12" style="display: block; font-size: 14px; font-weight: bold;  ">'.JText::_('VRLIBTWELVE').': </span><span class="Stile9" style="display: block; font-size: 14px;">'.$tdropdate.'</span></p><p style="margin: 0px 0 5px; padding: 0;"><span class="Stile12" style="display: block; font-size: 14px; font-weight: bold; float:left;">'. JText::_('VRLIBTTIMEFIN').':</span><span class="Stile9" style="display: block; font-size: 14px;">'.$tdroptime.'</span></p>';

		$idpricesnotshowtime= self::getidpricesnotshowtime();

		if((preg_match("/".$idprice.";/i", $idpricesnotshowtime))) {

		//if(vikrentcar::gettimeflagprice($idprice)=='1'){
		//
			$parsed = str_replace("{fecha_entrega}", '', $parsed);

			
			//$parsed = str_replace("{tdroptime}", $tdroptime, $parsed);

		}else{
			//no se muestra fecha de entrega
			$parsed = str_replace("{fecha_entrega}", $divsdropoff_date, $parsed);
			
			//$parsed = str_replace("{dropoff_date}", '-', $parsed);
			//$parsed = str_replace("{tdroptime}", '-', $parsed);

		}
		

		$parsed = str_replace("{dropoff_location}", $tdropplace, $parsed);
		
		

		

		
		
			
		$parsed = str_replace("{quienCancelo}", $quiencancelo, $parsed);
		$parsed = str_replace("{motivosCancel}", $motivocancelacion, $parsed);

		$parsed = str_replace("{saldo}", $saldo, $parsed);

		$parsed = str_replace("{infopersonal}", $linkinfopersonal, $parsed);




		
		
		//order details

		//
		//locations fee
		


			$orderdetails = "";
			$expdet = explode("\n", $tprices);
			$faredets = explode(":", $expdet[0]);
			$orderdetails .= '<div class="hireordata"><span style="font-size: 14px;">'.$faredets[0];
			if(!empty($expdet[1])) {
				$attrfaredets = explode(":", $expdet[1]);
				if(strlen($attrfaredets[1]) > 0) {
					$orderdetails .= ' - '.$attrfaredets[0].':'.$attrfaredets[1];
				}
			}


			$fareprice = trim(str_replace($currencyname, "", $faredets[1]));
			$orderdetails .= '</span><div align="right"><span style="font-size: 14px;">'.$currencyname.' '.number_format($fareprice, 2).'</span></div></div>';
			//options
			if(strlen($topts) > 0) {
				$expopts = explode("\n", $topts);
				foreach($expopts as $optinfo) {
					if(!empty($optinfo)) {
						$splitopt = explode(":", $optinfo);
						$optprice = trim(str_replace($currencyname, "", $splitopt[1]));
						$orderdetails .= '<div class="hireordata"><span style="font-size: 14px;">'.$splitopt[0].'</span><div align="right"><span style="font-size: 14px;">'.$currencyname.' '.number_format($optprice, 2).'</span></div></div>';
					}
				}
			}

			if(!empty($tlocfee) && $tlocfee > 0) {
			$orderdetails .= '<div class="hireordata"><spanstyle="font-size: 14px;">'.JText :: _('VRLOCFEETOPAY').'</span><div align="right"><span style="font-size: 14px;">'.$currencyname.' '.number_format($tlocfee, 2).'</span></div></div>';
			}
		//
		//coupon
			if(strlen($couponstr) > 0) {
				$expcoupon = explode(";", $couponstr);
				$orderdetails .= '<br/><div class="hireordata"><span style="font-size: 14px;">'.JText :: _('VRCCOUPON').' '.$expcoupon[2].'</span><div align="right"><span style="font-size: 14px;">- '.$currencyname.' '.number_format($expcoupon[1], 2).'</span></div></div>';
			}
		//
			$parsed = str_replace("{order_details}", $orderdetails, $parsed);

			$parsed = str_replace("{order_total}", $currencyname.' '.number_format($ttot, 2), $parsed);
			$parsed = str_replace("{order_link}", '<a href="'.$tlink.'">'.$tlink.'</a>', $parsed);


	

		$parsed = str_replace("{footer_emailtext}", $tfootm, $parsed);
		//
		
		
		
		return $parsed;
	}
	
	function makeemail($to, $subject, $ftitle, $ts, $custdata, $carname, $first, $second, $pricestr, $optstr, $tot, $link, $status, $place = "", $returnplace = "", $maillocfee = "", $orderid = "", $strcouponeff = "", $saldo=0, $linkinfopersonal=null, $idprice=null){
		
		if(JText :: _('VRINATTESA')==$status){

			$mostrar='standby';
		}

		if(JText :: _('VRCOMPLETED')==$status){

			$mostrar='confirmed';
		}

		if('canceled'==$status){

			$mostrar='canceled';
		}

		if('error'==$link){

			$mostrar='error';
		}




		$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencyname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$currencyname = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='adminemail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$adminemail = $dbo->loadResult();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='footerordmail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_config` WHERE `param`='sendjutility';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sendmethod = $dbo->loadAssocList();
		$useju = intval($sendmethod[0]['setting']) == 1 ? true : false;

		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sitelogo = $dbo->loadResult();


		$q = "SELECT  c.placa as placa, CONCAT(p.name, ' ', p.lname) as nameCond, p.num_doc as CedulaCond, p.movil as CelularCond , c.info as carInfo FROM `#__vikrentcar_orders` o INNER JOIN `#__vikrentcar_cars` c ON c.id=o.idcar INNER JOIN `#__vikrentcar_profiles` p ON p.user_id=c.idCond WHERE o.id=".$orderid.";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$datosConductor = $dbo->loadAssocList();
		
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$formdate = $dbo->loadResult();
		if ($formdate == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$footerordmail = $ft[0]['setting'];
		$textfooterordmail = strip_tags($footerordmail);


		if($mostrar=='canceled'){

			$q = "SELECT  c.motivo as motivosCancel, CONCAT(p.name, ' ',IFNULL(p.lname, ' ')) as nameQuienCancelo FROM `#__vikrentcar_canceledorders` c INNER JOIN `#__vikrentcar_profiles` p ON c.juser=p.user_id WHERE c.id_order=".$orderid.";";
			$dbo->setQuery($q);
			$dbo->Query($q);

		
			$datosCancelacion = $dbo->loadAssocList();

	
		}
		//text part
		$msg = $ftitle . "\n\n";
		$msg .= JText :: _('VRLIBEIGHT') . " " . date($df . ' H:i', $ts) . "\n";
		$msg .= JText :: _('VRLIBNINE') . ":\n" . $custdata . "\n";
		$msg .= JText :: _('VRLIBTEN') . ": " . $carname . "\n";
		$msg .= JText :: _('VRLIBELEVEN') . " " . date($df . ' H:i', $first) . "\n";
		$msg .= JText :: _('VRLIBTWELVE') . " " . date($df . ' H:i', $second) . "\n";
		$msg .= (!empty ($place) ? JText :: _('VRRITIROCAR') . ": " . $place . "\n" : "");
		$msg .= (!empty ($returnplace) ? JText :: _('VRRETURNCARORD') . ": " . $returnplace . "\n" : "");
		$msg .= $pricestr . "\n";
		$msg .= $optstr . "\n";
		if (!empty ($maillocfee) && $maillocfee > 0) {
			$msg .= JText :: _('VRLOCFEETOPAY') . ": " . number_format($maillocfee, 2) . " " . $currencyname . "\n\n";
		}
		
		

		 $msg .= JText :: _('VRLIBSEVEN') . ": " . $status . "\n\n";
		


		$msg .= JText :: _('VRLIBSIX') . " " . $tot . " " . $currencyname . "\n";
	    $msg .= JText :: _('VRLIBSEVEN') . ": " . $status . "\n\n";

		$msg .= JText :: _('VRLIBTENTHREE') . ": \n" . $link;
		
		$msg .= (strlen(trim($textfooterordmail)) > 0 ? "\n" . $textfooterordmail : "");
		//
		//html part
		$from_name = $adminemail;
		$from_address = $adminemail;
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = $to;
		$to_address = $to;
		//vikrentcar 1.5
		$tmpl = self::loadEmailTemplate($link, $mostrar);
		//
		if (!$useju) {
			require_once ("./components/com_vikrentcar/class/email_message.php");
			$email_message = new email_message_class;
			$email_message->SetEncodedEmailHeader("To", $to_address, $to_name);
			$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
			$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
			$email_message->SetHeader("Sender", $from_address);
			//			if(defined("PHP_OS")
			//			&& strcmp(substr(PHP_OS,0,3),"WIN"))
			//				$email_message->SetHeader("Return-Path",$error_delivery_address);

			$email_message->SetEncodedHeader("Subject", $subject);
			$attachlogo = false;
			if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
				$image = array (
				"FileName" => JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo, "Content-Type" => "automatic/name", "Disposition" => "inline");
				$email_message->CreateFilePart($image, $image_part);
				$image_content_id = $email_message->GetPartContentID($image_part);
				$attachlogo = true;
			}
			$tlogo = ($attachlogo ? "<img src=\"cid:" . $image_content_id . "\" alt=\"imglogo\"/>\n" : "");
		} else {
			$attachlogo = false;
			if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
				$attachlogo = true;
			}
			$tlogo = ($attachlogo ? "<img src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo . "\" alt=\"imglogo\"/>\n" : "");
		}
		//vikrentcar 1.5
		$tcname = $ftitle."\n";
		$todate = date($df . ' H:i', $ts)."\n";
		$tcustdata = nl2br($custdata)."\n";
		$tiname = $carname."\n";
		$tpickupdate = date($df . ' H:i', $first)."\n";
		$tdropdate = date($df . ' H:i', $second)."\n";
		$tpickupplace = (!empty ($place) ? $place."\n" : "");
		$tdropplace = (!empty ($returnplace) ? $returnplace."\n" : "");
		
	

			$tprices = $pricestr;
			$topts = $optstr;
			$tlocfee = $maillocfee;

			$ttot = $tot."\n";
		    $tlink = $link;


	
		
		$tfootm = $footerordmail;
		$hmess = self::parseEmailTemplate($tmpl, $orderid, $currencyname, $status, $tlogo, $tcname, $todate, $tcustdata, $tiname, $tpickupdate, $tdropdate, $tpickupplace, $tdropplace, $tprices, $topts, $tlocfee, $ttot, $tlink, $tfootm, $strcouponeff, $datosConductor, $datosCancelacion, $saldo, $linkinfopersonal,$df, $idprice);
		//
		
		if (!$useju) {
			$email_message->CreateQuotedPrintableHTMLPart($hmess, "", $html_part);
			$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($msg), "", $text_part);
			$alternative_parts = array (
				$text_part,
				$html_part
			);
			$email_message->CreateAlternativeMultipart($alternative_parts, $alternative_part);
			$related_parts = array (
				$alternative_part,
				$image_part
			);
			$email_message->AddRelatedMultipart($related_parts);
			$error = $email_message->Send();
			if (strcmp($error, "")) {
				//$msg = utf8_decode($msg);
				@ mail($to, $subject, $msg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
			}
		} else {
			$hmess = '<html>'."\n".'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>'."\n".'<body>'.$hmess.'</body>'."\n".'</html>';
			//JUtility :: sendMail($from_address, $from_name, $to, $subject, $hmess, true, null, null, null, $reply_address, $reply_address);
			return $hmess;
		}
		
	
		
		
	
		
		}

	function verificarDisponibilidadPiloto($idcar, $first, $second, $morehst=0){

		$dbo = & JFactory :: getDBO();

		$freeCond=false;


		//$q="SELECT * FROM `#__vikrentcar_orders` o  INNER JOIN `#__vikrentcar_cars` c  ON c.id=o.idcar LEFT JOIN `#__vikrentcar_esp_services` cves ON cves.id_Order=o.id WHERE c.idCond=".$idUser."  OR cves.id_Cond=".$idUser." AND o.ritiro>=".$nowts; 
		
		$q="SELECT c.idCond AS 'Propietario Car', cvo.id AS 'idOrden', cvo.ritiro AS 'ritiro',  cvo.consegna AS 'consegna'  FROM  `chun4_vikrentcar_cars` c INNER JOIN `chun4_vikrentcar_esp_services` cves ON cves.id_Cond=c.idCond INNER JOIN chun4_vikrentcar_orders cvo ON cvo.id=cves.id_Order WHERE c.id='".$idcar."';";
		$dbo->setQuery($q);
		$dbo->Query($q);

		$ordenes = $dbo->loadAssocList();

		foreach ($ordenes as $key => $value) {

		
			//if((($first<$value['ritiro'] && ($second)<$value['ritiro']) || ($first>($value['consegna']) && ($second)>($value['consegna']))) ){
			if(($first <=$value['ritiro']) && ($second >$value['ritiro']) || ($first >$value['ritiro']) && ($second <=$value['consegna']+$morehst) || ($first <=$value['consegna']+$morehst) && ($second >$value['consegna']+$morehst) ){
			
			$freeCond= true;
			
			

			}else{

			$freeCond= false;
		
			}

		}


		return $freeCond;



		
	}


	

	function arreglarcustdataReportes($key2, $val, $val_a, $filas='', $isexport=false){

	/*
				arreglo para hacer reporte sacando datos de custdata



				 */
				
				$noprint=false;

					switch ($key2) {

						case 'Funcionario':
						$noprint=true;

						

						
					
						$val_final=  $val_a['NOMBRE'].$val_a['APELLIDO'];


						break;

						case 'Destino Inicial':

						$noprint=true;

						
						$val_final= $val_a['DIRECCIÓN INICIAL'];

						break;

						

						case 'Destino Final':

						$noprint=true;

						$val_final=  $val_a['DIRECCIÓN FINAL'];
						break;

						

						
						case 'Costo SVC':

						if($isexport){

							$val_final=$val;


						}else{

							//$val_a= '<input type="text" name="costoCvs" id="costoCvs" />';
					    	$val_final= '<input type="text" name="costoCvs_'.$filas.'" id="costoCvs_'.$filas.'"  value="'.$val.'" /><div id ="divcost_'.$filas.'" >  <a class="linksavecost"  id="linkcosto_'.$filas.'"  href="">Guardar</a> </div>';



						}

					


						break;




				

						default:

						$val_final=$val;

						break;


						




					}

				

					return $val_final;





	}

	function convertCustdataToArray($datos){

		

		$datos2= split("\n", $datos);

		foreach($datos2 as $value){



		$datos3= split(":", $value);



		

		$arraydat[$datos3[0]]=$datos3[1];






		}

		return $arraydat;



	}

	function creareasyemail(){

		$dbo = & JFactory :: getDBO();

		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sitelogo = $dbo->loadResult();

		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$formdate = $dbo->loadResult();
		if ($formdate == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}

		if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
			$attachlogo = true;
		}
		$tlogo = ($attachlogo ? "<img src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo . "\" alt=\"imglogo\"/>\n" : "");
	


        $mensaje='Se ha registrado un nuevo aspirante, haga click en el siguiente enlace:';
        $link='<a href="'.JURI :: root() .'administrator/index.php?option=com_profiler&view=users&filtroasp=15">Haga Click Aquí </a>';
		$todate = date($df . ' H:i').'</br>';

		$tmpl =self::loadtemplateaspirante();

		$body = self::parseEmailTemplateasyPiloto($tmpl, $tlogo, $todate, $mensaje, $link );
		
		

		$hmess = '<html>'."\n".'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>'."\n".'<body>'.$body.'</body>'."\n".'</html>';

		return $hmess;



	}
	
	function crearEmailCancelacion($cancelClient, $subject, $ftitle, $ts, $custdata, $carname, $first, $second, $pricestr, $optstr, $tot, $link, $status, $place = "", $returnplace = "", $maillocfee = "", $orderid, $strcouponeff = ""){
		
		
		
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencyname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$currencyname = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='adminemail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$adminemail = $dbo->loadResult();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='footerordmail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_config` WHERE `param`='sendjutility';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sendmethod = $dbo->loadAssocList();
		$useju = intval($sendmethod[0]['setting']) == 1 ? true : false;
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sitelogo = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$formdate = $dbo->loadResult();
		if ($formdate == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$footerordmail = $ft[0]['setting'];
		$textfooterordmail = strip_tags($footerordmail);
		//text part
		$msg = $ftitle . "\n\n";
		$msg .= JText :: _('VRLIBEIGHT') . " " . date($df . ' H:i', $ts) . "\n";
		$msg .= JText :: _('VRLIBNINE') . ":\n" . $custdata . "\n";
		$msg .= JText :: _('VRLIBTEN') . ": " . $carname . "\n";
		$msg .= JText :: _('VRLIBELEVEN') . " " . date($df . ' H:i', $first) . "\n";
		$msg .= JText :: _('VRLIBTWELVE') . " " . date($df . ' H:i', $second) . "\n";
		$msg .= (!empty ($place) ? JText :: _('VRRITIROCAR') . ": " . $place . "\n" : "");
		$msg .= (!empty ($returnplace) ? JText :: _('VRRETURNCARORD') . ": " . $returnplace . "\n" : "");
		$msg .= $pricestr . "\n";
		$msg .= $optstr . "\n";
		if (!empty ($maillocfee) && $maillocfee > 0) {
			$msg .= JText :: _('VRLOCFEETOPAY') . ": " . number_format($maillocfee, 2) . " " . $currencyname . "\n\n";
		}
		$msg .= JText :: _('VRLIBSIX') . " " . $tot . " " . $currencyname . "\n";
		$msg .= JText :: _('VRLIBSEVEN') . ": " . $status . "\n\n";
		if($cancelClient=='1'){
		$msg .= JText :: _('VRLIBTENCANCCUST') . ": \n" ;	
		}else{
		$msg .= JText :: _('VRLIBTENCANCPILOT') . ": \n" ;
		}
		
		$msg .= (strlen(trim($textfooterordmail)) > 0 ? "\n" . $textfooterordmail : "");
		//
		//html part
		$from_name = $adminemail;
		$from_address = $adminemail;
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = $to;
		$to_address = $to;
		//vikrentcar 1.5
		$tmpl = self::loadEmailTemplate();
		
		$attachlogo = false;
		if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
			$attachlogo = true;
		}
		$tlogo = ($attachlogo ? "<img src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo . "\" alt=\"imglogo\"/>\n" : "");
	
	
			//vikrentcar 1.5
		$tcname = $ftitle."\n";
		$todate = date($df . ' H:i', $ts)."\n";
		$tcustdata = nl2br($custdata)."\n";
		$tiname = $carname."\n";
		$tpickupdate = date($df . ' H:i', $first)."\n";
		$tdropdate = date($df . ' H:i', $second)."\n";
		$tpickupplace = (!empty ($place) ? $place."\n" : "");
		$tdropplace = (!empty ($returnplace) ? $returnplace."\n" : "");
		$tprices = $pricestr;
		$topts = $optstr;
		$tlocfee = $maillocfee;
		$ttot = $tot."\n";
		$tlink = $link;
		$tfootm = $footerordmail;
		$hmess = self::parseEmailTemplate($tmpl, $orderid, $currencyname, $status, $tlogo, $tcname, $todate, $tcustdata, $tiname, $tpickupdate, $tdropdate, $tpickupplace, $tdropplace, $tprices, $topts, $tlocfee, $ttot, $tlink, $tfootm, $strcouponeff);
		//
		
		if (!$useju) {
			$email_message->CreateQuotedPrintableHTMLPart($hmess, "", $html_part);
			$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($msg), "", $text_part);
			$alternative_parts = array (
				$text_part,
				$html_part
			);
			$email_message->CreateAlternativeMultipart($alternative_parts, $alternative_part);
			$related_parts = array (
				$alternative_part,
				$image_part
			);
			$email_message->AddRelatedMultipart($related_parts);
			$error = $email_message->Send();
			if (strcmp($error, "")) {
				//$msg = utf8_decode($msg);
				@ mail($to, $subject, $msg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
			}
		} else {
			$hmess = '<html>'."\n".'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>'."\n".'<body>'.$hmess.'</body>'."\n".'</html>';
			return $hmess;
			
			//JUtility :: sendMail($from_address, $from_name, $to, $subject, $hmess, true, null, null, null, $reply_address, $reply_address);
		}
		//$hmess = self::parseEmailTemplate($tmpl, $orderid, $currencyname, $status, $tlogo, $tcname, $todate, $tcustdata, $tiname, $tpickupdate, $tdropdate, $tpickupplace, $tdropplace, $tprices, $topts, $tlocfee, $ttot, $tlink, $tfootm, $strcouponeff);
		
	}
		
	function crearBodyEmail($infoOrder){
	
	$hmess='<p><img src="administrator/components/com_vikrentcar/resources/piloto-automatico.png" border="0" alt="imglogo" /></p>';
	$hmess.='La Orden: '.$rows[0]['id'].' ha sido cancelada por el Cliente: '.$idorder;
	
	$hmess.='<p class=\"Stile1\">Piloto Automatico</p><div class=\"statusorder\">
		<div class=\"boxstatusorder\">
		<p class=\"Stile1\">'.JText :: _('VRCORDERNUMBER').':'.$infoOrder['id'].'</p>
		</div>
		<div class=\"boxstatusorder\"><span class=\"Stile1\">'.JText :: _('VRCOMPLETED').': <span class=\"confirmed\">'.JText :: _('VRCANCEL').'</span></span></div>
		<div class="boxstatusorder"><strong>'.JText :: _('VRLIBEIGHT').':'.Date($infoOrder['ts'],"d/m/Y H:i").'</div>
		</div>
		<div class=\"persdetail\">
		<h3 class=\"Stile1\">'.JText :: _('VRLIBNINE').':</h3>
		'.JText :: _('ORDER_NAME').':'.$infoUser['name'].'<br /> '.JText :: _('ORDER_LNAME').':'. $infoUser['lname'].'<br />'.JText :: _('ORDER_EMAIL').':'. $infoUser['Email'].'<br /> '.JText :: _('ORDER_PHONE').':'. $infoUser['phone'].'<br />'.JText :: _('ORDER_ADDRESS').':'. $infoUser['address'].'<br />'.JText :: _('ORDER_CITY').':'. $infoUser['city'].'</div>
		<div class="hiremainbox">
		<div class="hirecar clearfix">
		<p><span class=\"Stile1\">'.JText :: _('VRLIBTHREE').':'. $infoCar['name']. '</span></p>
		<div class=\"hiredate\">
		<p><span class=\"Stile12\">'.JText :: _('VRLIBFOUR').':</span> <span class=\"Stile9\">'.Date($infoOrder['ritiro'],"d/m/Y H:i").' </span></p>
		<p><span class=\"Stile12\">'.JText :: _('VRRITIROCAR').' </span> <span class=\"Stile9\">'.$infoOrder['idplace'].' </span></p>
		</div>
		<div class=\"hiredate\">
		<p><span class=\"Stile12\">'.JText :: _('VRLIBFIVE').': </span> <span class=\"Stile9\">'.Date($infoOrder['consegna'],"d/m/Y H:i").'</span></p>
		<p><span class=\"Stile12\">'.JText :: _('VRPPLACE').' </span> <span class=\"Stile9\">'.$infoOrder['idplace'].' </span></p>
		</div>
		</div>
		<div class=\"hireorderdetail\">
		<p><span class=\"Stile1\">'.JText :: _('VRCORDERDETAILS').':</span></p>
		<div class=\"hireordata\"><span class=\"Stile9\">'.JText :: _('VRRENTAL').'</span>
		<div align=\"right\"><span class=\"Stile9\">PESOS '.$infoOrder['totpaid'].'</span></div>
		</div>
		<div class=\"hireordata\"><span class=\"Stile9\">Blindado</span>
		<div align=\"right\"><span class=\"Stile9\">PESOS 100,000.00</span></div>
		</div>
		<div class=\"hireordata hiretotal\"><span class=\"Stile10\">Total</span>
		<div align=\"right\"><strong>PESOS 145,000.00</strong></div>
		</div>
		</div>
		<br/>';
		
		
	}


		
	function enviarEmailAcymailing($idUser, $listid,$subject, $body){
	////////////////////Envio de email acymailing///////////////////////////////////
	//incluye libreria helper 
	if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_acymailing'.DS.'helpers'.DS.'helper.php')){
	echo 'This code can not work without the AcyMailing Component';
	return false;
	}
	$mensajeriaEnable= self::getAcymailingAvail();

	
	//$mensajeriaEnable=MENSAJERIA;
	if($mensajeriaEnable==1){	

	
	
		$memberid = $idUser; 
		$listid =  $listid;
		$senddate = time();

		$userClass = acymailing_get('class.subscriber');
	
		$subid = $userClass->subid($memberid); 
		if(empty($subid)) return false; 

		
		
		
		
		//se crea email
		$mail = new stdClass();
		$mail->subject = $subject;
		//$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff);
		$mail->body = $body;
		$mail->userid=$memberid;
		$mail->senddate=$senddate;
		$mailClass = acymailing_get('class.mail');
		$mailid = $mailClass->save($mail);

	
	//se asigna lista a emial
	if($listid!=''){
	$listMailClass = acymailing_get('class.listmail');
	$Arrlistid= array($listid);
	$q= $listMailClass->save($mailid, $Arrlistid);
	}
	
	
	//se obtiene id acymailing del usuario 



	//se ingresa a la cola email 
	$db= JFactory::getDBO();
	
	$db->setQuery('INSERT IGNORE INTO #__acymailing_queue (`subid`,`mailid`,`senddate`,`priority`) VALUES ('.$db->Quote($subid).','.$db->Quote($mailid).','.$db->Quote($senddate).',1)');
	
	$db->query();
	//se procesa la cola que corresponde al email creado
	$queueClass= acymailing_get('class.queue');
	$totalSub=$queueClass->queue($mailid,time());
	$helperQueue= acymailing_get('helper.queue');
	$helperQueue->mailid=$mailid;
	$helperQueue->report = false;
	$helperQueue->total=$totalSub;
	$helperQueue->start = 1;
	$helperQueue->process();
	return true; 
	}


												
	}

	
function getInfoOrderSms($order, $tipo){


	

	$db= JFactory::getDBO();
	
	$query = 'SELECT  p.name as clienteName, p.lname as clienteLname ,o.custdata as info_Cliente, c.name as Nombre_Servicio, c.info as infocar, c.placa as Pl, o.ritiro as Fecha_Recogida, o.consegna  as Fecha_Entrega, pcond.name as nameCond, pcond.lname as lnameCond, pcond.movil as movilCond , o.idCond as CondOrder FROM #__vikrentcar_orders as o LEFT JOIN #__vikrentcar_profiles as p on o.ujid = p.user_id LEFT JOIN #__vikrentcar_cars as c on c.id = o.idcar  LEFT JOIN #__vikrentcar_profiles as pcond on pcond.user_id = c.idCond WHERE o.id = '.intval($order). ';';
	//$query = 'SELECT o.id as Num_Orden, o.status as Estado, o.custdata as addInfo, o.ritiro as Fecha_Recogida, o.consegna  as Fecha_Entrega  FROM #__vikrentcar_orders as o LEFT JOIN #__vikrentcar_profiles as p on o.ujid = p.user_id LEFT JOIN #__vikrentcar_cars as c on c.id = o.idcar WHERE o.id = '.intval($order). ';';
	$db->setQuery($query);
	$db->query();
	$dataorder = $db->loadAssocList();

	if(empty($dataorder[0]['Pl'])){
		
		$idConductor= $dataorder[0]['CondOrder'];

		if(empty($idConductor)){
			$dataorder[0]['Pl']='NO';
			$dataorder[0]['movilCond']='NO';
			$dataorder[0]['infocar']='NO';
			$dataorder[0]['nameCond']='NO';
			$dataorder[0]['lnameCond']='NO';

		}else{

			$query = 'SELECT  pcond.name as nameCond, pcond.lname as lnameCond , c.placa as placa, c.info as infocar, pcond.movil as movil FROM #__vikrentcar_cars as c  LEFT JOIN #__vikrentcar_profiles as pcond on pcond.user_id =  '.intval($idConductor). ' WHERE c.idCond = '.intval($idConductor). ';';
			$db->setQuery($query);
			$db->query();

			if($db->getNumRows() >0){


				$dataorder2 = $db->loadAssocList();

				//print_r($query);

				$dataorder[0]['Pl']=$dataorder2[0]['placa'];
				$dataorder[0]['movilCond']=$dataorder2[0]['movil'];
				$dataorder[0]['infocar']=$dataorder2[0]['infocar'];
				$dataorder[0]['nameCond']=$dataorder2[0]['nameCond'];
				$dataorder[0]['lnameCond']=$dataorder2[0]['lnameCond'];
			}else{



				$dataorder[0]['Pl']='NO';
				$dataorder[0]['movilCond']='NO';
				$dataorder[0]['infocar']='NO';
				$dataorder[0]['nameCond']='NO';
				$dataorder[0]['lnameCond']='NO';

			}


		}



		
	}
	
	if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
		$df = 'd/m/Y';
	} else {
		$df = 'Y/m/d';
	}

	//$fechaprogramada= date('l jS \of F Y',$dataorder[0]['ritiro']);
	//echo $fechaprogramada.'</br>';

	$arrayAddInfo= split("\n", $dataorder[0]['info_Cliente']);

	foreach ($arrayAddInfo as $key => $value) {

		$arrayInfoCliente= split(":", $value);
		//obtiene nombre
		if($arrayInfoCliente[0]=='Nombre'){
			
			$names= split(" ", trim($arrayInfoCliente[1]));
			
			$mensaje=  $names[0];

		}

		if($arrayInfoCliente[0]=='Apellido'){
			//concatena apellido
			$lnames= split(" ", trim($arrayInfoCliente[1]));
			$mensaje.= ' '.$lnames[0];
			$arregloSMS['pass']=$mensaje;
		}
		//obtiene Lugar de Recogida
		if($arrayInfoCliente[0]=='Lugar de Recogida'){
			
			$mensaje=  $arrayInfoCliente[1];
			$arregloSMS['Dest']=substr(trim($mensaje), 0, 20);
		}

		if($arrayInfoCliente[0]=='Vuelo'){
			$mensaje=  $arrayInfoCliente[1];
			$arregloSMS['Vuelo']=substr(trim($mensaje), 0, 6);

		}

		if($tipo==4){

			if($arrayInfoCliente[0]=='Teléfono' || $arrayInfoCliente[0]=='Phone'){
				$mensaje=  $arrayInfoCliente[1];
				$arregloSMS['Cel']=substr(trim($mensaje), 0, 10);
				$celi =substr(trim($mensaje), 0, 10);

			}



		}

		

			
	}





	$arregloSMS['Hr']= date( 'H:i', $dataorder[0]['Fecha_Recogida']);

	if($tipo==2){
	$names= split(" ", trim($dataorder[0]['nameCond']));

	if(count($names)==2){
		$arregloSMS['Cond']=$names[0].' '.$names[1];

	}else{

		$arregloSMS['Cond']=$names[0].' '.$names[1];
	}

	$lnames= split(" ", trim($dataorder[0]['lnameCond']));

	


		if(count($lnames)==2){
			$arregloSMS['Cond']=$names[0].' '.$lnames[0];

		}else{

			$arregloSMS['Cond']=$names[0].' '.$lnames[0];
		} 
	}

	if($tipo==4){

		$names= split(" ", trim($dataorder[0]['clienteName']));

		$lnames= split(" ", trim($dataorder[0]['clienteLname']));

		if(count($lnames)==2){
			$arregloSMS['Client']=$names[0].' '.$lnames[0];

		}else{

			$arregloSMS['Client']=$names[0].' '.$lnames[0];
		} 
	}


	//$arregloSMS['Cond']=$dataorder[0]['conductor'];
	if($tipo==2){
		$arregloSMS['Cel']=$dataorder[0]['movilCond'];
	}
	
	$namesServ= split(" ", trim($dataorder[0]['Nombre_Servicio']));
	//$nameservicio=$namesServ[0];
	if(count($namesServ)>2){
		//detecta el valor numerico del modelo del vehiculo para mostarlo
		foreach ($namesServ as $key => $value) {
			if(ctype_digit($value)){
			$nameservicio.=' '.$value;
			}

			if(!preg_match('/Servicio/i', $value)){
				
			$nameservicio.=$value.' ';

			}
		}
	}else{

		if($tipo==2){

		$arregloSMS['Veh']=substr($dataorder[0]['Nombre_Servicio'], 0, 15);
		}
	}
	if($tipo==2){
		$arregloSMS['Veh']=substr($nameservicio, 0, 20);
		$arregloSMS['Pl']=substr($dataorder[0]['Pl'], 0, 7);


	}

	$arrayAddInfo2= split("\n", $dataorder[0]['infocar']);
	
	foreach ($arrayAddInfo2 as $key => $value) {

		

		if($tipo==2){
			if(preg_match("/Color/i",$value)){
				$arrayInfoCliente2= split(":", $value);							
				//echo $key.' -'.' si tiene color'.$arrayInfoCliente2[1];
				$convertida=strip_tags($arrayInfoCliente2[1]);
				$arregloSMS['Color']=substr(trim($convertida), 0, 7);
			}
		}

		
	
	}	


	$lang            = JFactory::getLanguage();
	$currentLanguaje = $lang->getName();
							
	if(preg_match('[Spanish]', $currentLanguaje)){
		$lenguaje='1';
	}else{
		$lenguaje='0';
	}
	$fecha=date($dataorder[0]['Fecha_Recogida']);
	//echo $dataorder[0]['Fecha_Recogida'];
	$fechastr= self::convertDateToString($fecha,$lenguaje);
	/*echo $fechastr;
	echo 'pass:'.trim($arregloSMS['pass']);
	echo 'Hr:'.trim($arregloSMS['Hr']);
	echo 'Cond:'.trim($arregloSMS['Cond']);
	echo 'Cel:'.trim($arregloSMS['Cel']);
	echo 'Veh:'.trim($arregloSMS['Veh']);
	echo 'Color:'.trim($arregloSMS['Color']);
	echo 'Pl:'.trim($arregloSMS['Pl']);
	echo 'Dest:'.trim($arregloSMS['Dest']);*/
	
	
    $msgsms = self::buildCustData($arregloSMS,"\n");

	return 'Res: '.$fechastr."\n".trim($msgsms,' ');


	}

function crearcartemporal($idcar, $idUser){

	$dbo = & JFactory :: getDBO();
	
	//se clona auto
    $q="INSERT #__vikrentcar_cars( name,  placa,  idCond,  img,  idcat,  idcarat,  idopt,  info,  idplace,  avail,  units,  idretplace,  moreimgs)  SELECT   name,  placa, ".$idUser." ,  img,  idcat,  idcarat,  idopt,  info,  idplace,  0,  (-1),  idretplace,  moreimgs FROM #__vikrentcar_cars WHERE id =".$idcar;
    $dbo->setQuery($q);
	$dbo->Query($q);
	$pidcar= $dbo->insertid();


	//$q = "UPDATE `#__vikrentcar_cars` SET `idCond`='".$idUser."', units='-1', avail=0  WHERE `id`='" . $pidcar . "';";
	//$dbo->setQuery($q);
	//$dbo->Query($q);


	return $pidcar;

	



}

function asignarCarroEspecial($idcar, $idCond, $idOrder){

	$dbo = & JFactory :: getDBO();
	
	//se clona auto
    $q="INSERT #__vikrentcar_esp_services (id_Cond, id_Order, id_Cat, placa, infoCar, idplace) SELECT  ".$idCond." , ".$idOrder." ,idcat, placa,  info,  idplace  FROM #__vikrentcar_cars WHERE id =".$idcar.";";
    $dbo->setQuery($q);
	$dbo->Query($q);
	$pidservice_esp= $dbo->insertid();

	
	$q = "UPDATE `#__vikrentcar_orders` SET `idCond`='".$pidservice_esp."' WHERE `id`='" . $idOrder . "';";
	$dbo->setQuery($q);
	$dbo->Query($q);


	return $pidservice_esp;

	



}


function delettemporalcars(){

	$dbo = & JFactory :: getDBO();

	$config =& JFactory::getConfig();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
	$nowts  = $dateNow->getTimestamp();
	//saca ordenes viejas  con carros temporales asignadas
	
	$q="SELECT o.id as idorder, o.idcar, c.idcat FROM `#__vikrentcar_orders` o INNER JOIN #__vikrentcar_cars c ON o.idcar=c.id WHERE o.consegna<".$nowts." AND c.id>100 AND avail=0 AND units='-1'"; 
    $dbo->setQuery($q);
	$dbo->Query($q);
	$pcars= $dbo->loadAssocList();

	
	

	foreach ($pcars as  $value) {
		//saca id cars menores a 100 y que tengan categoria igual a cada una de las ordenes con carros temporales

		$q1="SELECT id FROM `#__vikrentcar_cars` WHERE id<100 AND idcat=".$value['idcat']; 
		$dbo->setQuery($q1);
		$dbo->Query($q1);
		$pidcar= $dbo->loadResult();

	   //se actualiza orden vieja con id de carro que reperesenta la categoria

		$q2 = "UPDATE `#__vikrentcar_orders` SET `idcar`=".$pidcar." WHERE `id`='" . $value['idorder'] . "';";
	    $dbo->setQuery($q2);
		$dbo->Query($q2);

		//se borra carro que ya no se usa

		$q="DELETE FROM chun4_vikrentcar_cars WHERE id=".$value['idcar'];


        $dbo->setQuery($q);
	    $dbo->Query($q);
		

		
	}

	//borra carros creados temporalmente que no tengan orden asociada hace limpieza 
	$q="SELECT cvc.id FROM #__vikrentcar_profiles p INNER JOIN #__vikrentcar_cars cvc ON user_id=cvc.idCond LEFT JOIN #__vikrentcar_orders cvo ON cvo.idcar=cvc.id WHERE cvc.idcat  NOT IN ('1;') AND  cvc.units='-1' AND cvo.id IS NULL AND cvc.id>100 ;";
	
    $dbo->setQuery($q);
	$dbo->Query($q);
	$mcars= $dbo->loadAssocList();

	foreach ($mcars as  $value) {





		$q="DELETE FROM #__vikrentcar_cars WHERE id=".$value['id'];


        $dbo->setQuery($q);
	    $dbo->Query($q);


	}


	//$idcars= $dbo->loadresultarray();
   
	//$cars= implode(",",$idcars);



	

	
	//borran carros temporales

	/*$q="DELETE FROM chun4_vikrentcar_cars WHERE id IN(".$cars.") AND id>100 AND avail=0  AND units='-1'";


    $dbo->setQuery($q);
	$dbo->Query($q);*/




	

}

function convertDateToString($fecha, $lang){
	
	
	if($lang=='0'){
	$mthNames = array("", "Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	$dayNames =  array("","Mon","Tue","Wed","Thu","Fri","Sat", "Sun");
	}else{
	$mthNames =  array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
	$dayNames =  array("","Lun","Mar","Mier","Jue","Vie","Sab", "Dom");
	}
	

	$curr_day=date('N',$fecha);
	$curr_date=date('d/m/Y',$fecha);
	//$curr_date=date('j');
	//$curr_month=(int)date('m');
	//$curr_year='2014';
	if($lang=='0'){
	 return ($dayNames[$curr_day] . " ".$curr_date);
	}else{
	 return ($dayNames[$curr_day] . " ".$curr_date);
	}
	
		
	
	
}
	function EstaInscritoLista($idUser, $listid){

		$db= JFactory::getDBO();
		$query = 'SELECT b.userid FROM #__acymailing_listsub as a LEFT JOIN #__acymailing_subscriber as b on a.subid = b.subid WHERE a.listid = '.intval($listid). ' AND a.status=1';
		$db->setQuery($query);
		$db->query();
		$inscritos = $db->loadAssocList();

		$flag=false;
		foreach ($inscritos as $cf) {
			
			if( $cf['userid']==(string)$idUser){
				
				$flag=true;
				break;

			}else{

				$flag=false;
			}

		}


		return $flag;

	}

	function enviarsms($idUser, $listid, $message, $order=''){


	$smsavail= self::getSMSAvail();

	if($smsavail==1){

	//$message= urlencode($message);
	$mainframe =& JFactory::getApplication();
	$document = & JFactory :: getDocument();
	$memberid = $idUser; 
	$listid =  $listid;
	$senddate = time();
	//usuario y contraseña para el servicio contratado de mensjeria sms
	$user='XXXXXXXXX';
	$password='YYYYYYYYY';
	
	
	//incluye libreria helper 
	/*if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_acymailing'.DS.'helpers'.DS.'helper.php')){
	echo 'This code can not work without the AcyMailing Component';
	return false;
	}*/
	
	


		//esta el usuario en lista de sms

		
		//url: "'.JRoute::_('pruebasms.php?user='.$user.'&password='.$password.'&message=HolaMundo&destination='.$phone).'",
		

		$infoProfile= self::getInfoProfile($memberid);
		$nameReceptor= $infoProfile[0]['name'].' '.$infoProfile[0]['lname'];
		//valida si el formato del celular es ok

		if (preg_match("/^([0-9]{10})$/", $infoProfile[0]['movil'])) {
		
			$phone= $infoProfile[0]['movil'];

			/*$declaration = '					
							jQuery.ajax({
								type: "POST",
								url: "'.JRoute::_('pruebasms.php').'",
								data: { user:'.$user.', password:'.$password.', message:'.$message.',destination:'.$phone.'}
							}).done(function(res) {
								alert("entro A");
							});';

							*/
						
			self::sendSmsconfirmed($user,$password, $message , $phone, $idUser, $order);
	
	
		}else{
			//si hay dos celulares separados con guión en el mismo campo se toma prioridad el primero

			if (preg_match("/^([0-9]{10}-[0-9]{10})$/", $infoProfile[0]['movil'])) {
			    $celulares= split('-', $infoProfile[0]['movil']);
			    $phone= $celulares[0];

			    self::sendSmsconfirmed($user,$password, $message , $phone, $idUser, $order);

			  
			
			} else {
			    //echo "Número de celular no valido".'</br>';
			}

		}
	
	//procesar cola de la lista
	
	if(!empty($listid)){

		self::procesarColaSMS($listid, $message, $order);

	}

		
	
	}
	}

function enviaremail($neworderid, $subject, $listaNotificacion){

		$dbo = & JFactory :: getDBO();

		

		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';
			
		} else {
			$df = 'Y/m/d';
			
		}

		$pitemid='520';




			$admail = vikrentcar :: getAdminMail();
			$q = "SELECT * FROM `#__vikrentcar_orders` WHERE `id`='" . $dbo->getEscaped($neworderid) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$rows = $dbo->loadAssocList();
				$rows[0]['admin_email'] = $admail;


				$psid= $rows[0]['sid'];
				$pts = $rows[0]['ts'];


				//$exppay = explode('=', $rows[0]['idpayment']);
				//$payment = vikrentcar :: getPayment($exppay[0]);
				//require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_vikrentcar". DS . "payments" . DS . $payment['file']);
				//$obj = new vikRentCarPayment($rows[0]);
				//$array_result = $obj->validatePayment();


				//if (true) {
				//if ($array_result['verified'] == 1) {
					//valid payment
					$ritplace = (!empty ($rows[0]['idplace']) ? vikrentcar :: getPlaceName($rows[0]['idplace']) : "");
					$consegnaplace = (!empty ($rows[0]['idreturnplace']) ? vikrentcar :: getPlaceName($rows[0]['idreturnplace']) : "");
					$realback = vikrentcar :: getHoursCarAvail() * 3600;
					$realback += $rows[0]['consegna'];
					//send mails
					$ftitle = vikrentcar :: getFrontTitle();
					$nowts = time();
					$carinfo = vikrentcar :: getCarInfo2($rows[0]['idcar'], $neworderid);
					$viklink = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $psid . "&ts=" . $pts."&Itemid=".$pitemid;
					//vikrentcar 1.5
					if($rows[0]['hourly'] == 1) {
						$q = "SELECT * FROM `#__vikrentcar_dispcosthours_master` WHERE `id`='" . $rows[0]['idtar'] . "';";
					}else {
						$q = "SELECT * FROM `#__vikrentcar_dispcost_master` WHERE `id`='" . $rows[0]['idtar'] . "';";
					}
					//
					$dbo->setQuery($q);
					$dbo->Query($q);
					$tar = $dbo->loadAssocList();

					$isPaquete= vikrentcar:: getPriceName($tar[0]['idprice']);
					//vikrentcar 1.5
					if($rows[0]['hourly'] == 1) {
						foreach($tar as $kt => $vt) {
							$tar[$kt]['days'] = 1;
						}
					}
					//
					//vikrentcar 1.6
					$checkhourscharges = 0;
					$hoursdiff = 0;
					$ppickup = $rows[0]['ritiro'];
					$prelease = $rows[0]['consegna'];
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
					if($checkhourscharges > 0 && $aehourschbasp == true) {
						$ret = vikrentcar::applyExtraHoursChargesCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, false, true, true);
						$tar = $ret['return'];
						$calcdays = $ret['days'];
					}
					if($checkhourscharges > 0 && $aehourschbasp == false) {
						$tar = vikrentcar::extraHoursSetPreviousFareCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, true);
						$tar = vikrentcar :: applySeasonsCar($tar, $rows[0]['ritiro'], $rows[0]['consegna'], $rows[0]['idplace']);
						$ret = vikrentcar::applyExtraHoursChargesCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, true, true, true);
						$tar = $ret['return'];
						$calcdays = $ret['days'];
					}else {
						$tar = vikrentcar :: applySeasonsCar($tar, $rows[0]['ritiro'], $rows[0]['consegna'], $rows[0]['idplace']);
					}
					//
					$pricestr = vikrentcar :: getPriceName($tar[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']) . (!empty ($tar[0]['attrdata']) ? "\n" . vikrentcar :: getPriceAttr($tar[0]['idprice']) . ": " . $tar[0]['attrdata'] : "");
					$isdue = vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']);
					$currencyname = vikrentcar :: getCurrencyName();
					$optstr = "";
					if (!empty ($rows[0]['optionals'])) {
						$stepo = explode(";", $rows[0]['optionals']);
						foreach ($stepo as $oo) {
							if (!empty ($oo)) {
								$stept = explode(":", $oo);
								$q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($stept[0]) . "';";
								$dbo->setQuery($q);
								$dbo->Query($q);
								if ($dbo->getNumRows() == 1) {
									$actopt = $dbo->loadAssocList();
									$realcost = (intval($actopt[0]['perday']) == 1 ? ($actopt[0]['cost'] * $rows[0]['days'] * $stept[1]) : ($actopt[0]['cost'] * $stept[1]));
									if (!empty ($actopt[0]['maxprice']) && $actopt[0]['maxprice'] > 0 && $realcost > $actopt[0]['maxprice']) {
										$realcost = $actopt[0]['maxprice'];
										if(intval($actopt[0]['hmany']) == 1 && intval($stept[1]) > 1) {
											$realcost = $actopt[0]['maxprice'] * $stept[1];
										}
									}
									$tmpopr = vikrentcar :: sayOptionalsPlusIva($realcost, $actopt[0]['idiva']);
									$isdue += $tmpopr;
									$optstr .= ($stept[1] > 1 ? $stept[1] . " " : "") . $actopt[0]['name'] . ": " . $tmpopr . " " . $currencyname . "\n";
								}
							}
						}
					}
					$maillocfee = "";
					if (!empty ($rows[0]['idplace']) && !empty ($rows[0]['idreturnplace'])) {
						$locfee = vikrentcar :: getLocFee($rows[0]['idplace'], $rows[0]['idreturnplace']);
						if ($locfee) {
							$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $rows[0]['days']) : $locfee['cost'];
							$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
							$isdue += $locfeewith;
							$maillocfee = $locfeewith;
						}
					}
					//vikrentcar 1.6 coupon
					$usedcoupon = false;
					$origisdue = $isdue;
					if(strlen($rows[0]['coupon']) > 0) {
						$usedcoupon = true;
						$expcoupon = explode(";", $rows[0]['coupon']);
						$isdue = $isdue - $expcoupon[1];
					}
					//
					if ($payment['charge'] > 0.00) {
						$shouldpay = $isdue;
						if($payment['ch_disc'] == 1) {
							//charge
							if($payment['val_pcent'] == 1) {
								//fixed value
								$shouldpay += $payment['charge'];
							}else {
								//percent value
								$percent_to_pay = $shouldpay * $payment['charge'] / 100;
								$shouldpay += $percent_to_pay;
							}
						}else {
							//discount
							if($payment['val_pcent'] == 1) {
								//fixed value
								$shouldpay -= $payment['charge'];
							}else {
								//percent value
								$percent_to_pay = $shouldpay * $payment['charge'] / 100;
								$shouldpay -= $percent_to_pay;
							}
						}
					}
					if (!vikrentcar :: payTotal()) {
						$percentdeposit = vikrentcar :: getAccPerCent();
						if ($percentdeposit > 0) {
							$shouldpay = $shouldpay * $percentdeposit / 100;
						}
					}
					//check if the total amount paid is the same as the total order
					if(array_key_exists('tot_paid', $array_result)) {
						$shouldpay = round($shouldpay, 2);
						$totreceived = round($array_result['tot_paid'], 2);
						if($shouldpay != $totreceived) {
							//the amount paid is different than the total order
							//fares might have changed or the deposit might be different
							//Sending just an email to the admin that will check
							//@mail($admail, JText :: _('VRCTOTPAYMENTINVALID'), JText::sprintf('VRCTOTPAYMENTINVALIDTXT', $rows[0]['id'], $totreceived." (".$array_result['tot_paid'].")", $shouldpay));
						}
					}

			}

			

				$body= self :: makeemail($rows[0]['custmail'], strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, ($rows[0]['status']=='confirmed'? JText :: _('VRCOMPLETED'):JText :: _('VRWAITINGPAYM')), $ritplace, $consegnaplace, $maillocfee,  $rows[0]['id'], $rows[0]['coupon'],'','',$tar[0]['idprice']);
				
				$envioexito= self::enviarEmailAcymailing($rows[0]['ujid'],$listaNotificacion,$subject,$body);

				

				$body= self :: makeemail($rows[0]['custmail'], strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'piloto', ($rows[0]['status']=='confirmed'? JText :: _('VRCOMPLETED'):JText :: _('VRWAITINGPAYM')), $ritplace, $consegnaplace, $maillocfee,  $rows[0]['id'], $rows[0]['coupon'],'','',$tar[0]['idprice']);
				
				
				//$datosPiloto= self::getNamesCond($carinfo['idCond'], $rows[0]['idCond']);
				$envioexito=  self::enviarEmailAcymailing($carinfo['idCond'],'',$subject,$body);

				if(!$envioexito){

					$body= self :: makeemail($rows[0]['custmail'], strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'error', ($rows[0]['status']=='confirmed'? JText :: _('VRCOMPLETED'):JText :: _('VRWAITINGPAYM')), $ritplace, $consegnaplace, $maillocfee,  $rows[0]['id'], $rows[0]['coupon'],'','',$tar[0]['idprice']);
				

					$envioexito=  self::enviarEmailAcymailing('',	$listaNotificacion ,'Error en el envio',$body);

				}




}

function procesarColaSMS($listid,$message, $order=''){

		$db= JFactory::getDBO();
		$query = 'SELECT b.userid FROM #__acymailing_listsub as a LEFT JOIN #__acymailing_subscriber as b on a.subid = b.subid WHERE a.listid = '.intval($listid). ' AND a.status=1';
		$db->setQuery($query);
		$db->query();

		if($db->getNumRows() >0){
			$inscritos = $db->loadAssocList();

			foreach ($inscritos as $cf) {
				$movilInscritoList= self::buscarMovil($cf['userid']);

				$quien= $cf['userid'];

				if(!$movilInscritoList){



				//echo 'Formato Celular incorrecto';

				}else{

					if(!empty ($message)  && !empty ($movilInscritoList) ){


						self::sendSmsconfirmed($user,$password, $message , $movilInscritoList, $cf['userid'], $order);

					
						}
					}	

			}
		}else{

			echo 'ho hay resultados'.'<br>';
		}


	}


	function sendSmsconfirmed($user,$password, $message , $destination, $idUser, $order=''){


		$config =& JFactory::getConfig();


		$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
		

		$user=USUARIO_SMS;
		$password=PASSWORD_SMS;
		$url_sms= URL_SERVICE_SMS;

		$user =self::getSMSUser();

		$password =self::getSMSPassword();

		$url_sms= self::getSMSUrl();

	

		
	
		$mysqlDateTime = $dateNow->format(DateTime::ISO8601);
		$message= trim($message);

		//$nowts  = $dateNow->getTimestamp() + $dateNow->getOffset();
		//$urlservicebase='http://sms1.signacom.com.co:9090/SignacomWebServer/SendMessage?';
		$urlservice= $url_sms.'?user='.urlencode($user).'&password='.urlencode($password).'&message='.urlencode($message).'&destination='.urlencode($destination);
		$dbo = & JFactory :: getDBO();



		$query = "INSERT INTO #__vikrentcar_sms (fecha_envio, message ,destination, urlservice ,id_receptor, id_order) VALUES ('".$mysqlDateTime."', '".$message."', '".$destination."' , '".$urlservice."' , '".$idUser."' , '".$order."');";


		$dbo->setQuery($query);
		$dbo->query();
		$lid = $dbo->insertid();

		
		

		$q = "SELECT `urlservice` FROM `#__vikrentcar_sms` ;";
		$dbo->setQuery($q);
		$dbo->query();
		$serviceSMS= $dbo->loadAssocList();

		$envioreal=  self::getsmsrealsend();
		if($envioreal==1){

			if($destination!='' || !empty($destination)){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $urlservice);
				//curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				//
				
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				
				
				$resultado = curl_exec ($ch);

				curl_close($ch);
				
				if(curl_errno($ch)){ 
				
				   $errorcurl=  'Mensaje SMS no enviado,  Curl error:  ' . curl_error($ch);

					$query = "UPDATE #__vikrentcar_sms SET Observacion='".$errorcurl."' WHERE id='" . $lid . "';";

					$dbo->setQuery($query);
					$dbo->query();
				}else{
				//if(curl_errno($ch)){ 
				
				 

					$query = "UPDATE #__vikrentcar_sms SET Observacion='".$resultado."' WHERE id='" . $lid . "';";

					$dbo->setQuery($query);
					$dbo->query();
				}
				//}
  
				
				
				
			}

		}


		return $resultado;


	}




	function buscarMovil($memberid){

		$infoProfile= self::getInfoProfile($memberid);
		//valida si el formato del celular es ok

		if (preg_match("/^([0-9]{10})$/", $infoProfile[0]['movil'])) {
		
			$phone= $infoProfile[0]['movil'];
/*
			$declaration = '					
							jQuery.ajax({
								type: "POST",
								url: "'.JRoute::_('pruebasms.php').'",
								data: { user:'.$user.', password:'.$password.', message:'.$message.',destination:'.$phone.'}
							}).done(function(res) {
								alert("entro A");
							});';

*/

		//$document->addScriptDeclaration($declaration);
		
			return $phone;

		}else{
			//si hay dos celulares separados con guión en el mismo campo se toma prioridad el primero

			if (preg_match("/^([0-9]{10}-[0-9]{10})$/", trim($infoProfile[0]['movil']))) {
			    $celulares= split('-', $infoProfile[0]['movil']);
			    $phone= $celulares[0];

/*
			    $declaration = '						
							jQuery.ajax({
								type: "POST",
								url: "'.JRoute::_('pruebasms.php').'",
								data: { user:'.$user.', password:'.$password.', message:'.$message.',destination:'.$phone.'}
							}).done(function(res) {
								alert("entro B");
							});';

*/

				//$document->addScriptDeclaration($declaration);
				
			return $phone;

			} else {
			    
			    return false;
			}

		}


	}

		function cancelarservicio($idorder,$format, $userOrden){

		//error_reporting(-1);
		$language = JFactory::getLanguage();
		$language->load('com_vikrentcar');
		
		
		$app =& JFactory::getApplication();

		//$task	 = JRequest :: getString('task', '', 'request');
		
		//$idorder	 = JRequest :: getString('idorder', '', 'request');

		//$format	 = JRequest :: getString('format', '', 'request');



		
		
		
		$config =& JFactory::getConfig();
		$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
		$nowts  = $dateNow->getTimestamp();

		$user =& JFactory::getUser();
		$userIdActual = $user->get( 'id' );
		$dbo = & JFactory :: getDBO();
		
		//$userOrden	 = JRequest :: getString('idClient', '', 'request');
		$userId =$userOrden;

	

		$tabla='orders';
		$idcarnew=NULL;
		$status='canceled';
		$UserPilot=false;
		$mostrarmensajecancelnoposiblepaquete=false;


		$lang = JFactory::getLanguage();

		$code_lang= $lang->getTag();



		if($code_lang=='es-ES'){

			$itemid='520';
			$idList='17';


		}else{

			$itemid='620';
			$idList='24';


		}

		if ($app->isAdmin()){

			require_once JPATH_ADMINISTRATOR.'/components/com_profiler/helpers/profiler.php';
			$idProfiler= ProfilerHelper::getUserProfiler($userId);



			$fechaactual= date($df.' H:i', $nowts);
			$motivo='Administración';

			$q="INSERT INTO `#__vikrentcar_canceledorders` (`date_time`,`juser`,`id_order`,`motivo`) VALUES('".$fechaactual."','".$userIdActual."','".$idorder."','".$motivo."');";
			$dbo->setQuery($q);
			$dbo->Query($q);

		}
	
		
		
		try {
			
			$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";

			
			$dbo->setQuery($q);
			$dbo->Query($q);
			$orderinfo = $dbo->loadAssocList();
			
			
			
				if($orderinfo[0]['status']=='canceled'){
				
				$app->enqueueMessage(JText::_('MSGCANCELSERVICEALREADY'));		
					
				}else{


					if($orderinfo[0]['status']=='standby'){

						$changeSaldo=false;

					}else{

						
						$coupons= self:: getCodeCouponClient($orderinfo[0]['ujid']);
						if((preg_match("/3/", $coupons['type']))) {

							$changeSaldo=false;

						}else{

							$changeSaldo=true;

						}

						

						

					}

			
				
			
				
				$q = "SELECT  `num_doc`, `name`, `lname`, `usertype` FROM `#__vikrentcar_profiles`  WHERE `user_id`='" . $userId . "';";

				
				$dbo->setQuery($q);
				$dbo->Query($q);
				$profileinfo = $dbo->loadAssocList();

				if($profileinfo[0]['usertype']=='4'){
					//es un piloto
					$UserPilot=true;


				}


					if($orderinfo[0]['id_paq']!=0){

							$reversapaquete = vikrentcar::reversarSaldoPaquete($orderinfo,$orderinfo[0]['ujid'], $nowts );

							if(!$reversapaquete){

								$mostrarmensajecancelnoposiblepaquete=true;

								vikrentcar::borrarPaquetesvencidos($orderinfo[0]['ujid']);

							}
							

					}



						//$nowts  = $dateNow->getTimestamp() + $dateNow->getOffset() +25200;
						//solo se puede cancelar 30 minutos antes
					if(!$mostrarmensajecancelnoposiblepaquete){

						$treintaminutos=30*60;// 30 minutos

						if( ($nowts)<=($orderinfo[0]['ritiro']- $treintaminutos)){

						


						//if(!$mostrarmensajecancelnoposiblepaquete){	


							$q="INSERT #__vikrentcar_orders_canceled(`id`,`idbusy`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`, `totpaid`,`idpayment`,`ujid`,`hourly`,`coupon`,`id_factura`,`idCond`, `id_paq`, `passangers`)  SELECT `id`,`idbusy`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`, `totpaid`,`idpayment`,`ujid`,`hourly`,`coupon`,`id_factura`,`idCond`, `id_paq`, `passangers` FROM `#__vikrentcar_orders` WHERE id =".$idorder;
						    $dbo->setQuery($q);
							$dbo->Query($q);



					
						
						

						//se libera el servicio
					
						

							$q= "DELETE FROM  `#__vikrentcar_busy`  where id = '".$orderinfo[0]['idbusy']."'";
							$dbo->setQuery($q);
							$dbo->Query($q);


							$q= "DELETE FROM  `#__vikrentcar_tmplock`  where idcar = '".$orderinfo[0]['idcar']."'";
							$dbo->setQuery($q);
							$dbo->Query($q);

							$q = "SELECT  * FROM `#__vikrentcar_orders_canceled`  WHERE `id`='" . $idorder . "';";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$orderinfo = $dbo->loadAssocList();

							if($orderinfo[0]['idCond']!=0 || $orderinfo[0]['idCond']!=''){

								$q = "SELECT  * FROM `#__vikrentcar_esp_services`  WHERE `id`='" . $orderinfo[0]['idCond'] . "';";
								$dbo->setQuery($q);
								$dbo->Query($q);
								$isEsp=false;
								if($dbo->getNumRows() > 0){	

									$infoEspCar = $dbo->loadAssocList();
									$isEs=true;

								}else{

									$isEs=false;


								}
								


							}
					
						 //if($orderinfo[0]['status']=='canceled'){
						
									

							//$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname']);
							$ftitle = vikrentcar :: getFrontTitle();
							$tarInfo = vikrentcar :: getTarInfo($orderinfo[0]['idtar'],$orderinfo[0]['hourly']);
							$optstr= vikrentcar::getOptInfo($orderinfo[0]['optionals'],$orderinfo[0]['days'], $tarInfo[0]['cost'] );
							$pricestr = vikrentcar :: getPriceName($tarInfo[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tarInfo[0]['cost'], $tarInfo[0]['idprice']) . (!empty ($tarInfo[0]['attrdata']) ? " " . $currencyname . "\n" . vikrentcar :: getPriceAttr($tarInfo[0]['idprice']) . ": " . $tarInfo[0]['attrdata'] : "");
							$carinfo = vikrentcar :: getCarInfo2($orderinfo[0]['idcar'],$infoEspCar[0]['id_Order'] );

							$ritplace = (!empty ($orderinfo[0]['idplace']) ? vikrentcar :: getPlaceName($orderinfo[0]['idplace']) : "");
							$consegnaplace = (!empty ($orderinfo[0]['idreturnplace']) ? vikrentcar :: getPlaceName($orderinfo[0]['idreturnplace']) : "");
							

							
							if(!$reversapaquete){

			                    if($changeSaldo){

			                    	
								$concepto= JText::sprintf('VRLIBNINECANCELSUBJECT',$idorder);
								$lid= vikrentcar::saveCredito($idorder,$orderinfo[0]['ujid'], $concepto);

								$newSaldo = vikrentcar::calcularNuevoSaldo($idorder, $orderinfo[0]['ujid'], $lid);

								if($newSaldo<0){

									$newSaldo=0;
								}

								vikrentcar::saveSaldo($newSaldo,$orderinfo[0]['ujid']);

								}
							}
							$saldo= vikrentcar::getSaldoUser($orderinfo[0]['ujid']);

							if($code_lang=='es-ES'){

								$linkinfopersonal= JURI :: root() . "index.php?option=com_fabrik&view=list&listid=35&Itemid=697";

							}else{


								$linkinfopersonal= JURI :: root() . "index.php?option=com_fabrik&view=list&listid=36&Itemid=698";

							}
							
							$emailadmin= vikrentcar::getAdminMail();

							$body= vikrentcar :: makeemail($emailadmin, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr,$orderinfo[0]['totpaid'], '2', 'canceled', $ritplace, $consegnaplace ,$maillocfee, $orderinfo[0]['id'], $orderinfo[0]['coupon'], $saldo, $linkinfopersonal);
							//$hmess= vikrentcar::crearEmailCancelacion( '1','', $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr, $orderinfo[0]['totpaid'], $link,$orderinfo[0]['status'], $place = "", $returnplace = "", $maillocfee = "", $orderinfo[0]['id'], $strcouponeff = "");
							$subject= JText::sprintf('VRLIBNINECANCELSUBJECT',$orderinfo[0]['id']);


							vikrentcar::enviarEmailAcymailing($orderinfo[0]['ujid'],LISTA_NOTIFICACION_RESERVAS,$subject,$body);


							
							
							$q="SELECT `user_id`, `num_doc`, `name`, `lname` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$carinfo['idCond']."'";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$juserPiloto = $dbo->loadAssocList();
							
							//se envia email al piloto confirmando la cancelacion de servicio
							//$body=undefined;
							$body= vikrentcar :: makeemail($emailadmin, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr,$orderinfo[0]['totpaid'], 'piloto', 'canceled', $ritplace, $consegnaplace ,$maillocfee, $orderinfo[0]['id'], $orderinfo[0]['coupon'], $saldo, $linkinfopersonal);
							$subject= JText::sprintf('VRLIBNINECANCELSUBJECT',$orderinfo[0]['id']);
							vikrentcar::enviarEmailAcymailing($juserPiloto[0]['user_id'],'',$subject,$body);

							$q= "DELETE FROM  `#__vikrentcar_".$tabla."`  where id = '".$idorder."'";
							$dbo->setQuery($q);
							$dbo->Query($q);
							/*if($isEs){

								$q= "DELETE FROM  `#__vikrentcar_esp_services`  where id_Order = '".$idorder."'";
								$dbo->setQuery($q);
								$dbo->Query($q);


							}*/
							




							if(!$UserPilot){


								if($reversapaquete){

									
									
									if ($app->isSite()){
										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname'].'<br/>'.JText::_('MSGCANCELSVCNEXT')."servico paquete reversado");
										$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
									}else{

										$app =& JFactory::getApplication();

										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE'));
										
										return '1';

										  //$app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
									}


								}else{

									
									if($app->isSite()){
										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname'].'<br/>'.JText::_('MSGCANCELSVCSALDO').": ".number_format($saldo, 0).'<br/>'.JText::_('MSGCANCELSVCNEXT'));
										$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
									}else{

										$app =& JFactory::getApplication();

										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE'));
										
										return '1';

									    //$app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
									}
								}
							
							}else{



								
								//$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=17&Itemid=520');
								if($app->isSite()){
									$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Por Piloto '." ".$$juserPiloto[0]['user_id'].': '. $juserPiloto[0]['name'].' '. $juserPiloto[0]['lname'].'<br/>');

									$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
								}else{

									$app =& JFactory::getApplication();

									$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE'));
									
									return '1';
									 //$app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
										
								}


							}

						
						}else{

							//se revierte proceso eliminando el registro guardado

						    $q= "DELETE FROM  #__vikrentcar_canceledorders  where id_order = '".$idorder."'";
							$dbo->setQuery($q);
							$dbo->Query($q);

		


						   
							//$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=17&Itemid=520');
							if($app->isSite()){
								 $app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE'));
								$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
							}else{

								$app =& JFactory::getApplication();

								$app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE2'));

								

								return '0';

								// $app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
							}

						}


					}else{


							$q= "DELETE FROM  #__vikrentcar_canceledorders  where id_order = '".$idorder."'";
							$dbo->setQuery($q);
							$dbo->Query($q);


						    
							//$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=17&Itemid=520');
							if($app->isSite()){
								$app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE2'));
								$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
							}else{

								$app =& JFactory::getApplication();

								$app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE2'));

								

								return '0';

								// $app->redirect(JURI::root().'administrator/index.php?option=com_profiler&view=user&layout=edit&id='.$idProfiler);		
							}



					}

				
				
				}

		
			
			
			
			
			/*$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";
			
			$dbo->setQuery($q);
			$dbo->Query($q);
			$orderinfo = $dbo->loadAssocList();*/
			//confirma si el servicio se cancelo con exito
			
			
			
		} catch (Exception $e) {
			
			//$app->enqueueMessage('Error: '.$e->getMessage());
			echo 'Error: '.$e->getMessage();
			
			
		}
		
		
		
		
	}

	function desaes(){


		echo '1';
	}



	function cancelarservicioOld(){

		//error_reporting(-1);
		
		
		$app =& JFactory::getApplication();

		$task	 = JRequest :: getString('task', '', 'request');
		//$idorder	 = JRequest :: getString('chun4_vikrentcar_canceledorders___id_order', '', 'request');
		$idorder	 = JRequest :: getString('idorder', '', 'request');

		$format	 = JRequest :: getString('format', '', 'request');



		
		
		
		$config =& JFactory::getConfig();
		$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
		$nowts  = $dateNow->getTimestamp();

		$user =& JFactory::getUser();
		$userIdActual = $user->get( 'id' );
		$dbo = & JFactory :: getDBO();
		
		$userOrden	 = JRequest :: getString('idClient', '', 'request');
		$userId =$userOrden;

	

		$tabla='orders';
		$idcarnew=NULL;
		$status='canceled';
		$UserPilot=false;
		$mostrarmensajecancelnoposiblepaquete=false;


		$lang = JFactory::getLanguage();

		$code_lang= $lang->getTag();



		if($code_lang=='es-ES'){

			$itemid='520';
			$idList='17';


		}else{

			$itemid='620';
			$idList='24';


		}

		if ($app->isAdmin()){

			require_once JPATH_ADMINISTRATOR.'/components/com_profiler/helpers/profiler.php';
			$idProfiler= ProfilerHelper::getUserProfiler($userId);



			$fechaactual= date($df.' H:i', $nowts);
			$motivo='Administración';

			$q="INSERT INTO `#__vikrentcar_canceledorders` (`date_time`,`juser`,`id_order`,`motivo`) VALUES('".$fechaactual."','".$userIdActual."','".$idorder."','".$motivo."');";
			$dbo->setQuery($q);
			$dbo->Query($q);

		}
	
		
		
		try {
			
			$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";

			
			$dbo->setQuery($q);
			$dbo->Query($q);
			$orderinfo = $dbo->loadAssocList();
			
			
			
				if($orderinfo[0]['status']=='canceled'){
				
				$app->enqueueMessage(JText::_('MSGCANCELSERVICEALREADY'));		
					
				}else{


					if($orderinfo[0]['status']=='standby'){

						$changeSaldo=false;

					}else{

						
						$coupons= self:: getCodeCouponClient($orderinfo[0]['ujid']);
						if((preg_match("/3/", $coupons['type']))) {

							$changeSaldo=false;

						}else{

							$changeSaldo=true;

						}

						

						

					}

			
				
			
				
				$q = "SELECT  `num_doc`, `name`, `lname`, `usertype` FROM `#__vikrentcar_profiles`  WHERE `user_id`='" . $userId . "';";

				
				$dbo->setQuery($q);
				$dbo->Query($q);
				$profileinfo = $dbo->loadAssocList();

				if($profileinfo[0]['usertype']=='4'){
					//es un piloto
					$UserPilot=true;


				}


					if($orderinfo[0]['id_paq']!=0){

							$reversapaquete = vikrentcar::reversarSaldoPaquete($orderinfo,$orderinfo[0]['ujid'], $nowts );

							if(!$reversapaquete){

								$mostrarmensajecancelnoposiblepaquete=true;

								vikrentcar::borrarPaquetesvencidos($orderinfo[0]['ujid']);

							}
							

					}



						//$nowts  = $dateNow->getTimestamp() + $dateNow->getOffset() +25200;
						//solo se puede cancelar 30 minutos antes
					if(!$mostrarmensajecancelnoposiblepaquete){

						$treintaminutos=30*60;// 30 minutos

						if( ($nowts)<=($orderinfo[0]['ritiro']- $treintaminutos)){

						


						//if(!$mostrarmensajecancelnoposiblepaquete){	


							$q="INSERT #__vikrentcar_orders_canceled(`id`,`idbusy`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`, `totpaid`,`idpayment`,`ujid`,`hourly`,`coupon`,`id_factura`,`idCond`, `id_paq`, `passangers`)  SELECT `id`,`idbusy`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`, `totpaid`,`idpayment`,`ujid`,`hourly`,`coupon`,`id_factura`,`idCond`, `id_paq`, `passangers` FROM `#__vikrentcar_orders` WHERE id =".$idorder;
						    $dbo->setQuery($q);
							$dbo->Query($q);



					
						
						

						//se libera el servicio
					
						

							$q= "DELETE FROM  `#__vikrentcar_busy`  where id = '".$orderinfo[0]['idbusy']."'";
							$dbo->setQuery($q);
							$dbo->Query($q);


							$q= "DELETE FROM  `#__vikrentcar_tmplock`  where idcar = '".$orderinfo[0]['idcar']."'";
							$dbo->setQuery($q);
							$dbo->Query($q);

							$q = "SELECT  * FROM `#__vikrentcar_orders_canceled`  WHERE `id`='" . $idorder . "';";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$orderinfo = $dbo->loadAssocList();
					
						 //if($orderinfo[0]['status']=='canceled'){
						
									

							//$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname']);
							$ftitle = vikrentcar :: getFrontTitle();
							$tarInfo = vikrentcar :: getTarInfo($orderinfo[0]['idtar'],$orderinfo[0]['hourly']);
							$optstr= vikrentcar::getOptInfo($orderinfo[0]['optionals'],$orderinfo[0]['days'], $tarInfo[0]['cost'] );
							$pricestr = vikrentcar :: getPriceName($tarInfo[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tarInfo[0]['cost'], $tarInfo[0]['idprice']) . (!empty ($tarInfo[0]['attrdata']) ? " " . $currencyname . "\n" . vikrentcar :: getPriceAttr($tarInfo[0]['idprice']) . ": " . $tarInfo[0]['attrdata'] : "");
							$carinfo = vikrentcar :: getCarInfo($orderinfo[0]['idcar']);

							$ritplace = (!empty ($orderinfo[0]['idplace']) ? vikrentcar :: getPlaceName($orderinfo[0]['idplace']) : "");
							$consegnaplace = (!empty ($orderinfo[0]['idreturnplace']) ? vikrentcar :: getPlaceName($orderinfo[0]['idreturnplace']) : "");
							

							
							if(!$reversapaquete){

			                    if($changeSaldo){

			                    	
								$concepto= JText::sprintf('VRLIBNINECANCELSUBJECT',$idorder);
								$lid= vikrentcar::saveCredito($idorder,$orderinfo[0]['ujid'], $concepto);

								$newSaldo = vikrentcar::calcularNuevoSaldo($idorder, $orderinfo[0]['ujid'], $lid);

								if($newSaldo<0){

									$newSaldo=0;
								}

								vikrentcar::saveSaldo($newSaldo,$orderinfo[0]['ujid']);

								}
							}
							$saldo= vikrentcar::getSaldoUser($orderinfo[0]['ujid']);

							if($code_lang=='es-ES'){

								$linkinfopersonal= JURI :: root() . "index.php?option=com_fabrik&view=list&listid=35&Itemid=697";

							}else{


								$linkinfopersonal= JURI :: root() . "index.php?option=com_fabrik&view=list&listid=36&Itemid=698";

							}
							
							$emailadmin= vikrentcar::getAdminMail();

							$body= vikrentcar :: makeemail($emailadmin, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr,$orderinfo[0]['totpaid'], '2', 'canceled', $ritplace, $consegnaplace ,$maillocfee, $orderinfo[0]['id'], $orderinfo[0]['coupon'], $saldo, $linkinfopersonal);
							//$hmess= vikrentcar::crearEmailCancelacion( '1','', $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr, $orderinfo[0]['totpaid'], $link,$orderinfo[0]['status'], $place = "", $returnplace = "", $maillocfee = "", $orderinfo[0]['id'], $strcouponeff = "");
							$subject= JText::sprintf('VRLIBNINECANCELSUBJECT',$orderinfo[0]['id']);


							vikrentcar::enviarEmailAcymailing($orderinfo[0]['ujid'],LISTA_NOTIFICACION_RESERVAS,$subject,$body);


							
							
							$q="SELECT `user_id`, `num_doc`, `name`, `lname` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$carinfo['idCond']."'";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$juserPiloto = $dbo->loadAssocList();
							
							//se envia email al piloto confirmando la cancelacion de servicio
							//$body=undefined;
							$body= vikrentcar :: makeemail($emailadmin, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr,$orderinfo[0]['totpaid'], 'piloto', 'canceled', $ritplace, $consegnaplace ,$maillocfee, $orderinfo[0]['id'], $orderinfo[0]['coupon'], $saldo, $linkinfopersonal);
							$subject= JText::sprintf('VRLIBNINECANCELSUBJECT',$orderinfo[0]['id']);
							vikrentcar::enviarEmailAcymailing($juserPiloto[0]['user_id'],'',$subject,$body);

							$q= "DELETE FROM  `#__vikrentcar_".$tabla."`  where id = '".$idorder."'";
							$dbo->setQuery($q);
							$dbo->Query($q);




							if(!$UserPilot){


								if($reversapaquete){

									
									
									if ($app->isSite()){
										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname'].'<br/>'.JText::_('MSGCANCELSVCNEXT')."servico paquete reversado");
										$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
									}else{

										$app =& JFactory::getApplication();

										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE'));
										
										return '1';

										  //$app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
									}


								}else{

									
									if($app->isSite()){
										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname'].'<br/>'.JText::_('MSGCANCELSVCSALDO').": ".number_format($saldo, 0).'<br/>'.JText::_('MSGCANCELSVCNEXT'));
										$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
									}else{

										$app =& JFactory::getApplication();

										$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE'));
										
										return '1';

									    //$app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
									}
								}
							
							}else{



								
								//$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=17&Itemid=520');
								if($app->isSite()){
									$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Por Piloto '." ".$$juserPiloto[0]['user_id'].': '. $juserPiloto[0]['name'].' '. $juserPiloto[0]['lname'].'<br/>');

									$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
								}else{

									$app =& JFactory::getApplication();

									$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE'));
									
									return '1';
									 //$app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
										
								}


							}

						
						}else{

							//se revierte proceso eliminando el registro guardado

						    $q= "DELETE FROM  #__vikrentcar_canceledorders  where id_order = '".$idorder."'";
							$dbo->setQuery($q);
							$dbo->Query($q);

		


						   
							//$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=17&Itemid=520');
							if($app->isSite()){
								 $app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE'));
								$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
							}else{

								$app =& JFactory::getApplication();

								$app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE2'));

								

								return '0';

								// $app->redirect(JURI::root().'administrator/index.php?option=com_profiler&format=raw&view=user&layout=edit&id='.$idProfiler);	
							}

						}


					}else{


							$q= "DELETE FROM  #__vikrentcar_canceledorders  where id_order = '".$idorder."'";
							$dbo->setQuery($q);
							$dbo->Query($q);


						    
							//$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=17&Itemid=520');
							if($app->isSite()){
								$app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE2'));
								$app->redirect(JURI::root().'index.php?#mostrarVentanaSaldo');
							}else{

								$app =& JFactory::getApplication();

								$app->enqueueMessage(JText::_('MSGCANCELSVCNOPOSIBLE2'));

								

								return '0';

								// $app->redirect(JURI::root().'administrator/index.php?option=com_profiler&view=user&layout=edit&id='.$idProfiler);		
							}



					}

				
				
				}

		
			
			
			
			
			/*$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";
			
			$dbo->setQuery($q);
			$dbo->Query($q);
			$orderinfo = $dbo->loadAssocList();*/
			//confirma si el servicio se cancelo con exito
			
			
			
		} catch (Exception $e) {
			
			//$app->enqueueMessage('Error: '.$e->getMessage());
			echo 'Error: '.$e->getMessage();
			
			
		}
		
		
		
		
	}
		
	function sendCustMail($to, $subject, $ftitle, $ts, $custdata, $carname, $first, $second, $pricestr, $optstr, $tot, $link, $status, $place = "", $returnplace = "", $maillocfee = "", $orderid = "", $strcouponeff = "") {
		$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencyname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$currencyname = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='adminemail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$adminemail = $dbo->loadResult();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='footerordmail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_config` WHERE `param`='sendjutility';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sendmethod = $dbo->loadAssocList();
		$useju = intval($sendmethod[0]['setting']) == 1 ? true : false;
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sitelogo = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$formdate = $dbo->loadResult();
		if ($formdate == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$footerordmail = $ft[0]['setting'];
		$textfooterordmail = strip_tags($footerordmail);
		//text part
		$msg = $ftitle . "\n\n";
		$msg .= JText :: _('VRLIBEIGHT') . " " . date($df . ' H:i', $ts) . "\n";
		$msg .= JText :: _('VRLIBNINE') . ":\n" . $custdata . "\n";
		$msg .= JText :: _('VRLIBTEN') . ": " . $carname . "\n";
		$msg .= JText :: _('VRLIBELEVEN') . " " . date($df . ' H:i', $first) . "\n";
		$msg .= JText :: _('VRLIBTWELVE') . " " . date($df . ' H:i', $second) . "\n";
		$msg .= (!empty ($place) ? JText :: _('VRRITIROCAR') . ": " . $place . "\n" : "");
		$msg .= (!empty ($returnplace) ? JText :: _('VRRETURNCARORD') . ": " . $returnplace . "\n" : "");
		$msg .= $pricestr . "\n";
		$msg .= $optstr . "\n";
		if (!empty ($maillocfee) && $maillocfee > 0) {
			$msg .= JText :: _('VRLOCFEETOPAY') . ": " . number_format($maillocfee, 2) . " " . $currencyname . "\n\n";
		}
		$msg .= JText :: _('VRLIBSIX') . " " . $tot . " " . $currencyname . "\n";
		$msg .= JText :: _('VRLIBSEVEN') . ": " . $status . "\n\n";
		$msg .= JText :: _('VRLIBTENTHREE') . ": \n" . $link;
		$msg .= (strlen(trim($textfooterordmail)) > 0 ? "\n" . $textfooterordmail : "");
		//
		//html part
		$from_name = $adminemail;
		$from_address = $adminemail;
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = $to;
		$to_address = $to;
		//vikrentcar 1.5
		$tmpl = self::loadEmailTemplate();
		//
		if (!$useju) {
			require_once ("./components/com_vikrentcar/class/email_message.php");
			$email_message = new email_message_class;
			$email_message->SetEncodedEmailHeader("To", $to_address, $to_name);
			$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
			$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
			$email_message->SetHeader("Sender", $from_address);
			//			if(defined("PHP_OS")
			//			&& strcmp(substr(PHP_OS,0,3),"WIN"))
			//				$email_message->SetHeader("Return-Path",$error_delivery_address);

			$email_message->SetEncodedHeader("Subject", $subject);
			$attachlogo = false;
			if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
				$image = array (
				"FileName" => JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo, "Content-Type" => "automatic/name", "Disposition" => "inline");
				$email_message->CreateFilePart($image, $image_part);
				$image_content_id = $email_message->GetPartContentID($image_part);
				$attachlogo = true;
			}
			$tlogo = ($attachlogo ? "<img src=\"cid:" . $image_content_id . "\" alt=\"imglogo\"/>\n" : "");
		} else {
			$attachlogo = false;
			if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
				$attachlogo = true;
			}
			$tlogo = ($attachlogo ? "<img src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo . "\" alt=\"imglogo\"/>\n" : "");
		}
		//vikrentcar 1.5
		$tcname = $ftitle."\n";
		$todate = date($df . ' H:i', $ts)."\n";
		$tcustdata = nl2br($custdata)."\n";
		$tiname = $carname."\n";
		$tpickupdate = date($df . ' H:i', $first)."\n";
		$tdropdate = date($df . ' H:i', $second)."\n";
		$tpickupplace = (!empty ($place) ? $place."\n" : "");
		$tdropplace = (!empty ($returnplace) ? $returnplace."\n" : "");
		$tprices = $pricestr;
		$topts = $optstr;
		$tlocfee = $maillocfee;
		$ttot = $tot."\n";
		$tlink = $link;
		$tfootm = $footerordmail;
		$hmess = self::parseEmailTemplate($tmpl, $orderid, $currencyname, $status, $tlogo, $tcname, $todate, $tcustdata, $tiname, $tpickupdate, $tdropdate, $tpickupplace, $tdropplace, $tprices, $topts, $tlocfee, $ttot, $tlink, $tfootm, $strcouponeff);
		//
		
		if (!$useju) {
			$email_message->CreateQuotedPrintableHTMLPart($hmess, "", $html_part);
			$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($msg), "", $text_part);
			$alternative_parts = array (
				$text_part,
				$html_part
			);
			$email_message->CreateAlternativeMultipart($alternative_parts, $alternative_part);
			$related_parts = array (
				$alternative_part,
				$image_part
			);
			$email_message->AddRelatedMultipart($related_parts);
			$error = $email_message->Send();
			if (strcmp($error, "")) {
				//$msg = utf8_decode($msg);
				@ mail($to, $subject, $msg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
			}
		} else {
			$hmess = '<html>'."\n".'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>'."\n".'<body>'.$hmess.'</body>'."\n".'</html>';
			JUtility :: sendMail($from_address, $from_name, $to, $subject, $hmess, true, null, null, null, $reply_address, $reply_address);
		}
		//
		
		return true;
	}

	function sendCustMailFromBack($to, $subject, $ftitle, $ts, $custdata, $carname, $first, $second, $pricestr, $optstr, $tot, $link, $status, $place = "", $returnplace = "", $maillocfee = "", $orderid = "", $strcouponeff = "") {
		//this function is called in the administrator site
		$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencyname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$currencyname = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='adminemail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$adminemail = $dbo->loadResult();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='footerordmail';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$ft = $dbo->loadAssocList();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_config` WHERE `param`='sendjutility';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sendmethod = $dbo->loadAssocList();
		$useju = intval($sendmethod[0]['setting']) == 1 ? true : false;
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sitelogo = $dbo->loadResult();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='dateformat';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$formdate = $dbo->loadResult();
		if ($formdate == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		$footerordmail = $ft[0]['setting'];
		$textfooterordmail = strip_tags($footerordmail);
		//text part
		$msg = $ftitle . "\n\n";
		$msg .= JText :: _('VRLIBEIGHT') . " " . date($df . ' H:i', $ts) . "\n";
		$msg .= JText :: _('VRLIBNINE') . ":\n" . $custdata . "\n";
		$msg .= JText :: _('VRLIBTEN') . ": " . $carname . "\n";
		$msg .= JText :: _('VRLIBELEVEN') . " " . date($df . ' H:i', $first) . "\n";
		$msg .= JText :: _('VRLIBTWELVE') . " " . date($df . ' H:i', $second) . "\n";
		$msg .= (!empty ($place) ? JText :: _('VRRITIROCAR') . ": " . $place . "\n" : "");
		$msg .= (!empty ($returnplace) ? JText :: _('VRRETURNCARORD') . ": " . $returnplace . "\n" : "");
		$msg .= $pricestr . "\n";
		$msg .= $optstr . "\n";
		if (!empty ($maillocfee) && $maillocfee > 0) {
			$msg .= JText :: _('VRLOCFEETOPAY') . ": " . number_format($maillocfee, 2) . " " . $currencyname . "\n\n";
		}
		$msg .= JText :: _('VRLIBSIX') . " " . $tot . " " . $currencyname . "\n";
		$msg .= JText :: _('VRLIBSEVEN') . ": " . $status . "\n\n";
		$msg .= JText :: _('VRLIBTENTHREE') . ": \n" . $link;
		$msg .= (strlen(trim($textfooterordmail)) > 0 ? "\n" . $textfooterordmail : "");
		//
		//html part
		$from_name = $adminemail;
		$from_address = $adminemail;
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = $to;
		$to_address = $to;
		//vikrentcar 1.5
		$tmpl = self::loadEmailTemplate();
		//
		if (!$useju) {
			require_once ("../components/com_vikrentcar/class/email_message.php");
			$email_message = new email_message_class;
			$email_message->SetEncodedEmailHeader("To", $to_address, $to_name);
			$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
			$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
			$email_message->SetHeader("Sender", $from_address);
			//			if(defined("PHP_OS")
			//			&& strcmp(substr(PHP_OS,0,3),"WIN"))
			//				$email_message->SetHeader("Return-Path",$error_delivery_address);

			$email_message->SetEncodedHeader("Subject", $subject);
			$attachlogo = false;
			if (!empty ($sitelogo) && @ file_exists('./components/com_vikrentcar/resources/' . $sitelogo)) {
				$image = array (
				"FileName" => JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo, "Content-Type" => "automatic/name", "Disposition" => "inline");
				$email_message->CreateFilePart($image, $image_part);
				$image_content_id = $email_message->GetPartContentID($image_part);
				$attachlogo = true;
			}
			$tlogo = ($attachlogo ? "<img src=\"cid:" . $image_content_id . "\" alt=\"imglogo\"/>\n" : "");
		} else {
			$attachlogo = false;
			if (!empty ($sitelogo) && @ file_exists('./components/com_vikrentcar/resources/' . $sitelogo)) {
				$attachlogo = true;
			}
			$tlogo = ($attachlogo ? "<img src=\"" . JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo . "\" alt=\"imglogo\"/>\n" : "");
		}
		//vikrentcar 1.5
		$tcname = $ftitle."\n";
		$todate = date($df . ' H:i', $ts)."\n";
		$tcustdata = nl2br($custdata)."\n";
		$tiname = $carname."\n";
		$tpickupdate = date($df . ' H:i', $first)."\n";
		$tdropdate = date($df . ' H:i', $second)."\n";
		$tpickupplace = (!empty ($place) ? $place."\n" : "");
		$tdropplace = (!empty ($returnplace) ? $returnplace."\n" : "");
		$tprices = $pricestr;
		$topts = $optstr;
		$tlocfee = $maillocfee;
		$ttot = $tot."\n";
		$tlink = $link;
		$tfootm = $footerordmail;
		$hmess = self::parseEmailTemplate($tmpl, $orderid, $currencyname, $status, $tlogo, $tcname, $todate, $tcustdata, $tiname, $tpickupdate, $tdropdate, $tpickupplace, $tdropplace, $tprices, $topts, $tlocfee, $ttot, $tlink, $tfootm, $strcouponeff);
		//
		
		if (!$useju) {
			$email_message->CreateQuotedPrintableHTMLPart($hmess, "", $html_part);
			$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($msg), "", $text_part);
			$alternative_parts = array (
				$text_part,
				$html_part
			);
			$email_message->CreateAlternativeMultipart($alternative_parts, $alternative_part);
			$related_parts = array (
				$alternative_part,
				$image_part
			);
			$email_message->AddRelatedMultipart($related_parts);
			$error = $email_message->Send();
			if (strcmp($error, "")) {
				//$msg = utf8_decode($msg);
				@ mail($to, $subject, $msg, "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8");
			}
		} else {
			$hmess = '<html>'."\n".'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>'."\n".'<body>'.$hmess.'</body>'."\n".'</html>';
			JUtility :: sendMail($from_address, $from_name, $to, $subject, $hmess, true, null, null, null, $reply_address, $reply_address);
		}
		//
		
		return true;
	}

	function paypalForm($imp, $tax, $sid, $ts, $carname, $currencysymb = "") {
		$dbo = & JFactory :: getDBO();
		$depositmess = "";
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='paytotal';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		if (intval($s[0]['setting']) == 0) {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='payaccpercent';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$per = $dbo->loadAssocList();
			if ($per[0]['setting'] > 0) {
				$imp = $imp * $per[0]['setting'] / 100;
				$tax = $tax * $per[0]['setting'] / 100;
				$depositmess = "<p><strong>" . JText :: _('VRLEAVEDEPOSIT') . " " . (number_format($imp + $tax, 2)) . " " . $currencysymb . "</strong></p><br/>";
			}
		}
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ccpaypal';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$acc = $dbo->loadAssocList();
		$q = "SELECT `id`,`setting` FROM `#__vikrentcar_texts` WHERE `param`='paymentname';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$payname = $dbo->loadAssocList();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='currencycodepp';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$paypalcurcode = trim($dbo->loadResult());
		$itname = (empty ($payname[0]['setting']) ? $carname : $payname[0]['setting']);
		$returl = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $sid . "&ts=" . $ts;
		$notifyurl = JURI :: root() . "index.php?option=com_vikrentcar&task=notifypayment&sid=" . $sid . "&ts=" . $ts;
		$form = "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">\n";
		$form .= "<input type=\"hidden\" name=\"business\" value=\"" . $acc[0]['setting'] . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"cmd\" value=\"_xclick\"/>\n";
		$form .= "<input type=\"hidden\" name=\"amount\" value=\"" . number_format($imp, 2) . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"item_name\" value=\"" . $itname . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"item_number\" value=\"" . $carname . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"quantity\" value=\"1\"/>\n";
		$form .= "<input type=\"hidden\" name=\"tax\" value=\"" . number_format($tax, 2) . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"shipping\" value=\"0.00\"/>\n";
		$form .= "<input type=\"hidden\" name=\"currency_code\" value=\"" . $paypalcurcode . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"no_shipping\" value=\"1\"/>\n";
		$form .= "<input type=\"hidden\" name=\"rm\" value=\"2\"/>\n";
		$form .= "<input type=\"hidden\" name=\"notify_url\" value=\"" . $notifyurl . "\"/>\n";
		$form .= "<input type=\"hidden\" name=\"return\" value=\"" . $returl . "\"/>\n";
		$form .= "<input type=\"image\" src=\"https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
		$form .= "</form>\n";
		return $depositmess . $form;
	}

	function sendJutility() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sendjutility';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return (intval($s[0]['setting']) == 1 ? true : false);
	}

	function saveOldOrders() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='oldorders';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return (intval($s[0]['setting']) == 1 ? true : false);
	}

	function getInfoProfile($user_id) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_profiles` WHERE `user_id`='".$user_id."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return $s;
	}

	function allowStats() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='allowstats';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return (intval($s[0]['setting']) == 1 ? true : false);
	}

	function sendMailStats() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sendmailstats';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$s = $dbo->loadAssocList();
		return (intval($s[0]['setting']) == 1 ? true : false);
	}

	function getPlaceName($idplace) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`name` FROM `#__vikrentcar_places` WHERE `id`='" . $dbo->getEscaped($idplace) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = @ $dbo->loadAssocList();
		return $p[0]['name'];
	}

	function getsplaces(){

		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`name` FROM `#__vikrentcar_places` ;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p =  $dbo->loadAssocList();
		return $p;

	}


	
	function getOrderInfo($id) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_orders` WHERE `id`='" . $dbo->getEscaped($id) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = @ $dbo->loadAssocList();
		return $p[0];
	}
	
	function getPlaceRestriccion($idplace) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `restriccion` FROM `#__vikrentcar_places` WHERE `id`='" . $dbo->getEscaped($idplace) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = @ $dbo->loadAssocList();
		return $p[0]['restriccion'];
	}

	function getPlaceRestriccionTime($idplace) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `timerest` FROM `#__vikrentcar_places` WHERE `id`='" . $dbo->getEscaped($idplace) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = @ $dbo->loadAssocList();
		return $p[0]['timerest'];
	}
	
	function getCreditoUser($idplace) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='" . $dbo->getEscaped($idplace) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = @ $dbo->loadAssocList();
		return $p[0]['credito'];
	}
	
	function getValueCreditUserHours($hours, $idprice, $idcar){
		
		$dbo = & JFactory :: getDBO();
		$q=  "SELECT `cost` FROM  `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hours . "' AND   `idprice`='".$idprice."'"." AND   `idcar`='".$idcar."'";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$cost = $dbo->loadAssocList();
		
		return $cost[0]['cost'];
		
	}
	
	function getValueCreditUserDays($days){
		//arreglar esto para horas
		$dbo = & JFactory :: getDBO();
		$q=  "SELECT `cost` FROM  `#__vikrentcar_dispcost` WHERE `days`='" . $days . "' AND   `idprice`='".$idprice."'"." AND   `idcar`='".$idcar."'";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = $dbo->loadAssocList();
		
		return $p[0]['cost'];
		
	}
	
	
	

	function getCategoryName($idcat) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id`,`name` FROM `#__vikrentcar_categories` WHERE `id`='" . $dbo->getEscaped($idcat) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$p = @ $dbo->loadAssocList();
		return $p[0]['name'];
	}

	function getLocFee($from, $to) {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_locfees` WHERE `from`='" . $dbo->getEscaped($from) . "' AND `to`='" . $dbo->getEscaped($to) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() > 0) {
			$res = $dbo->loadAssocList();
			return $res[0];
		}
		return false;
	}

	function sayLocFeePlusIva($cost, $idiva) {
		$dbo = & JFactory :: getDBO();
		$session =& JFactory::getSession();
		$sval = $session->get('ivaInclusa', '');
		if(strlen($sval) > 0) {
			$ivainclusa = $sval;
		}else {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$iva = $dbo->loadAssocList();
			$session->set('ivaInclusa', $iva[0]['setting']);
			$ivainclusa = $iva[0]['setting'];
		}
		if (intval($ivainclusa) == 0) {
			$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $idiva . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$piva = $dbo->loadAssocList();
				$subt = 100 + $piva[0]['aliq'];
				$op = ($cost * $subt / 100);
				//				$op=number_format($op, 2);
				return $op;
			}
		}
		return $cost;
	}

	function sayLocFeeMinusIva($cost, $idiva) {
		$dbo = & JFactory :: getDBO();
		$session =& JFactory::getSession();
		$sval = $session->get('ivaInclusa', '');
		if(strlen($sval) > 0) {
			$ivainclusa = $sval;
		}else {
			$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='ivainclusa';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$iva = $dbo->loadAssocList();
			$session->set('ivaInclusa', $iva[0]['setting']);
			$ivainclusa = $iva[0]['setting'];
		}
		if (intval($ivainclusa) == 1) {
			$q = "SELECT `aliq` FROM `#__vikrentcar_iva` WHERE `id`='" . $idiva . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$piva = $dbo->loadAssocList();
				$subt = 100 + $piva[0]['aliq'];
				$op = ($cost * 100 / $subt);
				return $op;
			}
		}
		return $cost;
	}
	
	function sortCarPrices($arr) {
		$newarr = array ();
		foreach ($arr as $k => $v) {
			$newarr[$k] = $v['cost'];
		}
		asort($newarr);
		$sorted = array ();
		foreach ($newarr as $k => $v) {
			$sorted[$k] = $arr[$k];
		}
		return $sorted;
	}
	
	function sortResults($arr) {
		$newarr = array ();
		foreach ($arr as $k => $v) {
			$newarr[$k] = $v[0]['cost'];
		}
		asort($newarr);
		$sorted = array ();
		foreach ($newarr as $k => $v) {
			$sorted[$k] = $arr[$k];
		}
		return $sorted;
	}

	function applySeasonalPrices($arr, $from, $to, $pickup) {
		$dbo = & JFactory :: getDBO();
		$carschange = array();
		$one = getdate($from);
		//leap years
		if(($one['year'] % 4) == 0 && ($one['year'] % 100 != 0 || $one['year'] % 400 == 0)) {
			$isleap = true;
		}else {
			$isleap = false;
		}
		//
		$baseone = mktime(0, 0, 0, 1, 1, $one['year']);
		$tomidnightone = intval($one['hours']) * 3600;
		$tomidnightone += intval($one['minutes']) * 60;
		$sfrom = $from - $baseone - $tomidnightone;
		$fromdayts = mktime(0, 0, 0, $one['mon'], $one['mday'], $one['year']);
		$two = getdate($to);
		$basetwo = mktime(0, 0, 0, 1, 1, $two['year']);
		$tomidnighttwo = intval($two['hours']) * 3600;
		$tomidnighttwo += intval($two['minutes']) * 60;
		$sto = $to - $basetwo - $tomidnighttwo;
		//leap years, last day of the month of the season
		if($isleap) {
			$leapts = mktime(0, 0, 0, 2, 29, $two['year']);
			if($two[0] >= $leapts) {
				$sfrom -= 86400;
				$sto -= 86400;
			}
		}
		//
		$q = "SELECT * FROM `#__vikrentcar_seasons` WHERE (`locations`='0' OR `locations`='" . $pickup . "') AND (" .
		 ($sto > $sfrom ? "(`from`<=" . $sfrom . " AND `to`>=" . $sto . ") " : "") .
		 ($sto > $sfrom ? "OR (`from`<=" . $sfrom . " AND `to`>=" . $sfrom . ") " : "(`from`<=" . $sfrom . " AND `to`<=" . $sfrom . " AND `from`>`to`) ") .
		 ($sto > $sfrom ? "OR (`from`<=" . $sto . " AND `to`>=" . $sto . ") " : "OR (`from`>=" . $sto . " AND `to`>=" . $sto . " AND `from`>`to`) ") .
		 ($sto > $sfrom ? "OR (`from`>=" . $sfrom . " AND `from`<=" . $sto . " AND `to`>=" . $sfrom . " AND `to`<=" . $sto . ")" : "OR (`from`>=" . $sfrom . " AND `from`>" . $sto . " AND `to`<" . $sfrom . " AND `to`<=" . $sto . " AND `from`>`to`)") .
		 ($sto > $sfrom ? " OR (`from`<=" . $sfrom . " AND `from`<=" . $sto . " AND `to`<" . $sfrom . " AND `to`<" . $sto . " AND `from`>`to`) OR (`from`>" . $sfrom . " AND `from`>" . $sto . " AND `to`>=" . $sfrom . " AND `to`>=" . $sto . " AND `from`>`to`)" : " OR (`from` <=" . $sfrom . " AND `to` >=" . $sfrom . " AND `from` >" . $sto . " AND `to` >" . $sto . " AND `from` < `to`)") .
		 ($sto > $sfrom ? " OR (`from` >=" . $sfrom . " AND `from` <" . $sto . " AND `to` <" . $sfrom . " AND `to` <" . $sto . " AND `from` > `to`)" : "").
		 ($sto > $sfrom ? " OR (`from` >" . $sfrom . " AND `from` >" . $sto . " AND `to` >=" . $sfrom . " AND `to` <" . $sto . " AND `from` > `to`)" : "").
		");";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$totseasons = $dbo->getNumRows();
		if ($totseasons > 0) {
			$seasons = $dbo->loadAssocList();
			$applyseasons = false;
			$mem = array();
			foreach ($arr as $k => $a) {
				$mem[$k]['daysused'] = 0;
				$mem[$k]['sum'] = array();
			}
			foreach ($seasons as $s) {
				$allcars = explode(",", $s['idcars']);
				$inits = $baseone + $s['from'];
				if ($s['from'] < $s['to']) {
					$ends = $basetwo + $s['to'];
				} else {
					//between 2 years
					if ($baseone < $basetwo) {
						//ex. 29/12/2012 - 14/01/2013
						$ends = $basetwo + $s['to'];
					} else {
						if (($sfrom >= $s['from'] && $sto >= $s['from']) OR ($sfrom < $s['from'] && $sto >= $s['from'] && $sfrom > $s['to'] && $sto > $s['to'])) {
							//ex. 25/12 - 30/12 with init season on 20/12 OR 27/12 for counting 28,29,30/12
							$tmpbase = mktime(0, 0, 0, 1, 1, ($one['year'] + 1));
							$ends = $tmpbase + $s['to'];
						} else {
							//ex. 03/01 - 09/01
							$ends = $basetwo + $s['to'];
							$tmpbase = mktime(0, 0, 0, 1, 1, ($one['year'] - 1));
							$inits = $tmpbase + $s['from'];
						}
					}
				}
				//leap years
				if($isleap == true) {
					$infoseason = getdate($inits);
					$leapts = mktime(0, 0, 0, 2, 29, $infoseason['year']);
					if($infoseason[0] >= $leapts) {
						$inits += 86400;
						$ends += 86400;
					}
				}
				//
				//week days
				$filterwdays = !empty($s['wdays']) ? true : false;
				$wdays = $filterwdays == true ? explode(';', $s['wdays']) : '';
				//
				//pickup must be after the begin of the season
				if($s['pickupincl'] == 1) {
					$pickupinclok = false;
					if($s['from'] < $s['to']) {
						if($sfrom >= $s['from'] && $sfrom <= $s['to']) {
							$pickupinclok = true;
						}
					}else {
						if(($sfrom >= $s['from'] && $sfrom > $s['to']) || ($sfrom < $s['from'] && $sfrom <= $s['to'])) {
							$pickupinclok = true;
						}
					}
				}else {
					$pickupinclok = true;
				}
				//
				if($pickupinclok == true) {
					foreach ($arr as $k => $a) {
						if (in_array("-" . $a[0]['idcar'] . "-", $allcars)) {
							$affdays = 0;
							for ($i = 0; $i < $a[0]['days']; $i++) {
								$todayts = $fromdayts + ($i * 86400);
								if ($todayts >= $inits && $todayts <= $ends) {
									//week days
									if($filterwdays == true) {
										$checkwday = getdate($todayts);
										if(in_array($checkwday['wday'], $wdays)) {
											$affdays++;
										}
									}else {
										$affdays++;
									}
									//
								}
							}
							if ($affdays > 0) {
								$applyseasons = true;
								$dailyprice = $a[0]['cost'] / $a[0]['days'];
								if (intval($s['type']) == 1) {
									//charge
									$cpercent = 100 + $s['diffcost'];
								} else {
									//discount
									$cpercent = 100 - $s['diffcost'];
								}
								$newprice = ($dailyprice * $cpercent / 100) * $affdays;
								$mem[$k]['sum'][] = $newprice;
								$mem[$k]['daysused'] += $affdays;
								$carschange[] = $a[0]['idcar'];
							}
						}
					}
				}
			}
			if ($applyseasons) {
				foreach ($mem as $k => $v) {
					if ($v['daysused'] > 0 && @ count($v['sum']) > 0) {
						$newprice = 0;
						$dailyprice = $arr[$k][0]['cost'] / $arr[$k][0]['days'];
						$restdays = $arr[$k][0]['days'] - $v['daysused'];
						$addrest = $restdays * $dailyprice;
						$newprice += $addrest;
						foreach ($v['sum'] as $add) {
							$newprice += $add;
						}
						$arr[$k][0]['cost'] = $newprice;
						$arr[$k][0]['affdays'] = $v['daysused'];
					}
				}
			}
		}
		//week days with no season
		$carschange = array_unique($carschange);
		$q="SELECT * FROM `#__vikrentcar_seasons` WHERE (`locations`='0' OR `locations`='" . $dbo->getEscaped($pickup) . "') AND ((`from` = 0 AND `to` = 0) OR (`from` IS NULL AND `to` IS NULL));";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() > 0) {
			$specials = $dbo->loadAssocList();
			$applyseasons = false;
			unset($mem);
			$mem = array();
			foreach ($arr as $k => $a) {
				$mem[$k]['daysused'] = 0;
				$mem[$k]['sum'] = array ();
			}
			foreach($specials as $s) {
				$allcars = explode(",", $s['idcars']);
				//week days
				$filterwdays = !empty($s['wdays']) ? true : false;
				$wdays = $filterwdays == true ? explode(';', $s['wdays']) : '';
				//
				foreach ($arr as $k => $a) {
					//only cars with no price modifications from seasons
					if (in_array("-" . $a[0]['idcar'] . "-", $allcars) && !in_array($a[0]['idcar'], $carschange)) {
						$affdays = 0;
						for ($i = 0; $i < $a[0]['days']; $i++) {
							$todayts = $fromdayts + ($i * 86400);
							//week days
							if($filterwdays == true) {
								$checkwday = getdate($todayts);
								if(in_array($checkwday['wday'], $wdays)) {
									$affdays++;
								}
							}
							//
						}
						if ($affdays > 0) {
							$applyseasons = true;
							$dailyprice = $a[0]['cost'] / $a[0]['days'];
							if (intval($s['type']) == 1) {
								//charge
								$cpercent = 100 + $s['diffcost'];
							} else {
								//discount
								$cpercent = 100 - $s['diffcost'];
							}
							$newprice = ($dailyprice * $cpercent / 100) * $affdays;
							$mem[$k]['sum'][] = $newprice;
							$mem[$k]['daysused'] += $affdays;
						}
					}
				}
			}
			if ($applyseasons) {
				foreach ($mem as $k => $v) {
					if ($v['daysused'] > 0 && @ count($v['sum']) > 0) {
						$newprice = 0;
						$dailyprice = $arr[$k][0]['cost'] / $arr[$k][0]['days'];
						$restdays = $arr[$k][0]['days'] - $v['daysused'];
						$addrest = $restdays * $dailyprice;
						$newprice += $addrest;
						foreach ($v['sum'] as $add) {
							$newprice += $add;
						}
						$arr[$k][0]['cost'] = $newprice;
						$arr[$k][0]['affdays'] = $v['daysused'];
					}
				}
			}
		}
		//end week days with no season
		return $arr;
	}

	function applySeasonsCar($arr, $from, $to, $pickup) {
		$dbo = & JFactory :: getDBO();
		$carschange = array();
		$one = getdate($from);
		//leap years
		if($one['year'] % 4 == 0 && ($one['year'] % 100 != 0 || $one['year'] % 400 == 0)) {
			$isleap = true;
		}else {
			$isleap = false;
		}
		//
		$baseone = mktime(0, 0, 0, 1, 1, $one['year']);
		$tomidnightone = intval($one['hours']) * 3600;
		$tomidnightone += intval($one['minutes']) * 60;
		$sfrom = $from - $baseone - $tomidnightone;
		$fromdayts = mktime(0, 0, 0, $one['mon'], $one['mday'], $one['year']);
		$two = getdate($to);
		$basetwo = mktime(0, 0, 0, 1, 1, $two['year']);
		$tomidnighttwo = intval($two['hours']) * 3600;
		$tomidnighttwo += intval($two['minutes']) * 60;
		$sto = $to - $basetwo - $tomidnighttwo;
		//leap years, last day of the month of the season
		if($isleap) {
			$leapts = mktime(0, 0, 0, 2, 29, $two['year']);
			if($two[0] >= $leapts) {
				$sfrom -= 86400;
				$sto -= 86400;
			}
		}
		//
		$q = "SELECT * FROM `#__vikrentcar_seasons` WHERE (`locations`='0' OR `locations`='" . $pickup . "') AND (" .
		 ($sto > $sfrom ? "(`from`<=" . $sfrom . " AND `to`>=" . $sto . ") " : "") .
		 ($sto > $sfrom ? "OR (`from`<=" . $sfrom . " AND `to`>=" . $sfrom . ") " : "(`from`<=" . $sfrom . " AND `to`<=" . $sfrom . " AND `from`>`to`) ") .
		 ($sto > $sfrom ? "OR (`from`<=" . $sto . " AND `to`>=" . $sto . ") " : "OR (`from`>=" . $sto . " AND `to`>=" . $sto . " AND `from`>`to`) ") .
		 ($sto > $sfrom ? "OR (`from`>=" . $sfrom . " AND `from`<=" . $sto . " AND `to`>=" . $sfrom . " AND `to`<=" . $sto . ")" : "OR (`from`>=" . $sfrom . " AND `from`>" . $sto . " AND `to`<" . $sfrom . " AND `to`<=" . $sto . " AND `from`>`to`)") .
		 ($sto > $sfrom ? " OR (`from`<=" . $sfrom . " AND `from`<=" . $sto . " AND `to`<" . $sfrom . " AND `to`<" . $sto . " AND `from`>`to`) OR (`from`>" . $sfrom . " AND `from`>" . $sto . " AND `to`>=" . $sfrom . " AND `to`>=" . $sto . " AND `from`>`to`)" : " OR (`from` <=" . $sfrom . " AND `to` >=" . $sfrom . " AND `from` >" . $sto . " AND `to` >" . $sto . " AND `from` < `to`)") .
		 ($sto > $sfrom ? " OR (`from` >=" . $sfrom . " AND `from` <" . $sto . " AND `to` <" . $sfrom . " AND `to` <" . $sto . " AND `from` > `to`)" : "").
		 ($sto > $sfrom ? " OR (`from` >" . $sfrom . " AND `from` >" . $sto . " AND `to` >=" . $sfrom . " AND `to` <" . $sto . " AND `from` > `to`)" : "").
		");";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$totseasons = $dbo->getNumRows();
		if ($totseasons > 0) {
			$seasons = $dbo->loadAssocList();
			$applyseasons = false;
			$mem = array ();
			foreach ($arr as $k => $a) {
				$mem[$k]['daysused'] = 0;
				$mem[$k]['sum'] = array ();
			}
			foreach ($seasons as $s) {
				$allcars = explode(",", $s['idcars']);
				$inits = $baseone + $s['from'];
				if ($s['from'] < $s['to']) {
					$ends = $basetwo + $s['to'];
				} else {
					//between 2 years
					if ($baseone < $basetwo) {
						//ex. 29/12/2012 - 14/01/2013
						$ends = $basetwo + $s['to'];
					} else {
						if (($sfrom >= $s['from'] && $sto >= $s['from']) OR ($sfrom < $s['from'] && $sto >= $s['from'] && $sfrom > $s['to'] && $sto > $s['to'])) {
							//ex. 25/12 - 30/12 with init season on 20/12 OR 27/12 for counting 28,29,30/12
							$tmpbase = mktime(0, 0, 0, 1, 1, ($one['year'] + 1));
							$ends = $tmpbase + $s['to'];
						} else {
							//ex. 03/01 - 09/01
							$ends = $basetwo + $s['to'];
							$tmpbase = mktime(0, 0, 0, 1, 1, ($one['year'] - 1));
							$inits = $tmpbase + $s['from'];
						}
					}
				}
				//leap years
				if($isleap == true) {
					$infoseason = getdate($inits);
					$leapts = mktime(0, 0, 0, 2, 29, $infoseason['year']);
					if($infoseason[0] >= $leapts) {
						$inits += 86400;
						$ends += 86400;
					}
				}
				//
				//week days
				$filterwdays = !empty($s['wdays']) ? true : false;
				$wdays = $filterwdays == true ? explode(';', $s['wdays']) : '';
				//
				//pickup must be after the begin of the season
				if($s['pickupincl'] == 1) {
					$pickupinclok = false;
					if($s['from'] < $s['to']) {
						if($sfrom >= $s['from'] && $sfrom <= $s['to']) {
							$pickupinclok = true;
						}
					}else {
						if(($sfrom >= $s['from'] && $sfrom > $s['to']) || ($sfrom < $s['from'] && $sfrom <= $s['to'])) {
							$pickupinclok = true;
						}
					}
				}else {
					$pickupinclok = true;
				}
				//
				if($pickupinclok == true) {
					foreach ($arr as $k => $a) {
						if (in_array("-" . $a['idcar'] . "-", $allcars)) {
							$affdays = 0;
							for ($i = 0; $i < $a['days']; $i++) {
								$todayts = $fromdayts + ($i * 86400);
								if ($todayts >= $inits && $todayts <= $ends) {
									//week days
									if($filterwdays == true) {
										$checkwday = getdate($todayts);
										if(in_array($checkwday['wday'], $wdays)) {
											$affdays++;
										}
									}else {
										$affdays++;
									}
									//
								}
							}
							if ($affdays > 0) {
								$applyseasons = true;
								$dailyprice = $a['cost'] / $a['days'];
								if (intval($s['type']) == 1) {
									//charge
									$cpercent = 100 + $s['diffcost'];
								} else {
									//discount
									$cpercent = 100 - $s['diffcost'];
								}
								$newprice = ($dailyprice * $cpercent / 100) * $affdays;
								$mem[$k]['sum'][] = $newprice;
								$mem[$k]['daysused'] += $affdays;
								$carschange[] = $a['idcar'];
							}
						}
					}
				}
			}
			if ($applyseasons) {
				foreach ($mem as $k => $v) {
					if ($v['daysused'] > 0 && @ count($v['sum']) > 0) {
						$newprice = 0;
						$dailyprice = $arr[$k]['cost'] / $arr[$k]['days'];
						$restdays = $arr[$k]['days'] - $v['daysused'];
						$addrest = $restdays * $dailyprice;
						$newprice += $addrest;
						foreach ($v['sum'] as $add) {
							$newprice += $add;
						}
						$arr[$k]['cost'] = $newprice;
						$arr[$k]['affdays'] = $v['daysused'];
					}
				}
			}
		}
		//week days with no season
		$carschange = array_unique($carschange);
		$q="SELECT * FROM `#__vikrentcar_seasons` WHERE (`locations`='0' OR `locations`='" . $dbo->getEscaped($pickup) . "') AND ((`from` = 0 AND `to` = 0) OR (`from` IS NULL AND `to` IS NULL));";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if($dbo->getNumRows() > 0) {
			$specials = $dbo->loadAssocList();
			$applyseasons = false;
			unset($mem);
			$mem = array();
			foreach ($arr as $k => $a) {
				$mem[$k]['daysused'] = 0;
				$mem[$k]['sum'] = array ();
			}
			foreach($specials as $s) {
				$allcars = explode(",", $s['idcars']);
				//week days
				$filterwdays = !empty($s['wdays']) ? true : false;
				$wdays = $filterwdays == true ? explode(';', $s['wdays']) : '';
				//
				foreach ($arr as $k => $a) {
					//only cars with no price modifications from seasons
					if (in_array("-" . $a['idcar'] . "-", $allcars) && !in_array($a['idcar'], $carschange)) {
						$affdays = 0;
						for ($i = 0; $i < $a['days']; $i++) {
							$todayts = $fromdayts + ($i * 86400);
							//week days
							if($filterwdays == true) {
								$checkwday = getdate($todayts);
								if(in_array($checkwday['wday'], $wdays)) {
									$affdays++;
								}
							}
							//
						}
						if ($affdays > 0) {
							$applyseasons = true;
							$dailyprice = $a['cost'] / $a['days'];
							if (intval($s['type']) == 1) {
								//charge
								$cpercent = 100 + $s['diffcost'];
							} else {
								//discount
								$cpercent = 100 - $s['diffcost'];
							}
							$newprice = ($dailyprice * $cpercent / 100) * $affdays;
							$mem[$k]['sum'][] = $newprice;
							$mem[$k]['daysused'] += $affdays;
						}
					}
				}
			}
			if ($applyseasons) {
				foreach ($mem as $k => $v) {
					if ($v['daysused'] > 0 && @ count($v['sum']) > 0) {
						$newprice = 0;
						$dailyprice = $arr[$k]['cost'] / $arr[$k]['days'];
						$restdays = $arr[$k]['days'] - $v['daysused'];
						$addrest = $restdays * $dailyprice;
						$newprice += $addrest;
						foreach ($v['sum'] as $add) {
							$newprice += $add;
						}
						$arr[$k]['cost'] = $newprice;
						$arr[$k]['affdays'] = $v['daysused'];
					}
				}
			}
		}
		//end week days with no season
		return $arr;
	}

	function areTherePayments() {
		$dbo = & JFactory :: getDBO();
		$q = "SELECT `id` FROM `#__vikrentcar_gpayments` WHERE `published`='1';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		return $dbo->getNumRows() > 0 ? true : false;
	}

	function getPayment($idp) {
		if (!empty ($idp)) {
			$dbo = & JFactory :: getDBO();
			$q = "SELECT * FROM `#__vikrentcar_gpayments` WHERE `id`='" . $dbo->getEscaped($idp) . "' AND `published`='1';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$payment = $dbo->loadAssocList();
				return $payment[0];
			} else {
				return false;
			}
		}
		return false;
	}
	
	function applyHourlyPrices($arrtar, $hoursdiff, $pattr) {
		
		
		$dbo = & JFactory :: getDBO();
		if($pattr==undefined || $pattr==''  ){
			
		$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC, `#__vikrentcar_dispcosthours`.`idcar` ASC;";
		}else{
		$q = "SELECT * FROM `#__vikrentcar_dispcosthours`  WHERE attrdata LIKE '%".$pattr."%' AND `hours`='" . $hoursdiff . "' ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC, `#__vikrentcar_dispcosthours`.`idcar` ASC;";	
		}





		/*if($pattr== 'Elegido'){

			//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $daysdiff. "AND `idcar`=6 ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
								      

		 	//$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' AND `idcar`=6 ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC, `#__vikrentcar_dispcosthours`.`idcar` ASC;";
			$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC, `#__vikrentcar_dispcosthours`.`idcar` ASC;";
							        
		}*/


		$dbo->setQuery($q);
		$dbo->Query($q);

		if ($dbo->getNumRows() > 0) {
			$hourtars = $dbo->loadAssocList();
			$hourarrtar = array();
			foreach ($hourtars as $tar) {
				$hourarrtar[$tar['idcar']][] = $tar;
			}
			foreach($arrtar as $idcar => $tar) {
				if(array_key_exists($idcar, $hourarrtar)) {
					foreach($tar as $ind => $fare) {
						//check if idprice exists in $hourarrtar
						foreach($hourarrtar[$idcar] as $hind => $hfare) {
							if($fare['idprice'] == $hfare['idprice']) {
								$arrtar[$idcar][$ind]['id'] = $hourarrtar[$idcar][$hind]['id'];
								$arrtar[$idcar][$ind]['cost'] = $hourarrtar[$idcar][$hind]['cost'];
								$arrtar[$idcar][$ind]['attrdata'] = $hourarrtar[$idcar][$hind]['attrdata'];
								$arrtar[$idcar][$ind]['hours'] = $hourarrtar[$idcar][$hind]['hours'];
							}
						}
					}
				}
			}
		}
		
		
		return $arrtar;
	}
	
	function applyHourlyPricesCar($arrtar, $hoursdiff, $idcar, $filterprice = false, $pattr) {
		$dbo = & JFactory :: getDBO();
		
		$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE attrdata LIKE '%".$pattr."%' AND  `hours`='" . $hoursdiff . "' AND `idcar`='" . $dbo->getEscaped($idcar) . "'".($filterprice == true ? " AND `idprice`='".$arrtar[0]['idprice']."'" : "")." ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC;";	
		//
		//if($pattr==undefined || $pattr==''  ){
		/*
		if(empty ($pattr) ){
		$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' AND `idcar`='" . $dbo->getEscaped($idcar) . "'".($filterprice == true ? " AND `idprice`='".$arrtar[0]['idprice']."'" : "")." ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC;";
		}else{

		$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE attrdata='".$pattr."' AND  `hours`='" . $hoursdiff . "' AND `idcar`='" . $dbo->getEscaped($idcar) . "'".($filterprice == true ? " AND `idprice`='".$arrtar[0]['idprice']."'" : "")." ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC;";	
		}
		*/



		/*if($pattr== 'Elegido'){

			//$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='" . $daysdiff. "AND `idcar`=6 ORDER BY `#__vikrentcar_dispcost`.`cost` ASC, `#__vikrentcar_dispcost`.`idcar` ASC;";
								      

		 	//$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' AND `idcar`=6 ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC, `#__vikrentcar_dispcosthours`.`idcar` ASC;";
			$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `hours`='" . $hoursdiff . "' AND attrdata='".$pattr."'  AND `idcar`='" . $dbo->getEscaped($idcar) . "' ORDER BY `#__vikrentcar_dispcosthours`.`cost` ASC, `#__vikrentcar_dispcosthours`.`idcar` ASC;";
							        
		}*/

		$dbo->setQuery($q);
		$dbo->Query($q);


		
		if ($dbo->getNumRows() > 0) {
			$arrtar = $dbo->loadAssocList();
			foreach($arrtar as $k => $v) {
				$arrtar[$k]['days'] = 1;
			}
		}
		return $arrtar;
	}
	
	function extraHoursSetPreviousFare($arrtar, $ehours, $daysdiff) {
		//set the fare to the days of rental - 1 where hours charges exist
		//to be used when the hours charges need to be applied after the special prices
		$dbo = & JFactory :: getDBO();
		$idcars = array_keys($arrtar);
		if(count($idcars) > 0 && $daysdiff > 1) {
			$q="SELECT * FROM `#__vikrentcar_hourscharges` WHERE `ehours`='".$ehours."' AND `idcar` IN (".implode(",", $idcars).");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$ehcharges = $dbo->loadAssocList();
				$arrehcharges = array();
				foreach($ehcharges as $ehc) {
					$arrehcharges[$ehc['idcar']][]=$ehc;
				}
				$idcars = array_keys($arrehcharges);
				$newdaysdiff = $daysdiff - 1;
				$q="SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='".$newdaysdiff."' AND `idcar` IN (".implode(",", $idcars).");";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() > 0) {
					//only if there are fares for ($daysdiff - 1) otherwise dont apply extra hours charges
					$prevdaytars = $dbo->loadAssocList();
					$prevdayarrtar = array();
					foreach($prevdaytars as $pdtar) {
						$prevdayarrtar[$pdtar['idcar']][]=$pdtar;
					}
					//set fares for 1 day before of rental
					$newdispcostvals = array();
					$newdispcostattr = array();
					foreach($arrehcharges as $idc => $ehc) {
						if(array_key_exists($idc, $prevdayarrtar)) {
							foreach($prevdayarrtar[$idc] as $vp) {
								foreach($ehc as $hc) {
									if($vp['idprice'] == $hc['idprice']) {
										$newdispcostvals[$idc][$hc['idprice']] = $vp['cost'];
										$newdispcostattr[$idc][$hc['idprice']] = $vp['attrdata'];
									}
								}
							}
						}
					}
					if(count($newdispcostvals) > 0) {
						foreach($arrtar as $idc => $tar) {
							if(array_key_exists($idc, $newdispcostvals)) {
								foreach($tar as $krecp => $recp) {
									if(array_key_exists($recp['idprice'], $newdispcostvals[$idc])) {
										$arrtar[$idc][$krecp]['cost'] = $newdispcostvals[$idc][$recp['idprice']];
										$arrtar[$idc][$krecp]['attrdata'] = $newdispcostattr[$idc][$recp['idprice']];
										$arrtar[$idc][$krecp]['days'] = $newdaysdiff;
										$arrtar[$idc][$krecp]['ehours'] = $ehours;
									}
								}
							}
						}
					}
					//
				}
			}
		}
		return $arrtar;
	}
	
	function extraHoursSetPreviousFareCar($tar, $idcar, $ehours, $daysdiff, $filterprice = false) {
		//set the fare to the days of rental - 1 where hours charges exist
		//to be used when the hours charges need to be applied after the special prices
		$dbo = & JFactory :: getDBO();
		if($daysdiff > 1) {
			$q="SELECT * FROM `#__vikrentcar_hourscharges` WHERE `ehours`='".$ehours."' AND `idcar`='".$idcar."'".($filterprice == true ? " AND `idprice`='".$tar[0]['idprice']."'" : "").";";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$ehcharges = $dbo->loadAssocList();
				$newdaysdiff = $daysdiff - 1;
				$q="SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='".$newdaysdiff."' AND `idcar`='".$idcar."'".($filterprice == true ? " AND `idprice`='".$tar[0]['idprice']."'" : "").";";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if ($dbo->getNumRows() > 0) {
					//only if there are fares for ($daysdiff - 1) otherwise dont apply extra hours charges
					$prevdaytars = $dbo->loadAssocList();
					//set fares for 1 day before of rental
					$newdispcostvals = array();
					$newdispcostattr = array();
					foreach($ehcharges as $ehc) {
						foreach($prevdaytars as $vp) {
							if($vp['idprice'] == $ehc['idprice']) {
								$newdispcostvals[$ehc['idprice']] = $vp['cost'];
								$newdispcostattr[$ehc['idprice']] = $vp['attrdata'];
							}
						}
					}
					if(count($newdispcostvals) > 0) {
						foreach($tar as $kp => $f) {
							if(array_key_exists($f['idprice'], $newdispcostvals)) {
								$tar[$kp]['cost'] = $newdispcostvals[$f['idprice']];
								$tar[$kp]['attrdata'] = $newdispcostattr[$f['idprice']];
								$tar[$kp]['days'] = $newdaysdiff;
								$tar[$kp]['ehours'] = $ehours;
							}
						}
					}
					//
				}
			}
		}
		return $tar;
	}
	
	function applyExtraHoursChargesPrices($arrtar, $ehours, $daysdiff, $aftersp = false) {
		$dbo = & JFactory :: getDBO();
		$idcars = array_keys($arrtar);
		if(count($idcars) > 0 && $daysdiff > 1) {
			$q="SELECT * FROM `#__vikrentcar_hourscharges` WHERE `ehours`='".$ehours."' AND `idcar` IN (".implode(",", $idcars).");";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$ehcharges = $dbo->loadAssocList();
				$arrehcharges = array();
				foreach($ehcharges as $ehc) {
					$arrehcharges[$ehc['idcar']][]=$ehc;
				}
				$idcars = array_keys($arrehcharges);
				$newdaysdiff = $daysdiff - 1;
				if($aftersp == true) {
					//after having applied special prices, dont consider the fares for ($daysdiff - 1)
					//apply extra hours charges
					$newdispcostvals = array();
					$newdispcostattr = array();
					foreach($arrehcharges as $idc => $ehc) {
						if(array_key_exists($idc, $arrtar)) {
							foreach($arrtar[$idc] as $vp) {
								foreach($ehc as $hc) {
									if($vp['idprice'] == $hc['idprice']) {
										$newdispcostvals[$idc][$hc['idprice']] = $vp['cost'] + $hc['cost'];
										$newdispcostattr[$idc][$hc['idprice']] = $vp['attrdata'];
									}
								}
							}
						}
					}
					if(count($newdispcostvals) > 0) {
						foreach($arrtar as $idc => $tar) {
							if(array_key_exists($idc, $newdispcostvals)) {
								foreach($tar as $krecp => $recp) {
									if(array_key_exists($recp['idprice'], $newdispcostvals[$idc])) {
										$arrtar[$idc][$krecp]['cost'] = $newdispcostvals[$idc][$recp['idprice']];
										$arrtar[$idc][$krecp]['attrdata'] = $newdispcostattr[$idc][$recp['idprice']];
										$arrtar[$idc][$krecp]['days'] = $newdaysdiff;
										$arrtar[$idc][$krecp]['ehours'] = $ehours;
									}
								}
							}
						}
					}
					//
				}else {
					//before applying special prices
					$q="SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='".$newdaysdiff."' AND `idcar` IN (".implode(",", $idcars).");";
					$dbo->setQuery($q);
					$dbo->Query($q);
					if ($dbo->getNumRows() > 0) {
						//only if there are fares for ($daysdiff - 1) otherwise dont apply extra hours charges
						$prevdaytars = $dbo->loadAssocList();
						$prevdayarrtar = array();
						foreach($prevdaytars as $pdtar) {
							$prevdayarrtar[$pdtar['idcar']][]=$pdtar;
						}
						//apply extra hours charges
						$newdispcostvals = array();
						$newdispcostattr = array();
						foreach($arrehcharges as $idc => $ehc) {
							if(array_key_exists($idc, $prevdayarrtar)) {
								foreach($prevdayarrtar[$idc] as $vp) {
									foreach($ehc as $hc) {
										if($vp['idprice'] == $hc['idprice']) {
											$newdispcostvals[$idc][$hc['idprice']] = $vp['cost'] + $hc['cost'];
											$newdispcostattr[$idc][$hc['idprice']] = $vp['attrdata'];
										}
									}
								}
							}
						}
						if(count($newdispcostvals) > 0) {
							foreach($arrtar as $idc => $tar) {
								if(array_key_exists($idc, $newdispcostvals)) {
									foreach($tar as $krecp => $recp) {
										if(array_key_exists($recp['idprice'], $newdispcostvals[$idc])) {
											$arrtar[$idc][$krecp]['cost'] = $newdispcostvals[$idc][$recp['idprice']];
											$arrtar[$idc][$krecp]['attrdata'] = $newdispcostattr[$idc][$recp['idprice']];
											$arrtar[$idc][$krecp]['days'] = $newdaysdiff;
											$arrtar[$idc][$krecp]['ehours'] = $ehours;
										}
									}
								}
							}
						}
						//
					}
				}
			}
		}
		return $arrtar;
	}
	
	function applyExtraHoursChargesCar($tar, $idcar, $ehours, $daysdiff, $aftersp = false, $filterprice = false, $retarray = false) {
		$dbo = & JFactory :: getDBO();
		$newdaysdiff = $daysdiff;
		if($daysdiff > 1) {
			$q="SELECT * FROM `#__vikrentcar_hourscharges` WHERE `ehours`='".$ehours."' AND `idcar`='".$idcar."'".($filterprice == true ? " AND `idprice`='".$tar[0]['idprice']."'" : "").";";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$ehcharges = $dbo->loadAssocList();
				$newdaysdiff = $daysdiff - 1;
				if($aftersp == true) {
					//after having applied special prices, dont consider the fares for ($daysdiff - 1) because done already
					//apply extra hours charges
					$newdispcostvals = array();
					$newdispcostattr = array();
					foreach($ehcharges as $ehc) {
						foreach($tar as $vp) {
							if($vp['idprice'] == $ehc['idprice']) {
								$newdispcostvals[$ehc['idprice']] = $vp['cost'] + $ehc['cost'];
								$newdispcostattr[$ehc['idprice']] = $vp['attrdata'];
							}
						}
					}
					if(count($newdispcostvals) > 0) {
						foreach($tar as $kt => $f) {
							if(array_key_exists($f['idprice'], $newdispcostvals)) {
								$tar[$kt]['cost'] = $newdispcostvals[$f['idprice']];
								$tar[$kt]['attrdata'] = $newdispcostattr[$f['idprice']];
								$tar[$kt]['days'] = $newdaysdiff;
								$tar[$kt]['ehours'] = $ehours;
							}
						}
					}
					//
				}else {
					//before applying special prices
					$q="SELECT * FROM `#__vikrentcar_dispcost` WHERE `days`='".$newdaysdiff."' AND `idcar`='".$idcar."'".($filterprice == true ? " AND `idprice`='".$tar[0]['idprice']."'" : "").";";
					$dbo->setQuery($q);
					$dbo->Query($q);
					if ($dbo->getNumRows() > 0) {
						//only if there are fares for ($daysdiff - 1) otherwise dont apply extra hours charges
						$prevdaytars = $dbo->loadAssocList();
						//apply extra hours charges
						$newdispcostvals = array();
						$newdispcostattr = array();
						foreach($ehcharges as $ehc) {
							foreach($prevdaytars as $vp) {
								if($vp['idprice'] == $ehc['idprice']) {
									$newdispcostvals[$ehc['idprice']] = $vp['cost'] + $ehc['cost'];
									$newdispcostattr[$ehc['idprice']] = $vp['attrdata'];
								}
							}
						}
						if(count($newdispcostvals) > 0) {
							foreach($tar as $kt => $f) {
								if(array_key_exists($f['idprice'], $newdispcostvals)) {
									$tar[$kt]['cost'] = $newdispcostvals[$f['idprice']];
									$tar[$kt]['attrdata'] = $newdispcostattr[$f['idprice']];
									$tar[$kt]['days'] = $newdaysdiff;
									$tar[$kt]['ehours'] = $ehours;
								}
							}
						}
						//
					}
				}
			}
		}
		if($retarray == true) {
			$ret = array();
			$ret['return'] = $tar;
			$ret['days'] = $newdaysdiff;
			return $ret;
		}else {
			return $tar;
		}
	}

/**
 *La función evalua en tabla "chun4_vikrentcar_places" el campo "restriccion" la expresion regular almacenada alli 
 *es comparada con el ultimo caracter de la placa, el dia de la semana (0-6) y el ultimo caracter del dia del mes
 *si la placa es impar y el dia es impar y es un dia diferente a sabado o domingo se filtran los vehiculos con placa impar
 *si la placa es par y el dia es par y es un dia diferente a sabado y domingo se filtran los vehiculos con placa par
 *si el dia es sabado o domingo no se filtra ningun vehiculo y la restriccion de pico y placa no se aplica
 */
function evalRegEx($pplace, $car,$arrtar,$kk, $first=null, $second=null){
	
	$restriccion = vikrentcar :: getPlaceRestriccion($pplace);
	
	
	
	//se detecta los dos tipos de expresiones regulares
	
	
	//$creditValue = vikrentcar :: getCreditoUser(0);
	
	$placa= trim($car[0]['placa']);
	
	
	$lengthPlaca= strlen($placa);
	$lastStringPlaca= mb_substr($placa, $lengthPlaca-1);
	
	$ppickupdate = JRequest :: getString('pickupdate', '', 'request');
	//$lengthDate= strlen($ppickupdate);
	//$lastStringDate= mb_substr($ppickupdate, $lengthDate-1);
	
	
	$time = strtotime($ppickupdate);
	
	$monthDay = date('d',$time);
	$lengthDay= strlen($monthDay);
	$lastStringDay= mb_substr($monthDay, $lengthDay-1);


	if($monthDay%2==0){

		$ispar=true;

	}else{

		$ispar=false;

	}
	
	

	//detecta si regular expresion corresponde a dias de la semana
	if(preg_match('/\(\[1\]\{1\}\[(.+)\]\{1\}\)\|\(\[2\]\{1\}\[(.+)\]\{1\}\)|\(\[3\]\{1\}\[(.+)\]\{1\}\)|\(\[4\]\{1\}\[(.+)\]\{1\}\)|\(\[5\]\{1\}\[(.+)\]\{1\}\)|\(\[6\]\{1\}\[(.+)\]\{1\}\)|\(\[0\]\{1\}\[(.+)\]\{1\}\)/',$restriccion)){
		$weekDay= date('w',$time);
		$stringEval=$weekDay.$lastStringPlaca;
	}else{
		$weekDay= date('w',$time);
		$stringEval= $lastStringPlaca.$weekDay.$lastStringDay;
	}
	
	if(ereg ($restriccion, $stringEval)){
	//if(preg_match($restriccion, $stringEval)){
	//
		$isTimeRestriccion=  self::restriccionTiempo($pplace, $first, $second,  $ispar);

		if($isTimeRestriccion){

			

			return 	$arrtar;
			
		}else{
			
			unset ($arrtar[$kk]);

			
		}
	
		
		
		
	}else{
		
		
		//no hace nada
	}
	
	return 	$arrtar;
	

}
public function restriccionTiempo($pplace, $ppickupdate, $pconsegna, $ispar){

	$fecha= date("Y/m/d", $ppickupdate );

	$fechatimestamp =strtotime($fecha); //

	
    
	$datatimrestjson =vikrentcar::getPlaceRestriccionTime($pplace);

	if($datatimrestjson=='0'){

		return false;
	}

	$arraydata= json_decode($datatimrestjson,true);

	if($ispar){

		$datatimerest= $arraydata[0]['par'];

	}else{

	    $datatimerest= $arraydata[1]['impar'];
	}

	

	$timeinirest =  $fechatimestamp + $datatimerest['ti1'];
	$timefinrest=  $fechatimestamp + $datatimerest['tf1'];

	

	$timeinirest2=  $fechatimestamp + $datatimerest['ti2'];
	$timefinrest2=  $fechatimestamp + $datatimerest['tf2'];


   
   $A= $ppickupdate< $timeinirest && $pconsegna < $timeinirest;
   $B= $ppickupdate> $timefinrest && $pconsegna < $timeinirest2;
   $C = $ppickupdate > $timefinrest2 && $pconsegna> $timefinrest2;
   
	//$newtime= (floor($timestamp/86400)*86400);
	//
	if(($A) ||  ($B)  ||  ($C)){
		//si se cumple esta fuera de los rangos de restrigccion
		//
		

			return true;
			//
			
	}else{

		//si hay restriccion de tiempo

			return false;
			
	}

	

}

function withPicoPlaca($pplace, $car, $ppickupdate, $second=null){
	
	$restriccion = vikrentcar :: getPlaceRestriccion($pplace);
	
	
	
	
	
	$placa= $car;

	$placa = trim($placa);
	
	
	$lengthPlaca= strlen($placa);
	$lastStringPlaca= mb_substr($placa, $lengthPlaca-1);
	
	
	//$lengthDate= strlen($ppickupdate);
	//$lastStringDate= mb_substr($ppickupdate, $lengthDate-1);
	
	
	//$time = strtotime($ppickupdate);
	$time = $ppickupdate;
	
	$monthDay = date('d',$time);

	if($monthDay%2==0){

		$ispar=true;

	}else{

		$ispar=false;

	}
	$lengthDay= strlen($monthDay);
	$lastStringDay= mb_substr($monthDay, $lengthDay-1);
	
	
	

	//detecta si regular expresion corresponde a dias de la semana
	if(preg_match('/\(\[1\]\{1\}\[(.+)\]\{1\}\)\|\(\[2\]\{1\}\[(.+)\]\{1\}\)|\(\[3\]\{1\}\[(.+)\]\{1\}\)|\(\[4\]\{1\}\[(.+)\]\{1\}\)|\(\[5\]\{1\}\[(.+)\]\{1\}\)|\(\[6\]\{1\}\[(.+)\]\{1\}\)|\(\[0\]\{1\}\[(.+)\]\{1\}\)/',$restriccion)){
		$weekDay= date('w',$time);
		$stringEval=$weekDay.$lastStringPlaca;
	}else{
		$weekDay= date('w',$time);
		$stringEval= $lastStringPlaca.$weekDay.$lastStringDay;
	}
	
	if(ereg ($restriccion, $stringEval)){


		$isTimeRestriccion=  self::restriccionTiempo($pplace, $first, $second, $ispar);

		if($isTimeRestriccion){

			return false;
		}else{

			return true;
		}
	
		
		
		
	}else{
		
		
		return false;
	}
	
	
	

}


function TieneRestriccion($pplace, $car, $ppickupdate){
	
	
	$restriccion = vikrentcar :: getPlaceRestriccion($pplace);
	
	
	//se detecta los dos tipos de expresiones regulares
	
	
	//$creditValue = vikrentcar :: getCreditoUser(0);
	
	$placa= $car['placa'];
	
	
	$lengthPlaca= strlen($car['placa']);
	$lastStringPlaca= mb_substr($car['placa'], $lengthPlaca-1);
	
	
	//$lengthDate= strlen($ppickupdate);
	//$lastStringDate= mb_substr($ppickupdate, $lengthDate-1);
	
	
	$time = strtotime($ppickupdate);
	 
	$monthDay = date('d',$time);
	
	$lengthDay= strlen($monthDay);
	$lastStringDay= mb_substr($monthDay, $lengthDay-1);
	
	
	

	//detecta si regular expresion corresponde a dias de la semana
	if(preg_match('/\(\[1\]\{1\}\[(.+)\]\{1\}\)\|\(\[2\]\{1\}\[(.+)\]\{1\}\)|\(\[3\]\{1\}\[(.+)\]\{1\}\)|\(\[4\]\{1\}\[(.+)\]\{1\}\)|\(\[5\]\{1\}\[(.+)\]\{1\}\)|\(\[6\]\{1\}\[(.+)\]\{1\}\)|\(\[0\]\{1\}\[(.+)\]\{1\}\)/',$restriccion)){
		$weekDay= date('w',$time);
		$stringEval=$weekDay.$lastStringPlaca;
	}else{
		$weekDay= date('w',$time);
		
		$stringEval= $lastStringPlaca.$weekDay.$lastStringDay;
	}
	
	if(ereg ($restriccion, $stringEval)){
	//if(preg_match($restriccion, $stringEval)){
		
		return false;
		
	}else{
		
		
		return 	true;
	}
	
	
	

}


}

class vikResizer {

	function proportionalImage($fileimg, $dest, $towidth, $toheight) {
		if (!file_exists($fileimg)) {
			return false;
		}
		if (empty ($towidth) && empty ($toheight)) {
			copy($fileimg, $dest);
			return true;
		}

		list ($owid, $ohei, $type) = getimagesize($fileimg);

		if ($owid > $towidth || $ohei > $toheight) {
			$xscale = $owid / $towidth;
			$yscale = $ohei / $toheight;
			if ($yscale > $xscale) {
				$new_width = round($owid * (1 / $yscale));
				$new_height = round($ohei * (1 / $yscale));
			} else {
				$new_width = round($owid * (1 / $xscale));
				$new_height = round($ohei * (1 / $xscale));
			}

			$imageresized = imagecreatetruecolor($new_width, $new_height);

			switch ($type) {
				case '1' :
					$imagetmp = imagecreatefromgif($fileimg);
					break;
				case '2' :
					$imagetmp = imagecreatefromjpeg($fileimg);
					break;
				default :
					$imagetmp = imagecreatefrompng($fileimg);
					break;
			}

			imagecopyresampled($imageresized, $imagetmp, 0, 0, 0, 0, $new_width, $new_height, $owid, $ohei);

			switch ($type) {
				case '1' :
					imagegif($imageresized, $dest);
					break;
				case '2' :
					imagejpeg($imageresized, $dest);
					break;
				default :
					imagepng($imageresized, $dest);
					break;
			}

			imagedestroy($imageresized);
			return true;
		} else {
			copy($fileimg, $dest);
		}
		return true;
	}

	function bandedImage($fileimg, $dest, $towidth, $toheight, $rgb) {
		if (!file_exists($fileimg)) {
			return false;
		}
		if (empty ($towidth) && empty ($toheight)) {
			copy($fileimg, $dest);
			return true;
		}

		$exp = explode(",", $rgb);
		if (count($exp) == 3) {
			$r = trim($exp[0]);
			$g = trim($exp[1]);
			$b = trim($exp[2]);
		} else {
			$r = 0;
			$g = 0;
			$b = 0;
		}

		list ($owid, $ohei, $type) = getimagesize($fileimg);

		if ($owid > $towidth || $ohei > $toheight) {
			$xscale = $owid / $towidth;
			$yscale = $ohei / $toheight;
			if ($yscale > $xscale) {
				$new_width = round($owid * (1 / $yscale));
				$new_height = round($ohei * (1 / $yscale));
				$ydest = 0;
				$diff = $towidth - $new_width;
				$xdest = ($diff > 0 ? round($diff / 2) : 0);
			} else {
				$new_width = round($owid * (1 / $xscale));
				$new_height = round($ohei * (1 / $xscale));
				$xdest = 0;
				$diff = $toheight - $new_height;
				$ydest = ($diff > 0 ? round($diff / 2) : 0);
			}

			$imageresized = imagecreatetruecolor($towidth, $toheight);

			$bgColor = imagecolorallocate($imageresized, (int) $r, (int) $g, (int) $b);
			imagefill($imageresized, 0, 0, $bgColor);

			switch ($type) {
				case '1' :
					$imagetmp = imagecreatefromgif($fileimg);
					break;
				case '2' :
					$imagetmp = imagecreatefromjpeg($fileimg);
					break;
				default :
					$imagetmp = imagecreatefrompng($fileimg);
					break;
			}

			imagecopyresampled($imageresized, $imagetmp, $xdest, $ydest, 0, 0, $new_width, $new_height, $owid, $ohei);

			switch ($type) {
				case '1' :
					imagegif($imageresized, $dest);
					break;
				case '2' :
					imagejpeg($imageresized, $dest);
					break;
				default :
					imagepng($imageresized, $dest);
					break;
			}

			imagedestroy($imageresized);

			return true;
		} else {
			copy($fileimg, $dest);
		}
		return true;
	}

	function croppedImage($fileimg, $dest, $towidth, $toheight) {
		if (!file_exists($fileimg)) {
			return false;
		}
		if (empty ($towidth) && empty ($toheight)) {
			copy($fileimg, $dest);
			return true;
		}

		list ($owid, $ohei, $type) = getimagesize($fileimg);

		if ($owid <= $ohei) {
			$new_width = $towidth;
			$new_height = ($towidth / $owid) * $ohei;
		} else {
			$new_height = $toheight;
			$new_width = ($new_height / $ohei) * $owid;
		}

		switch ($type) {
			case '1' :
				$img_src = imagecreatefromgif($fileimg);
				$img_dest = imagecreate($new_width, $new_height);
				break;
			case '2' :
				$img_src = imagecreatefromjpeg($fileimg);
				$img_dest = imagecreatetruecolor($new_width, $new_height);
				break;
			default :
				$img_src = imagecreatefrompng($fileimg);
				$img_dest = imagecreatetruecolor($new_width, $new_height);
				break;
		}

		imagecopyresampled($img_dest, $img_src, 0, 0, 0, 0, $new_width, $new_height, $owid, $ohei);

		switch ($type) {
			case '1' :
				$cropped = imagecreate($towidth, $toheight);
				break;
			case '2' :
				$cropped = imagecreatetruecolor($towidth, $toheight);
				break;
			default :
				$cropped = imagecreatetruecolor($towidth, $toheight);
				break;
		}

		imagecopy($cropped, $img_dest, 0, 0, 0, 0, $owid, $ohei);

		switch ($type) {
			case '1' :
				imagegif($cropped, $dest);
				break;
			case '2' :
				imagejpeg($cropped, $dest);
				break;
			default :
				imagepng($cropped, $dest);
				break;
		}

		imagedestroy($img_dest);
		imagedestroy($cropped);

		return true;
	}

}

function sayMonth($idm) {
	switch ($idm) {
		case '12' :
			$ret = JText :: _('VRMONTHTWELVE');
			break;
		case '11' :
			$ret = JText :: _('VRMONTHELEVEN');
			break;
		case '10' :
			$ret = JText :: _('VRMONTHTEN');
			break;
		case '9' :
			$ret = JText :: _('VRMONTHNINE');
			break;
		case '8' :
			$ret = JText :: _('VRMONTHEIGHT');
			break;
		case '7' :
			$ret = JText :: _('VRMONTHSEVEN');
			break;
		case '6' :
			$ret = JText :: _('VRMONTHSIX');
			break;
		case '5' :
			$ret = JText :: _('VRMONTHFIVE');
			break;
		case '4' :
			$ret = JText :: _('VRMONTHFOUR');
			break;
		case '3' :
			$ret = JText :: _('VRMONTHTHREE');
			break;
		case '2' :
			$ret = JText :: _('VRMONTHTWO');
			break;
		default :
			$ret = JText :: _('VRMONTHONE');
			break;
	}
	return $ret;
}

function totElements($arr) {
	$n = 0;
	if (is_array($arr)) {
		foreach ($arr as $a) {
			if (!empty ($a)) {
				$n++;
			}
		}
		return $n;
	}
	return false;
}

function encryptCookie($str) {
	for ($i = 0; $i < 5; $i++) {
		$str = strrev(base64_encode($str));
	}
	return $str;
}

function decryptCookie($str) {
	for ($i = 0; $i < 5; $i++) {
		$str = base64_decode(strrev($str));
	}
	return $str;
}

function read($str) {
	for ($i = 0; $i < strlen($str); $i += 2)
		$var .= chr(hexdec(substr($str, $i, 2)));
	return $var;
}

function checkComp($lf, $h, $n) {
	$a = $lf[0];
	$b = $lf[1];
	for ($i = 0; $i < 5; $i++) {
		$a = base64_decode(strrev($a));
		$b = base64_decode(strrev($b));
	}
	if ($a == $h || $b == $h || $a == $n || $b == $n) {
		return true;
	} else {
		$a = str_replace('www.', "", $a);
		$b = str_replace('www.', "", $b);
		if ((!empty ($a) && (preg_match("/" . $a . "/i", $h) || preg_match("/" . $a . "/i", $n))) || (!empty ($b) && (preg_match("/" . $b . "/i", $h) || preg_match("/" . $b . "/i", $n)))) {
			return true;
		}
	}
	return false;
}

define('CREATIVIKAPP', 'com_vikrentcar');

class CreativikDotIt {
	function CreativikDotIt() {
		$this->headers = array (
			"Referer" => "",
			"User-Agent" => "CreativikDotIt/1.0",
			"Connection" => "close"
		);
		$this->version = "1.1";
		$this->ctout = 15;
		$this->f_redha = false;
	}

	function exeqer($url) {
		$rcodes = array (
			301,
			302,
			303,
			307
		);
		$rmeth = array (
			'GET',
			'HEAD'
		);
		$rres = false;
		$this->fd_redhad = false;
		$ppred = array ();
		do {
			$rres = $this->sendout($url);
			$url = false;
			if ($this->f_redha && in_array($this->edocser, $rcodes)) {
				if (($this->edocser == 303) || in_array($this->method, $rmeth)) {
					$url = $this->resphh['Location'];
				}
			}
			if ($url && strlen($url)) {
				if (isset ($ppred[$url])) {
					$this->rore = "tceriderpool";
					$rres = false;
					break;
				}
				if (is_numeric($this->f_redha) && (count($ppred) > $this->f_redha)) {
					$this->rore = "tceriderynamoot";
					$rres = false;
					break;
				}
				$ppred[$url] = true;
			}
		} while ($url && strlen($url));
		$rep_qer_daeh = array (
			'Host',
			'Content-Length'
		);
		foreach ($rep_qer_daeh as $k => $v)
			unset ($this->headers[$v]);
		if (count($ppred) > 1)
			$this->fd_redhad = array_keys($ppred);
		return $rres;
	}

	function dliubh() {
		$daeh = "";
		foreach ($this->headers as $name => $value) {
			$value = trim($value);
			if (empty ($value))
				continue;
			$daeh .= "{$name}: $value\r\n";
		}
		$daeh .= "\r\n";
		return $daeh;
	}

	function sendout($url) {
		$time_request_start = time();
		$urldata = parse_url($url);
		if (!$urldata["port"])
			$urldata["port"] = ($urldata["scheme"] == "https") ? 443 : 80;
		if (!$urldata["path"])
			$urldata["path"] = '/';
		if ($this->version > "1.0")
			$this->headers["Host"] = $urldata["host"];
		unset ($this->headers['Authorization']);
		if (!empty ($urldata["query"]))
			$urldata["path"] .= "?" . $urldata["query"];
		$request = $this->method . " " . $urldata["path"] . " HTTP/" . $this->version . "\r\n";
		$request .= $this->dliubh();
		$this->tise = "";
		$hostname = $urldata['host'];
		$time_connect_start = time();
		$fp = @ fsockopen($hostname, $urldata["port"], $errno, $errstr, $this->ctout);
		$connect_time = time() - $time_connect_start;
		if ($fp) {
			stream_set_timeout($fp, 3);
			fputs($fp, $request);
			$meta = stream_get_meta_data($fp);
			if ($meta['timed_out']) {
				$this->rore = "sdnoceseerhtfotuoemitetirwtekcosdedeecxe";
				return false;
			}
			$cerdaeh = false;
			$data_length = false;
			$chunked = false;
			while (!feof($fp)) {
				if ($data_length > 0) {
					$line = fread($fp, $data_length);
					$data_length -= strlen($line);
				} else {
					$line = fgets($fp, 10240);
					if ($chunked) {
						$line = trim($line);
						if (!strlen($line))
							continue;
						list ($data_length,) = explode(';', $line);
						$data_length = (int) hexdec(trim($data_length));
						if ($data_length == 0) {
							break;
						}
						continue;
					}
				}
				$this->tise .= $line;
				if ((!$cerdaeh) && (trim($line) == "")) {
					$cerdaeh = true;
					if (preg_match('/\nContent-Length: ([0-9]+)/i', $this->tise, $matches)) {
						$data_length = (int) $matches[1];
					}
					if (preg_match("/\nTransfer-Encoding: chunked/i", $this->tise, $matches)) {
						$chunked = true;
					}
				}
				$meta = stream_get_meta_data($fp);
				if ($meta['timed_out']) {
					$this->rore = "sceseerhttuoemitdaertekcos";
					return false;
				}
				if (time() - $time_request_start > 5) {
					$this->rore = "maxtransfertimefivesecs";
					return false;
					break;
				}
			}
			fclose($fp);
		} else {
			$this->rore = $urldata['scheme'] . " otdeliafnoitcennoc " . $hostname . " trop " . $urldata['port'];
			return false;
		}
		do {
			$neldaeh = strpos($this->tise, "\r\n\r\n");
			$serp_daeh = explode("\r\n", substr($this->tise, 0, $neldaeh));
			$pthats = trim(array_shift($serp_daeh));
			foreach ($serp_daeh as $line) {
				list ($k, $v) = explode(":", $line, 2);
				$this->resphh[trim($k)] = trim($v);
			}
			$this->tise = substr($this->tise, $neldaeh +4);
			if (!preg_match("/^HTTP\/([0-9\.]+) ([0-9]+) (.*?)$/", $pthats, $matches)) {
				$matches = array (
					"",
					$this->version,
					0,
					"HTTP request error"
				);
			}
			list (, $pserver, $this->edocser, $this->txet) = $matches;
		} while (($this->edocser == 100) && ($neldaeh));
		$ok = ($this->edocser == 200);
		return $ok;
	}

	function ksa($url) {
		$this->method = "GET";
		return $this->exeqer($url);
	}

}

function validEmail($email) {
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex) {
		$isValid = false;
	} else {
		$domain = substr($email, $atIndex +1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64) {
			// local part length exceeded
			$isValid = false;
		} else
			if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else
				if ($local[0] == '.' || $local[$localLen -1] == '.') {
					// local part starts or ends with '.'
					$isValid = false;
				} else
					if (preg_match('/\\.\\./', $local)) {
						// local part has two consecutive dots
						$isValid = false;
					} else
						if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
							// character not valid in domain part
							$isValid = false;
						} else
							if (preg_match('/\\.\\./', $domain)) {
								// domain part has two consecutive dots
								$isValid = false;
							} else
								if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
									// character not valid in local part unless 
									// local part is quoted
									if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
										$isValid = false;
									}
								}
		if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
			// domain not found in DNS
			$isValid = false;
		}
	}
	return $isValid;
}

function checkCodiceFiscale($cf) {
	if ($cf == '')
		return false;
	if (strlen($cf) != 16)
		return false;
	$cf = strtoupper(str_replace(" ", "", $cf));
	if (!ereg("^[A-Z0-9]+$", $cf)) {
		return false;
	}
	$s = 0;
	for ($i = 1; $i <= 13; $i += 2) {
		$c = $cf[$i];
		if ('0' <= $c && $c <= '9')
			$s += ord($c) - ord('0');
		else
			$s += ord($c) - ord('A');
	}
	for ($i = 0; $i <= 14; $i += 2) {
		$c = $cf[$i];
		switch ($c) {
			case '0' :
				$s += 1;
				break;
			case '1' :
				$s += 0;
				break;
			case '2' :
				$s += 5;
				break;
			case '3' :
				$s += 7;
				break;
			case '4' :
				$s += 9;
				break;
			case '5' :
				$s += 13;
				break;
			case '6' :
				$s += 15;
				break;
			case '7' :
				$s += 17;
				break;
			case '8' :
				$s += 19;
				break;
			case '9' :
				$s += 21;
				break;
			case 'A' :
				$s += 1;
				break;
			case 'B' :
				$s += 0;
				break;
			case 'C' :
				$s += 5;
				break;
			case 'D' :
				$s += 7;
				break;
			case 'E' :
				$s += 9;
				break;
			case 'F' :
				$s += 13;
				break;
			case 'G' :
				$s += 15;
				break;
			case 'H' :
				$s += 17;
				break;
			case 'I' :
				$s += 19;
				break;
			case 'J' :
				$s += 21;
				break;
			case 'K' :
				$s += 2;
				break;
			case 'L' :
				$s += 4;
				break;
			case 'M' :
				$s += 18;
				break;
			case 'N' :
				$s += 20;
				break;
			case 'O' :
				$s += 11;
				break;
			case 'P' :
				$s += 3;
				break;
			case 'Q' :
				$s += 6;
				break;
			case 'R' :
				$s += 8;
				break;
			case 'S' :
				$s += 12;
				break;
			case 'T' :
				$s += 14;
				break;
			case 'U' :
				$s += 16;
				break;
			case 'V' :
				$s += 10;
				break;
			case 'W' :
				$s += 22;
				break;
			case 'X' :
				$s += 25;
				break;
			case 'Y' :
				$s += 24;
				break;
			case 'Z' :
				$s += 23;
				break;
		}
	}
	if (chr($s % 26 + ord('A')) != $cf[15])
		return false;
	return true;
}

function secureString($string) {
	$search = array (
		'/<\?((?!\?>).)*\?>/s'
	);
	return preg_replace($search, '', $string);
}

function cleanString4Db($str) {
	$var = $str;
	if (get_magic_quotes_gpc()) {
		$var = stripslashes($str);
	}
	$var = str_replace("'", "`", $var);
	return secureString($var);
}

function caniWrite($path) {
	if ($path {
		strlen($path) - 1 }
	== '/') // ricorsivo return a temporary file path
	return caniWrite($path . uniqid(mt_rand()) . '.tmp');
else
	if (is_dir($path))
		return caniWrite($path . '/' . uniqid(mt_rand()) . '.tmp');
// check tmp file for read/write capabilities
$rm = file_exists($path);
$f = @ fopen($path, 'a');
if ($f === false)
	return false;
fclose($f);
if (!$rm)
	unlink($path);
return true;
}

function realInt($num) {
	for ($i = 0; $i < strlen($num); $i++) {
		if (!ctype_digit($num {
			$i })) {
			return false;
		}
	}
	return true;
}

function realDecimal($num) {
	for ($i = 0; $i < strlen($num); $i++) {
		if (!ctype_digit($num {
			$i }) && $num {
			$i }
		!= "." && $num {
			$i }
		!= ",") {
			return false;
		}
	}
	return true;
}





?>
