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


function getOrderSummary()
	{

	    $params = &JComponentHelper::getParams('com_k2store');
		$model = $this->getModel('mycart');
		$view = $this->getView( 'mycart', 'html' );	
		$view->set( '_view', 'mycart' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->assign( 'items', $model->get('Data'));
		$view->assign( 'params', $params );
		$view->assign( 'summary', true );
		$view->setLayout( 'ajax' );

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

Class K2StoreUtilities {

	function number($amount, $options='')
    {
        // default to whatever is in config
		$config = &JComponentHelper::getParams('com_k2store');
        $options = (array) $options;
        $post = '';
        $pre = '';
        
        $default_currency = $config->get('currency_code', 'USD');
        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : $config->get('currency_num_decimals', '2');
        $thousands = isset($options['thousands']) ? $options['thousands'] : $config->get('currency_thousands', ',');
        $decimal = isset($options['decimal']) ? $options['decimal'] : $config->get('currency_decimal', '.');
        $currency_symbol = isset($options['currency']) ? $options['currency'] : $config->get('currency', '$');
        $currency_position = isset($options['currency_position']) ? $options['currency_position'] : $config->get('currency_position', 'pre');
        if($currency_position == 'post') {
			$post = $currency_symbol;
		} else {
			$pre = $currency_symbol;
		}
		
        //$return = $pre.number_format($amount, $num_decimals, $decimal, $thousands).$post;
        $return = number_format($amount, $num_decimals, $decimal, $thousands);
        return $return;
    }
	
	/**
	 * getItemName() - Get the name of an item.
	 *
	 * @param string $order_code The order code of the item.
	 */
	function getItemName($order_code) {
		$db		=& JFactory::getDBO();
		
		$query= "SELECT * FROM #__k2_items WHERE id=".$order_code;
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->title;
	}


}


?>

