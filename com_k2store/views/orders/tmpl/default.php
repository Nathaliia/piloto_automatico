<?php
/*
 * --------------------------------------------------------------------------------
   Weblogicx India  - K2 Store v 2.4
 * --------------------------------------------------------------------------------
 * @package		Joomla! 1.5x
 * @subpackage	K2 Store
 * @author    	Weblogicx India http://www.weblogicxindia.com
 * @copyright	Copyright (c) 2010 - 2015 Weblogicx India Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link		http://weblogicxindia.com
 * --------------------------------------------------------------------------------
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//print_r($this->state); 
//print_r($this->pagination); 
?>
<form action="<?php echo JRoute::_('index.php?option=com_k2store&view=orders')?>" method="post" name="adminForm" enctype="multipart/form-data">
<div id="k2store_orders_list">

	<table class="userTable" width="100%">
	<thead>
				<tr class="jorder_rowhead">
					<th width="1%">
						<?php echo JText::_('NO'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('Order Date'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('Invoice No'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('Order ID'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('Total'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('Order Status'); ?>
					</th>					
					
				</tr>
			</thead>
	
			<tfoot>
				<tr>
					<td colspan="6" class="jorder_row">
							<div style="float: right; padding: 5px;"><?php echo @$this->pagination->getResultsCounter(); ?></div>
							<?php echo @$this->pagination->getPagesLinks(); ?>
					</td>
				</tr>
			</tfoot>		
			<tbody>
			
	<?php
			$k = 0;	
		for($i=0; $i<count($this->orders); $i++) {
			$row = $this->orders[$i];
			$link = JRoute::_('index.php?option=com_k2store&view=orders&task=view&id='.$row->id);
		?>
	
	<tr class="k2store_order_<?php echo "row$k"; ?>">
					<td>
					<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td>
					<?php echo JHTML::_('date', $row->created_date, '%d-%m-%Y'); ?>
					</td>
					<td>
					<?php echo $row->id; ?>
					</td>
					<td>
					<a href='<?php echo $link; ?>'><?php echo $row->order_id; ?></a>
					</td>
					<td>
					<?php echo K2StoreUtilities::number( $row->orderpayment_amount, array( 'thousands'=>'' ) ); ?>
					</td>
					<td>
					<?php echo $row->order_state; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
		</table> 	

</div>
	
	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$this->state->direction; ?>" />
</form>
