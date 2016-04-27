/**
 * @author Robert
 */
head.ready(function() {
	Array.from($$('.fabrikList tr')).each(function(r){
		document.id(r).addEvent('mouseover', function(e){
			if (r.hasClass('oddRow0') || r.hasClass('oddRow1')){
				r.addClass('fabrikHover');
			}
		}, r);
		
		document.id(r).addEvent('mouseout', function(e){
			r.removeClass('fabrikHover');
		}, r);
		
		document.id(r).addEvent('click', function(e){
			if (r.hasClass('oddRow0') || r.hasClass('oddRow1')){
				$$('.fabrikList tr').each(function(rx){
					rx.removeClass('fabrikRowClick');
				});
				r.addClass('fabrikRowClick');
			}
		}, r);
	});
})


jQuery.noConflict();
	jQuery(document).ready(function() {

   //jQuery("#chun4_vikrentcar_profiles___doc_type").uniform();
   //jQuery('input[type=radio],select, input[type=checkbox], table', '.fabrikForm').uniform();
   //jQuery("#chun4_vikrentcar_profiles___doc_type").css('width','100%');
   	jQuery('#list_20_com_fabrik_20').DataTable();


   });


	
