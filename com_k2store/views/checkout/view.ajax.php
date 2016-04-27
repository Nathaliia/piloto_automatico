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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the K2Store component - ajax layout
 *
 * @static
 * @package		Joomla
 * @subpackage	K2Store
 * @since 1.0
 */
class K2StoreViewCheckout extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		$model		= &$this->getModel();
		
		//get data		
		$items	=& $this->get('Data');

		$this->assignRef('items',		$items);
		$this->assignRef('model',		$model);
		$this->setLayout( 'ajax' );
		parent::display($tpl);

		$mainframe->close();
		
		
	}

}
?>
