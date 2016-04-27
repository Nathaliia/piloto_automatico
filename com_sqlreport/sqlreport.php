<?php

/**
 * @category	Core
 * @package		SQL Reports
 * @copyright 	(C) 2006-2010 Components Lab, Lda. - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.helper');
require_once(JApplicationHelper::getPath( 'html' ));
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'tables');

$id = JRequest::getVar( 'id', '', '', 'int' );

switch( $task ){
	case 'report':
		executeReport($id);
		break;

	default:
		showReports($option);
		break;
}

function executeReport( $id )
{
	$db =& JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$row =& JTable::getInstance('sqlreport', 'Table');
	$row->load( $id );

	$document = & JFactory::getDocument();
	$document->setTitle( $row->title );
	
	// Fields
	$select = '';
	$table_header = '';
	$fields_name = '';
	$fields = explode( '|', $row->fields );
	for( $i=0; $i<count($fields); $i++ )
	{
		$table_header .= '<th>'.strtoupper($fields[$i]).'</th>';
		$fields_name  .= $fields[$i].',';
		$select  .= '`'.$fields[$i].'`,';
	}
	
	// Filters
	$filter = '';
	$filters_save = '';
	$filters = explode( '***', $row->filters );
	for( $i=0; $i<count($filters); $i++ )
	{
		$filter_line = explode( '|', $filters[$i] );
		$filter_field = $filter_line[0];
		
		if( $filter_field != '' ) {
			$filter_cond  = $filter_line[1];
			$filter_value = $filter_line[2];
		
			$filter .= '`'.$filter_field.'`'.$filter_cond."'".$filter_value."'".' AND ';
			$filters_save .= $filter_field.'|'.$filter_cond.'|'.$filter_value.'***';
		}
	}
	
	// Clean
	$filter = substr( $filter, 0, strlen($filter)-5 );
	$select = substr( $select, 0, strlen($select)-1 );
	$fields_name = substr( $fields_name, 0, strlen($fields_name)-1 );
	
	$sql = "SELECT ".$select." FROM ".$row->table.($filter!='' ? " WHERE ".$filter : "");
	$db->setQuery( $sql ); 
	$rows = $db->loadObjectList();

	$permission = 0;
	if( !$user->id && $row->permissions=='all' ) {
		$permission = 1;
	}
	if( $user->get('gid') < 19 && $user->id && ( $row->permissions=='all' || $row->permissions=='registered' ) ) {
		$permission = 1;
	}
	if( $user->get('gid') >= 19 ) {
		$permission = 1;
	}
	
	if( $permission ) {
		HTML_Report::executeReport( $sql, $rows, $table_header, $fields_name, $row->table, $filters_save );
	}
}

function showReports($option)
{
	$document = & JFactory::getDocument();
	$document->setTitle( "Reports" );

	$user =& JFactory::getUser();
	$db =& JFactory::getDBO();
	
	$sql = "SELECT id, title, description, permissions FROM #__sqlreport";

	if( !$user->id ) {
		$sql .= " WHERE permissions='all'";
	}
	if( $user->get('gid') < 19 && $user->id ) {
		$sql .= " WHERE permissions='all' OR permissions='registered'";
	}
	if( $user->get('gid') >= 19 ) {
		$sql .= "";
	}

	$db->setQuery( $sql ); 
	$rows = $db->loadObjectList();

	HTML_Report::showReports($rows, $option);
}

?>