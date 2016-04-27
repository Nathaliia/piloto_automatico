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

$car = $this->car;
$busy = $this->busy;

$currencysymb = vikrentcar :: getCurrencySymb();
$showpartlyres=vikrentcar::showPartlyReserved();

echo vikrentcar :: getFullFrontTitle();

if (!empty ($car['img'])) {
?>
<img src="<?php echo JURI::root(); ?>administrator/components/com_vikrentcar/resources/<?php echo $car['img']; ?>" class="vrclistimg"/>
<?php

}
?>
<span class="vrclistcarname"><?php echo $car['name']; ?></span>
<span class="vrclistcarcat"><?php echo vikrentcar::sayCategory($car['idcat']); ?></span>
<?php

if ($car['cost'] > 0) {
?>
<span class="vrcliststartfrom"><?php echo JText::_('VRCLISTSFROM'); ?></span>
<span class="car_cost"><?php echo $currencysymb; ?> <?php echo $car['cost']; ?></span>
<?php

}
$pmonth = JRequest :: getInt('month', '', 'request');
$arr=getdate();
$mon=$arr['mon'];
$realmon=($mon < 10 ? "0".$mon : $mon);
$year=$arr['year'];
$day=$realmon."/01/".$year;
$dayts=strtotime($day);
$validmonth=false;
if($pmonth > 0 && $pmonth >= $dayts) {
	$validmonth=true;
}
$moptions="";
for($i=0; $i < 12; $i++) {
	$moptions.="<option value=\"".$dayts."\"".($validmonth && $pmonth == $dayts ? " selected=\"selected\"" : "").">".sayMonth($arr['mon'])." ".$arr['year']."</option>\n";
	$next=$arr['mon'] + 1;
	$dayts=mktime(0, 0, 0, ($next < 10 ? "0".$next : $next), 01, $arr['year']);
	$arr=getdate($dayts);
}

?>

<div class="vrcdetsep"></div>

<form action="<?php echo JRoute::_('index.php?option=com_vikrentcar&view=cardetails&carid='.$car['id']); ?>" method="post" name="vrcmonths">
<select name="month" onchange="javascript: document.vrcmonths.submit();" class="vrcselectm"><?php echo $moptions; ?></select>
</form>

<div class="vrcdetsep"></div>  

<div class="vrclegendediv">

	<span class="vrclegenda"><div class="vrclegfree">&nbsp;</div> <?php echo JText::_('VRLEGFREE'); ?></span>
	<?php
	if($showpartlyres) {
	?>
	<span class="vrclegenda"><div class="vrclegwarning">&nbsp;</div> <?php echo JText::_('VRLEGWARNING'); ?></span>
	<?php
	}
	?>
	<span class="vrclegenda"><div class="vrclegbusy">&nbsp;</div> <?php echo JText::_('VRLEGBUSY'); ?></span>
	
</div>


<div class="vrcdetsep"></div>

<table class="vrccalcontainer"><tr>
<?php
$check=false;
if(@is_array($busy)) {
	$check=true;
}
if($validmonth) {
	$arr=getdate($pmonth);
	$mon=$arr['mon'];
	$realmon=($mon < 10 ? "0".$mon : $mon);
	$year=$arr['year'];
	$day=$realmon."/01/".$year;
	$dayts=strtotime($day);
	$newarr=getdate($dayts);
}else {
	$arr=getdate();
	$mon=$arr['mon'];
	$realmon=($mon < 10 ? "0".$mon : $mon);
	$year=$arr['year'];
	$day=$realmon."/01/".$year;
	$dayts=strtotime($day);
	$newarr=getdate($dayts);
}

