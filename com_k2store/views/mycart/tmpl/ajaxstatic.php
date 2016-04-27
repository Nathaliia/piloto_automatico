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
	$link = JRoute::_('index.php?option=com_k2store&view=mycart&Itemid='.$this->params->get('itemid'));
?>
			<?php if ( $this->items) { ?>
				<table id="cart">
					<tr>
						<th width="10%" ><?php echo JText::_('QUANTITY'); ?></th>
						<th width="25%"><?php echo JText::_('ITEMNAME'); ?></th>
						<th width="10%"><?php echo JText::_('ITEMID'); ?></th>
						<th width="15%"><?php echo JText::_('UNIT PRICE'); ?></th>
						<th width="15%"><?php echo JText::_('TOTAL'); ?></th>
					</tr>
					<?php
						$total_price = $i = 0;
						foreach ( $this->items as $order_code=>$quantity ) :
							//get the item price
							$item_price = $this->model->getItemPrice($order_code);
							
							//get item tax
							$get_item_tax = $this->model->getItemTax($order_code);
							
							//calculate total price	
							$total_price += $quantity*$item_price;
							$total_tax += $quantity*$get_item_tax;
						
					?>
						<?php echo $i++%2==0 ? "<tr class='cartline even product$order_code'>" : "<tr class='cartline odd product$order_code'>"; ?>
							<td class="quantity center"><?php echo $quantity; ?></td>
							<td class="item_name"><?php echo $this->model->getItemName($order_code); ?></td>
							<td class="order_code"><?php echo $order_code; ?></td>
							<td class="unit_price_td"><span class="just_unit_price"><?php echo K2StorePrices::number($item_price); ?></span></td>
							<td class="extended_price"><span class="just_ext_price"><?php echo K2StorePrices::number($this->model->getItemTotal($order_code, $quantity)); ?></span></td>
						</tr>
					<?php endforeach; ?>
					<tr><td align="right" colspan="4"><?php echo JText::_('TOTAL'); ?> : &nbsp;&nbsp;</td><td id="total_price"><span><?php echo K2StorePrices::number($total_price); ?></span></td></tr>
					
					<?php if ($this->params->get('show_tax_total')) { ?>
					<tr><td align="right" colspan="4"><?php echo JText::_('TOTAL TAX'); ?> : &nbsp;&nbsp;</td><td id="total_tax">
					<?php echo K2StoreUtilities::number( $total_tax); ?>					
					</td></tr>
					<?php } ?>
					
				</table>
			<?php } else { ?>
				<p class="center"><?php echo JText::_('NO ITEMS'); ?></p>
			<?php } ?>
