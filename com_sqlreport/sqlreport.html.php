<?php

/**
 * @category	Core
 * @package		SQL Reports
 * @copyright 	(C) 2006-2010 Components Lab, Lda. - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

class HTML_Report
{
	function showReports($rows, $option)
	{ ?>
		<div class="componentheading">Reports</div>
		
		<?php
		foreach($rows as $row)
		{
			$link = JRoute::_( 'index.php?option='.$option.'&task=report&id='.$row->id );
			echo '<h4><a href="'.$link.'">'.$row->title.'</a></h4>'.$row->description.'<br /><hr />';
		} 
		if( count($rows) == 0 ) {
			echo 'No reports available!';
		} ?>
		<?php
	}

	function executeReport( $sql, &$rows, $table_header, $fields_name, $table, $filters_save ) { 
		$db =& JFactory::getDBO(); 
		$user =& JFactory::getUser(); ?>
		
		<script type="text/javascript" src="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/js/jquery.tablesorter.pack.js"></script>
		<script type="text/javascript" src="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/js/interface.js"></script>
		<link rel="stylesheet" href="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/blue/style.css" type="text/css" media="print, projection, screen" />
		<link rel="stylesheet" href="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/style.css" type="text/css" media="print, projection, screen" />
		<script> jQuery.noConflict(); </script>
		
		<script>
		function printReport()
		{
			window.open( "<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/ajax/print.php?table=<?php echo $table; ?>&fields=<?php echo str_replace(',','|',$fields_name); ?>&filters=<?php echo $filters_save; ?>&username=<?php echo $user->username; ?>&user_id=<?php echo $user->id; ?>&usertype=<?php echo $user->usertype; ?>", "PrintReport", "height=500,width=500,resizable=1,scrollbars=yes,toolbar=yes" );
		}
		</script>
		
		<div style="width:100%">
			<div style="float:left;">
				<a href="index.php?option=com_sqlreport&Itemid=<?php echo JRequest::getVar( 'Itemid', '', '', 'int' ); ?>"><img src="administrator/components/com_sqlreport/images/database_24x24.png" align="absmiddle" border="0" alt="Home" /> Home</a>
				&nbsp;&nbsp;&nbsp;
				<div id="loading" name="loading" style="display:none;"><img src="administrator/components/com_sqlreport/images/progressbar.gif" border="0" alt="Loading" /></div>
			</div>
			<div style="float:right;">
				<a href="javascript:;" onclick="javascript:printReport();"><img src="administrator/components/com_sqlreport/images/print_24x24.png" align="absmiddle" border="0" alt="Print Report" />  Print report</a>
				&nbsp;&nbsp;&nbsp;
				<a href="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/ajax/pdf.php?table=<?php echo $table; ?>&fields=<?php echo str_replace(',','|',$fields_name); ?>&filters=<?php echo $filters_save; ?>&username=<?php echo $user->username; ?>&user_id=<?php echo $user->id; ?>&usertype=<?php echo $user->usertype; ?>" target="_blank"><img src="administrator/components/com_sqlreport/images/print_24x24.png" align="absmiddle" border="0" alt="PDF Report" />  PDF report</a>
				&nbsp;&nbsp;&nbsp;
				<a href="<?php echo substr_replace(JURI::root(), '', -1, 1); ?>/administrator/components/com_sqlreport/ajax/cvs.php?table=<?php echo $table; ?>&fields=<?php echo str_replace(',','|',$fields_name); ?>&filters=<?php echo $filters_save; ?>&username=<?php echo $user->username; ?>&user_id=<?php echo $user->id; ?>&usertype=<?php echo $user->usertype; ?>" target="_blank"><img src="administrator/components/com_sqlreport/images/script_24x24.png" align="absmiddle" border="0" alt="CVS Report" />  CVS report</a>
			</div>
		</div>
		<div style="clear:both;"></div>
			
		<table class="tablesorter" id="myTable">
		<thead> 
		<tr>     
			<?php echo $table_header; ?>
		</tr> 
		</thead> 
		<tbody><?php
		for( $i=0; $i<count($rows); $i++ )
		{
			$row = $rows[$i];
			
			print '<tr>';
			$fields = explode( ',', $fields_name );
			for( $z=0; $z<count($fields); $z++ )
			{
				print '<td>'.$row->$fields[$z].'</td>';
			}
			print '</tr>';
		}
		?>
		</tbody> 
		</table><?php
	}
}
?>
