<?php defined('_JEXEC') or die('Restricted access'); 
?>

<?php
    if (!empty($this->shipping_name)) 
    {  
	?>    
       <div class="shippingName">
       <?php echo JText::_('Standard Shipping Method'); ?>       
       [<?php echo $this->shipping_name; ?>]
       </div>
    <?php
    }
        else
    {
        ?>
        <div class="note">
        <?php echo JText::_( "NO SHIPPING RATES FOUND" ); ?>
        </div>
        <?php
    }
?>
