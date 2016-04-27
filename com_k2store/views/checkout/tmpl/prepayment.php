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
	defined('_JEXEC') or die('Restricted access'); 
	$order = @$this->order;
	$row = $this->address;
	$plugin_html = @$this->plugin_html;
?>

<div class='componentheading'>
    <span><?php echo JText::_( "Review Checkout Selections and Submit Payment" ); ?></span>
</div>

    <!--    ORDER SUMMARY   -->
    <h3><?php echo JText::_("Order Summary") ?></h3>
		<div class="k2storeOrderSummary">
			<?php echo @$this->orderSummary; ?>
		</div>
	
	<?php if($this->params->get('show_billing_address') || $this->address): ?>
		<div class="k2storeShippingAddress">
			<h3><?php echo JText::_('Shipping Address'); ?></h3>
			<p><?php echo $row->first_name; ?>&nbsp;<?php echo $row->last_name; ?></p>
			<p><?php echo $row->address_1; ?></p>
			<p><?php echo $row->address_2; ?></p>
			<p><?php echo $row->city; ?> &nbsp; <?php echo $row->zip; ?></p>
			<p><?php echo $row->state; ?>&nbsp; , &nbsp;<?php echo $row->country; ?> </p>
		</div>
	<?php endif; ?>	
	
   
    <div class="reset"></div>
        
    <!--    PAYMENT METHOD   -->
    <h3><?php echo JText::_("Payment Method"); ?></h3>

	<?php echo $plugin_html; ?>    
