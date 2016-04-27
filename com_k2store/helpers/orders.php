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

class K2StoreOrdersHelper {
	
	
	  function sendUserEmail($user_id, $order_id, $payment_status, $order_status, $order_state_id)
    {
        $mainframe =& JFactory::getApplication();
                
        // grab config settings for sender name and email
        $config     = &JComponentHelper::getParams('com_k2store');
        $k2params = &JComponentHelper::getParams('com_k2');
        $mailfrom   = $config->get( 'emails_defaultemail', $mainframe->getCfg('mailfrom') );
        $fromname   = $config->get( 'emails_defaultname', $mainframe->getCfg('fromname') );
     
       
        $sitename   = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $siteurl    = $config->get( 'siteurl', JURI::root() );
        
        //now get the order table's id based on order id
        $id = K2StoreOrdersHelper::_getOrderKey($order_id);
           

        $html = K2StoreOrdersHelper::_getHtmlFormatedOrder($id, $user_id);
     
        
        $ourl = $siteurl.'index.php?option=com_k2store&view=orders&task=view&id='.$id;
        
       $recipient = K2StoreOrdersHelper::_getUser($user_id);
   
        $mailer =& JFactory::getMailer();
        $mode = 1;
        
        $subject = JText::sprintf('ORDER USER EMAIL SUB', $recipient->name, $sitename);
        
        $msg = '';
        $msg .= JText::sprintf('ORDER PLACED_HEADER', $recipient->name, $sitename);
        $msg .= $html;
        $msg .= JText::sprintf('ORDER PLACED_FOOTER', $ourl);
       //send attachments as well
        
        //allow_attachment_downloads
        
        //attachements
        
        //send attachments, only when the order state is confirmed and attachments are allowed
        if ($config->get('allow_attachment_downloads'))  {      
	        if ($order_state_id == 1) {
			
	        $attachments = K2StoreOrdersHelper::getAttachments($order_id);
	        
	        $path = $k2params->get('attachmentsFolder', NULL);
			if (is_null($path)) {
            $savepath = JPATH_ROOT.DS.'media'.DS.'k2'.DS.'attachments';
			} else {
            $savepath = $path;
			}
			
	        
			if (count($attachments)>0) {
				$msg .='<br />----------------------------------------------------------------------------------------------------------- <br />';
				$msg .= JText::_('The attached file(s) to this email').': <br />';
				foreach($attachments as $attachment) {
					$myfile = trim($attachment->titleAttribute);
					$msg .= 'File: '.$myfile.'<br />';
					$att = $savepath.DS.$myfile;
					$mailer->addAttachment($att);
				}//foreach
			}//if count
	        
			}
		}       
      
        if ($recipient) 
        {           
            
            $mailer->addRecipient($recipient->email);
            $mailer->addCC( $config->get('admin_email'));
            $mailer->setSubject( $subject );
            $mailer->setBody($msg);
            $mailer->IsHTML($mode);          
            $mailer->setSender(array( $mailfrom, $fromname ));
         	$mailer->send();
       }

        return true;
    }
    
    
    
    function _getUser($uid)
    {
	
        $db =& JFactory::getDBO();
        $q = "SELECT name, email FROM #__users "
           . "WHERE id = {$uid}"
           ;
       
        $db->setQuery($q);
        $user_email = $db->loadObject();
            
        if ($error = $db->getErrorMsg()) {
            JError::raiseError(500, $error);
            return false;
        }

        return $user_email;               
    }
    
    
    function getAttachments($order) {

		global $mainframe;
		$db =& JFactory::getDBO();
 		$all_attachments = Array(); 

		//get all the items for this order
		$query = "SELECT * FROM #__k2store_orderitems WHERE order_id=".$order;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		//if no items found then exit now!
		if ($items==0) {
		  return $all_attachments;  //return empty array
		}

		//loop through items, generating a list of attachments
		foreach($items as $item) {
			$sql = "SELECT * FROM #__k2_attachments WHERE itemID =".$item->product_id;
			$db->setQuery( $sql );
			$attachments = $db->loadObjectList();
			//accumulate all attachments into one big array
		  $all_attachments = array_merge($all_attachments, (array)$attachments);
		}//foreach
		
		//ok, all done - return the resulting array of attachments
		return $all_attachments;		
	}//function getOrderAttachments
   
   
   function _getOrderKey($order_id) {
	   
	   $db = &JFactory::getDBO();
	   $query = 'SELECT id FROM #__k2store_orders WHERE order_id='.$db->Quote($order_id);
	   $db->setQuery($query);
	   return $db->loadResult();   	   
   }
   
   
   function _getHtmlFormatedOrder($id, $user_id) {
	   
		$app = &JFactory::getApplication();
		$html = ' ';
		
	    JLoader::register( "K2StoreViewOrders", JPATH_SITE."/components/com_k2store/views/orders/view.html.php" );
	    
	     $config = array();
		 $config['base_path'] = JPATH_SITE."/components/com_k2store";  
        if ($app->isAdmin())
        {
            // finds the default Site template
            $db = JFactory::getDBO();
            $query = "SELECT template FROM #__templates_menu WHERE `client_id` = '0' AND `menuid` = '0';";
            $db->setQuery( $query );
            $template = $db->loadResult();
            
            jimport('joomla.filesystem.file');
            if (JFile::exists(JPATH_SITE.'/templates/'.$template.'/html/com_k2store/orders/orderemail.php'))
            {
                // (have to do this because we load the same view from the admin-side Orders view, and conflicts arise)            
                $config['template_path'] = JPATH_SITE.'/templates/'.$template.'/html/com_k2store/orders';                
            }
        }
        
        $view = new K2StoreViewOrders( $config );
		
		require_once(JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models'.DS.'orders.php');		
		$model =  new K2StoreModelOrders();
	    //lets set the id first
        $model->setId($id);

        $order = $model->getTable( 'orders' );
        $order->load( $model->getId() );       
        $row = $model->getItem();
        
        if (empty($user_id) || $user_id != $row->user_id)
        {
            return $html;
        }
        
	    $view->set( '_controller', 'orders' );
        $view->set( '_view', 'orders' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $model, true );
        $view->assign( 'row', $row );
		$params = &JComponentHelper::getParams('com_k2store');
		$show_tax = $params->get('show_tax_total');
        $view->assign( 'show_tax', $show_tax );              
        $view->assign( 'order', $order );
        $view->assign( 'params', $params );
        $view->setLayout( 'orderemail' );
       
        //$this->_setModelState();
        ob_start();
        $view->display();      
        $html .= ob_get_contents();
        ob_end_clean();   
        return $html;	   
   }
}
?>
