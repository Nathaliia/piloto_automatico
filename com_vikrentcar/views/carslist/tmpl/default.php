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

$cars=$this->cars;
$category=$this->category;
$navig=$this->navig;

$currencysymb = vikrentcar :: getCurrencySymb();

if(is_array($category)) {
	?>
	<h3 class="vrcclistheadt"><?php echo $category['name']; ?></h3>
	<?php
	if(strlen($category['descr']) > 0) {
		?>
		<div class="vrccatdescr">
			<?php echo $category['descr']; ?>
		</div>
		<?php
	}
}else {
	echo vikrentcar :: getFullFrontTitle();
}

?>
<div class="vrclistcontainer">
<ul class="vrclist">
<?php
foreach($cars as $c) {
	//$carats = vikrentcar::getCarCarat($c['idcarat']);
	$carats = vikrentcar::getCarCaratOriz($c['idcarat']);
	?>
	<li>
		<?php
		if(!empty($c['img'])) {
		?>
		<img src="<?php echo JURI::root(); ?>administrator/components/com_vikrentcar/resources/<?php echo $c['img']; ?>" class="vrclistimg"/>
		<?php
		}
		?>
		<span class="vrclistcarname"><?php echo $c['name']; ?></span>
		<span class="vrclistcarcat"><?php echo vikrentcar::sayCategory($c['idcat']); ?></span>
		<div class="vrclistcardescr"><?php echo $c['info']; ?></div>
		<?php
		if($c['cost'] > 0) {
		?>
		<div class="vrclistdivcost">
			<span class="vrcliststartfrom"><?php echo JText::_('VRCLISTSFROM'); ?></span>
			<span class="car_cost"><?php echo $currencysymb; ?> <?php echo $c['cost']; ?></span>
		</div>
		<?php
		}
		?>
		<div class="vrclistsep"></div>
		<div class="vrclistcarcarats"><?php echo $carats; ?></div>
		<div class="vrclistsep"></div>
		<span class="vrclistgoon"><a href="<?php echo JRoute::_('index.php?option=com_vikrentcar&view=cardetails&carid='.$c['id']); ?>"><?php echo JText::_('VRCLISTPICK'); ?></a></span>
	</li>
	<?php
}
?>
</ul>
</div>

<?php
//pagination
if(strlen($navig) > 0) {
	echo $navig;
}
?>