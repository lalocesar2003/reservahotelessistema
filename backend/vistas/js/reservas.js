/*=============================================
Tabla Reservas
=============================================*/

// $.ajax({

//     "url":"ajax/tablaReservas.ajax.php",
//     success: function(respuesta){
      
//      console.log("respuesta", respuesta);

//     }

// })

$(".tablaReservas").DataTable({
  "ajax":"ajax/tablaReservas.ajax.php",
  "deferRender": true,
  "retrieve": true,
  "processing": true,
  "language": {

     "sProcessing":     "Procesando...",
    "sLengthMenu":     "Mostrar _MENU_ registros",
    "sZeroRecords":    "No se encontraron resultados",
    "sEmptyTable":     "Ningún dato disponible en esta tabla",
    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix":    "",
    "sSearch":         "Buscar:",
    "sUrl":            "",
    "sInfoThousands":  ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
      "sFirst":    "Primero",
      "sLast":     "Último",
      "sNext":     "Siguiente",
      "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    }

   }

});

/*=============================================
FECHAS RESERVA
=============================================*/

$('.datepicker.entrada').datepicker({
  startDate: '0d',
  datesDisabled: '0d',
  format: 'yyyy-mm-dd',
  todayHighlight:true
});


/*=============================================
EDITAR RESERVA
=============================================*/

$(document).on("click", ".editarReserva", function(){

	var descripcion = $(this).attr("descripcion");
	var idHabitacion = $(this).attr("idHabitacion");
	var fechaIngreso = $(this).attr("fechaIngreso");
	var fechaSalida = $(this).attr("fechaSalida");
	var idReserva = $(this).attr("idReserva");

	$(".agregarCalendario").html('<div id="calendar"></div>');

	// Agregar descripción al título del modal  
	$(".modal-title span").html(descripcion);

	 // Agregar las fechas de reserva al formulario
	$(".datepicker.entrada").val(fechaIngreso);
    $(".datepicker.salida").val(fechaSalida);

    // Agregar id de la habitación al botón ver disponibilidad
  	$(".verDisponibilidad").attr("idHabitacion", idHabitacion);

  	//Agregar id de la reserva al botón guardar
  	$(".guardarNuevaReserva").attr("idReserva", idReserva);

  	//Traer las resertvas existentes de la habitación
  	var totalEventos = [];
  	var datos = new FormData();
  	datos.append("idHabitacion", idHabitacion);

  	$.ajax({

	    url:"ajax/reservas.ajax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){
	    	
	    	for(var i = 0; i < respuesta.length; i++){

	    		if(fechaIngreso != respuesta[i]["fecha_ingreso"]){

		    		// Agregamos las fechas que ya están reservadas de esa habitación
		    		totalEventos.push(

		    			{
			              "start": respuesta[i]["fecha_ingreso"],
			              "end": respuesta[i]["fecha_salida"],
			              "rendering": 'background',
			              "color": '#847059'
			            }

		    		)

		    	}

	    	}

	    	 // Agregamos las fechas de la reserva
		     totalEventos.push(
		         {
		            "start": fechaIngreso,
		            "end": fechaSalida,
		            "rendering": 'background',
		            "color": '#FFCC29'
		          }
		      )
        

	    	/*=============================================
      		CALENDARIO
      		=============================================*/

      		$('#calendar').fullCalendar({

      			defaultDate:fechaIngreso,
      			header: {
		          left: 'prev',
		          center: 'title',
		          right: 'next'
		        },
		        events:totalEventos

      		});

	    }

	})

	/*=============================================
	Agregar la misma cantidad de días para la fecha de salida
	=============================================*/

	var diasReserva = $(this).attr("diasReserva");

	$('.datepicker.entrada').change(function(){

	 	var fechaEntrada = new Date($(this).val());
	 	fechaEntrada.setDate(fechaEntrada.getDate() + Number(diasReserva)+1);

	 	mes = ("0"+Number(fechaEntrada.getMonth()+1)).slice(-2);
	 	dia = ("0"+fechaEntrada.getDate()).slice(-2);

	 	$('.datepicker.salida').val(fechaEntrada.getFullYear()+"-"+mes+"-"+dia);

	})

})


