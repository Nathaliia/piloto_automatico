/**
 * @author Robert
 */
var table;

	jQuery.noConflict();
		jQuery(document).ready(function() {



		table =jQuery('.display.responsive').DataTable({
		"aLengthMenu": [[5,10, 15, 50, 75, -1,], [5,10, 15, 50, 75, "Todos"]],
		dom: 'lfBrtip',
		buttons: [
		'copy',  'excel'
		]
		
		

		});

		
	   

		

		







		});
