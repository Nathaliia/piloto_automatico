var datosventana;


function crearVentanaRedeban(data){

    var width = jQuery(window).width();
    var height = jQuery(window).height();
 

debugger;

var tipoTerminal= jQuery(data).find('input[name="ptipoTerminal"]').val();
var idTerminal= jQuery(data).find('input[name="pidTerminal"]').val();
var idAdquiriente= jQuery(data).find('input[name="pidAdquiriente"]').val();
idTransaccionTerminal= jQuery(data).find('input[name="pidTransaccionTerminal"]').val();
idTransaccionActual= jQuery(data).find('input[name="pidTransaccionActual"]').val();
numeroFactura= jQuery(data).find('input[name="pnumeroFactura"]').val();
montoTotal= jQuery(data).find('input[name="pmontoTotal"]').val();
var funcion= jQuery(data).find('input[name="pfuncion"]').val();
var descRespuesta= jQuery(data).find('input[name="pdescRespuesta"]').val();
var codRespuesta= jQuery(data).find('input[name="pcodRespuesta"]').val();

 debugger;

    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

         

      eventer(messageEvent,function(e) {
                //alert(e.origin);
      datosventana=e.data;     
      parent.SqueezeBox.close(e);
      
         

    

         

    },false);


  SqueezeBox.addEvent('onClose', function(e) {

   
   debugger;

   
    jQuery('input[name="idTransaccionActual"]').val(idTransaccionActual);

      if(datosventana=='Finalizacion'){
        jQuery('.htmlContent').html(' ');

           var pUrl= urlredeban
           jQuery('.htmlContent').append('<p><img src='+imagenajax+' /></p>');

           jQuery.ajax({

                    type: "POST",
                    url:pUrl,
                    data:{funcion:'1', idTransaccionTerminal:idTransaccionTerminal,idTransaccionActual:idTransaccionActual, numeroFactura:numeroFactura , montoTotal:montoTotal },
                    success: function (data) {

                    debugger; 
                    jQuery('.htmlContent').html(data);
                    //jQuery('.htmlContent').html(' <div class="vrcvordudata">'+data+'</div>');

                       



                   
                    }
            });
       }

      if(datosventana=='Cancelacion'){

        jQuery('input[name="idTransaccionActual"]').val(idTransaccionActual);


      }

      if(datosventana=='Error'){

          jQuery('input[name="idTransaccionActual"]').val(idTransaccionActual);

      }

      
   });

if(funcion=='error'){

alert('Error con Servidor de Pagos');

}else{

/*SqueezeBox.initialize({
size: {x: 750, y: 400}
});*/
//SqueezeBox.setContent('iframe','//www.pagosrbm.com:8443/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal=ESB10071&idTransaccion=<?php echo $idTransaccionActual; ?>');
//SqueezeBox.resize({x: 750, y: 400})
//
//
if(funcion=='Rechazado'){
debugger;

SqueezeBox.initialize({
size: {x: 400, y: 80}


});

var newElem = new Element( 'div' );
//newElem.setStyle('border', 'solid 2px black');
//newElem.setStyle('width', '100px');
//
        if(codRespuesta=='9002' || codRespuesta=='9003' || codRespuesta=='9004'|| codRespuesta=='9006'){

         


        /*newElem.appendText("Código de Error:"+codRespuesta);
        newElem.appendChild(document.createElement("br"));
        newElem.appendText("Descripción Respuesta: "+descRespuesta);
        newElem.appendChild(document.createElement("br"));
        newElem.appendText("Estado de Transacción: "+funcion);
        newElem.appendChild(document.createElement("br"));

        SqueezeBox.setContent('adopt', newElem);
        SqueezeBox.resize({x: 400, y: 80});*/

         var errmsg = 'Error: '+codRespuesta+','+ descRespuesta+', Estado Transacción:'+funcion; 
         mostrarMensaje('<div class="err" style="font-size:12px;">'+errmsg+'</div>', 'Error', '1');

        }else{


        //intentar de nuevo
        SqueezeBox.open("https://www.pagosrbm.com/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal="+idTerminal+"&idTransaccion="+idTransaccionActual, {
        handler: 'iframe', 
        size: { x: (width-(width*0.5)), y: (height-(height*0.1)) }
        });

        }




}else{
        debugger;
        SqueezeBox.open("https://www.pagosrbm.com/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal="+idTerminal+"&idTransaccion="+idTransaccionActual, {
        handler: 'iframe', 
        size: { x: (width-(width*0.5)), y: (height-(height*0.1)) }

        });

}
}




}

function dopayment(){

  

  

}



jQuery(document).ready(function() {







       


});