/*=============================================
VER DISPONIBILIDAD NUEVA RESERVA
=============================================*/

$(document).on("click",".verDisponibilidad", function(){

	var fechaIngreso = $(".datepicker.entrada").val();
  	var fechaSalida = $(".datepicker.salida").val();
  	var idHabitacion = $(this).attr("idHabitacion");

  	// Reiniciar Calendario cada vez que busque disponibilidad
  	$(".agregarCalendario").html('<div id="calendar"></div>');

  	var totalEventos = [];
  	var opcion1 = [];
  	var opcion2 = [];
  	var opcion3 = [];
  	var validarDisponibilidad = false;

  	var datos = new FormData();
  	datos.append("idHabitacion", idHabitacion);

  	$.ajax({

	    url:"ajax/reservas.ajax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

	    	for(var i = 0; i < respuesta.length; i++){

	    		/* VALIDAR CRUCE DE FECHAS OPCIÓN 1 */         

	    		if(fechaIngreso == respuesta[i]["fecha_ingreso"]){

	    			opcion1[i] = false;            

	    		}else{

	    			opcion1[i] = true;

	    		}

	    		/* VALIDAR CRUCE DE FECHAS OPCIÓN 2 */         

	    		if(fechaIngreso > respuesta[i]["fecha_ingreso"] && fechaIngreso < respuesta[i]["fecha_salida"]){

	    			opcion2[i] = false;            

	    		}else{

	    			opcion2[i] = true;

	    		}

	    		/* VALIDAR CRUCE DE FECHAS OPCIÓN 3 */         

	    		if(fechaIngreso < respuesta[i]["fecha_ingreso"] && fechaSalida > respuesta[i]["fecha_ingreso"]){

	    			opcion3[i] = false;            

	    		}else{

	    			opcion3[i] = true;

	    		}

	    		 /* VALIDAR DISPONIBILIDAD */    

		        if(opcion1[i] == false || opcion2[i] == false || opcion3[i] == false){

		          validarDisponibilidad = false;
		        
		        }else{

		          validarDisponibilidad = true;
		         
		        }

		        if(!validarDisponibilidad){

		        	totalEventos.push(
			        	{
			        		"start": respuesta[i]["fecha_ingreso"],
			        		"end": respuesta[i]["fecha_salida"],
			        		"rendering": 'background',
			        		"color": '#847059'
			        	}
		        	)

		        	$(".infoDisponibilidad").html('<h5 class="pb-5 float-left">¡Lo sentimos, no hay disponibilidad para esa fecha!<br><br><strong>¡Vuelve a intentarlo!</strong></h5>');

		        	$(".guardarNuevaReserva").attr("fechaIngreso", "");
            		$(".guardarNuevaReserva").attr("fechaSalida", "");

		        	break;

		        }else{

		          totalEventos.push(
		            {
		              "start": respuesta[i]["fecha_ingreso"],
		              "end": respuesta[i]["fecha_salida"],
		              "rendering": 'background',
		              "color": '#847059'
		            }

		          )

		          $(".infoDisponibilidad").html('<h1 class="pb-5 float-left">¡Está Disponible!</h1>');

		         $(".guardarNuevaReserva").attr("fechaIngreso", fechaIngreso);
         		 $(".guardarNuevaReserva").attr("fechaSalida", fechaSalida); 

		        }


	    	}// FIN CICLO FOR

	    	if(validarDisponibilidad){

		        totalEventos.push(
		           {
		              "start": fechaIngreso,
		              "end": fechaSalida,
		              "rendering": 'background',
		              "color": '#FFCC29'
		            }
		        )

		    }

		    $('#calendar').fullCalendar({
		        defaultDate:fechaIngreso,
		        header: {
		            left: 'prev',
		            center: 'title',
		            right: 'next'
		        },
		        events:totalEventos

		    });

	    }

	})

})

/*=============================================
Guardar nueva reserva
=============================================*/

