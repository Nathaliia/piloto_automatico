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
$edit_link = JRoute::_('index.php?option=com_k2store&view=checkout&address_edit=1&Itemid='.$this->params->get('itemid'));
$payment_link = JRoute::_('index.php?option=com_k2store&view=checkout&Itemid='.$this->params->get('itemid'));
$action = JRoute::_('index.php?option=com_k2store&view=checkout&task=preparePayment&Itemid='.$this->params->get('itemid'));
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'popup.php');

// Load the form validation behavior
JHTML::_('behavior.formvalidation');
if (count($this->plugins) > 1 ) {
	$js_validation = 'onsubmit="return validate(this);"';	
} else {
	$js_validation = '';
}
?>

<script type="text/javascript">

<!--

	function validate(form) {
	var result;
	
	result = getCheckedButton('payment_plugin', form);
	
		if (result) {
			return true;
		} else {
			alert('<?php echo JText::_('Select a payment method'); ?>');
			return false;
		}
	}
		
	function getCheckedButton(group, form) {
		if (typeof group == 'string') group = form.elements[group];
		
		for (var i = 0, n = group.length; i < n; ++i)
		if (group[i].checked) return group[i];
		return null;
	}

-->

</script>

		<div class="k2storeOrderSummary">
			<?php echo @$this->orderSummary; ?>
		</div>
		<table class="billing_shipping" width="100%">
			<tr>
				<?php 
				if($this->params->get('show_billing_address') || $this->address): ?>
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
					<?php echo $this->order->shipping->shipping_name; ?>			 
				</div>
				</td>
				<?php endif; ?>
			</tr>
		</table>
		<hr />  
		<form name="userForm" class="form-validate" id="userForm" method="post" action="<?php echo $action; ?>" <?php echo $js_validation; ?> >
			
		 <div id='onCheckoutPayment_wrapper'>
			<h3><?php echo JText::_('Select a Payment Method'); ?></h3>
            <?php
                 if ($this->plugins) 
                {     
				    foreach ($this->plugins as $plugin) 
                    {
                        ?>
                        <input value="<?php echo $plugin->element; ?>" class="payment_plugin" name="payment_plugin" type="radio" onclick="k2storeGetPaymentForm('<?php echo $plugin->element; ?>', 'payment_form_div');" <?php echo (!empty($plugin->checked)) ? "checked" : ""; ?>/>
                        <?php echo JText::_( $plugin->name ); ?>
                        <br/>
                        <?php
                    }
                }
            ?>
            
        </div>
        
        <div id='payment_form_div' style="padding-top: 10px;">
				<?php
		if (!empty($this->payment_form_div))
		{
			echo $this->payment_form_div;
		}
		?>

        </div>
        
        <div id="validationmessage" style="padding-top: 10px;"></div>
        
        <div id="customer_note">
			<h3><?php echo JText::_('Customer Note'); ?></h3>
			<textarea name="customer_note" rows="3" cols="40"><?php echo $this->order->customer_note; ?> </textarea> 
        </div>
        <div class="clr"></div>
		 
        <div id="checkbox_tos"><input type="checkbox" class="required" id="k2store_tos" name="k2store_tos" value="1" /> 
        <label for="k2store_tos"> 
        <?php echo K2StorePopup::popup($this->tos_link, JText::_('Terms and Conditions')); ?>
        </label>
        </div>
		<!-- <input type="submit" value="Confirm and Pay" /> -->
		<button class="button validate" type="submit"><?php echo JText::_('Confirm and Pay'); ?></button>
		
		<input type="hidden" name="option" value="com_k2store" />
		<input type="hidden" name="view" value="checkout" />
		<input type="hidden" name="task" value="preparePayment" />
		 <?php echo JHTML::_( 'form.token' ); ?>
		</form>
