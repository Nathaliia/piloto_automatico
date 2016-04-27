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
$items = @$this->cartobj->items;
$subtotal = @$this->cartobj->subtotal;

?>
	<div class="minicart">
	
	<?php 
	$i = 0; $subtotal = 0; $qtytotal = 0; 
	if($items) {
		foreach ($items as $item) : 
			$subtotal = $subtotal + $item->subtotal;
			$qtytotal = $qtytotal + $item->product_qty;
			$i++;
		endforeach;
	}	
	?>
	  <p><?php echo JText::_(count($items).' items in cart'); ?>	</p>
	</div>
	
		<div class="miniCartButton">
			<a href="<?php echo $link; ?>"><?php echo JText::_('View Cart'); ?></a>
		</div>
		
