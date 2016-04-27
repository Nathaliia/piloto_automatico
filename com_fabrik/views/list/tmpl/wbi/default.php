<?php
/**
 * Fabrik List Template: Default
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

$document = & JFactory :: getDocument();

$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery-1.8.2.min.js');
$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/dataTable/css/jquery.dataTables.min.css');
$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/dataTable/css/responsive.dataTables.min.css');
$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/dataTable/css/buttons.dataTables.min.css');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/jquery.dataTables.min.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/dataTables.responsive.min.js');



//$document->addScript('https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/dataTables.buttons.min.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/buttons.flash.min.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/jszip.min.js');
//$document->addScript('//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/vfs_fonts.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/buttons.html5.min.js');
$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/buttons.print.min.js');

if ($this->tablePicker != '') : ?>
	<div style="text-align:right"><?php echo JText::_('COM_FABRIK_LIST') ?>: <?php echo $this->tablePicker; ?></div>
<?php
endif;
if ($this->getModel()->getParams()->get('show-title', 1)) :?>
	<h1><?php echo $this->table->label;?></h1>
<?php endif;?>

<?php echo $this->table->intro;?>
<form class="fabrikForm" action="<?php echo $this->table->action;?>" method="post" id="<?php echo $this->formid;?>" name="fabrikList">

<?php echo $this->loadTemplate('buttons');
if ($this->showFilters) :
	echo $this->loadTemplate('filter');
endif;

/*
 * For some really ODD reason loading the headings template inside the group
* template causes an error as $this->_path['template'] doesnt cotain the correct
* path to this template - go figure!
*/

$this->headingstmpl = $this->loadTemplate('headings');
$this->showGroup = true;
?>

<div class="">
<?php foreach ($this->pluginBeforeList as $c) :
	echo $c;
endforeach;?>
	<div class="">
		<table class="display responsive table-bordered" cellspacing="0" width="100%"  id="list_<?php echo $this->table->renderid;?>" >
		
		

			<?php
			echo '<thead>' . $this->headingstmpl . '</thead>';
	
		$gCounter = 0;
		foreach ($this->rows as $groupedby => $group) :
			if ($this->isGrouped) :
				$this->groupHeading = $this->grouptemplates[$groupedby] . ' ( ' . count($group) . ' )';
				echo $this->loadTemplate('group_heading');
			endif; ?>
			<tbody class="bodyc">
			<!--<tr>
			<tr>
				<td class="groupdataMsg" colspan="<?php echo count($this->headings)?>">
					<div class="emptyDataMessage" style="<?php echo $this->emptyStyle?>">
						<?php echo $this->emptyDataMessage; ?>
					</div>
				</td>
			</tr>-->
<?php
			foreach ($group as $this->_row) :
				echo $this->loadTemplate('row');
		 	endforeach;
		 	?>
		<?php if ($this->hasCalculations) : ?>
				<tr class="fabrik_calculations">
				<?php
				foreach ($this->calculations as $cal) :
					echo "<td>";
					echo array_key_exists($groupedby, $cal->grouped) ? $cal->grouped[$groupedby] : $cal->calc;
					echo  "</td>";
				endforeach;
				?>
				</tr>

			<?php endif;?>
			</tbody>
			<?php
			$gCounter++;
		endforeach;

		$this->showGroup = false;
	
		?>
		</table>
		<?php print_r($this->hiddenFields);?>
	</div>
</div>
</form>
<?php echo $this->table->outro;?>

<style type="text/css">

input[type='search'] {

	margin-bottom: 15px !important;
}

.fabrik___heading{

	color: #41AFAA;
}

.userhead{
	
	
	width: 300px !important;
	
    
}




table {

        
  font-family: Verdana,sans-serif;
}




</style>

