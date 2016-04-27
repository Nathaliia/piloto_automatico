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

$locations = $this->locations;
$alllocations = $this->alllocations;

if(count($locations) > 0) {
	$lats = array();
	$lngs = array();
	foreach($locations as $l) {
		$lats[] = $l['lat'];
		$lngs[] = $l['lng'];
	}
	$document = & JFactory :: getDocument();
	if(vikrentcar::loadJquery()) {
		$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery-1.8.2.min.js');
	}
	$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
	?>
	<script language="JavaScript" type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function(){
		var map = new google.maps.Map(document.getElementById("vrcmapcanvas"), {mapTypeId: google.maps.MapTypeId.ROADMAP});
		<?php
		foreach($locations as $l) {
			?>
		var marker<?php echo $l['id']; ?> = new google.maps.Marker({
			position: new google.maps.LatLng(<?php echo $l['lat']; ?>, <?php echo $l['lng']; ?>),
			map: map,
			title: '<?php echo addslashes($l['name']); ?>'
		});	
			<?php
			if(strlen(trim(strip_tags($l['descr']))) > 0) {
				?>	
		var tooltip<?php echo $l['id']; ?> = '<div class="vrcgmapinfow"><h3><?php echo addslashes($l['name']); ?></h3><div class="vrcgmapinfowdescr"><?php echo addslashes(preg_replace('/\s\s+/', ' ', $l['descr'])); ?></div></div>';
		var infowindow<?php echo $l['id']; ?> = new google.maps.InfoWindow({
			content: tooltip<?php echo $l['id']; ?>
		});
		google.maps.event.addListener(marker<?php echo $l['id']; ?>, 'click', function() {
			infowindow<?php echo $l['id']; ?>.open(map, marker<?php echo $l['id']; ?>);
		});
				<?php
			}
		}
		?>
		
		var lat_min = <?php echo min($lats); ?>;
		var lat_max = <?php echo max($lats); ?>;
		var lng_min = <?php echo min($lngs); ?>;
		var lng_max = <?php echo max($lngs); ?>;
		
		map.setCenter(new google.maps.LatLng( ((lat_max + lat_min) / 2.0), ((lng_max + lng_min) / 2.0) ));
		map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(lat_min, lng_min), new google.maps.LatLng(lat_max, lng_max)));
		
	});
	</script>
	<div id="vrcmapcanvas" style="width: 99%; height: 350px;"></div>
	<br clear="all"/>
	<?php
	foreach($alllocations as $loc) {
		if(strlen($loc['opentime']) > 0) {
			$parts = explode("-", $loc['opentime']);
			$opent=vikrentcar::getHoursMinutes($parts[0]);
			$closet=vikrentcar::getHoursMinutes($parts[1]);
			$tsopen = mktime($opent[0], $opent[1], 0, 1, 1, 2012);
			$tsclose = mktime($closet[0], $closet[1], 0, 1, 1, 2012);
			$stropeningtime = date('H:i', $tsopen).' - '.date('H:i', $tsclose);
		}else {
			$stropeningtime = "";
		}
		?>
		<div class="vrclocationbox">
			<h3 class="vrcloclistlocname"><?php echo $loc['name']; ?></h3>
		<?php
		if(strlen($stropeningtime) > 0) {
			?>
			<div class="vrcloclistloctimebox">
			<?php echo JText::_('VRCLOCLISTLOCOPENTIME'); ?>
			<span class="vrcloclistloctimehour"><?php echo $stropeningtime; ?></span>
			</div>
			<?php
		}
		?>
			<div class="vrcloclistlocdescr"><?php echo $loc['descr']; ?></div>
		</div>
		<?php
	}
}

?>