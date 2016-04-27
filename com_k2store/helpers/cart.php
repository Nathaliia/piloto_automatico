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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.parameter' );
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'prices.php'); 

class K2StoreHelperCart 
{


public function addItem( $item )
    {
        $session =& JFactory::getSession();
        $user =& JFactory::getUser();
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
        $table = JTable::getInstance( 'MyCart', 'Table' );

        // first, determine if this product+attribute+vendor(+additonal_keys) exists in the cart
        // if so, update quantity
        // otherwise, add as new item
        // return the cart object with cart_id (to be used by plugins, etc)
        
        $keynames = array();
        $item->user_id = (empty($item->user_id)) ? $user->id : $item->user_id;
        $keynames['user_id'] = $item->user_id;
        if (empty($item->user_id))
        {
            $keynames['session_id'] = $session->getId();
        }
        $keynames['product_id'] = $item->product_id;
        $keynames['product_attributes'] = $item->product_attributes;

        if ($table->load($keynames))
        {
			
            $table->product_qty = $table->product_qty + $item->product_qty;
        }
            else
        {
            foreach($item as $key=>$value)
            {
                if(property_exists($table, $key))
                {
                    $table->set($key, $value);
                }
            }
        }
       
        $date = JFactory::getDate();
        $table->last_updated = $date->toMysql();
        $table->session_id = $session->getId();

        if (!$table->save())
        {
            JError::raiseNotice('updateCart', $table->getError());
        }          

        return $table;
    }
  
  
      function checkIntegrity( $cart_id, $id_type='user_id' )
    {
        $user_id = 0;
        $session_id = '';
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
        //JModel::addIncludePath( JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models' );
        
        // get the cart's items as well as user info (if logged in)
       
        require_once(JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models'.DS.'mycart.php');
        $model = new K2StoreModelMycart();  
        
        switch ($id_type)
        {
            case "session":
            case "session_id":
                $model->setState('filter_session', $cart_id);
                $session_id = $cart_id;        
                break;
            case "user":
            case "user_id":
            default:
                $model->setState('filter_user', $cart_id);
                $user_id = $cart_id;
                break;                
        }
       
        return true;
    }    
    

  
  

function getAjaxCart(&$item, &$item_price) {
		//require_once (JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models'.DS.'mycart.php');
		$app = &JFactory::getApplication();
		$html = '';
		JLoader::register( "K2StoreViewMyCart", JPATH_SITE."/components/com_k2store/views/mycart/view.html.php" );
		$layout = 'addtocart';
		$view = new K2StoreViewMyCart( );
		//$view->_basePath = JPATH_ROOT.DS.'components'.DS.'com_k2store';
		$view->addTemplatePath(JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'views'.DS.'mycart'.DS.'tmpl');
		$view->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'com_k2store'.DS.'mycart');
		JModel::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models'.DS.'mycart.php');
		JLoader::import('mycart', JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models');
		$model =  new K2StoreModelMyCart();
		$product_id = $item->id;
		$item->product_id = $item->id;
		$view->assign( '_basePath', JPATH_SITE.DS.'components'.DS.'com_k2store' );
		$view->set( '_controller', 'mycart' );
		$view->set( '_view', 'mycart' );
		$view->set( '_doTask', true );
		$view->set( 'hidemenu', true );
		$view->setModel( $model, true );
		$view->setLayout( $layout );
		$view->assign( 'product_id', $product_id);
		$config = JComponentHelper::getParams('com_k2store');
		$show_tax = $config->get('show_tax_total','1');
		$show_attributes = $config->get( 'show_product_attributes', 1);
		$view->assign( 'show_tax', $show_tax );
		$view->assign( 'params', $config );
		$view->assign( 'show_attributes', $show_attributes );
		
		//get attributes 
		$attributes = $model->getAttributes($product_id);
		
		
		//tax
		
		if ( $show_tax )
		{
			$tax = K2StorePrices::getItemTax($product_id);
			$item->taxrate = $tax;
			$item->tax = $tax *  $item_price;
			
		}
		
		
	/*	
		if(count($attributes)) {
			
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables');	
			$table = JTable::getInstance( 'ProductAttributeOptions', 'Table' );
			foreach ( $attributes as $attribute )
			{
				// load the attrib's object
				$table->load( $attribute->productattribute_id);
				// update the price
				//$row->price = $row->price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
				
				// is not + or -
				if ( $table->productattributeoption_prefix == '=' )
				{
					$item_price = floatval( $table->productattributeoption_price );
				}
				else
				{
					$item_price = $item_price + floatval( "$table->productattributeoption_prefix" . "$table->productattributeoption_price" );
				}
				$item->sku .= $table->productattributeoption_code;
			}
			
			
		}
		*/
		//quantity
		$item->product_quantity = 1;
		
		$item->price = $item_price;
		$view->assign( 'attributes', $attributes );
		$view->assign( 'params', $config );
		$view->assign( 'item', $item );
		
		ob_start( );
		$view->display( );
		$html = ob_get_contents( );
		ob_end_clean( );
		
		return $html;	
	
}

function dispayPriceWithTax( $price = '0', $tax = '0', $show = '0' )
	{
		$txt = '';
		if ( $show && $tax )
		{
			$txt .= K2StorePrices::number( $price );
			$txt .= JText::sprintf('SHOW_TAX_WITH_PRICE', K2StorePrices::number($tax) );			
		
		}
		else
		{
			$txt .= K2StorePrices::number( $price );
		}
		
		return $txt;
	}


function getDefaultAttributeOptions( $attributes )
	{
		$default = array( );
		foreach ( @$attributes as $attribute )
		{
			JModel::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_k2store' . DS . 'models' );
			$model = JModel::getInstance( 'ProductAttributeOptions', 'K2StoreModel' );
			$model->setState( 'filter_attribute', $attribute->productattribute_id );
			$model->setState( 'order', 'a.ordering' );
			$items = $model->getData( );
			if ( count( $items ) )
			{
				$default[$attribute->productattribute_id] = $items[0]->productattributeoption_id;
			}
			else
			{
				$default[$attribute->productattribute_id] = 0;
			}
		}
		
		return $default;
	}
	

/**
	 * 
	 * @param $session_id
	 * @param $user_id
	 * @return unknown_type
	 */
	function mergeSessionCartWithUserCart( $session_id, $user_id )
	{
	    $date = JFactory::getDate();
	    $session =& JFactory::getSession();
	    
        JModel::addIncludePath( JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models' );
        $model = JModel::getInstance( 'Mycart', 'K2StoreModel' );
        $model->setState( 'filter_user', '0' );
        $model->setState( 'filter_session', $session_id );
        $session_cartitems = $model->getData();

        $this->deleteSessionCartItems( $session_id );
        
        if (!empty($session_cartitems))
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
            $table = JTable::getInstance( 'Mycart', 'Table' );
            foreach ($session_cartitems as $session_cartitem)
            {
                $keynames = array();
                $keynames['user_id'] = $user_id;
                $keynames['product_id'] = $session_cartitem->product_id;
                $keynames['product_attributes'] = $session_cartitem->product_attributes;
                
                if ($table->load($keynames))
                {
                    // the quantity as set in the session takes precedence
                    $table->product_qty = $session_cartitem->product_qty;
                }
                    else
                {
                    foreach($session_cartitem as $key=>$value)
                    {
                        if(property_exists($table, $key))
                        {
                            $table->set($key, $value);
                        }
                    }
                    // this is a new cartitem, so set cart_id = 0
                    $table->cart_id = '0';
                }
                
                $table->user_id = $user_id;
                $table->session_id = $session->getId();
                $table->last_updated = $date->toMysql();
                
                if (!$table->save())
                {
                    JError::raiseNotice('updateCart', $table->getError());
                }
            }
        }
	}

	function deleteSessionCartItems( $session_id )
	{
		
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'query.php'); 
        
        $db = JFactory::getDBO();

        $query = new K2StoreQuery();
        $query->delete();
        $query->from( "#__k2store_mycart" );
        $query->where( "`session_id` = '$session_id' " );
        $query->where( "`user_id` = '0'" );
        $db->setQuery( (string) $query );
        if (!$db->query())
        {
            $db->setError( $db->getErrorMsg() );
            return false;
        }
        return true;
	}
	
