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

jimport('joomla.application.component.controller');
JLoader::register( 'K2StoreHelperCart', JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'helpers'.DS.'cart.php');
class K2StoreControllerCheckout extends JController
{
	
	var $_order        = null; 
	var $defaultShippingMethod = null; // set in constructor
	var $initial_order_state   = 4;
	var $_cartitems = null;
	
	function __construct()
	{		
		parent::__construct();

		$cart_helper = new K2StoreHelperCart();
		$items = $cart_helper->getProductsInfo();
		$this->_cartitems = $items;
		$params = &JComponentHelper::getParams('com_k2store');
		$this->defaultShippingMethod = $params->get('defaultShippingMethod', '1');
		// create the order object
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
		$this->_order = JTable::getInstance('Orders', 'Table');		
		
	}	
	
	function display() {
		
		$app = &JFactory::getApplication();
		$user 		=	& JFactory::getUser();
		$guest = JRequest::getVar( 'guest', '0' );
		$address_edit = JRequest::getVar( 'address_edit', '0' );
		$params = &JComponentHelper::getParams('com_k2store');
		
		$items = $this->_cartitems;
		
		$task = JRequest::getVar('task');
		if (empty($items) && $task != 'confirmPayment' )
		{
			$msg = JText::_('You have no items in the cart');
			$link = JRoute::_('index.php');
			$app->redirect($link, $msg);
		}
		
		if($guest == '1')
			$guest = true;
		else 
			$guest = false;
		
		if (empty($user->id) && !$guest)
		{
			// Display a form for selecting either to register or to login
			JRequest::setVar('layout', 'form');
			
		}
		//if guest checkout is allowed get it
		else if(empty($user->id) && $guest && $params->get('allow_guest_checkout'))
		{
			
			$order =& $this->_order;
			$order = $this->populateOrder(true);
			
			//minimum order value check
			if(!$this->checkMinimumOrderValue($order)) {
				$msg = JText::_('Please add more products as minimum order value is').K2StorePrices::number($params->get('global_minordervalue'));
				$link = JRoute::_('index.php?option=com_k2store&view=mycart&Itemid='.$params->get('itemid', 99));
				$app->redirect($link, $msg);
			}
			//shipping
			// Checking whether shipping is required
			$showShipping = false;		

			$cartsModel = $this->getModel('mycart');			
			if ($isShippingEnabled = $cartsModel->getShippingIsEnabled())
			{
				$showShipping = true;
				$this->setShippingMethod();								
			}
			
			// now that the order object is set, get the orderSummary html
			
			$html = $this->getOrderSummary();			
			$view = $this->getView( 'checkout', 'html' );
			$view->set( 'hidemenu', false);
			$view->assign( 'order', $order );
			$view->assign( 'orderSummary', $html );			
			JRequest::setVar('layout', 'guest');
			
		}
		// Already Logged in, a traditional checkout
		else
		{		
			
			$order =& $this->_order;
			$order = $this->populateOrder(false);
			
			//minimum order value check
			if(!$this->checkMinimumOrderValue($order)) {
				$msg = JText::_('Please add more products as minimum order value is').K2StorePrices::number($params->get('global_minordervalue'));
				$link = JRoute::_('index.php?option=com_k2store&view=mycart&Itemid='.$params->get('itemid', 99));
				$app->redirect($link, $msg);
			}
			
			$model		= &$this->getModel('checkout');
			$address = $model->checkShippingAddress();
			//get the view
			$view = $this->getView( 'checkout', 'html' );

			// Checking whether shipping is required
			$showShipping = false;		

			$cartsModel = $this->getModel('mycart');			
			if ($isShippingEnabled = $cartsModel->getShippingIsEnabled())
			{
				$showShipping = true;
				$this->setShippingMethod();								
			}

			if($showShipping)
	        {
				$shipping_layout = "shipping_yes";		
		        $shipping_method_form = $this->getShippingHtml( $shipping_layout );
				$view->assign( 'showShipping', $showShipping );
				$view->assign( 'shipping_method_form', $shipping_method_form );
			}
			
			$html = $this->getOrderSummary();	
			
			$view->set( 'hidemenu', false);
			$view->assign( 'order', $order );
			$view->assign( 'address', $address );
			$view->assign( 'orderSummary', $html );
			
			//$view->assign( 'params', $params );			
		
			if (!$params->get('show_billing_address') || $address) {
				JRequest::setVar('layout', 'default');
				$view->setLayout('default');
			} elseif ($params->get('show_billing_address')) {
				JRequest::setVar('layout', 'address');
				$view->setLayout('address');
			}
								
		}
		
		if($address_edit) {
			JRequest::setVar('layout', 'address');			
			$view->setLayout('address');
		} 	
		
		parent::display();		
	}
	
function getOrderSummary()
	{
		// get the order object
		$order =& $this->_order; 
		$model = $this->getModel('mycart');
		$view = $this->getView( 'checkout', 'html' );
		$view->set( '_controller', 'checkout' );
		$view->set( '_view', 'checkout' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );	
		$params = &JComponentHelper::getParams('com_k2store');
		$show_tax = $params->get('show_tax_total');
        $view->assign( 'show_tax', $params->get('show_tax_total'));
        $view->assign( 'params', $params);
        $view->assign( 'order', $order );
       
		$orderitems = $order->getItems();
	//	print_r($orderitems);
        require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'prices.php'); 
        
