var tourLocales = {
      nextBtn: " ",
      prevBtn: " ",
      doneBtn: " "
      
      
    };

var tour = {
      id: "welcome-tour",
      steps: [
        {
          target: "#header",
          placement: "bottom",
          title: "Bienvenido a Piloto Automatico",
          content: "Es su primera vez en el sitio?, siga los pasos o cierre el tour si ya los conoce",
          xOffset: 'center',
          arrowOffset: 'center'
		  
        },
        {
          target: ".sp-submenu.sub-level.open",
          placement: "left",
          title: "Autenticación",
          content: "Ingrese a su cuenta o regístrese",
          arrowOffset: 'center'
         
        },
		 {
          target: ".column.radios",
          placement: "top",
          title: "Fecha Inicial",
          content: "Selecione la fecha en que desea reservar su servicio",
          xOffset: 'center',
          arrowOffset: 'center'
        },
		 {
          target: ".column.p2",
          placement: "top",
          title: "Ciudad y Servicio",
          content: "Selecione la ciudad y la categoría de servicio deseado",
          xOffset: 'center',
          arrowOffset: 'center'
        },
		 {
          target: ".column.p3",
          placement: "top",
          title: "Tipo y Cantidad",
          content: "Selecione el sub tipo de servicio y cantidad que desea",
          xOffset: 'center',
          arrowOffset: 'center'
        },
		 {
          target: "#dDonde",
          placement: "top",
          title: "Datos Adicionales",
          content: "ingrese información adicional como Dirección Inicial, final y tiempo inicial",
          xOffset: 'center',
          arrowOffset: 'center'
        },
		 {
          target: "#search-submit",
          placement: "bottom",
          title: "Envíe su Consulta",
          content: "Haga clic en el botón agregar y envie su consulta",
          arrowOffset: 'center'
          
         
        },
		 {
          target: ".htmlContent",
          placement: "bottom",
          title: "Listado de Vehículos",
          content: 'Una vez realizada la busqueda se mostrará un listado de vehículos, seleccione el que desee de la lista<div class=""></div>',
          xOffset: 'center',
          arrowOffset: 'center'
		  
         },
		 {
          target: "#cchekout",
          placement: "bottom",
          title: "Checkout",
          content: 'Para ver detalles de pago y confirmar su reserva haga clic en el botón checkout',
          arrowOffset: 'center'
         
		  
         },
		    {
          target: ".htmlContent",
          placement: "bottom",
          title: "Confirmación",
          content: 'Para confirmar la orden u ordenes diligencie el formulario de usuario final que aparecerá aquí y oprima Confirmar Servicio<div class=""></div>',
          xOffset: 'center',
          arrowOffset: 'center'
		  
         },
          {
          target: "#menu-item-517",
          placement: "bottom",
          title: "Ordenes",
          content: 'Podrá consultar sus Ordenes en el menú Cuenta /Ordenes Clientes',
          arrowOffset: 'center'

        
      
         }
		 
      ],
       i18n: tourLocales,
       showPrevButton: true

     
      
    };

    // Start the tour!
   
    hopscotch.startTour(tour);
   
    