<?php

/**
 * @category	Core
 * @package		SQL Reports
 * @copyright 	(C) 2006-2010 Components Lab, Lda. - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function sqlreportBuildRoute(&$query)
{
  $segments = array();

  if(isset($query['task'])) 
  {
    $segments[] = $query['task'];		
    unset($query['task']);
  };
	
  if(isset($query['id']))
  {
    $segments[] = $query['id'];
    unset($query['id']);
  };

  return $segments;
}

function sqlreportParseRoute($segments)
{
  $vars = array();
  $vars['task']	= $segments[0];
  if( isset($segments[1]) ) {
	$vars['id'] = $segments[1];
  }
  return $vars;
}

?>
