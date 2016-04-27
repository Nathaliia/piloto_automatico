<?php
/**
 * @package Unite Revolution Slider for Joomla 1.7-2.5
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;


class UniteRevolutionController extends JControllerUniteBaseRev
{
	
	/**
	 * show some image
	 */
	public function showimage(){
		UniteFunctionJoomlaRev::showImageFromRequest();
		exit();
	}
	
	
	/**
	 * 
	 * get css of some slider
	 */
	public function getcss(){
		
		dmp("output css");
		exit();
	}
	
	
	/**
	 * 
	 * default display function
	 */
	public function display($cachable = false, $urlparams = array()){
		echo "nothing here";
		exit();
	}
	
}