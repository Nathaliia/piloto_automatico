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
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'k2item.php');
$items = @$this->cartobj->items;
$subtotal = @$this->cartobj->subtotal;
$state = @$this->state;
$quantities = array();
$action = JRoute::_('index.php?option=com_k2store&view=mycart&task=update&Itemid='.$this->params->get('itemid'));
$checkout_url = JRoute::_('index.php?option=com_k2store&view=checkout&Itemid='.$this->params->get('itemid'));
?>

<?php if(!$this->popup): ?>
<div id="k2storeCartPopup">
<?php endif; ?>
	
<div class='componentheading'>
    <span><?php echo JText::_( "My Shopping Cart" ); ?></span>
</div>

<div class="k2store_cartitems">
    <?php if (!empty($items)) { ?>
    <form action="<?php echo $action; ?>" method="post" name="adminForm" enctype="multipart/form-data">

        <table id="cart" class="" style="clear: both;" width="100%">
            <thead>
                <tr>
                    <?php if($this->params->get('show_thumb_cart')) : ?>
					<th style="text-align: left;"><?php echo JText::_( "Item" ); ?></th>
                    <?php endif; ?>
                    <th style="text-align: left;"><?php echo JText::_( "Item Description" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "Quantity" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "Total" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "Remove" ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; $subtotal = 0;?> 
            <?php foreach ($items as $item) : ?>
            	
            	<?php            	
            	//	$product_params = new JParameter( trim(@$item->cartitem_params) );
            	//	$link = $product_params->get('product_url', "index.php?option=com_k2&view=item&id=".$item->product_id);
					$link = JRoute::_("index.php?option=com_k2&view=item&id=".$item->product_id);
            		$link = JRoute::_($link);
            		$image = K2StoreItem::getK2Image($item->product_id, $this->params->get('cartimage_size', '_XS')); 
            	?>
            
                <tr class="row<?php echo $k; ?>">
                   <?php if($this->params->get('show_thumb_cart')) : ?>
                    <td style="text-align: center;">
                        <?php if(!empty($image)) : ?>
							<img src="<?php echo $image; ?>" alt="<?php echo $item->product_name; ?>" title="<?php echo $item->product_name; ?>" /> 
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td>
                        <a href="<?php echo $link; ?>">
                            <?php echo $item->product_name; ?>
                            <?php echo $item->product_id; ?>
                        </a>
                        <br/>
                        
                        <?php if (!empty($item->attributes_names)) : ?>
	                        <?php echo $item->attributes_names; ?>
	                        <br/>
	                    <?php endif; ?>
	                    <input name="product_attributes[<?php echo $item->cart_id; ?>]" value="<?php echo $item->product_attributes; ?>" type="hidden" />                       
                      
                        <?php if (!empty($item->product_sku)) : ?>
                            <b><?php echo JText::_( "SKU" ); ?>:</b>
                            <?php echo $item->product_sku; ?>
                            <br/>
                        <?php endif; ?>
                      
                         <?php echo JText::_( "Price" ); ?>: <?php echo K2StorePrices::number($item->product_price); ?>
                      
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php $type = 'text'; 
                       ?>
                        
                        <input name="quantities[<?php echo $item->cart_id; ?>]" type="<?php echo $type; ?>" size="3" maxlength="3" value="<?php echo $item->product_qty; ?>" />
                        
                        <!-- Keep Original quantity to check any update to it when going to checkout -->
                        <input name="original_quantities[<?php echo $item->cart_id; ?>]" type="hidden" value="<?php echo $item->product_qty; ?>" />
                    </td>
                    <td style="text-align: right;">                       
                        <?php $subtotal = $subtotal + $item->subtotal; ?>
                        <?php echo K2StorePrices::number($item->subtotal); ?>
                    </td>
                    <td><a title="Remove Item" onclick="k2storeCartRemove(this, <?php echo $item->cart_id; ?>, <?php echo $item->product_id; ?>, 2)"> <div class="k2storeCartRemove">&nbsp;</div> </a>  </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
               	<tr class="cart_subtotal">
                    <td colspan="<?php echo $colspan=($this->params->get('show_thumb_cart'))? 3:2 ?>" style="font-weight: bold;">
                        <?php echo JText::_( "Subtotal" ); ?>
                    </td>
                    <td colspan="1" style="text-align: right;">
                        <?php echo K2StorePrices::number($subtotal); ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <table width="100%">
           
               <tr>
                    <td colspan="5">
                        <input style="float: right;" type="submit" class="button" value="<?php echo JText::_('Update Quantities'); ?>" name="update" />
                    </td>
                </tr>
                
                <tr>
                	<td colspan="5" style="white-space: nowrap;">
                        <b><?php echo JText::_( "Tax and Shipping Totals" ); ?></b>
                        <br/>
                        <?php
                            echo JText::_( "Calculated during checkout process" );
                    	?>
              	 	</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <?php if (!empty($this->return)) { ?>
                        [<a href="<?php echo $this->return; ?>">
                            <?php echo JText::_( "Continue Shopping" ); ?>
                        </a>]
                        <?php } ?>
                    </td>
                    <td style="text-align: right;" nowrap>
				        <div style="float: right;">
				        [<a href="<?php echo $checkout_url; ?>">
				            <?php echo JText::_( "Begin Checkout" ); ?>
				        </a>]
				        </div>
                    </td>
                </tr>
        
        </table>
        
        <input type="hidden" name="boxchecked" value="" />
    </form>
    <?php } else { ?>
    <p><?php echo JText::_( "No items in your cart" ); ?></p>
    <?php } ?>
</div>
<?php if(!$this->popup): ?>
</div>
<?php endif; ?>
