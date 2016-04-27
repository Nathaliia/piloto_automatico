var rowlist;
var list_id;


jQuery(document).ready(function() {


Fabrik.addEvent('fabrik.list.row.selected', function(list){
    

    rowlist=  list.rowid;

    list_id =list.listid;


  });

/*jQuery('.fabrik_action').on('click', 'a', function(event) {

      debugger;

                              event.preventDefault();
                              debugger;
                              SqueezeBox.initialize();
                              SqueezeBox.addEvent('onClose', function(e) {

                              //location.reload();


                              });
                              var ur= jQuery(this).attr('href');
                              SqueezeBox.open(jQuery(this).attr('href'),{
                              handler: 'iframe', 
                              size: { x: 1100, y: 600 }

                              });
 

                              });*/

var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var mEvent = eventMethod == "attachEvent" ? "submit" : "submit";

 eventer(mEvent,function(e) {

      debugger;

      e.preventDefault();

     if(jQuery('#inputPlanilla').val()==''){

            alert('Campo Planilla no puede ser vacio');

            return false;
      }


      var datos =jQuery('#formPlanilla').serialize();
      var url =jQuery('#formPlanilla').attr('action');

      datos =datos+'&rowlist='+rowlist+'&format=raw';

        jQuery.ajax({

        type: "POST",
        url:url,
        data:datos,
        success: function (data) {

          var response = jQuery.trim( data );
          
          
          parent.SqueezeBox.close(e);
            

            jQuery('#list_20_com_fabrik_20_row_'+rowlist).find('.chun4_vikrentcar_orders___planilla').html(response);

          
                   
          }
        });
     

      


 });

  
   jQuery('.fabrik_action').on('click', 'a.php-0', function(event) {

            debugger;



            event.preventDefault();
            debugger;
            var  url="index.php?option=com_vikrentcar";

            jQuery.ajax({

                    type: "POST",
                    url:url,
                    data:{task:'formPlanilla', format:'raw'},
                    success: function (data) {

                        debugger;

                       newElement =  jQuery(data);
                       
                        SqueezeBox.initialize({size: {x: 350, y: 400}});
                        SqueezeBox.addEvent('onClose', function(e) {

                        });

                        SqueezeBox.setContent('string', newElement);
                        SqueezeBox.resize({x: 360, y: 380});
                       
                        SqueezeBox.open();
                      

                       



                   
                    }
            });

           

           
           

     });
           


     


});

