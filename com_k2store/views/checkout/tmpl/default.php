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
$row = $this->address;
$cart_edit_link = JRoute::_('index.php?option=com_k2store&view=mycart&Itemid='.$this->params->get('itemid'));
$edit_link = JRoute::_('index.php?option=com_k2store&view=checkout&address_edit=1&Itemid='.$this->params->get('itemid'));
$payment_link = JRoute::_('index.php?option=com_k2store&view=checkout&task=selectpayment&Itemid='.$this->params->get('itemid'));
$action = JRoute::_('index.php?option=com_k2store&view=checkout&task=selectpayment&Itemid='.$this->params->get('itemid'));

?>
	<form name="userForm" id="userForm" method="post" action="<?php echo $action; ?>" >
		<div class="k2storeOrderSummary">
			<?php echo @$this->orderSummary; ?>
		</div>
			<table class="billing_shipping" width="100%">
			<tr>
				<?php if($this->params->get('show_billing_address') || $this->address): ?>
				<td valign="top">
				<div class="k2storeShippingAddress">
					<h3><?php echo JText::_('Shipping Address'); ?></h3>
					<p><?php echo $row->first_name; ?>&nbsp;<?php echo $row->last_name; ?>
					<br />
					<?php echo $row->address_1; ?>,
				
					<?php if(!empty($row->address_2)): ?>
					 <?php echo $row->address_2; ?>
					<?php endif; ?> 
				
					<?php echo $row->city; ?> &nbsp;, <?php echo $row->zip; ?>					
				
					<?php if(!empty($row->state)): ?>
					 <?php echo $row->state; ?>&nbsp;,
					<?php endif; ?> 
				
					<?php echo $row->country; ?> 
					</p>
				</div>
				</td>
				<?php endif; ?>	
				
				<?php if($this->showShipping): ?>
				<td valign="top">
				<div class="showShipping">
					<h3><?php echo JText::_('Shipping Information'); ?></h3>
					<?php echo $this->shipping_method_form; ?>			 
				</div>
				</td>
				<?php endif; ?>
			</tr>
		</table>		
			<div class="k2storeCheckout">
				
					<?php if($this->params->get('show_billing_address')): ?>
					<input type="button" value="<?php echo JText::_('Edit Address'); ?>" onclick="window.location = '<?php echo $edit_link; ?>';" />
					<?php endif; ?>
					<input type="button" value="<?php echo JText::_('Edit your cart'); ?>" onclick="window.location = '<?php echo $cart_edit_link; ?>';" />
					<input type="button" value="<?php echo JText::_('Proceed to Payment'); ?>" onclick="window.location = '<?php echo $payment_link; ?>';" />				
	
			</div>
			
		</form>	