        $tax_sum = 0;
        foreach ($orderitems as &$item)
        {
          $item->price = $item->orderitem_price + floatval( $item->orderitem_attributes_price );            
            $tax = 0;
            if ($show_tax)
            {            	
		       
				$product_tax_rate = K2StorePrices::getItemTax($item->product_id);	
				$tax = $product_tax_rate * ($item->orderitem_price + floatval( $item->orderitem_attributes_price ));
		       
		        $item->price = $item->orderitem_price + floatval( $item->orderitem_attributes_price ) + $tax;
                $item->orderitem_final_price = $item->price * $item->orderitem_quantity;
               
                $order->order_subtotal += ($tax * $item->orderitem_quantity);    
            }
            $tax_sum += ($tax * $item->orderitem_quantity);
        }        
        
        // Checking whether shipping is required
		$showShipping = false;
		
		if ($isShippingEnabled = $model->getShippingIsEnabled())
		{
			$showShipping = true;
			$view->assign( 'shipping_total', $order->getShippingTotal() );
		}
		$view->assign( 'showShipping', $showShipping );
		
        $view->assign( 'orderitems', $orderitems );		
		$view->setLayout( 'cartsummary' );

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}


function save()
	{
		$mainframe = &JFactory::getApplication();
		JRequest::checkToken() or jexit('Invalid Token');
		$post = JRequest::get('post');	   
		$type = JRequest::getWord('savetype', 'order');
		//$model = $this->getModel($type);
		$model = $this->getModel($type);
		
		if(!$model->save($post)) {
			$msg = JText::_('Error in Saving');	
			$link = JRoute::_('index.php?option=com_k2store&view=mycart');
		} else {
			$link = JRoute::_('index.php?option=com_k2store&view=checkout&task=selectpayment');
			$msg = JText::_('Shipping address saved sucessfully');	
		}
		$mainframe->redirect($link, $msg);		
	}
	
	
	function populateOrder($guest = false)
	{
		$order =& $this->_order;
		$order->shipping_method_id = $this->defaultShippingMethod;
		
		//$items = $cart_helper->getProductsInfo();
		$items = $this->_cartitems;		
		foreach ($items as $item)
		{
			$order->addItem( $item );
		}
		// get the order totals
		$order->calculateTotals();

		return $order;
	}

	
	function checkMinimumOrderValue($order) {
		
		$params = &JComponentHelper::getParams('com_k2store');
		
		$min_value = $params->get('global_minordervalue');
		if(!empty($min_value)) {
			if($order->order_subtotal >= $min_value) {
			 return true;	
			} else {
			 return false;
			}
		} else {
			return true;
		}
						
	}
	
	
	
