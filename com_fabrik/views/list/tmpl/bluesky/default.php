<?php
/**
 * Fabrik List Template: Bluesky
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



$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery.uniform3.min.js');



$document->addScript(JURI::root().'components/com_vikrentcar/resources/dataTable/js/jquery.dataTables.js');
$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/dataTable/css/jquery.dataTables.css');



if ($this->tablePicker != '') : ?>
	<div style="text-align:right"><?php echo JText::_('COM_FABRIK_LIST') ?>: <?php echo $this->tablePicker; ?></div>
<?php
endif;
if ($this->getModel()->getParams()->get('show-title', 1)) :?>
	<h1><?php echo $this->table->label;?></h1>
<?php endif;?>

<?php echo $this->table->intro;?>
<form class="fabrikForm" action="<?php echo $this->table->action;?>" method="post" id="<?php echo $this->formid;?>" name="fabrikList">
<?php
echo $this->loadTemplate('buttons');
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
<div class="fabrikDataContainer">
<?php foreach ($this->pluginBeforeList as $c) :
	echo $c;
endforeach;?>
	<div class="boxflex">
		<table class="fabrikList" id="list_<?php echo $this->table->renderid;?>" >
		 <tfoot>
			<tr class="fabrik___heading">
				<td colspan="<?php echo count($this->headings);?>">
					<?php echo $this->nav;?>
				</td>
			</tr>
		 </tfoot>
		<?php
		$gCounter = 0;
		foreach ($this->rows as $groupedby => $group) :
			if ($gCounter == 0) :
				echo '<thead>' . $this->headingstmpl . '</thead>';
			endif;
			if ($this->isGrouped) :
				$this->groupHeading = $this->grouptemplates[$groupedby] . ' ( ' . count($group) . ' )';
				echo $this->loadTemplate('group_heading');
			endif; ?>
			<tbody class="fabrik_groupdata">
			<tr>
				<td class="groupdataMsg" colspan="<?php echo count($this->headings)?>">
					<div class="emptyDataMessage" style="<?php echo $this->emptyStyle?>">
						<?php echo $this->emptyDataMessage; ?>
					</div>
				</td>
			</tr>
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

			<?php endif; ?>
			</tbody>
			<?php
			$gCounter++;
		endforeach;

		$this->showGroup = false;
		for ($x = $gCounter; $x < $this->limitLength; $x ++) :
			$this->groupHeading = 'hidden ' . $x;
			echo $this->loadTemplate('group_heading');
			echo '<tbody class="fabrik_groupdata" style="display:none"></tbody>';
		endfor;
		?>
		</table>
		<?php print_r($this->hiddenFields);?>
	</div>
</div>
</form>
<?php echo $this->table->outro;?>