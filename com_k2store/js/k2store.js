window.addEvent('domready', function() {
			
			var container = 'miniK2StoreCart';
			var cartcontainer = '.cart_form';
			
			if ($(container))	{
				doMiniCart();
			}
		
		 }); //end dom ready
		 
function doMiniCart() {
var container = 'miniK2StoreCart';
	var murl = k2storeURL+'index.php?option=com_k2store&view=mycart&task=ajaxmini';
	var a=new Request.HTML({
		url:murl,
		method:"post",
		onSuccess:function(responseTree, responseElements, responseHTML, responseJavaScript) { 
							if ($(container))	{
								$(container).set('html', responseHTML); 
							}
						 }		

		}).send();
	}


function k2storeGetPaymentForm( element, container )
{
    var url = k2storeURL+'index.php?option=com_k2store&view=checkout&task=getPaymentForm&format=raw&payment_element=' + element;
    k2storeDoTask( url, container, document.adminForm );
}


function k2storeDoTask( url, container, form, msg ) 
	{
		k2storeNewModal(msg);
		
    	// if url is present, do validation
		if (url && form) 
		{	
			// loop through form elements and prepare an array of objects for passing to server
			var str = new Array();
			for(i=0; i<form.elements.length; i++)
			{
				postvar = {
					name : form.elements[i].name,
					value : form.elements[i].value,
					checked : form.elements[i].checked,
					id : form.elements[i].id
				};
				str[i] = postvar;
			}
			// execute Ajax request to server
            var a=new Request.JSON({
				url: url,		
                method:"post",
				//data:{"elements":Json.toString(str)},
                onComplete: function(response){
                    if ($(container)) { $(container).set('html', response.msg); }
                    (function() { document.body.removeChild($('k2storeModal')); }).delay(500);
                    return true;
                }
            }).send();
		}
			else if (url && !form) 
		{
			// execute Ajax request to server
            var a=new Request.JSON({
                url:url,
                method:"post",
                onComplete: function(response){
                    if ($(container)) { $(container).set('html', response.msg); }
                    (function() { document.body.removeChild($('k2storeModal')); }).delay(500);
            }
            }).send();			
		}
	}

	/**
	 * 
	 * @param {String} msg message for the modal div (optional)
	 */
	function k2storeNewModal (msg)
	{
	    if (typeof window.innerWidth != 'undefined') {
	        var h = window.innerHeight;
	        var w = window.innerWidth;
	    } else {
	        var h = document.documentElement.clientHeight;
	        var w = document.documentElement.clientWidth;
	    }
	    var t = (h / 2) - 15;
	    var l = (w / 2) - 15;
		var i = document.createElement('img');
		var s = window.location.toString();
		var src = 'components/com_k2store/images/ajax-loader.gif';
		i.src = (s.match(/administrator\/index.php/)) ? '../' + src : src;
		i.style.position = 'absolute';
		i.style.top = t + 'px';
		i.style.left = l + 'px';
		i.style.backgroundColor = '#000000';
		i.style.zIndex = '100001';
		var d = document.createElement('div');
		d.id = 'k2storeModal';
		d.style.position = 'fixed';
		d.style.top = '0px';
		d.style.left = '0px';
		d.style.width = w + 'px';
		d.style.height = h + 'px';
		d.style.backgroundColor = '#000000';
		d.style.opacity = 0.5;
		d.style.filter = 'alpha(opacity=50)';
		d.style.zIndex = '100000';
		d.appendChild(i);
	    if (msg != '' && msg != null) {
		    var m = document.createElement('div');
		    m.style.position = 'absolute';
		    m.style.width = '200px';
		    m.style.top = t + 50 + 'px';
		    m.style.left = (w / 2) - 100 + 'px';
		    m.style.textAlign = 'center';
		    m.style.zIndex = '100002';
		    m.style.fontSize = '1.2em';
		    m.style.color = '#ffffff';
		    m.innerHTML = msg;
		    d.appendChild(m);
		}
		document.body.appendChild(d);
	}
	
	
function k2storeCartChange(event, orderCode){
        var row = event.getParent().getParent(); 
        var q = event.value;
        if (q=='') q=0;
        var quantity = isNaN(q) ? 0 : parseInt(q); 
        var myurl = 'index.php?option=com_k2store&view=mycart&task=ajax';
        if (quantity!=0) {
          //quantity is not zero..so setup the ajax call
          
          
          var myAjax = new  Request.HTML(
            {
              method: 'get',
              url: myurl,
              data: 'quantity[' + orderCode + ']=' + quantity,
              update: $('k2store_container'),
              //when the request is made, turn the row text colour RED
              onRequest: function(){new Fx.Style(row,'color',{duration:300}).start('#000000', '#fb6c6c');},
              //when ajax call successful, turn the text colour of the row back to black
              //and update the prices/totals/etc.
				onSuccess: function(response) { 
			    //start the effect
			
				  },//onSuccess
				  //if ajax call fails, then force a page refresh straight to com_k2store
              onFailure: function(){
                  window.location("index.php?option=com_k2store&view=mycart&quantity[" + orderCode + "]=" + quantity);
              }//onFailure
            }
          ).send();//var myAjax
        }//if quantity
    }

function k2storeCartRemove(event, cart_id, product_id, pop_up){
	 this.stop();
    	var row = event.getParent().getParent();
    	var container;
    	if(pop_up==1) {
			container = $('sbox-content');
		} else {
			container = $('k2storeCartPopup');
		}
    	var minicontainer = 'miniK2StoreCart';
    	var myurl = 'index.php?option=com_k2store&view=mycart&task=update&popup='+pop_up;
      var myAjax = new Request.HTML(
							{
							  url: myurl,
							  method: "get",
                              data:"remove=1&cid["+cart_id+"]=" + product_id,
                              update: container,
                              onSuccess: function(response) { 
								  if ($(minicontainer))	{
										doMiniCart();
									}
									},//onSuccess
          							onFailure: function() {
          							  window.location("index.php?option=com_k2store&view=mycart&task=update&remove=1&cid["+cart_id+"]=" + product_id);
          							}//onFailure
                            }).send();
    }
    
    
function k2storeAddToCart( url, task, form, doModal, msg ) 
{
	var container = 'miniK2StoreCart';
   // if (doModal == true) { k2storeNewModal(msg); }
    
    if (task == 'addtocart') 
    {
        // loop through form elements and prepare an array of objects for passing to server
        var str = new Array();
        for(i=0; i<form.elements.length; i++)
        {
            postvar = {
                name : form.elements[i].name,
                value : form.elements[i].value,
                checked : form.elements[i].checked,
                id : form.elements[i].id
            };
            str[i] = postvar;
        }
        
        // execute Ajax request to server
        var url1 ='index.php?option=com_k2store&view=mycart&task=ajax&tmpl=component';
		    var ajreq1= new Request.HTML({
						 url: url1,
						 method:'post',
						 data:{'elements':JSON.encode(str)},
						 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) { 
							SqueezeBox.setContent('iframe', responseHTML);
							SqueezeBox.applyContent(responseHTML);
							if ($(container))	{
							}
						 }		
		}).send();
    }
    else 
    {
        if (doModal == true) { (function() { document.body.removeChild($('k2storeModal')); }).delay(500); }
        form.task.value = task;
        form.submit();
    }
}
