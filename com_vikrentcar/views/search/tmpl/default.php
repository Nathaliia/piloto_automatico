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

$res=$this->res;
$days=$this->days;
$pickup=$this->pickup;
$release=$this->release;
$place=$this->place;
$navig=$this->navig;
$pattr=$this->pattr;
$city_ini=$this->city_ini;
$add_ini=$this->add_ini;
$city_fin=$this->city_fin;
$add_fin=$this->add_fin;
$pidprice=$this->pidprice;
$add_vuelo=$this->add_vuelo;




$numpasangers=$this->numpasangers;

$lang = JFactory::getLanguage();

$code_lang= $lang->getTag();

$tooltipsearch= JText::_('VRSEARCHINPUT');

$currencysymb = vikrentcar :: getCurrencySymb();
?>
		<p class="vrcarsfound"><?php echo JText::_('VRCARSFND'); ?>: <?php echo count($res); ?></p>
		<?php

$returnplace = JRequest :: getInt('returnplace', '', 'request');
$pitemid = JRequest :: getInt('Itemid', '', 'request');

?>
<table id="results" class="display" cellspacing="0" width="100%">
	<thead>
            <tr>
                <th><?php echo JText::_('VRTITLERESULTS'); ?></th>
            </tr>
        </thead>
     <tbody>
<?php
foreach ($res as $k => $r) {

	?>

	 <tr>

	 	<td>


	<?php
	$getcar = vikrentcar :: getCarInfo($k);

	echo '<div class="placa style=\'display:none\'">'.$getcar['placa'].'</div>';
	//$carats = vikrentcar :: getCarCarat($getcar['idcarat']);
	$carats = vikrentcar :: getCarCaratOriz2($getcar['idcarat']);



	$namecategoria= explode("/",vikrentcar::sayCategory($getcar['idcat']));

	 if($code_lang=="es-ES"){

	 	$namecategoria=$namecategoria[0];

	 }else{

	 	$namecategoria=$namecategoria[1];
	 }
	
?>
			<div class="car_result">
			<form action="<?php echo JRoute::_('index.php?option=com_vikrentcar'); ?>" method="get">
			<input type="hidden" name="option" value="com_vikrentcar"/>
  			<input type="hidden" name="caropt" value="<?php echo $k; ?>"/>
  			<input type="hidden" name="days" value="<?php echo $days; ?>"/>
  			<input type="hidden" name="pickup" value="<?php echo $pickup; ?>"/>
  			<input type="hidden" name="release" value="<?php echo $release; ?>"/>
  			<input type="hidden" name="place" value="<?php echo $place; ?>"/>
  			<input type="hidden" name="returnplace" value="<?php echo $returnplace; ?>"/>
  			<input type="hidden" name="task" value="showprc"/>
  			<input type="hidden" name="pattr" value="<?php echo $pattr; ?>"/>
  			<input type="hidden" name="pidprice" value="<?php echo $pidprice; ?>"/>
  			<input type="hidden" name="city_ini" value="<?php echo $city_ini; ?>"/>
  			<input type="hidden" name="add_ini" value="<?php echo $add_ini; ?>"/>
  			<input type="hidden" name="city_fin" value="<?php echo $city_fin; ?>"/>
  			<input type="hidden" name="add_fin" value="<?php echo $add_fin; ?>"/>
  			<input type="hidden" name="add_vuelo" value="<?php echo $add_vuelo; ?>"/>
  			<input type="hidden" name="numpasangers" value="<?php echo $numpasangers; ?>"/>
  			<input type="hidden" name="precioinicial" value="<?php echo vikrentcar::sayCostPlusIva($r[0]['cost'], $r[0]['idprice']); ?>"/>
			<div id=""class="vrcstablecar">
				
			<div>
			<div class="vrcrowcname ordenar"><?php echo $namecategoria; ?>  <?php echo $getcar['name']; ?></div>
			<div  width="130px" valign="top" align="left"><img class="imgresult ordenar" alt="<?php echo $getcar['name']; ?>" src="<?php echo JURI::root(); ?>administrator/components/com_vikrentcar/resources/<?php echo $getcar['img']; ?>"/></div>
			<div valign="top" align="left" width="80%">
			<div>
			
			
			<div class="vrcrowcdescr ordenar"><?php echo (strlen(strip_tags($getcar['info'])) > 200 ? substr(strip_tags($getcar['info']), 0, 200).' ...' : $getcar['info']); ?></div>
			<div class="vrcsrowprice ordenar"><div class="vrcsrowpricediv"><span class="vrcstartfrom"><?php echo JText::_('VRSTARTFROM'); ?></span> <span class="car_cost"><?php echo $currencysymb; ?> <?php echo number_format(vikrentcar::sayCostPlusIva($r[0]['cost'], $r[0]['idprice']), 0); ?></span></div></div>
			<div <?php echo $carats; ?></div >
			<div class=''><input type="submit" name="goon" value="<?php echo JText::_('VRBUTTONADD'); ?>" class="booknow ordenar"/></div>
			</div>
			
			</div>
			</div>
			</div>
			<?php

	if (!empty ($pitemid)) {
?>
				<input type="hidden" name="Itemid" value="<?php echo $pitemid; ?>"/>
				<?php

	}
?>
			</form>
			</div>
			<div class="car_separator"></div>
			</td>
			<?php



}
		



?>

</tr>
</body>	
</table>	



<!--
		<div class="goback">
			<a href="<?php //echo JRoute::_('index.php?option=com_vikrentcar&view=vikrentcar&pickup='.$pickup.'&return='.$release); ?>"><?php //echo JText::_('VRCHANGEDATES'); ?></a>
		</div>

-->
<?php

//pagination
if(strlen($navig) > 0) {
	echo $navig;
}

?>

 <script language="JavaScript" type="text/javascript">

 var tooltipsearchcontent="<?php echo $tooltipsearch ?>";

   jQuery(document).ready(function() {

   	jQuery('.placa').css('display','none');

    jQuery('#results').DataTable({
		"aLengthMenu": [[5, 10, 15, 50, 75, -1,], [5,10, 15, 50, 75, "Todos"]],
		dom: 'lfprtip'
		
	});


	


   	});

    jQuery('input[type="search"]').tooltipster({
            theme: 'tooltipster-punk',
            position:'bottom',
            content: tooltipsearchcontent
                                 
    });


   

   </script>