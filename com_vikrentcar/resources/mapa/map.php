<?php

define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../..' ));
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' ); 
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' ); 
require_once( JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php' );



$mainframe = JFactory::getApplication('site');
$mainframe->initialise();


$select = $_GET["select"];


$extraurban = $_GET["extraurban"];



$language = JFactory::getLanguage();

$language->load('mod_vikrentcar');

?>
<link rel="stylesheet" type="text/css" href="../style.css">
<script src="../jquery-1.8.2.min.js"></script>

<style>
#map-container {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    overflow-y: hidden;
}

#map-container #centered-marker {
    margin: auto;
    position: absolute;
    top: 0; left: 0; bottom: 0; right: 0;
}

#map-container #map-canvas {
    height: 100%;
}

h1 {

     font-family: Verdana, arial;
}

h3 {

    font-family: Verdana, arial;

}

.text{
    font-size: 14px;
    font-family: Verdana, arial;
}

input{
    width: auto;
   
    padding-top: 2px;
    padding-right: 2px;
    padding-bottom: 2px;
    border: 1px solid grey;
    color: black !important;
}
.wrapper2{
    position: relative;
}

.static{
    position: absolute;
    padding: 9px;
    white-space: nowrap;
}
</style>
<div id="map-container">
	<div id="map-canvas"></div>
