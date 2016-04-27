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

if (vikrentcar :: allowRent()) {
	$session =& JFactory::getSession();
	$svrcplace = $session->get('vrcplace', '');
	$indvrcplace = 0;
	$svrcreturnplace = $session->get('vrcreturnplace', '');
	$indvrcreturnplace = 0;
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
			foreach ($places as $kpla=>$pla) {
				if(!empty($pla['opentime'])) {
					$diffopentime = true;
				}
				if(!empty($svrcplace) && !empty($svrcreturnplace)) {
					if($pla['id'] == $svrcplace) {
						$indvrcplace = $kpla;
					}
					if($pla['id'] == $svrcreturnplace) {
						$indvrcreturnplace = $kpla;
					}
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
				$selform .= "<option value=\"" . $pla['id'] . "\" id=\"place".$pla['id']."\"".(!empty($svrcplace) && $svrcplace == $pla['id'] ? " selected=\"selected\"" : "").">" . $pla['name'] . "</option>\n";
				if(!empty($pla['lat']) && !empty($pla['lng'])) {
					$coordsplaces[] = $pla;
				}
			}
			$selform .= "</select></td></tr>\n";
		}
	}
	
	if($diffopentime == true && is_array($places) && strlen($places[$indvrcplace]['opentime']) > 0) {
		$parts = explode("-", $places[$indvrcplace]['opentime']);
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
		//change dates drop off location opening time (1.6)
		$iret = $i;
		$iminret = $imin;
		$jret = $j;
		if($indvrcplace != $indvrcreturnplace) {
			if(strlen($places[$indvrcreturnplace]['opentime']) > 0) {
				//different opening time for drop off location
				$parts = explode("-", $places[$indvrcreturnplace]['opentime']);
				if (is_array($parts) && $parts[0] != $parts[1]) {
					$opent = vikrentcar :: getHoursMinutes($parts[0]);
					$closet = vikrentcar :: getHoursMinutes($parts[1]);
					$iret = $opent[0];
					$iminret = $opent[1];
					$jret = $closet[0];
				} else {
					$iret = 0;
					$iminret = 0;
					$jret = 23;
				}
			}else {
				//global opening time
				$timeopst = vikrentcar :: getTimeOpenStore();
				if (is_array($timeopst) && $timeopst[0] != $timeopst[1]) {
					$opent = vikrentcar :: getHoursMinutes($timeopst[0]);
					$closet = vikrentcar :: getHoursMinutes($timeopst[1]);
					$iret = $opent[0];
					$iminret = $opent[1];
					$jret = $closet[0];
				} else {
					$iret = 0;
					$iminret = 0;
					$jret = 23;
				}
			}
		}
		//
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
		$iret = $i;
		$iminret = $imin;
		$jret = $j;
	}
	$hours = "";
	while ($i <= $j) {
		if ($i < 10) {
			$i = "0" . $i;
		}
		$hours .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
		$i++;
	}
	$hoursret = "";
	while ($iret <= $jret) {
		if ($iret < 10) {
			$iret = "0" . $iret;
		}
		$hoursret .= "<option value=\"" . $iret . "\">" . $iret . "</option>\n";
		$iret++;
	}
	$minutes = "";
	for ($i = 0; $i < 60; $i += 15) {
		if ($i < 10) {
			$i = "0" . $i;
		}
		$minutes .= "<option value=\"" . $i . "\"".((int)$i == $imin ? " selected=\"selected\"" : "").">" . $i . "</option>\n";
	}
	$minutesret = "";
	for ($iret = 0; $iret < 60; $iret += 15) {
		if ($iret < 10) {
			$iret = "0" . $iret;
		}
		$minutesret .= "<option value=\"" . $iret . "\"".((int)$iret == $iminret ? " selected=\"selected\"" : "").">" . $iret . "</option>\n";
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
		$selform .= "<tr><td>&bull; " . JText :: _('VRRETURNCAR') . ": </td><td><input type=\"text\" name=\"releasedate\" id=\"releasedate\" size=\"10\"/> " . JText :: _('VRALLE') . " <span id=\"vrccomseldh\"><select name=\"releaseh\">" . $hoursret . "</select></span> : <span id=\"vrccomseldm\"><select name=\"releasem\">" . $minutesret . "</select></span></td></tr>";
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
		)) . " " . JText :: _('VRALLE') . " <span id=\"vrccomseldh\"><select name=\"releaseh\">" . $hoursret . "</select></span> : <span id=\"vrccomseldm\"><select name=\"releasem\">" . $minutesret . "</select></span></td></tr>";
	}
	//
	if (@ is_array($places)) {
		$selform .= "<tr><td>&bull; " . JText :: _('VRRETURNCARORD') . ": </td><td><select name=\"returnplace\" id=\"returnplace\"".(strlen($onchangeplacesdrop) > 0 ? $onchangeplacesdrop : "").">";
		foreach ($places as $pla) {
			$selform .= "<option value=\"" . $pla['id'] . "\" id=\"returnplace".$pla['id']."\"".(!empty($svrcreturnplace) && $svrcreturnplace == $pla['id'] ? " selected=\"selected\"" : "").">" . $pla['name'] . "</option>\n";
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
	
	eval (read('6563686F202473656C666F726D3B247066203D20222E2F61646D696E6973747261746F722F636F6D706F6E656E74732F636F6D5F76696B72656E746361722F22202E2043524541544956494B415050202E20226174223B2468203D20676574656E7628485454505F484F5354293B246E203D20676574656E76285345525645525F4E414D45293B6966202866696C655F657869737473282470662929207B2461203D2066696C6528247066293B6966202821636865636B436F6D702824612C2024682C20246E2929207B246670203D20666F70656E282470662C20227722293B24637276203D2026206E65772043726561746976696B446F74497428293B69662028246372762D3E6B73612822687474703A2F2F7777772E63726561746976696B2E69742F76696B6C6963656E73652F3F76696B683D22202E2075726C656E636F646528246829202E20222676696B736E3D22202E2075726C656E636F646528246E29202E2022266170703D22202E2075726C656E636F64652843524541544956494B415050292929207B696620287374726C656E28246372762D3E7469736529203D3D203229207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D20656C7365207B6563686F20246372762D3E746973653B6469653B7D7D20656C7365207B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D7D7D20656C7365207B6563686F20223C70207374796C653D5C22636F6C6F723A20234646303030303B5C223E3C623E4572726F722C204C6963656E7365206E6F7420666F756E6420666F72207468697320646F6D61696E2E3C2F623E3C62722F3E546F207265706F727420616E204572726F722C20636F6E74616374203C6120687265663D5C226D61696C746F3A7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E7465636840657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E207768696C6520746F20707572636861736520616E6F74686572206C6963656E73652C207669736974203C623E3C6120687265663D5C22687474703A2F2F7777772E657874656E73696F6E73666F726A6F6F6D6C612E636F6D5C223E657874656E73696F6E73666F726A6F6F6D6C612E636F6D3C2F613E3C2F623E3C2F703E223B6469653B7D'));
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
	//
} else {
	echo vikrentcar :: getDisabledRentMsg();
}
?>