$(document).on("click",".guardarNuevaReserva", function(){

	var fechaIngreso = $(this).attr("fechaIngreso");
  	var fechaSalida = $(this).attr("fechaSalida");
  	var idReserva = $(this).attr("idReserva");

  	if(fechaIngreso == "" || fechaSalida == ""){

	     swal({
	          title: "Error al guardar",
	          text: "¡No ha seleccionado fechas válidas!",
	          type: "error",
	          confirmButtonText: "¡Cerrar!"
	        });

	     return;

  	}

  	var datos = new FormData();
    datos.append("idReserva", idReserva);
    datos.append("fechaIngreso", fechaIngreso);
    datos.append("fechaSalida", fechaSalida);

    $.ajax({

	    url:"ajax/reservas.ajax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    success:function(respuesta){

	    	 if(respuesta == "ok"){
	    	 	swal({
	    	 		type: "success",
	    	 		title: "¡CORRECTO!",
	    	 		text: "La reserva ha sido modificada correctamente",
	    	 		showConfirmButton: true,
	    	 		confirmButtonText: "Cerrar"
	    	 	}).then(function(result){

	    	 		if(result.value){

	    	 			window.location = "reservas";

	    	 		}
	    	 	})

	    	 }

	    }

	})

})

/*=============================================
Cancelar reserva
=============================================*/

$(document).on("click",".eliminarReserva", function(){

	var idReserva = $(this).attr("idReserva");

	swal({
		title: '¿Está seguro de cancelar esta reserva?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, cancelar reserva!'
	}).then(function(result){

		if(result.value){

			var datos = new FormData();
			datos.append("idReserva", idReserva);
			datos.append("fechaIngreso", null);
			datos.append("fechaSalida", null);

			$.ajax({

				url:"ajax/reservas.ajax.php",
				method: "POST",
				data: datos,
				cache: false,
				contentType: false,
				processData: false,
				success:function(respuesta){

					if(respuesta == "ok"){
						swal({
							type: "success",
							title: "¡CORRECTO!",
							text: "La reserva ha sido cancelada correctamente",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){

							if(result.value){

								window.location = "reservas";

							}
						})

					}

				}

			})	

		}

	})

})

/*=============================================
FECHAS NUEVA RESERVA (Inicializar datepicker)
=============================================*/
$('.datepicker.entradaNueva, .datepicker.salidaNueva').datepicker({
  startDate: '0d',
  datesDisabled: '0d',
  format: 'yyyy-mm-dd',
  todayHighlight:true
});