</div>
<script>

	var map, lastAddressFound, iterator = 0, pos, mapZones = [], zones;
    var marker;
    var infowindow;
    var direcciontemp='';
    var zoneDisplayId;

    var select ="<?php echo $select;  ?>";
    var extraurban ="<?php echo $extraurban;  ?>";

    var titleMsgBox='<?php echo JText::_("VRTITLEBOX"); ?>';
    var estaDir= '<?php echo JText::_("VRESTADDBUTTOM"); ?>';
    var zonelabel= '<?php echo JText::_("VRZONELABEL"); ?>';

    var isiniUrban=false;
    var isfinUrban=false;

    var isiniExtraUrban=false;
    var isfinExtraUrban=false;

   

    var    contentString='';

       
   
    /*jQuery('#map-container').on('keypress', '#dirurbanaini', function() {

        
         
          var oldval= jQuery('#dirurbanaini').val();

          var datomarker=direcciontemp+'-';

          if(datomarker.indexOf(oldval)!=-1){


          }else{

            jQuery('#dirurbanaini').val(datomarker);

          }
    });*/

    
    

     jQuery('#map-container').on('click', '#dirurbanaini', function() {

        jQuery('#alertmsg').html('');


     });
           
    
    jQuery('#map-container').on('click', '#botonok', function() {


      

        

        var dirfinal= jQuery('#dirurbanaini').val();

        var dircompleta= dirfinal.split('-');

      

        if(dirfinal==''){

            jQuery('#alertmsg').html('<?php echo JText::_("VRMSGALERTMSG"); ?>');


        }else{

          if(select=='ini'){

            //detecta si es formulario de zonas o de extraurbana que es extraurban=1
            if(extraurban!=1){

                   

                parent.jQuery('#tipo_carro').val('Traslado Urbano').change();

                parent.jQuery('#zona_ini4b').val(zoneDisplayId).change();

                parent.jQuery('#zona_ini4b').val(zoneDisplayId).change();

                parent.jQuery('#labelfrom4b').html(' '+ zoneDisplayId);

                parent.jQuery('#add_ini4b').val(direcciontemp+'-'+dirfinal);

                var lattmp=  marker.getPosition().lat();
                var lngtmp=  marker.getPosition().lng();

                parent.jQuery('#latini4b').val(lattmp);
                parent.jQuery('#lngini4b').val(lngtmp);

                

                parent.jQuery("#add_ini4b").tooltipster('content', 'Zona '+zoneDisplayId+' :'+ direcciontemp+'-'+dirfinal);

                isiniUrban=true;

                if(zoneDisplayId=='Extraurbana'){

                    

                    parent.jQuery('#isEUini4b').val('1');

                    //if(parent.jQuery('#add_fin4b').val()!=''){
                    if(parseInt(parent.jQuery('#isEUini4b').val()) || parseInt(parent.jQuery('#isEUfin4b').val())){
                        //La combinación de zonas corresponde a un Traslado Extra-Urbano

                        alert('<?php echo JText::_("VRMSGZONAFUERACOBERTURA"); ?>');

                        parent.jQuery('#add_fin').val(parent.jQuery('#add_fin4b').val());
                        parent.jQuery('#add_ini').val(direcciontemp+'-'+dirfinal);


                        parent.jQuery('#isEUini').val(parent.jQuery('#isEUini4b').val());
                        parent.jQuery('#isEUfin').val(parent.jQuery('#isEUfin4b').val());

                        parent.jQuery('#tipo_carro').val('Traslado Extra-Urbano').change();

                        parent.jQuery('#latini').val(parent.jQuery('#latini4b').val());
                        parent.jQuery('#latfin').val(parent.jQuery('#latfin4b').val());

                        parent.jQuery('#lngini').val(parent.jQuery('#lngini4b').val());
                        parent.jQuery('#lngfin').val(parent.jQuery('#lngfin4b').val());

                        parent.jQuery("#add_ini").tooltipster( 'content', parent.jQuery("#add_ini4b").tooltipster('content'));
                        parent.jQuery("#add_fin").tooltipster('content', parent.jQuery("#add_fin4b").tooltipster('content'));

                        //parent.jQuery('#isEUini').val('1');

                        parent.jQuery("#labelfrom").html(parent.jQuery("#labelfrom4b").html());
                        parent.jQuery("#labelto").html(parent.jQuery("#labelto4b").html());


                        clearInputs('Traslado Urbano');

                        window.parent.SqueezeBox.close();


                    }

                 

                }else{

                     parent.jQuery('#isEUini4b').val('0');

                     /* if(parseInt(parent.jQuery('#isEUini4b').val()) || parseInt(parent.jQuery('#isEUfin4b').val())){

                         alert('Fuera de zonas de cobertura, \nPara esta zona seleccione Traslado Extra-Urbano');

                        parent.jQuery('#add_fin').val(parent.jQuery('#add_fin4b').val());
                        parent.jQuery('#add_ini').val(direcciontemp+'-'+dirfinal);

                        parent.jQuery('#tipo_carro').val('Traslado Extra-Urbano').change();

                        parent.jQuery('#latini').val(parent.jQuery('#latini4b').val());
                        parent.jQuery('#latfin').val(parent.jQuery('#latfin4b').val());

                        parent.jQuery('#lngini').val(parent.jQuery('#lngini4b').val());
                        parent.jQuery('#lngfin').val(parent.jQuery('#lngfin4b').val());

                        parent.jQuery("#add_ini").tooltipster( 'content', parent.jQuery("#add_ini4b").tooltipster('content'));
                        parent.jQuery("#add_fin").tooltipster('content', parent.jQuery("#add_fin4b").tooltipster('content'));

                        

                        parent.jQuery('#isEUini').val(parent.jQuery('#isEUini4b').val());
                        parent.jQuery('#isEUfin').val(parent.jQuery('#isEUfin4b').val());

                        window.parent.SqueezeBox.close();


                      }*/



                }

           
            }else{





                parent.jQuery('#tipo_carro').val('Traslado Extra-Urbano').change();

                

               // parent.jQuery('#labelfrom').html(' '+ zoneDisplayId);

                parent.jQuery('#add_ini').val(direcciontemp+'-'+dirfinal);

                parent.jQuery("#add_ini").tooltipster('content', 'Zona '+zoneDisplayId+' :'+ direcciontemp+'-'+dirfinal);

                parent.jQuery('#labelfrom').html( ' '+ zoneDisplayId);

                var lattmp=  marker.getPosition().lat();
                var lngtmp=  marker.getPosition().lng();

                parent.jQuery('#latini').val(lattmp);
                parent.jQuery('#lngini').val(lngtmp);


                 if(zoneDisplayId!='Extraurbana'){

                     isiniExtraUrban=true;

                     parent.jQuery('#isEUini').val('0');
                    
                      if((parseInt(parent.jQuery('#isEUini').val())==0 && parseInt(parent.jQuery('#isEUfin').val())==0)){
                     //if(parent.jQuery('#add_ini').val()!=''){
                     //Esta combinación de zonas corresponde a un Traslado Urbano
                           alert('<?php echo JText::_("VRMSGZONASURBANAS"); ?>');
                            parent.jQuery('#isEUini4b').val('1');

                            parent.jQuery('#add_ini4b').val(parent.jQuery('#add_ini').val());
                            parent.jQuery('#isEUini4b').val(parent.jQuery('#isEUini').val());
                            parent.jQuery('#add_fin4b').val(parent.jQuery('#add_fin').val());
                            parent.jQuery('#isEUfin4b').val(parent.jQuery('#isEUfin').val());

                            parent.jQuery('#latini4b').val(parent.jQuery('#latini').val());
                            parent.jQuery('#latfin4b').val(parent.jQuery('#latfin').val());

                            parent.jQuery('#lngini4b').val(parent.jQuery('#lngini').val());
                            parent.jQuery('#lngfin4b').val(parent.jQuery('#lngfin').val());

                            parent.jQuery('#tipo_carro').val('Traslado Urbano').change();

                            parent.jQuery("#add_ini4b").tooltipster( 'content', parent.jQuery("#add_ini").tooltipster('content'));
                            parent.jQuery("#add_fin4b").tooltipster('content', parent.jQuery("#add_fin").tooltipster('content'));

                            parent.jQuery("#labelfrom4b").html(parent.jQuery("#labelfrom").html());
                            parent.jQuery("#labelto4b").html(parent.jQuery("#labelto").html());

                            var zoneidini= getidZone( parent.jQuery('#lngini4b').val(),parent.jQuery('#latini4b').val());
                            var zoneidfin= getidZone( parent.jQuery('#lngfin4b').val(),parent.jQuery('#latfin4b').val());


                            parent.jQuery('#zona_ini4b').val(zoneidini).change();

                            parent.jQuery('#zona_fin4b').val(zoneidfin).change();

                            clearInputs('Traslado Extra-Urbano');
                        }

                     


                 }else{

                    parent.jQuery('#isEUini').val('1');

                    if(parseInt(parent.jQuery('#isEUini4b').val()) || parseInt(parent.jQuery('#isEUfin4b').val())){

                         alert('<?php echo JText::_("VRMSGZONASURBANAS"); ?>');


                    }


                 }

                

                //parent.jQuery("#add_ini").tooltipster('content', direcciontemp+'-'+dirfinal);



            }


                   

            }

            if(select=='fin'){


                if(extraurban!=1){

                    parent.jQuery('#tipo_carro').val('Traslado Urbano').change();
                   

                    parent.jQuery('#zona_fin4b').val(zoneDisplayId).change();

                    parent.jQuery('#labelto4b').html( ' '+ zoneDisplayId);

                    parent.jQuery('#add_fin4b').val(direcciontemp+'-'+dirfinal);

                    var lattmp=  marker.getPosition().lat();
                    var lngtmp=  marker.getPosition().lng();

                    parent.jQuery('#latfin4b').val(lattmp);
                    parent.jQuery('#lngfin4b').val(lngtmp);

                    parent.jQuery("#add_fin4b").tooltipster('content','Zona '+zoneDisplayId+' :'+ direcciontemp+'-'+dirfinal);

                    isfinUrban=true;

                     if(zoneDisplayId=='Extraurbana'){

                        //if(parent.jQuery('#add_ini4b').val()!=''){

                          parent.jQuery('#isEUfin4b').val('1');

                             //if(parent.jQuery('#add_fin4b').val()!=''){
                            if(parseInt(parent.jQuery('#isEUini4b').val()) || parseInt(parent.jQuery('#isEUfin4b').val())){
                            //La combinación de zonas corresponde a un Traslado Extra-Urbano

                            alert('<?php echo JText::_("VRMSGZONAFUERACOBERTURA"); ?>');

                            parent.jQuery('#add_ini').val(parent.jQuery('#add_ini4b').val());

                             parent.jQuery('#add_fin').val(direcciontemp+'-'+dirfinal);

                            parent.jQuery('#tipo_carro').val('Traslado Extra-Urbano').change();

                            parent.jQuery('#latini').val(parent.jQuery('#latini4b').val());
                            parent.jQuery('#latfin').val(parent.jQuery('#latfin4b').val());

                            parent.jQuery('#lngini').val(parent.jQuery('#lngini4b').val());
                            parent.jQuery('#lngfin').val(parent.jQuery('#lngfin4b').val());

                            parent.jQuery("#add_ini").tooltipster( 'content', parent.jQuery("#add_ini4b").tooltipster('content'));
                            parent.jQuery("#add_fin").tooltipster('content', parent.jQuery("#add_fin4b").tooltipster('content'));

                            parent.jQuery('#isEUini').val(parent.jQuery('#isEUini4b').val());
                            parent.jQuery('#isEUfin').val(parent.jQuery('#isEUfin4b').val());

                            parent.jQuery("#labelfrom").html(parent.jQuery("#labelfrom4b").html());
                            parent.jQuery("#labelto").html(parent.jQuery("#labelto4b").html());


                            clearInputs('Traslado Urbano');

                            window.parent.SqueezeBox.close();


                        }

                       

                    }else{

                         parent.jQuery('#isEUfin4b').val('0');
                    }

                    //parent.jQuery("#add_fin4b").tooltipster('show');
                }else{


                    parent.jQuery('#tipo_carro').val('Traslado Extra-Urbano').change();
                   

                    

                    //parent.jQuery('#labelto').html( ' '+ zoneDisplayId);

                    parent.jQuery('#add_fin').val(direcciontemp+'-'+dirfinal);

                    parent.jQuery("#add_fin").tooltipster('content', 'Zona '+zoneDisplayId+' :'+ direcciontemp+'-'+dirfinal);

                    parent.jQuery('#labelto').html( ' '+ zoneDisplayId);


                    var lattmp=  marker.getPosition().lat();
                    var lngtmp=  marker.getPosition().lng();

                    parent.jQuery('#latfin').val(lattmp);
                    parent.jQuery('#lngfin').val(lngtmp);

                    //si zona es zona urbana pero si esta addini es extraUrbano no cambia formulario 

                     if(zoneDisplayId!='Extraurbana'){

                         isfinExtraUrban=true;

                       
                        //if(parent.jQuery('#add_fin').val()!=''

                          parent.jQuery('#isEUfin').val('0');

                             //if(parent.jQuery('#add_fin4b').val()!=''){
                            if((parseInt(parent.jQuery('#isEUini').val())==0 && parseInt(parent.jQuery('#isEUfin').val())==0)){
                            alert('<?php echo JText::_("VRMSGZONASURBANAS"); ?>');

                            parent.jQuery('#isEUfin4b').val('0');


                            parent.jQuery('#add_fin4b').val(parent.jQuery('#add_fin').val());

                            parent.jQuery('#isEUini4b').val(parent.jQuery('#isEUini').val());
                            parent.jQuery('#add_ini4b').val(parent.jQuery('#add_ini').val());
                            parent.jQuery('#isEUfin4b').val(parent.jQuery('#isEUfin').val());


                            

                            parent.jQuery('#latini4b').val(parent.jQuery('#latini').val());
                            parent.jQuery('#latfin4b').val(parent.jQuery('#latfin').val());

                            parent.jQuery('#lngini4b').val(parent.jQuery('#lngini').val());
                            parent.jQuery('#lngfin4b').val(parent.jQuery('#lngfin').val());

                             parent.jQuery('#tipo_carro').val('Traslado Urbano').change();

                            parent.jQuery("#add_ini4b").tooltipster( 'content', parent.jQuery("#add_ini").tooltipster('content'));
                             parent.jQuery("#add_fin4b").tooltipster('content', parent.jQuery("#add_fin").tooltipster('content'));

                            parent.jQuery("#labelfrom4b").html(parent.jQuery("#labelfrom").html());
                            parent.jQuery("#labelto4b").html(parent.jQuery("#labelto").html());

                            var zoneidini= getidZone( parent.jQuery('#lngini4b').val(),parent.jQuery('#latini4b').val());
                            var zoneidfin= getidZone( parent.jQuery('#lngfin4b').val(),parent.jQuery('#latfin4b').val());


                            parent.jQuery('#zona_ini4b').val(zoneidini).change();

                            parent.jQuery('#zona_fin4b').val(zoneidfin).change();



                           clearInputs('Traslado Extra-Urbano');

                            window.parent.SqueezeBox.close();


                         }



                    


                    }else{



                    parent.jQuery('#isEUfin').val('1');


               

                    }



                    //parent.jQuery("#add_fin").tooltipster('content', direcciontemp+'-'+dirfinal);
                }


                
            }

             window.parent.SqueezeBox.close();

        }

        


    });

	
	function initialize() {
	
		map = new google.maps.Map(document.getElementById("map-canvas"), {
			zoom: 12,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng(4.6721351,-74.0489278)
		});

    

        var platini=0;
        var plngini=0;


        if(select=='ini'){

             if(extraurban!=1){

                 platini =parent.jQuery('#latini4b').val();
                plngini=  parent.jQuery('#lngini4b').val();


             }else{

                platini =parent.jQuery('#latini').val();
                plngini=  parent.jQuery('#lngini').val();


             }


            



        }


         if(select=='fin'){

             if(extraurban!=1){

                platini =parent.jQuery('#latfin4b').val();
                plngini=  parent.jQuery('#lngfin4b').val();


             }else{

                platini =parent.jQuery('#latfin').val();
                plngini=  parent.jQuery('#lngfin').val();


             }



        }

        if(platini=='' || plngini==''){
        
		
    		marker = new google.maps.Marker({
    			position: new google.maps.LatLng(4.6721351,-74.0489278),
    			map: map,
    			draggable:true,
    			title:'<?php echo JText::_("VRMSGDRAG"); ?>'
    		});


        }else{

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(platini,plngini),
                map: map,
                draggable:true,
                title:'<?php echo JText::_("VRMSGDRAG"); ?>'
            });

             map.setCenter(marker.getPosition());



        }

            

        
		
		google.maps.event.addListener(marker, 'dragend', function() 

        {   

            if (infowindow) {
                infowindow.close();
            }

            
			setZone(marker.getPosition());
		});
		
		if (zones === undefined) {
			
			getZones(fillMap);
		}
		else {
			
			fillMap();
		}
	}
	
	function loadScript() {
	  var script = document.createElement('script');
	  script.type = 'text/javascript';
	  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp' +
		  '&signed_in=true&callback=initialize';
	  document.body.appendChild(script);
	}

	window.onload = loadScript;
    
    function getZones(callback) {
        
        jQuery.ajax({
            url: 'https://app.pilotoautomatico.co/zones',
            type: 'GET',
		    dataType: 'jsonp'
        })
            .done(function (response) {
            
                zones = response;


                callback();
            })
            .fail(function (jqXHR, textStatus, errorThrown) {

                showAjaxError(jqXHR, textStatus, errorThrown);
            });
    }
	    
    function fillMap() {
        
        zones.forEach(function (zone) {

            var paths = [];

            zone.points.forEach(function (point) {

                paths.push(new google.maps.LatLng(point.lat, point.lng));
            });

            var polygon = new google.maps.Polygon({
                paths: paths,
                strokeColor: zone.strokeColor,
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: zone.fillColor,
                fillOpacity: 0.2,
                clickable: false
            });

            polygon.setMap(map);

            mapZones.push({ id: zone.id, color: zone.fillColor, polygon: polygon});
        });

        google.maps.event.trigger(map, 'resize');
    }

    function clearInputs(form){

        if(form=='Traslado Extra-Urbano'){

            parent.jQuery('#add_ini').val('');
            parent.jQuery('#add_fin').val('');
            parent.jQuery('#isEUini').val('');
            parent.jQuery('#isEUfin').val('');
            parent.jQuery('#latini').val('');
            parent.jQuery('#lngini').val('');
            parent.jQuery('#latfin').val('');
            parent.jQuery('#lngfin').val('');

            parent.jQuery("#labelfrom").html('');
            parent.jQuery("#labelto").html('');

            parent.jQuery("#add_ini").tooltipster('content', '');
            parent.jQuery("#add_fin").tooltipster('content', '');

             parent.jQuery("#add_ini").tooltipster('disable');
            parent.jQuery("#add_fin").tooltipster('disable');

           



        }


        if(form=='Traslado Urbano'){

            parent.jQuery('#add_ini4b').val('');
            parent.jQuery('#add_fin4b').val('');
            parent.jQuery('#isEUini4b').val('');
            parent.jQuery('#isEUfin4b').val('');
            parent.jQuery('#latini4b').val('');
            parent.jQuery('#lngini4b').val('');
            parent.jQuery('#latfin4b').val('');
            parent.jQuery('#lngfin4b').val('');

            parent.jQuery("#labelfrom4b").html('');
            parent.jQuery("#labelto4b").html('');

            parent.jQuery("#add_ini4b").tooltipster('content', '');
            parent.jQuery("#add_fin4b").tooltipster('content', '');

            parent.jQuery("#add_ini4b").tooltipster('disable');
            parent.jQuery("#add_fin4b").tooltipster('disable');


        }

      


        


    }

    function getidZone(lng ,lat){

        debugger;

      
        var currentZone;

        var zoneid;


        var LatLng = new google.maps.LatLng(lat, lng);
          mapZones.forEach(function (mapZone) {
            
            if (google.maps.geometry.poly.containsLocation(LatLng, mapZone.polygon)) {

                debugger;
                
                currentZone = mapZone;

                
            }
        });

          if (currentZone !== undefined) {


               zoneid = currentZone.id;
            
            if (currentZone.id == 0) {
                
                zoneid = 'Extraurbana';
            }

            return zoneid;
            
           


         

        }
        else {



            console.log('Fuera de zonas de cobertura, para esta zona seleccione Traslado Extra-Urbano');
        }





    }
	
	function setZone(LatLng) {

       
        debugger;
        
        var address;
        var currentZone;
        

        codeLatLng(LatLng.lat(), LatLng.lng());
        
        mapZones.forEach(function (mapZone) {
            
            if (google.maps.geometry.poly.containsLocation(LatLng, mapZone.polygon)) {
                
                currentZone = mapZone;

                
            }
        });
        
        if (currentZone !== undefined) {


               zoneDisplayId = currentZone.id;
            
            if (currentZone.id == 0) {
                
                zoneDisplayId = 'Extraurbana';
            }

            zonaselected =zoneDisplayId ;
            
            console.log('zona: ' + zoneDisplayId);


            if(zoneDisplayId==undefined){

                zoneDisplayId='Extraurbana';

               
            }

            /*if(zoneDisplayId!='Extraurbana'){

              
            }else{

                if(extraurban!=1){

                    alert('Fuera de zonas de cobertura, \nPara esta zona seleccione Traslado Extra-Urbano');


                    parent.jQuery('#tipo_carro').val('Traslado Extra-Urbano').change();
                   

                    window.parent.SqueezeBox.close();

                }

            }*/

           

            //jQuery('#zona_ini4b').val(zoneDisplayId).change();


         

        }
        else {

            zoneDisplayId='Extraurbana';

              alert('<?php echo JText::_("VRMSGFUERAZONAS"); ?>');
            console.log('Fuera de zonas de cobertura, para esta zona seleccione Traslado Extra-Urbano');
        }
    }

