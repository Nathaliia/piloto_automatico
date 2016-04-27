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
	$link = JRoute::_('index.php?option=com_k2store&view=checkout');
?>

<div id="container">
			<h1><?php echo $shopname; ?> - <?php echo JText::_('SHOPPING CART'); ?></h1>
			<?php if ( $this->items) : ?>
			<form action="index.php?option=com_k2store" method="get" name="userForm" id="userForm">
				<table id="cart">
					<tr>
						<th><?php echo JText::_('QUANTITY'); ?></th>
						<th><?php echo JText::_('ITEMNAME'); ?></th>
						<th><?php echo JText::_('ITEMID'); ?></th>
						<th><?php echo JText::_('UNIT PRICE'); ?></th>
						<th><?php echo JText::_('TOTAL'); ?></th>
					</tr>
					<?php
						$total_price = $i = 0;
						foreach ( $this->items as $order_code=>$quantity ) :
							$total_price += $quantity*$this->model->getItemPrice($order_code);
					?>
						<?php echo $i++%2==0 ? "<tr>" : "<tr class='odd'>"; ?>
							<td class="quantity center"><?php echo $quantity; ?></td>
							<td class="item_name"><?php echo $this->model->getItemName($order_code); ?></td>
							<td class="order_code"><?php echo $order_code; ?></td>
							<td class="unit_price"><?php echo $currency;?><?php echo $this->model->getItemPrice($order_code); ?></td>
							<td class="extended_price"><?php echo $currency;?><?php echo ($this->model->getItemPrice($order_code)*$quantity); ?></td>
						</tr>
					<?php endforeach; ?>
					<tr><td align="right" colspan="4"><?php echo JText::_('TOTAL'); ?> : &nbsp;&nbsp;</td><td id="total_price"><?php echo $currency;?><?php echo $total_price; ?></td></tr>
				</table>
				<table>
					<tr>
					<td><input type="button" value="<?php echo JText::_('CHECKOUT'); ?>" onclick="SqueezeBox.close(); window.location = '<?php echo $link; ?>';" />
					<td><input type="button" value="<?php echo JText::_('CONTINUE SHOPPING'); ?>" onclick="SqueezeBox.close();" />
					</tr>
				</table>
				<input type="hidden" name="option" value="com_k2store" />
				<input type="hidden" name="view" value="checkout" />
			</form>
			<?php else: ?>
				<p class="center"><?php echo JText::_('NO ITEMS'); ?></p>
			<?php endif; ?>
		</div>