function guestsave() {
	
		$mainframe = &JFactory::getApplication();
		JRequest::checkToken() or jexit('Invalid Token');
		$session = JFactory::getSession();
		
		$guest = JRequest::getVar( 'guest', '0' );
		
		if ($guest != 1) {
			
			$link = JRoute::_('index.php?option=com_k2store&view=mycart');
			$msg = JText::_('Only guests are allowed to checkout this way');
			$mainframe->redirect($link, $msg);
			
		}
		
		//get post vars
		
		$post = JRequest::get('post');
		
		//validate captcha
		
		if ($post['captcha'] != $session->get('expect')) {
		
			$link = JRoute::_('index.php?option=com_k2store&view=checkout&guest=1');
			$msg = JText::_('Wrong captcha');
			$mainframe->redirect($link, $msg);				
			
		}
		
		
		if(empty($post['first_name']) || empty($post['last_name']) || empty($post['email_address']) || empty($post['address_1']) || empty($post['city']) || empty($post['zip']) || empty($post['country']) || empty($post['phone_1']) )  {
			
			$link = JRoute::_('index.php?option=com_k2store&view=checkout&guest=1');
			$msg = JText::_('All fields are required');
			$mainframe->redirect($link, $msg);
			
		}
		
		
		$session->set('guestaddress', $post);
		$session->set('isguest', 1);
		
		$link = JRoute::_('index.php?option=com_k2store&view=checkout&task=selectpayment');
	//	$msg = JText::_('Shipping address saved sucessfully');
		$mainframe->redirect($link);		
		
 }
 
 
 //hipping method set
 
 function setShippingMethod()
	{

		// get the order object so we can populate it
		$order =& $this->_order; // a TableOrders object (see constructor)
		//get rates
		$rate = & $this->getShippingRates();
		
		// set the shipping method
		$order->shipping = new JObject();
		$order->shipping->shipping_price      = $rate[0]->shipping_method_price;
		$order->shipping->shipping_extra      = $rate[0]->shipping_method_handling;
		$order->shipping->shipping_name       = $rate[0]->shipping_method_name;
		$order->shipping->shipping_method_id  = $rate[0]->shipping_method_id;
					
		// get the order totals
		$order->calculateTotals();
		return;
	}
 
	
	function getShippingHtml( $layout='shipping_yes' )
	{
		$order =& $this->_order;
		$params = &JComponentHelper::getParams('com_k2store');
		$html = '';
		$model = $this->getModel( 'Checkout', 'K2StoreModel' );
		$view   = $this->getView( 'checkout', 'html' );
		$view->set( '_controller', 'checkout' );
		$view->set( '_view', 'checkout' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->setLayout( $layout );
		$rates = array();

		switch (strtolower($layout))
		{
			case "shipping_no":
				break;
			case "shipping_yes":
			default:
				$view->assign( 'params', $params );
				$view->assign( 'shipping_name', $order->shipping->shipping_name  );				
				break;
		}

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function getShippingRates()
	{ 
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'library'.DS.'shipping.php');
		$shipping_helper = new K2StoreShipping;
		
		$order =& $this->_order;
		
		$rates = array();
		JModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'models');
        $model = JModel::getInstance('ShippingMethods', 'K2StoreModel');
		$model->setState('filter_published', 1);
		
        if ($methods = $model->getItemFront())
        {
	        foreach( $methods as $method )
            {
		        // filter the list of methods according to geozone
                $ratemodel = JModel::getInstance('ShippingRates', 'K2StoreModel');
                $ratemodel->setState('filter_shippingmethod', $method->id);
                if ($ratesexist = $ratemodel->getList())
                {
                    $total = $shipping_helper->getTotal($method->id, $order->getItems());
           
                    if ($total)
                    {
                    	$total->shipping_method_type = $method->shipping_method_type;
                        $rates[] = $total;
                    }
                }
            }
        }      
        return $rates;		
	}
	
 
 
 
 	/**
	 * Prepare the review tmpl
	 *
	 * @return unknown_type
	 */
	function selectpayment()
	{  
		
		$app =&JFactory::getApplication();
		
		$cart_helper = new K2StoreHelperCart();
		$items = $cart_helper->getProductsInfo();
		//$items = $this->_cartitems;
		if(count($items) < 1) {
			$msg = JText::_('You have no items in the cart');
			$link = JRoute::_('index.php');
			$app->redirect($link, $msg);
		}

		// get the posted values
		$values = JRequest::get('post');
		// get the order object so we can populate it
		$order =& $this->_order; // a TableOrders object (see constructor)
		
		$session = JFactory::getSession();
		$guest = $session->get('isguest');
		$params = &JComponentHelper::getParams('com_k2store');
		
		$guest_address = $session->get('guestaddress');
		
		$user_id = JFactory::getUser()->id;
		//if user is guest, let is create a silent login for him.
		if($guest && empty($user_id) && $params->get('allow_guest_checkout')) {
			 $result = $this->registerNewUser($guest_address);
			 
			 if($result['error']) {
				$msg = JText::_('Error in saving user').'&nbsp;'.$result['msg'];	
				$link = JRoute::_('index.php?option=com_k2store&view=checkout&guest=1&Itemid='.$params->get('itemid'));
				$app->redirect($link, $msg);
			 } else {
				 $user_id = JFactory::getUser()->id; 
			 }			
		}
		
		//all users would be logged in at this point. So do not allow non-user beyond this point.
		
		if(empty($user_id)) {
			$msg = JText::_('You must be logged in to access this area');	
			$link = JRoute::_('index.php?option=com_k2store&view=mycart');
			$app->redirect($link, $msg);
		}
	
		// Guest Checkout
		$order->user_id = $user_id;
			
		// get the items and add them to the order
        
       
		//$items = $cart_helper->getProductsInfo();
		//$items = $this->_cartitems;
		foreach ($items as $item)
		{
			$order->addItem( $item );
		}
		
		//shipping
		// Checking whether shipping is required
			$showShipping = false;		

			$cartsModel = $this->getModel('mycart');			
			if ($isShippingEnabled = $cartsModel->getShippingIsEnabled())
			{
				$showShipping = true;
				$this->setShippingMethod();				
			}

		// get the order totals
		$order->calculateTotals();
			
		// now that the order object is set, get the orderSummary html
		$html = $this->getOrderSummary();

		$values = JRequest::get('post');

		
		//Set display
		$view = $this->getView( 'checkout', 'html' );
		$view->setLayout('selectpayment');
		$view->set( '_doTask', true);

		//Get and Set Model
		$model = $this->getModel('checkout');
		$view->setModel( $model, true );
		
		//get adddress
		if($params->get('show_billing_address')) {
			$address = $model->checkShippingAddress();
			$view->assign( 'address', $address );
		}
		
		//assign the terms and conditions link
		$tos_link = JRoute::_('index.php?option=com_k2&view=item&tmpl=component&id='.$params->get('termsid'));
		$view->assign( 'tos_link', $tos_link);
		$view->assign( 'showShipping', $showShipping );
		$view->assign('values', $values);
		$view->assign('guest', $guest);
		$view->set( 'hidemenu', false);
		$view->assign( 'order', $order );
		$view->assign( 'orderSummary', $html );

		$showPayment = true;
		if ((float)$order->order_total == (float)'0.00')
		{
			$showPayment = false;
		}
		$view->assign( 'showPayment', $showPayment );

		require_once (JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'helpers'.DS.'plugin.php');
		$payment_plugins = K2StoreHelperPlugin::getPluginsWithEvent( 'onK2StoreGetPaymentPlugins' );
		
        $dispatcher =& JDispatcher::getInstance();
        JPluginHelper::importPlugin ('k2store');

        $plugins = array();
        if ($payment_plugins)
        {
            foreach ($payment_plugins as $plugin)
            {
                $results = $dispatcher->trigger( "onK2StoreGetPaymentOptions", array( $plugin->element, $order ) );
                if (in_array(true, $results, true))
                {
                    $plugins[] = $plugin;
                }
            }
        }

        if (count($plugins) == 1)
        {
            $plugins[0]->checked = true;
            ob_start();
            $this->getPaymentForm( $plugins[0]->element );
            $html = json_decode( ob_get_contents() );
            ob_end_clean();
            $view->assign( 'payment_form_div', $html->msg );                                               
        }
                  
		$view->assign('plugins', $payment_plugins);
		
		
		$view->display();		
	}
	
	function getPaymentForm($element='')
	{
		
		$values = JRequest::get('post');
		$html = '';
		$text = "";
		$user = JFactory::getUser();
		if (empty($element)) { $element = JRequest::getVar( 'payment_element' ); }
		$results = array();
		$dispatcher    =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('k2store');
		
		$results = $dispatcher->trigger( "onK2StoreGetPaymentForm", array( $element, $values ) );		
		for ($i=0; $i<count($results); $i++)
		{
			$result = $results[$i];
			$text .= $result;
		}

		$html = $text;

		// set response array
		$response = array();
		$response['msg'] = $html;

		// encode and echo (need to echo to send back to browser)
		echo json_encode($response);

		return;
	}

 	
 	function registerNewUser ($values){

		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
		
		//  Register an User
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'library'.DS.'user.php');	
		$userHelper = new K2StoreHelperUser;
			
		
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		if ($userHelper->emailExists($values['email_address']))
		{
			// TODO user already exists		
			$response['error'] = '1';	
			$response['msg'] = JText::_('Email already exist!');
			$response['key'] = 'email_address';
			return $response;
		}
		else
		{
			// create the details array with new user info
			$details = array(
					'email' => $values['email_address'],
					'name' => $values['first_name'],
					'username' => $values['email_address']			
				);

				// use a random password, and send password2 for the email
				jimport('joomla.user.helper');
                $details['password']    = JUserHelper::genRandomPassword();
                $details['password2']   = $details['password'];
                
			// create the new user
			$msg = $this->getError();
			$user = $userHelper->createNewUser($details, $msg);

			$userHelper->login( 
				    array('username' => $user->username, 'password' => $details['password']) 
			);
			
		if($user->id) {
				$model1 = $this->getModel('address');
				$success = $model1->save($values);
				
				if(!$success) {
					return false;
				}
			}	
		if (empty($user->id))
			{
				return false;	// TODO what to do if creating new user failed?
			}			
		
			return true;
		}
	}
	
	
	
	/**
	 * This method occurs before payment is attempted
	 * and fires the onPrePayment plugin event
	 *
	 * @return unknown_type
	 */
	function preparePayment()
	{
		// verify that form was submitted by checking token
		JRequest::checkToken() or jexit( 'K2storeControllerCheckout::preparePayment - Invalid Token' );
		
		$app = &JFactory::getApplication();
		$user 		=	& JFactory::getUser();
		$params = &JComponentHelper::getParams('com_k2store');	
		// 1. save the order to the table with a 'pre-payment' status

		// Get post values
		$values = JRequest::get('post');
	
		If(!$this->validateSelectPayment($values)) {
			$link = JRoute::_('index.php?option=com_k2store&view=checkout&task=selectpayment');
			$app->redirect($link, $this->getError());
			return false;
		}
		
		$user = JFactory::getUser();
		$order_id = time();
		$values['order_id'] = $order_id;
		
		// Save the order with a pending status
		if (!$this->saveOrderItems($values))
		{
			// Output error message and halt
			JError::raiseNotice( 'Error Saving Order', $this->getError() );
			return false;
		}
			
		// Get Order Object
		$order =& $this->_order;
		
		//shipping
	// Checking whether shipping is required
		$showShipping = false;		
	
		$cartsModel = $this->getModel('mycart');			
		if ($isShippingEnabled = $cartsModel->getShippingIsEnabled())
		{
			$showShipping = true;
			$this->setShippingMethod();				
		}

		$orderpayment_type = $values['payment_plugin'];
		$transaction_status = JText::_( "Incomplete" );
		// in the case of orders with a value of 0.00, use custom values
		if ( (float) $order->order_total == (float)'0.00' )
		{
			$orderpayment_type = 'free';
			$transaction_status = JText::_( "Complete" );
		}
		
		$order->user_id = JFactory::getUser()->id;					
		$order->ip_address = $_SERVER['REMOTE_ADDR'];
		//get the customer note
		$customer_note = JRequest::getVar('customer_note', '', 'post', 'string'); 
		$order->customer_note = $customer_note;
		// Save an order with an Incomplete status
		$order->order_id = $order_id;
		$order->orderpayment_type = $orderpayment_type; // this is the payment plugin selected
		$order->transaction_status = $transaction_status; // payment plugin updates this field onPostPayment
		$order->orderpayment_amount = $order->order_total; // this is the expected payment amount.  payment plugin should verify actual payment amount against expected payment amount	
		if (!$order->save())
		{
			// Output error message and halt
			JError::raiseNotice( 'Error Saving Pending Payment Record', $order->getError() );
			return false;
		}
		
		// send the order_id and orderpayment_id to the payment plugin so it knows which DB record to update upon successful payment
		$values["order_id"]             = $order->order_id;
		//$values["orderinfo"]            = $order->orderinfo;
		$values["orderpayment_id"]      = $order->id;
		$values["orderpayment_amount"]  = $order->orderpayment_amount;
		
		$model	= &$this->getModel('checkout');
		$address = $model->checkShippingAddress();
	
		if($params->get('show_billing_address') || $address) {	
		
			foreach ($address as $key=>$value) {			
				$values['orderinfo'][$key] = $value;			
			}			
		}
		$values['orderinfo']['user_email'] = $user->email;

		// IMPORTANT: Store the order_id in the user's session for the postPayment "View Invoice" link
		
		$app->setUserState( 'k2store.order_id', $order->order_id );
		$app->setUserState( 'k2store.orderpayment_id', $order->id );
			
		
		// in the case of orders with a value of 0.00, we redirect to the confirmPayment page
		if ( (float) $order->order_total == (float)'0.00' )
		{
			$app->redirect( 'index.php?option=com_k2store&view=checkout&task=confirmPayment' );
			return;
		}

		$dispatcher    =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('k2store');
		
		$results = $dispatcher->trigger( "onK2StorePrePayment", array( $values['payment_plugin'], $values ) );

		// Display whatever comes back from Payment Plugin for the onPrePayment
		$html = "";
		for ($i=0; $i<count($results); $i++)
		{
			$html .= $results[$i];
		}

		// get the order summary
		$summary = $this->getOrderSummary();
	
		// Set display
		$view = $this->getView( 'checkout', 'html' );
		$view->setLayout('prepayment');
		$view->set( '_doTask', true);
		$view->assign('order', $order);
		$view->assign('plugin_html', $html);
		$view->assign('orderSummary', $summary);
		$view->assign('address', $address);
		$view->setModel( $model, true );

		$view->display();

		return;
	}
	
	
		/**
	 * Saves the order to the database
	 *
	 * @param $values
	 * @return unknown_type
	 */
	function saveOrder($values)
	{
		$error = false;
		$order =& $this->_order; // a TableOrders object (see constructor)
		//$order->bind( $values );
		$order->user_id = JFactory::getUser()->id;					
		$order->ip_address = $_SERVER['REMOTE_ADDR'];
		//$this->setAddresses( $values );
		

		//get the items and add them to the order
		
		
  		$cart_helper = new K2StoreHelperCart();
		$reviewitems = $cart_helper->getProductsInfo();
		
		 foreach ($reviewitems as $reviewitem)
			{
				$order->addItem( $reviewitem );
			}
		
		$order->order_state_id = $this->initial_order_state;
		$order->calculateTotals();
	
		//$order->getInvoiceNumber();

		$model  = JModel::getInstance('Orders', 'K2StoreModel');
		//TODO: Do Something with Payment Infomation
		if ( $order->save() )
		{
		 	$model->setId( $order->id );

			// save the order items
			if (!$this->saveOrderItems())
			{
				// TODO What to do if saving order items fails?
				$error = true;
			}

			// save the order vendors
			if (!$this->saveOrderVendors())
			{
				// TODO What to do if saving order vendors fails?
				$error = true;
			}

			// save the order info
			if (!$this->saveOrderInfo())
			{
				// TODO What to do if saving order info fails?
				$error = true;
			}

			// save the order history
			if (!$this->saveOrderHistory())
			{
				// TODO What to do if saving order history fails?
				$error = true;
			}

			// save the order taxes
			if (!$this->saveOrderTaxes())
			{
				// TODO What to do if saving order taxes fails?
				$error = true;
			}

			// save the order shipping info
			if (!$this->saveOrderShippings())
			{
				// TODO What to do if saving order shippings fails?
				$error = true;
			}
			
		    // save the order coupons
            if (!$this->saveOrderCoupons())
            {
                // TODO What to do if saving order coupons fails?
                $error = true;
            }            
		}

		if ($error)
		{
			return false;
		}
		
		
		
		return true;
	}
	
	
		/**
	 * Saves each individual item in the order to the DB
	 *
	 * @return unknown_type
	 */
	function saveOrderItems($values)
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables' );
		$order =& $this->_order;
		$order_id = $values['order_id'];
		//review things once again
		$cart_helper = new K2StoreHelperCart();
		$reviewitems = $cart_helper->getProductsInfo();
		
		 foreach ($reviewitems as $reviewitem)
			{
				$order->addItem( $reviewitem );
			}
		
		$order->order_state_id = $this->initial_order_state;
		$order->calculateTotals();
		
		
		$items = $order->getItems();

		if (empty($items) || !is_array($items))
		{
			$this->setError( "saveOrderItems:: ".JText::_( "Items Array is Invalid" ) );
			return false;
		}
			
		$error = false;
		$errorMsg = "";
		foreach ($items as $item)
		{
			$item->order_id = $order_id;

			if (!$item->save())
			{
				// track error
				$error = true;
				$errorMsg .= $item->getError();
			}
			else
			{
					
				// Save the attributes also
				if (!empty($item->orderitem_attributes))
				{
					$attributes = explode(',', $item->orderitem_attributes);
					foreach (@$attributes as $attribute)
					{
						unset($productattribute);
						unset($orderitemattribute);
						$productattribute = JTable::getInstance('ProductAttributeOptions', 'Table');
						$productattribute->load( $attribute );
						$orderitemattribute = JTable::getInstance('OrderItemAttributes', 'Table');
						$orderitemattribute->orderitem_id = $item->orderitem_id;
						$orderitemattribute->productattributeoption_id = $productattribute->productattributeoption_id;
						$orderitemattribute->orderitemattribute_name = $productattribute->productattributeoption_name;
						$orderitemattribute->orderitemattribute_price = $productattribute->productattributeoption_price;
						$orderitemattribute->orderitemattribute_code = $productattribute->productattributeoption_code;
						$orderitemattribute->orderitemattribute_prefix = $productattribute->productattributeoption_prefix;
						if (!$orderitemattribute->save())
						{
							// track error
							$error = true;
							$errorMsg .= $orderitemattribute->getError();
						}
					}
				}
			}
		}

		if ($error)
		{
			$this->setError( $errorMsg );
			return false;
		}
		return true;
	}
	

