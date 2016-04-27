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

class K2StoreControllerOrders extends JController
{
	
	function __construct()
	{
        if (empty(JFactory::getUser()->id))
        {
            $url = JRoute::_( "index.php?option=com_k2store&view=orders" );
            $redirect = "index.php?option=com_user&view=login&return=".base64_encode( $url );
            $redirect = JRoute::_( $redirect, false );
            JFactory::getApplication()->redirect( $redirect );
            return;
        }
		parent::__construct();
		
	}	
	
	function display() {
		
		$app = &JFactory::getApplication();
		$user = &JFactory::getUser();
			
		$params = &JComponentHelper::getParams('com_k2store');
		$model  = $this->getModel('orders');
		$ns = 'com_k2store.orders';
		
		$state = $this->_setModelState();
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		//$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$limitstart	= (empty($_GET['limitstart'])) ? 0 : $app->getUserStateFromRequest($ns.'.limitstart', 'limitstart', 0, 'int');
	
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$state['limit']  	= $limit;
		$state['limitstart'] = $limitstart;
		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.'.$model->getTable()->getKeyName(), 'cmd');
		$state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
		
		 foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );   
        }
		
		$model->setState('filter_userid', $user->id);
		$orders = $model->getList();
		$view = $this->getView( 'orders', 'html' );
		$view->set( '_controller', 'orders' );
        $view->set( '_view', 'orders' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $model, true );
        $view->assign( 'state', $model->getState());
        $view->assign( 'orders', $orders );
        $view->assign( 'params', $params );
		$view->assign( 'pagination', $model->getPagination());
		$view->setLayout( 'default' );
		$this->_setModelState();
		$view->display();	
		
	}
	
	
	 function view() 
    {
    	// if the user cannot view order, fail
        $model  = $this->getModel('orders');
        $order = $model->getTable( 'orders' );
        $order->load( $model->getId() );
        $orderitems = &$order->getItems();
                
        $row = $model->getItem();      
        $user_id = JFactory::getUser()->id;
        if (empty($user_id) || $user_id != $row->user_id)
        {
        	$this->messagetype  = 'notice';
        	$this->message      = JText::_( 'Invalid Order' );
            $redirect = "index.php?option=com_k2store&view=orders";
            $redirect = JRoute::_( $redirect, false );
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }
        
        //Tienda::load( 'TiendaUrl', 'library.url' );
        
        $view = $this->getView( 'orders', 'html' );
        $view->set( '_controller', 'orders' );
        $view->set( '_view', 'orders' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $model, true );
        $view->assign( 'row', $row );
		$params = &JComponentHelper::getParams('com_k2store');
		$show_tax = $params->get('show_tax_total');
        $view->assign( 'show_tax', $show_tax );
        foreach ($orderitems as &$item)
        {
      		$item->orderitem_price = $item->orderitem_price + floatval( $item->orderitem_attributes_price );       		
        	$taxtotal = 0;
            if($show_tax)
            {
            	$taxtotal = ($item->orderitem_tax / $item->orderitem_quantity);            	
            }                   
            $item->orderitem_price = $item->orderitem_price + $taxtotal;
            $item->orderitem_final_price = $item->orderitem_price * $item->orderitem_quantity;
            $order->order_subtotal += ($taxtotal * $item->orderitem_quantity);    
        }      
              
        $view->assign( 'order', $order );
        $view->assign( 'params', $params );
        $view->setLayout( 'view' );
        $this->_setModelState();
        $view->display();        
    }
    
    
    
     function printOrder() 
    {
    	// if the user cannot view order, fail
        $model  = $this->getModel('orders');
        $order = $model->getTable( 'orders' );
        $order->load( $model->getId() );
        $orderitems = &$order->getItems();
                
        $row = $model->getItem();      
        $user_id = JFactory::getUser()->id;
        if (empty($user_id) || $user_id != $row->user_id)
        {
        	$this->messagetype  = 'notice';
        	$this->message      = JText::_( 'Invalid Order' );
            $redirect = "index.php?option=com_k2store&view=orders";
            $redirect = JRoute::_( $redirect, false );
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }
        
        //Tienda::load( 'TiendaUrl', 'library.url' );
        
        $view = $this->getView( 'orders', 'html' );
        $view->set( '_controller', 'orders' );
        $view->set( '_view', 'orders' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $model, true );
        $view->assign( 'row', $row );
		$params = &JComponentHelper::getParams('com_k2store');
		$show_tax = $params->get('show_tax_total');
        $view->assign( 'show_tax', $show_tax );
        foreach ($orderitems as &$item)
        {
      		$item->orderitem_price = $item->orderitem_price + floatval( $item->orderitem_attributes_price );       		
        	$taxtotal = 0;
            if($show_tax)
            {
            	$taxtotal = ($item->orderitem_tax / $item->orderitem_quantity);            	
            }                   
            $item->orderitem_price = $item->orderitem_price + $taxtotal;
            $item->orderitem_final_price = $item->orderitem_price * $item->orderitem_quantity;
            $order->order_subtotal += ($taxtotal * $item->orderitem_quantity);    
        }      
              
        $view->assign( 'params', $params );
        $view->assign( 'order', $order );
        $view->setLayout( 'print' );
        $this->_setModelState();
        $view->display();        
    }
	
    
	
	   function _setModelState()
    {
	    $app = JFactory::getApplication();
	    $params = &JComponentHelper::getParams('com_k2store');
        $model = $this->getModel('orders');
        $ns = 'com_k2store.orders';
	
		$state = array();
		$state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$state['limitstart'] = (empty($_GET['limitstart'])) ? 0 : $app->getUserStateFromRequest($ns.'.limitstart', 'limitstart', 0, 'int');
		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.'.$model->getTable()->getKeyName(), 'cmd');
		$state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
		$state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
		$state['filter_enabled'] 	= $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
		$state['id']        = JRequest::getVar('id', JRequest::getVar('id', '', 'get', 'int'), 'post', 'int');


        // adjust offset for when filter has changed
        if (
            $app->getUserState( $ns.'orderstate' ) != $app->getUserStateFromRequest($ns.'orderstate', 'filter_orderstate', '', '') 
        )
        {
            $state['limitstart'] = '0';
        }

        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.created_date', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
        
        $state['filter_orderstate'] = $app->getUserStateFromRequest($ns.'orderstate', 'filter_orderstate', '', 'string');
        
        $state['filter_userid']     = JFactory::getUser()->id;
        $filter_userid = $app->getUserStateFromRequest($ns.'userid', 'filter_userid', JFactory::getUser()->id, 'int');
        
        $state['filter_total']      = $app->getUserStateFromRequest($ns.'total', 'filter_total', '', 'float');
      
        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );   
        }
        return $state;
    }
    
	
}
?>