	function updateUserCartItemsSessionId( $user_id, $session_id )
	{
        $db = JFactory::getDBO();

        require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'query.php'); 
        $query = new K2StoreQuery();
        
        $query->update( "#__k2store_mycart" );
        $query->set( "`session_id` = '$session_id' " );
        $query->where( "`user_id` = '$user_id'" );
        $db->setQuery( (string) $query );
        if (!$db->query())
        {
            $db->setError( $db->getErrorMsg() );
            return false;
        }
        return true;
	}
	
	
/**
	 * Briefly, this method "converts" the items in the cart to a order Object
	 *
	 * @return array of OrderItem
	 */
	function getProductsInfo()
	{
	    
		JModel::addIncludePath( JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models' );
		//JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
		$model = JModel::getInstance( 'Mycart', 'K2StoreModel');

		$session =& JFactory::getSession();
		$user =& JFactory::getUser();
		$model->setState('filter_user', $user->id );
		if (empty($user->id))
		{
			$model->setState('filter_session', $session->getId() );
		}
		
		$cartitems = $model->getData();
	
		$productitems = array();
		
		$cartitem= array();
		
		foreach ($cartitems as $cartitem)
		{
			if ($productItem = K2StoreHelperCart::getItemInfo($cartitem->product_id))
			{
				$productItem->price = $productItem->product_price = !$cartitem->product_price_override->override ? $cartitem->product_price : $productItem->price;	
				//$productItem->price = $productItem->product_price = $cartitem->product_price;	
				
    			// TODO Push this into the orders object->addItem() method?
    			$orderItem = JTable::getInstance('OrderItems', 'Table');
    			$orderItem->product_id                    = $productItem->product_id;
    		//	$orderItem->orderitem_sku                 = $cartitem->product_sku;
    			$orderItem->orderitem_name                = $productItem->product_name;
    			$orderItem->orderitem_quantity            = $cartitem->product_qty;
				$orderItem->orderitem_price               = $productItem->product_price;
    			$orderItem->orderitem_attributes          = $cartitem->product_attributes;
    			$orderItem->orderitem_attribute_names     = $cartitem->attributes_names;
    			$orderItem->orderitem_attributes_price    = $cartitem->orderitem_attributes_price;
    			$orderItem->orderitem_final_price         = ($orderItem->orderitem_price + $orderItem->orderitem_attributes_price) * $orderItem->orderitem_quantity;
 		
    			// TODO When do attributes for selected item get set during admin-side order creation?
    			array_push($productitems, $orderItem);
            }
	   }	   
	   return $productitems;
    }	
	
	function getItemInfo($id) {
		$db		=& JFactory::getDBO();
		$query= "SELECT * FROM #__k2_items WHERE id=".$id;
		$db->setQuery($query);
		$row = $db->loadObject();
		
		//create an object and return
		$item = new JObject;
		$item->product_id = $id;
		$item->product_name = $row->title;
		$item->price = K2StoreHelperCart::getItemPrice($id);
		
		return $item;
		
	}

	function getItemPrice($id) {
		
		$item_price = K2StorePrices::getItemPrice($id);
		return $item_price;
	}
	
	
	/**
	 * Given an order_id, will remove the order's items from the user's cart
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	function removeOrderItems( $orderpayment_id )
	{
		// load the order to get the user_id
		JModel::addIncludePath( JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
		$cart = JTable::getInstance( 'Mycart', 'Table' );
		$model = JModel::getInstance( 'Orders', 'K2StoreModel' );
		$model->setId($orderpayment_id);
		$order = $model->getItem();		
		if (!empty($order->id))
		{
			// foreach orderitem
			foreach ($order->orderitems as $orderitem)
			{
				// remove from user's cart
				$ids = array('user_id'=>$order->user_id, 'product_id'=>$orderitem->product_id, 'product_attributes'=>$orderitem->orderitem_attributes );
				$cart->delete( $ids );
			}
		}
	}
	
	
	
	
 }
 ?>   