for($jj = 1; $jj <= vikrentcar::numCalendars(); $jj++) {
	echo "<td valign=\"top\">";
	$cal="";
	?>
	<table class="vrccal">
	<tr><td colspan="7" align="center"><strong><?php echo sayMonth($newarr['mon'])." ".$newarr['year']; ?></strong></td></tr>
	<tr class="vrccaldays"><td><?php echo JText::_('VRSUN'); ?></td><td><?php echo JText::_('VRMON'); ?></td><td><?php echo JText::_('VRTUE'); ?></td><td><?php echo JText::_('VRWED'); ?></td><td><?php echo JText::_('VRTHU'); ?></td><td><?php echo JText::_('VRFRI'); ?></td><td><?php echo JText::_('VRSAT'); ?></td></tr>
	<tr>
	<?php
	for($i=0; $i < $newarr['wday']; $i++){
		$cal.="<td align=\"center\">&nbsp;</td>";
	}
	while ($newarr['mon']==$mon) {
		$dclass="vrctdfree";
		$dalt="";
		$bid="";
		if ($check) {
			$totfound=0;
			foreach($busy as $b){
				$tmpone=getdate($b['ritiro']);
				$rit=($tmpone['mon'] < 10 ? "0".$tmpone['mon'] : $tmpone['mon'])."/".($tmpone['mday'] < 10 ? "0".$tmpone['mday'] : $tmpone['mday'])."/".$tmpone['year'];
				$ritts=strtotime($rit);
				$tmptwo=getdate($b['consegna']);
				$con=($tmptwo['mon'] < 10 ? "0".$tmptwo['mon'] : $tmptwo['mon'])."/".($tmptwo['mday'] < 10 ? "0".$tmptwo['mday'] : $tmptwo['mday'])."/".$tmptwo['year'];
				$conts=strtotime($con);
				if ($newarr[0]>=$ritts && $newarr[0]<=$conts) {
					$totfound++;
				}
			}
			if($totfound >= $car['units']) {
				$dclass="vrctdbusy";
			}elseif($totfound > 0) {
				if($showpartlyres) {
					$dclass="vrctdwarning";
				}
			}
		}
		$useday=($newarr['mday'] < 10 ? "0".$newarr['mday'] : $newarr['mday']);
		if($totfound == 1) {
			$cal.="<td align=\"center\" class=\"".$dclass."\">".$useday."</td>\n";
		}elseif($totfound > 1) {
			$cal.="<td align=\"center\" class=\"".$dclass."\">".$useday."</td>\n";
		}else {
			$cal.="<td align=\"center\" class=\"".$dclass."\">".$useday."</td>\n";
		}
		$cal.=($newarr['wday']==6 ? "</tr>\n<tr>" : "");
		$next=$newarr['mday'] + 1;
		$dayts=mktime(0, 0, 0, ($newarr['mon'] < 10 ? "0".$newarr['mon'] : $newarr['mon']), ($next < 10 ? "0".$next : $next), $newarr['year']);
		$newarr=getdate($dayts);
	}
	
	if($newarr['wday'] > 0) {
		for($i=$newarr['wday']; $i <= 6; $i++){
			$cal.="<td align=\"center\">&nbsp;</td>";
		}
	}
	
	echo $cal;
	?>
	</tr>
	</table>
	<?php
	echo "</td>";
	if ($mon==12) {
		$mon=1;
		$year+=1;
		$dayts=mktime(0, 0, 0, ($mon < 10 ? "0".$mon : $mon), 01, $year);
	}else {
		$mon+=1;
		$dayts=mktime(0, 0, 0, ($mon < 10 ? "0".$mon : $mon), 01, $year);
	}
	$newarr=getdate($dayts);
	
	if (($jj % 3)==0) {
		echo "</tr>\n<tr>";
	}
}

?>
</tr></table>

<div class="vrcdetsep"></div>

<p><strong><?php echo JText::_('VRCSELECTPDDATES'); ?></strong></p>

<?php

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
	$pitemid = JRequest :: getInt('Itemid', '', 'request');
	$vrcdateformat = vikrentcar::getDateFormat();
	if ($vrcdateformat == "%d/%m/%Y") {
		$df = 'd/m/Y';
	} else {
		$df = 'Y/m/d';
	}
	$coordsplaces = array();
	$selform = "<div class=\"vrcdivsearch\"><form action=\"".JRoute::_('index.php?option=com_vikrentcar')."\" method=\"get\"><table class=\"vrccalform\">\n";
	$selform .= "<input type=\"hidden\" name=\"option\" value=\"com_vikrentcar\"/>\n";
	$selform .= "<input type=\"hidden\" name=\"task\" value=\"search\"/>\n";
	$selform .= "<input type=\"hidden\" name=\"cardetail\" value=\"".$car['id']."\"/>\n";
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
	$selform .= "<tr><td></td><td><input type=\"submit\" name=\"search\" value=\"" . JText::_('VRCBOOKTHISCAR') . "\" class=\"vrcdetbooksubmit\"/></td></tr>\n";
	$selform .= "</table>\n";
	$selform .= (!empty ($pitemid) ? "<input type=\"hidden\" name=\"Itemid\" value=\"" . $pitemid . "\"/>" : "") . "</form></div>";
	//locations on google map
	if(count($coordsplaces) > 0) {
		JHTML::_('behavior.modal');
		$selform = '<div class="vrclocationsbox"><div class="vrclocationsmapdiv"><a href="'.JURI::root().'index.php?option=com_vikrentcar&view=locationsmap&tmpl=component" rel="{handler: \'iframe\', size: {x: 750, y: 600}}" class="modal" target="_blank">'.JText::_('VRCLOCATIONSMAP').'</a></div></div>'.$selform;
	}
	//
	echo $selform;
} else {
	echo vikrentcar :: getDisabledRentMsg();
}

?>