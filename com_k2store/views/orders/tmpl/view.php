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

//no direct access
 defined('_JEXEC') or die('Restricted access'); 
$row = @$this->row; 
$order = @$this->order; 
$items = @$order->getItems();
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'popup.php');
?>

<div class='componentheading'>
	<span><?php echo JText::_( "Order Detail" ); ?></span>
</div>

    <div style="float: right;">
        <?php
        $url = JRoute::_( "index.php?option=com_k2store&view=orders&task=printOrder&tmpl=component&id=".@$row->id );
        $text = JText::_( "Print Invoice" );
        echo K2StorePopup::popup( $url, $text ); 
        ?>
    </div>
    <?php
    echo "<< <a href='".JRoute::_("index.php?option=com_k2store&view=orders")."'>".JText::_( 'Return to List' )."</a>";
    ?>
	
	<div id="orders">
	<table class="orders" >
		<tr id="order_info">
			<td> 		<h3><?php echo JText::_("Order Information"); ?></h3> </td>
			<td>
				<div>
				<table class="orderInfoTable" >
					<tr >
						<td style="width:90px"> </td>
						<td> </td>
					</tr>
					<tr>
						<td> <strong><?php echo JText::_("Order ID"); ?></strong> </td>
						<td> <?php echo @$row->order_id; ?> </td>
					</tr>
					<tr>
						<td> <strong><?php echo JText::_("Invoice No"); ?></strong> </td>
						<td> <?php echo @$row->id; ?> </td>
					</tr>
					<tr>
						<td> <strong><?php echo JText::_("Date"); ?></strong> </td>
						<td> <?php echo JHTML::_('date', $row->created_date, $this->params->get('date_format', '%a, %d %b %Y, %I:%M%p')); ?> </td>
					</tr>
					<tr>
						<td> <strong><?php echo JText::_("Status"); ?></strong> </td>
						<td> <?php echo @$row->order_state; ?> </td>
					</tr>
				
				</table>
	            </div> 
			</td>
		</tr>
		
		<tr id="payment_info"  style="background-color: #CEE0E8;">
			<td> <h3><?php echo JText::_("Payment Information"); ?></h3>  </td>
			<td>
				<div>
				<table class="paymentTable" >
					<tr  ><td></td>
					</tr>
					<tr>
						<td> <strong><?php echo JText::_("Amount"); ?></strong> </td>
						<td> <?php echo K2StorePrices::number( $row->order_total ); ?> </td>
					</tr>
					<tr>
						<td> <strong><?php echo JText::_("Billing Address"); ?></strong><br/><br/><br/><br/><br/></td>
						<td>
							<?php
		                    echo $row->first_name." ".$row->last_name."<br/>";
		                    echo $row->address_1.", ";
		                    echo $row->address_2 ? $row->address_2.", " : "<br/>";
		                    echo $row->city.", ";
		                    echo $row->state ? $row->state." - " : "";
		                    echo $row->zip." <br/>";
		                    echo $row->country." <br/> Ph:";
		                    echo $row->phone_1;
		                    echo $row->phone_2 ? $row->phone_2.", " : "<br/>";
		                    echo $row->fax ? $row->fax : "";
		                    ?>
						</td>
					</tr>
	
				</table>
	            </div> 			
			</td>
		</tr>
		<tr>
			<td> <strong><?php echo JText::_("Associated Payment Records"); ?></strong><br/></td>
			<td> 
			   <div>
		       <table class="paymentTable">

				<tr>
					<td><strong><?php echo JText::_('Payment Type'); ?></strong></td>
					<td><?php echo $row->orderpayment_type; ?> </td>
				</tr>
				
				<?php if ($row->orderpayment_type == 'payment_offline') { ?>
				<tr>
					<td><strong><?php echo JText::_('Payment Mode'); ?></strong></td>
					<td><?php echo $row->transaction_details; ?> </td>
				</tr>
				<?php } ?>
				
				<tr>
					<td><strong><?php echo JText::_('Transaction ID'); ?></strong></td>
					<td><?php echo $row->transaction_id; ?> </td>
				</tr>
				
				<tr>
					<td><strong><?php echo JText::_('Payment Status'); ?></strong></td>
					<td><?php echo $row->transaction_status; ?></td>
				</tr>				
			</table>
            </div> </td>
		</tr>
		<tr>
			<td> <strong><?php echo JText::_("Customer Note"); ?></strong><br/></td>
			<td> 
			 <table class="paymentTable">
				<tr>
					<td colspan="2"><?php echo $row->customer_note; ?> </td>
				</tr>
				</table>
			</td>	
		</tr>
	</table>	
	</div>	

	<div id="items_info">
		<h3><?php echo JText::_("Items in Order"); ?></h3>
		
		<table id="cart_order" class="" style="clear: both;">
		<thead>
			<tr>
                <th style="text-align: left;"><?php echo JText::_("Item"); ?></th>
                <th style="width: 150px; text-align: center;"><?php echo JText::_("Quantity"); ?></th>
                <th style="width: 150px; text-align: right;"><?php echo JText::_("Amount"); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : ?>

			<tr class='row<?php echo $k; ?>'>
                <td>
                    <a href="<?php echo JRoute::_( "index.php?option=com_k2&view=item&id=".$item->product_id ); ?>">
                        <?php echo JText::_( $item->orderitem_name ); ?>
                    </a>
                    <br/>
                    
                    <?php if (!empty($item->orderitem_attribute_names)) : ?>
                        <?php echo $item->orderitem_attribute_names; ?>
                        <br/>
                    <?php endif; ?>
                    
                    <?php if (!empty($item->orderitem_sku)) : ?>
                        <b><?php echo JText::_( "SKU" ); ?>:</b>
                        <?php echo $item->orderitem_sku; ?>
                        <br/>
                    <?php endif; ?>

                        <b><?php echo JText::_( "Price" ); ?>:</b>
                        <?php echo K2StorePrices::number( $item->orderitem_price); ?>
                  
                </td>
                <td style="text-align: center;">
                    <?php echo $item->orderitem_quantity; ?>
                </td>
                <td style="text-align: right;">
                    <?php echo K2StorePrices::number( $item->orderitem_final_price ); ?>
                </td>
			</tr>
		<?php $i=$i+1; $k = (1 - $k); ?>
		<?php endforeach; ?>
		
		<?php if (empty($items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('No items found'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
        <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">
            <?php echo JText::_( "Subtotal" ); ?>
            </th>
            <th style="text-align: right;">
            <?php echo K2StorePrices::number($order->order_subtotal); ?>
            </th>
        </tr>
        
            <tr>
                <th colspan="2" style="text-align: right;">
                    <?php
                    if (!empty($this->show_tax)) { echo JText::_("Product Tax Included"); } 
                    else { echo JText::_("Product Tax"); }
                    ?>
                </th>
                <th style="text-align: right;">
                <?php echo K2StorePrices::number($row->order_tax); ?>
                </th>
            </tr>
            
           <tr>
            <th colspan="2" style="text-align: right;">
            <?php echo JText::_( "Shipping" ); ?>
            </th>
            <th style="text-align: right;">
            <?php echo K2StorePrices::number($row->order_shipping); ?>
            </th>
        </tr>
        <tr>
         <th colspan="2" style="text-align: right;">
           <?php 
            if (!empty($row->order_discount ))
                  	{
                          echo "(-)";
                         echo JText::_("Discount")." (".$this->params->get('global_discount')."%) :";
                  	}
            ?>
            </th>
           <th style="text-align: right;">
            <?php echo K2StorePrices::number($row->order_discount); ?>
            </th>
         </tr>   
        <tr>
            <th colspan="2" style="font-size: 120%; text-align: right;">
            <?php echo JText::_( "Total" ); ?>
            </th>
            <th style="font-size: 120%; text-align: right;">
            <?php echo K2StorePrices::number($row->order_total); ?>
            </th>
            
        </tr>
        </tfoot>
		</table>
	</div>
