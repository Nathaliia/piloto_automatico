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
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
$state = @$this->state;
$order = @$this->order;
$items = @$this->orderitems;
?>
<div class="k2store_cartitems">
           <table id="cart" class="" width="100%" style="clear: both;">
            <thead>
                <tr>
                    <th style="text-align: left;"><?php echo JText::_( "Product" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "Quantity" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "Total" ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; ?> 
            <?php foreach ($items as $item) : ?>
                <tr class="row<?php echo $k; ?>">
                    <td>
                        <a href="<?php echo JRoute::_("index.php?option=com_k2&view=item&id=".$item->product_id); ?>">
                            <?php echo $item->orderitem_name; ?>
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

                            <?php echo JText::_( "Price" ); ?>:
                            <?php echo K2StorePrices::number($item->price); ?>                         
                  
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php echo $item->orderitem_quantity;?>  
                    </td>
                    <td style="text-align: right;">
                        <?php echo K2StorePrices::number($item->orderitem_final_price); ?>
                                               
                    </td>
                </tr>
              
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
               	<tr class="cart_subtotal">
                    <td colspan="2" style="font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_( "Subtotal" ); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo K2StorePrices::number($order->order_subtotal); ?>
                    </td>
                </tr>                
            </tfoot>
        </table>
        <table class="" width="100%" style="clear: both;">
                <tr>
                    <td colspan="2" style="white-space: nowrap;">
                        <b><?php echo JText::_( "Tax and Shipping Totals" ); ?></b>
                        <br/>
                    </td>
                    <td colspan="2" style="text-align: right;">
                    <?php 
                        	if( $order->order_tax )
		                   	{
		                   		if (!empty($this->show_tax)) { echo JText::_("Product Tax Included").":<br>"; }
		                   	    else { echo JText::_("Product Tax").":<br>"; }    
		                   	}
		                
                    	if (!empty($this->showShipping))
                    	{
                            echo JText::_("Shipping and Handling").":<br>";                          
                    	}
                    	
                    	if (!empty($order->order_discount ))
                    	{
                            //echo JText::_("Discount")."&nbsp;(".$this->params->get('global_discount')."%) :";
                            echo "(-)";
                            echo JText::_("Discount")." (".$this->params->get('global_discount')."%) :";
                    	}


                    ?>
                    </td>
                    <td colspan="2" style="text-align: right;">
                     <?php 
                        	if( $order->order_tax )
                            echo K2StorePrices::number($order->order_tax) . "<br>";    
                        
                        if (!empty($this->showShipping))
                        {
                            echo K2StorePrices::number($order->order_shipping) . "<br>";
                        }
                        
                        if( $order->order_discount )
                        echo K2StorePrices::number($order->order_discount)
                   
                    ?>                  
                    </td>
                </tr>
                <tr>
                	<td colspan="3" style="font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_( "Total" ); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo K2StorePrices::number($order->order_total); ?>
                    </td>
                </tr>                
        </table>
        <hr />        
</div>
