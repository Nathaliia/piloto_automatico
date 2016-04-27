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

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

class K2StoreModelAddress extends JModel {

public function getList()
    {
        $list = parent::getList();
        
        // If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }
        
        foreach($list as $item)
        {
            $item->link = 'index.php?option=com_k2store&view=addresses&task=edit&id='.$item->address_id;
        }
        return $list;
    }
    
    
    public function getShippingAddress() {
		 
		$user =	& JFactory::getUser();
		$db = &JFactory::getDBO();
		
		$query = "SELECT * FROM #__k2store_address WHERE user_id={$user->id}";
		$db->setQuery($query);
		return $db->loadObject();
		 
	 } 
    
   public function save($post) {
	   
		$mainframe = &JFactory::getApplication();
		$db = &JFactory::getDBO();
        $user = &JFactory::getUser();
        
	 
		$row = &JTable::getInstance('Address', 'Table');
	
		if (!$row->bind($post)) {
           $mainframe->redirect('index.php?option=com_k2store&view=checkout', $row->getError(), 'error');
           return false;
        }
       
        $row->user_id = $user->id;
        
        if (!$row->check()) {
            $mainframe->redirect('index.php?option=com_k2store&view=checkout', $row->getError(), 'error');
            return false;
        }
        
        if (!$row->store()) {
            $mainframe->redirect('index.php?option=com_k2store&view=checkout', $row->getError(), 'error');
            return false;
        }
	    
	   return true;
	   
   }
   
}
