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

require_once(JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models'.DS.'_base.php' );

class K2StoreModelOrders extends K2StoreModelBase {
	
	public function getList()
	{
	    if (empty( $this->_list ))
	    {
			 $list = parent::getList();
			if( empty( $list ) ){
                return array();
            }
         $this->_list = $list;    
		}
		
		return $this->_list;
	}
	
	
   protected function _buildQueryWhere(&$query)
    {
        $filter     = $this->getState('filter');
       	$filter_orderstate	= $this->getState('filter_orderstate');
       	$filter_userid	= $this->getState('filter_userid');
        $filter_user	= $this->getState('filter_user');
        $filter_ordernumber    = $this->getState('filter_ordernumber');
        $filter_orderstates = $this->getState('filter_orderstates');
        
       	if ($filter)
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();

			$where[] = 'LOWER(tbl.order_id) LIKE '.$key;
			$where[] = 'LOWER(ui.first_name) LIKE '.$key;
			$where[] = 'LOWER(ui.last_name) LIKE '.$key;
			$where[] = 'LOWER(u.email) LIKE '.$key;
			$where[] = 'LOWER(u.username) LIKE '.$key;
			$where[] = 'LOWER(u.name) LIKE '.$key;

			$query->where('('.implode(' OR ', $where).')');
       	}
        
        
    	if (strlen($filter_user))
        {
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_user ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(ui.first_name) LIKE '.$key;
			$where[] = 'LOWER(ui.last_name) LIKE '.$key;
			$where[] = 'LOWER(u.email) LIKE '.$key;
			$where[] = 'LOWER(u.username) LIKE '.$key;
			$where[] = 'LOWER(u.name) LIKE '.$key;
			$where[] = 'LOWER(u.id) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
       	}

        if (strlen($filter_orderstate))
        {
            $query->where('tbl.order_state_id = '.$this->_db->Quote($filter_orderstate));
        }
       	
        if (is_array($filter_orderstates) && !empty($filter_orderstates))
        {
            $query->where('tbl.order_state_id IN('.implode(",", $filter_orderstates).')' );
        }
        
        if (strlen($filter_userid))
        {
            $query->where('tbl.user_id = '.$this->_db->Quote($filter_userid));
        }
      
    }
    
	protected function _buildQueryFields(&$query)
	{
		$field = array();

		$field[] = " tbl.* ";
		$field[] = " u.name AS user_name ";
		$field[] = " u.username AS user_username ";	
		$field[] = " u.email ";
		$field[] = " ui.address_1 ";
		$field[] = " ui.address_2 ";
		$field[] = " ui.city ";
		$field[] = " ui.zip ";
		$field[] = " ui.state ";
		$field[] = " ui.country ";
		$field[] = " ui.phone_1 ";
		$field[] = " ui.phone_2 ";
		$field[] = " ui.fax ";
		$field[] = " ui.first_name as first_name";
		$field[] = " ui.last_name as last_name";		
		
        $field[] = "
            (
            SELECT 
                COUNT(*)
            FROM
                #__k2store_orderitems AS items 
            WHERE 
                items.order_id = tbl.order_id 
            ) 
            AS items_count 
        ";

		$query->select( $field );
	}
	
	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__k2store_address AS ui ON ui.user_id = tbl.user_id');
		$query->join('LEFT', '#__users AS u ON u.id = tbl.user_id');        
	}

    protected function _buildQueryOrder(&$query)
    {
		$order      = $this->_db->getEscaped( $this->getState('order') );
       	$direction  = $this->_db->getEscaped( strtoupper($this->getState('direction') ) );
		if ($order)
		{
       		$query->order("$order $direction");
       	}
       	else
       	{
            $query->order("tbl.id ASC");
       	}
    }	
	
	public function getItem($emptyState=true)
	{
	    if (empty( $this->_item ))
	    {
	       
            JModel::addIncludePath( JPATH_SITE.DS.'components'.DS.'com_k2store'.DS.'models' );
            
            $query = $this->getQuery();
			// TODO Make this respond to the model's state, so other table keys can be used
			// perhaps depend entirely on the _buildQueryWhere() clause?
			$keyname = $this->getTable()->getKeyName();
			$value	= $this->_db->Quote( $this->getId() );
			$query->where( "tbl.$keyname = $value" );
			$this->_db->setQuery( (string) $query );
			$item = $this->_db->loadObject();
            if ($item)
            {
              
                //retrieve the order's items
                $model = JModel::getInstance( 'OrderItems', 'K2StoreModel' );
                $model->setState( 'filter_orderid', $item->order_id);
                $model->setState( 'order', 'tbl.orderitem_name' );
                $model->setState( 'direction', 'ASC' );
                $item->orderitems = $model->getList();
                foreach ($item->orderitems as $orderitem)
                {
                    $model = JModel::getInstance( 'OrderItemAttributes', 'K2StoreModel' );
                    $model->setState( 'filter_orderitemid', $orderitem->orderitem_id);
                    $attributes = $model->getList();
                    $attributes_names = array();
                    $attributes_codes = array();
                    foreach ($attributes as $attribute)
                    {
                        // store a csv of the attrib names
                        $attributes_names[] = JText::_( $attribute->orderitemattribute_name );
                        if($attribute->orderitemattribute_code) 
                            $attributes_codes[] = JText::_( $attribute->orderitemattribute_code );
                    }
                    $orderitem->attributes_names = implode(', ', $attributes_names);
                    $orderitem->attributes_codes = implode(', ', $attributes_codes);
                    
                    // adjust the price
                    $orderitem->orderitem_price = $orderitem->orderitem_price + floatval($orderitem->orderitem_attributes_price);
                }
              
              
            }
            
            $this->_item = $item;
	    }
		
        return $this->_item;
	}		
	
	
	
}
