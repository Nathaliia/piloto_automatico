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
$item = @$this->item;
$formName = 'adminForm_'.$item->product_id; 
require_once (JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'helpers'.DS.'cart.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'select.php');
$action = JRoute::_('index.php?option=com_k2store&view=mycart&Itemid='.$this->params->get('itemid'));
//$action = JRoute::_('index.php?option=com_k2store&view=mycart&Itemid='.$item->product_id);

?>

<form action="<?php echo $action; ?>" method="post" class="adminform" id="<?php echo $formName; ?>" name="<?php echo $formName; ?>" enctype="multipart/form-data" >

	<!--base price-->
    <span id="product_price_<?php echo $item->product_id; ?>" class="product_price">
    	<?php  echo K2StoreHelperCart::dispayPriceWithTax($item->price, $item->tax, $this->show_tax); ?>
    </span>

  <!--attribute options-->
    <div id='product_attributeoptions_<?php echo $item->product_id; ?>' class="product_attributeoptions">
    <?php
    $default = K2StoreHelperCart::getDefaultAttributeOptions($this->attributes);
    
    foreach ($this->attributes as $attribute)
    {
        ?>
        <div class="pao" id='productattributeoption_<?php echo $attribute->productattribute_id; ?>'>
        <?php
        echo "<span>".$attribute->productattribute_name." : </span>";
        
        $key = 'attribute_'.$attribute->productattribute_id;
        $selected = (!empty($values[$key])) ? $values[$key] : $default[$attribute->productattribute_id]; 
        
         // Selected attribute options (for child attributes)
		$selected_opts = (!empty($this->selected_opts)) ? json_decode($this->selected_opts) : 0; 
    
		if(!count($selected_opts))
		{
			$selected_opts = 0;
		}        
        $attribs = array('class' => 'inputbox', 'size' => '1','onchange'=>"k2storeUpdateAddToCart( 'k2store_product_".$this->product_id."', document.".$formName." );");
        echo K2StoreSelect::productattributeoptions( $attribute->productattribute_id, $selected, $key, $attribs, null, $selected_opts  );
    
        ?>
        
        </div>
        <?php
    }
    ?>
    
    </div>
    
     <div id='product_quantity_input_<?php echo $item->product_id; ?>' class="product_quantity_input">
		<span class="title"><?php echo JText::_( "Quantity" ); ?>:</span>
		<input type="text" name="product_qty" value="<?php echo $item->product_quantity; ?>" size="5" />
		
     </div>
     
     
      <!-- Add to cart button --> 
    <div id='add_to_cart_<?php echo $item->product_id; ?>' class="add_to_cart" style="display: block;"> 
        <input type="hidden" name="product_id" value="<?php echo $item->product_id; ?>" />
        <input type="hidden" id="task" name="task" value="" />
        <?php echo JHTML::_( 'form.token' ); ?>
       <!--  <input type="hidden" name="return" value="<?php echo base64_encode( JUri::getInstance()->toString() ); ?>" />  -->
		<?php 
		$onclick = "k2storeAddToCart( 'index.php?option=com_k2store&view=mycart&Itemid=1', 'addtocart', document.".$formName.", true, '".JText::_( 'Processing' )."' );"; 	
		//$onclick = "k2storeAddToCart( 'index.php?option=com_k2store&view=mycart&Itemid=1', 'addtocart', document.".$formName.", true, '".JText::_( 'Processing' )."' );"; 	
		//$onclick = "document.".$formName.".submit();";
		?>
        
        <?php 
            switch ($this->params->get('cartbutton', 'image')) 
            {
                case "button":
                    ?>
                    <input onclick="<?php echo $onclick; ?>" value="<?php echo JText::_('Add to Cart'); ?>" type="button" class="addcart button" />
                    <?php
                    break;
                case "image":
                default:
                	$image = JURI::root(true).'/components/com_k2store/images/add-to-cart.gif';
                    ?> 
                    <img class='addcart' src='<?php echo $image; ?>' alt='<?php echo JText::_('Add to Cart'); ?>' onclick="<?php echo $onclick; ?>" />
                    <?php
                    break;
            }
        ?>
    </div>

</form>