// 2. Cuando la entrada cambie, configuramos la salida para que bloquee días anteriores
$('.datepicker.entradaNueva').change(function () {
  var fechaEntrada = $(this).val();
  
  $('.datepicker.salidaNueva').val(""); // Limpiamos si había una fecha vieja
  $('.datepicker.salidaNueva').datepicker('destroy'); // Destruimos la instancia anterior
  
  $('.datepicker.salidaNueva').datepicker({
    startDate: fechaEntrada, // Bloquea todo lo anterior al ingreso
    datesDisabled: fechaEntrada, // Opcional: Evita que marquen salida el mismo día que entraron
    format: "yyyy-mm-dd",
    todayHighlight:true
  });
});
/*=============================================
GUARDAR RESERVA MANUAL (Con validación de cruce)
=============================================*/
$(document).on("click", ".btnGuardarReservaManual", function(){

    var idHabitacion = $("#nuevaHabitacion").val();
    var fechaIngreso = $(".datepicker.entradaNueva").val();
    var fechaSalida = $(".datepicker.salidaNueva").val();
    var pagoReserva = $("#nuevoPago").val();
    var descripcion = $("#nuevaDescripcion").val();
    
    // ATENCIÓN: ID del usuario genérico "Huésped Presencial"
    var idUsuarioManual = 15; 

    // 1. Validar que los campos no estén vacíos
    if(idHabitacion == "" || fechaIngreso == "" || fechaSalida == "" || pagoReserva == ""){
        swal({
            title: "Error",
            text: "¡Todos los campos obligatorios deben estar llenos!",
            type: "error",
            confirmButtonText: "¡Cerrar!"
        });
        return;
    }

if(new Date(fechaIngreso) >= new Date(fechaSalida)){
        swal({
            title: "Fechas incorrectas",
            text: "¡La fecha de salida debe ser posterior a la fecha de ingreso!",
            type: "error",
            confirmButtonText: "¡Cerrar!"
        });
        return;
    }

    // 2. Consultar las reservas existentes de esta habitación vía AJAX
    var datosHabitacion = new FormData();
    datosHabitacion.append("idHabitacion", idHabitacion);

    $.ajax({
        url:"ajax/reservas.ajax.php",
        method: "POST",
        data: datosHabitacion,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success:function(respuesta){

            var validarDisponibilidad = true;

            // Recorremos las reservas existentes para ver si chocan con nuestras fechas
            for(var i = 0; i < respuesta.length; i++){
                
                // Opción 1: Los ingresos son exactamente el mismo día
                var cruce1 = (fechaIngreso == respuesta[i]["fecha_ingreso"]) ? false : true;
                
                // Opción 2: El ingreso nuevo cae en medio de una reserva existente
                var cruce2 = (fechaIngreso > respuesta[i]["fecha_ingreso"] && fechaIngreso < respuesta[i]["fecha_salida"]) ? false : true;
                
                // Opción 3: El ingreso nuevo es antes, pero la salida nueva se mete en una reserva existente
                var cruce3 = (fechaIngreso < respuesta[i]["fecha_ingreso"] && fechaSalida > respuesta[i]["fecha_ingreso"]) ? false : true;

                // Si alguna de las 3 opciones es falsa, hay un cruce
                if(cruce1 == false || cruce2 == false || cruce3 == false){
                    validarDisponibilidad = false;
                    break; // Detenemos el ciclo, ya sabemos que está ocupada
                }
            }

            // 3. Si no hay disponibilidad, detenemos todo y avisamos
            if(!validarDisponibilidad){
                swal({
                    title: "¡Fechas Ocupadas!",
                    text: "La habitación ya se encuentra reservada en esas fechas. Por favor, elige otra habitación u otras fechas.",
                    type: "error",
                    confirmButtonText: "¡Entendido!"
                });
                return; 
            }

            // 4. Si pasamos la validación (está disponible), procedemos a GUARDAR
            var datosReserva = new FormData();
            datosReserva.append("idHabitacionNueva", idHabitacion);
            datosReserva.append("fechaIngresoNueva", fechaIngreso);
            datosReserva.append("fechaSalidaNueva", fechaSalida);
            datosReserva.append("pagoReserva", pagoReserva);
            datosReserva.append("descripcion", descripcion);
            datosReserva.append("idUsuarioManual", idUsuarioManual);

            $.ajax({
                url:"ajax/reservas.ajax.php",
                method: "POST",
                data: datosReserva,
                cache: false,
                contentType: false,
                processData: false,
                success:function(res){
                    if(res.trim() == "ok"){
                        swal({
                            type: "success",
                            title: "¡CORRECTO!",
                            text: "La reserva manual ha sido creada y las fechas bloqueadas.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "reservas";
                            }
                        })
                    } else {
                         swal({
                            type: "error",
                            title: "¡Error!",
                            text: "Hubo un problema al guardar la reserva en la base de datos.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        });
                    }
                }
            });

        }
    });

});
/*=============================================
SELECT DEPENDIENTE: CATEGORÍA -> HABITACIÓN
=============================================*/
$("#nuevaCategoria").change(function(){

    var idCategoria = $(this).val();

    // Si regresa a la opción por defecto, vaciamos y bloqueamos el select de habitaciones
    if(idCategoria == ""){
        $("#nuevaHabitacion").html('<option value="">Primero seleccione una categoría...</option>');
        $("#nuevaHabitacion").prop("disabled", true);
        return;
    }

    var datos = new FormData();
    datos.append("idCategoria", idCategoria);

    $.ajax({
        url:"ajax/reservas.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success:function(respuesta){
            
            // Limpiamos el select, agregamos la opción por defecto y lo habilitamos
            $("#nuevaHabitacion").empty();
            $("#nuevaHabitacion").append('<option value="">Seleccione una habitación...</option>');
            $("#nuevaHabitacion").prop("disabled", false);

            // Llenamos el select con la respuesta de la base de datos
            for(var i = 0; i < respuesta.length; i++){
                $("#nuevaHabitacion").append('<option value="'+respuesta[i]["id_h"]+'">'+respuesta[i]["estilo"]+'</option>');
            }
        }
    })
})