function validateSelectPayment($values) {
	
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';
		
		if(empty($values['payment_plugin'])) {
			$response['msg'] =  JText::_('Select a payment method');
			$response['error'] = '1';			
		}
		
		
		if(empty($values['k2store_tos'])) {
			$response['msg'] =  JText::_('You should agree to the terms');
			$response['error'] = '1';			
		}
		
		$dispatcher    =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('k2store');
		
		//verify the form data
		$results = array();
		$results = $dispatcher->trigger( "onK2StoreGetPaymentFormVerify", array( $values['payment_plugin'], $values) );

			for ($i=0; $i<count($results); $i++)
			{
				$result = $results[$i];
				if (!empty($result->error))
				{
					$response['msg'] =  $result->message;
					$response['error'] = '1';
				}
				
			}
		
		if($response['error']) {
			$this->setError($response['msg']);
			return false;
		}
		return true;		
	
}


/**
	 * This method occurs after payment is attempted,
	 * and fires the onPostPayment plugin event
	 *
	 * @return unknown_type
	 */
	function confirmPayment()
	{
		$orderpayment_type = JRequest::getVar('orderpayment_type');

		// Get post values
		$values = JRequest::get('post');

		// get the order_id from the session set by the prePayment
		$app =& JFactory::getApplication();
		$orderpayment_id = (int) $app->getUserState( 'k2store.orderpayment_id' );
		$order_link = 'index.php?option=com_k2store&view=orders&task=view&id='.$orderpayment_id;

		$dispatcher =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('k2store');
		
		$html = "";
		$order =& $this->_order;
		$order->load( array('id'=>$orderpayment_id));

		if ( (!empty($orderpayment_id)) && (float) $order->order_total == (float)'0.00' )
		{
			$order->order_state_id = '1'; // PAYMENT RECEIVED
			$order->save();

		}
		else
		{
			// get the payment results from the payment plugin
			$results = $dispatcher->trigger( "onK2StorePostPayment", array( $orderpayment_type, $values ) );

			// Display whatever comes back from Payment Plugin for the onPrePayment
			for ($i=0; $i<count($results); $i++)
			{
				$html .= $results[$i];
			}
			
			// re-load the order in case the payment plugin updated it
			$order->load( array('id'=>$orderpayment_id) );
		}

		// $order_id would be empty on posts back from Paypal, for example
		if (!empty($orderpayment_id))
		{
			// Set display
			$view = $this->getView( 'checkout', 'html' );
			$view->setLayout('postpayment');
			$view->set( '_doTask', true);
			$view->assign('order_link', $order_link );
			$view->assign('plugin_html', $html);

			// Get and Set Model
			$model = $this->getModel('checkout');
			$view->setModel( $model, true );

			// get the articles to display after checkout
			$articles = array();
		
			$view->display();
		}
		return;
	}
	
	
	
	

}
?>