function crearhtmlcontent(zoneDisplayId, direcciontemp){

    if(zoneDisplayId==undefined){

        zoneDisplayId='Extraurbana';
    }


    infowindow = null;

    contentString = '<div id="contentinfowin">'+
              '<div id="siteNotice">'+
              '</div>'+
              '<h1 id="firstHeading" class="firstHeading">'+titleMsgBox+'</h1>'+
              '<div id="bodyContent">'+
              '<h3>'+zonelabel+': '+zoneDisplayId+'</h3>'+             
              '<div class="wrapper2">'+
              '<div id="part1dir" class="static text"><span id="dirstatic">'+direcciontemp+'-</span></div>'+
             '<input id="dirurbanaini" class="text" type="text"  />'+
             '</div>'+
             '<input style="display:inline; float:left;" id="botonok" type="submit" value="'+estaDir+'">'+
             '<div style="display:inline; float:left;" id="alertmsg"></div>'+
              '</div>'+
              '</div>';

   

    infowindow = new google.maps.InfoWindow({content: contentString});






}  

function codeLatLng(lat, lng) {

    


  var geocoder = new google.maps.Geocoder();
  

  var latlng = new google.maps.LatLng(lat, lng);
  geocoder.geocode({
    'latLng': latlng
  }, function (results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      if (results[0]) {
        
        console.log(results[0]);
            

        address= results[0];

    //if(zoneDisplayId!='Extraurbana'){


        if(select=='ini'){

       
                var addressformat=  address.formatted_address;
                 var dir =addressformat.split(/-|,/);

                 direcciontemp =dir[0];

                 crearhtmlcontent(zoneDisplayId, direcciontemp);

                infowindow.open(map, marker);

                

                

                 setTimeout(function(){ 

                     var ancho= jQuery('#dirstatic').width();

                     jQuery('#dirurbanaini').css('padding-left', ancho+6 );

                    // jQuery('#botonok', '#contentinfowin').uniform();
                   

                 }, 300);






               

          



        }

        if(select=='fin'){

            var addressformat=  address.formatted_address;
            var dir =addressformat.split(/-|,/);

            direcciontemp =dir[0];

            
            crearhtmlcontent(zoneDisplayId, direcciontemp);

            infowindow.open(map, marker);


              setTimeout(function(){ 

                 var ancho= jQuery('#dirstatic').width();

                 jQuery('#dirurbanaini').css('padding-left', ancho+6 );

                // jQuery('#botonok', '#contentinfowin').uniform();
                
            


               

             }, 300);

             




        }

   

      } else {
        alert('Direccion no encontrada');
      }
    } else {
      alert('Geocoder failed due to: ' + status);
    }
  });
}
</script>