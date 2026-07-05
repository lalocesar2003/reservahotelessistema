console.log("reservas.js LOADED");
window.onerror = function (msg, src, line, col, err) {
  console.error("[window.onerror]", msg, src + ":" + line + ":" + col, err);
};

/*=============================================
FECHAS RESERVA
=============================================*/
$(".datepicker.entrada").datepicker({
  startDate: "0d",
  datesDisabled: "0d",
  format: "yyyy-mm-dd",
  todayHighlight: true,
});

$(".datepicker.entrada").change(function () {
  $(".datepicker.salida").attr("readonly", false);

  var fechaEntrada = $(this).val();

  $(".datepicker.salida").datepicker({
    startDate: fechaEntrada,
    datesDisabled: fechaEntrada,
    format: "yyyy-mm-dd",
  });
});

/*=============================================
SELECTS ANIDADOS
=============================================*/

$(".selectTipoHabitacion").change(function () {
  var ruta = $(this).val();

  if (ruta != "") {
    $(".selectTemaHabitacion").html("");
  } else {
    $(".selectTemaHabitacion").html("<option>Temática de habitación</option>");
  }

  var datos = new FormData();
  datos.append("ruta", ruta);

  $.ajax({
    url: urlPrincipal + "ajax/habitaciones.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      $("input[name='ruta']").val(respuesta[0]["ruta"]);

      for (var i = 0; i < respuesta.length; i++) {
        $(".selectTemaHabitacion").append(
          '<option value="' +
            respuesta[i]["id_h"] +
            '">' +
            respuesta[i]["estilo"] +
            "</option>"
        );
      }
    },
  });
});

/*=============================================
CÓDIGO ALEATORIO
=============================================*/

var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

function codigoAleatorio(chars, length) {
  codigo = "";

  for (var i = 0; i < length; i++) {
    rand = Math.floor(Math.random() * chars.length);
    codigo += chars.substr(rand, 1);
  }

  return codigo;
}

/*=============================================
CALENDARIO
=============================================*/

if ($(".infoReservas").html() != undefined) {
  var idHabitacion = $(".infoReservas").attr("idHabitacion");
  console.log("idHabitacion", idHabitacion);
  var fechaIngreso = $(".infoReservas").attr("fechaIngreso");
  var fechaSalida = $(".infoReservas").attr("fechaSalida");
  var dias = $(".infoReservas").attr("dias");

  var totalEventos = [];
  var opcion1 = [];
  var opcion2 = [];
  var opcion3 = [];
  var validarDisponibilidad = false;

  var datos = new FormData();
  datos.append("idHabitacion", idHabitacion);

  $.ajax({
    url: urlPrincipal + "ajax/reservas.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.length == 0) {
        $("#calendar").fullCalendar({
          defaultDate: fechaIngreso,
          header: {
            left: "prev",
            center: "title",
            right: "next",
          },
          events: [
            {
              start: fechaIngreso,
              end: fechaSalida,
              rendering: "background",
              color: "#FFCC29",
            },
          ],
        });

        colDerReservas();
      } else {
        for (var i = 0; i < respuesta.length; i++) {
          /* VALIDAR CRUCE DE FECHAS OPCIÓN 1 */

          if (fechaIngreso == respuesta[i]["fecha_ingreso"]) {
            opcion1[i] = false;
          } else {
            opcion1[i] = true;
          }

          /* VALIDAR CRUCE DE FECHAS OPCIÓN 2 */

          if (
            fechaIngreso > respuesta[i]["fecha_ingreso"] &&
            fechaIngreso < respuesta[i]["fecha_salida"]
          ) {
            opcion2[i] = false;
          } else {
            opcion2[i] = true;
          }

          /* VALIDAR CRUCE DE FECHAS OPCIÓN 3 */

          if (
            fechaIngreso < respuesta[i]["fecha_ingreso"] &&
            fechaSalida > respuesta[i]["fecha_ingreso"]
          ) {
            opcion3[i] = false;
          } else {
            opcion3[i] = true;
          }

          console.log("opcion1[i]", opcion1[i]);
          console.log("opcion2[i]", opcion2[i]);
          console.log("opcion3[i]", opcion3[i]);

          /* VALIDAR DISPONIBILIDAD */

          if (
            opcion1[i] == false ||
            opcion2[i] == false ||
            opcion3[i] == false
          ) {
            validarDisponibilidad = false;
          } else {
            validarDisponibilidad = true;
          }

          // console.log("validarDisponibilidad", validarDisponibilidad);

          if (!validarDisponibilidad) {
            totalEventos.push({
              start: respuesta[i]["fecha_ingreso"],
              end: respuesta[i]["fecha_salida"],
              rendering: "background",
              color: "#847059",
            });

            $(".infoDisponibilidad").html(
              '<h5 class="pb-5 float-left">¡Lo sentimos, no hay disponibilidad para esa fecha!<br><br><strong>¡Vuelve a intentarlo!</strong></h5>'
            );

            break;
          } else {
            totalEventos.push({
              start: respuesta[i]["fecha_ingreso"],
              end: respuesta[i]["fecha_salida"],
              rendering: "background",
              color: "#847059",
            });

            $(".infoDisponibilidad").html(
              '<h1 class="pb-5 float-left">¡Está Disponible!</h1>'
            );

            colDerReservas();
          }
        }
        // FIN CICLO FOR

        if (validarDisponibilidad) {
          totalEventos.push({
            start: fechaIngreso,
            end: fechaSalida,
            rendering: "background",
            color: "#FFCC29",
          });
        }

        $("#calendar").fullCalendar({
          defaultDate: fechaIngreso,
          header: {
            left: "prev",
            center: "title",
            right: "next",
          },
          events: totalEventos,
        });
      }
    },
  });
}

/*=============================================
FUNCIÓN COL.DERECHA RESERVAS
=============================================*/

function colDerReservas() {
  $(".colDerReservas").show();

  var codigoReserva = codigoAleatorio(chars, 9);

  var datos = new FormData();
  datos.append("codigoReserva", codigoReserva);

  $.ajax({
    url: urlPrincipal + "ajax/reservas.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      // 1) Poner el mismo código tanto en el DOM como en el botón
      const $btn = $(".pagarReserva");
      let cod = codigoReserva;
      if (respuesta) cod = codigoReserva + codigoAleatorio(chars, 3);
      $(".codigoReserva").html(cod);
      if ($btn.length) $btn.attr("codigoReserva", cod);

      // 2) Asegura que el botón tenga id/fechas (si vinieran vacíos)
      if ($btn.length) {
        const $info = $(".infoReservas");
        if (!$btn.attr("idHabitacion"))
          $btn.attr("idHabitacion", $info.attr("idHabitacion"));
        if (!$btn.attr("fechaIngreso"))
          $btn.attr("fechaIngreso", $info.attr("fechaIngreso"));
        if (!$btn.attr("fechaSalida"))
          $btn.attr("fechaSalida", $info.attr("fechaSalida"));

        // DEBUG
        console.log("[colDerReservas] btn attrs:", {
          codigoReserva: $btn.attr("codigoReserva"),
          idHabitacion: $btn.attr("idHabitacion"),
          fechaIngreso: $btn.attr("fechaIngreso"),
          fechaSalida: $btn.attr("fechaSalida"),
          plan: $btn.attr("plan"),
          personas: $btn.attr("personas"),
          pagoReserva: $btn.attr("pagoReserva"),
        });
      } else {
        console.warn("No existe .pagarReserva en el DOM.");
      }

      // 3) Reengancha handlers sin duplicarlos
      $(".elegirPlan").off("change.res").on("change.res", cambioPlanesPersonas);
      $(".cantidadPersonas")
        .off("change.res")
        .on("change.res", cambioPlanesPersonas);

      // 4) Cálculo inicial por si el usuario no toca nada
      try {
        cambioPlanesPersonas();
      } catch (e) {}
    },
    error: function (xhr) {
      console.warn("ajax/reservas.ajax.php error:", xhr && xhr.responseText);
    },
  });
}

function cambioPlanesPersonas() {
  switch ($(".cantidadPersonas").val()) {
    case "2":
      $(".precioReserva span").html(
        $(".elegirPlan").val().split(",")[0] * dias
      );
      $(".precioReserva span").number(true);
      $(".pagarReserva").attr(
        "pagoReserva",
        $(".elegirPlan").val().split(",")[0] * dias
      );
      $(".pagarReserva").attr("plan", $(".elegirPlan").val().split(",")[1]);
      $(".pagarReserva").attr("personas", $(".cantidadPersonas").val());

      break;

    case "3":
      $(".precioReserva span").html(
        Number($(".elegirPlan").val().split(",")[0] * 0.25) +
          Number($(".elegirPlan").val().split(",")[0]) * dias
      );
      $(".precioReserva span").number(true);
      $(".pagarReserva").attr(
        "pagoReserva",
        Number($(".elegirPlan").val().split(",")[0] * 0.25) +
          Number($(".elegirPlan").val().split(",")[0]) * dias
      );
      $(".pagarReserva").attr("plan", $(".elegirPlan").val().split(",")[1]);
      $(".pagarReserva").attr("personas", $(".cantidadPersonas").val());

      break;

    case "4":
      $(".precioReserva span").html(
        Number($(".elegirPlan").val().split(",")[0] * 0.5) +
          Number($(".elegirPlan").val().split(",")[0]) * dias
      );
      $(".precioReserva span").number(true);
      $(".pagarReserva").attr(
        "pagoReserva",
        Number($(".elegirPlan").val().split(",")[0] * 0.5) +
          Number($(".elegirPlan").val().split(",")[0]) * dias
      );
      $(".pagarReserva").attr("plan", $(".elegirPlan").val().split(",")[1]);
      $(".pagarReserva").attr("personas", $(".cantidadPersonas").val());

      break;

    case "5":
      $(".precioReserva span").html(
        Number($(".elegirPlan").val().split(",")[0] * 0.75) +
          Number($(".elegirPlan").val().split(",")[0]) * dias
      );
      $(".precioReserva span").number(true);
      $(".pagarReserva").attr(
        "pagoReserva",
        Number($(".elegirPlan").val().split(",")[0] * 0.75) +
          Number($(".elegirPlan").val().split(",")[0]) * dias
      );
      $(".pagarReserva").attr("plan", $(".elegirPlan").val().split(",")[1]);
      $(".pagarReserva").attr("personas", $(".cantidadPersonas").val());

      break;
  }
}

/*=============================================
FUNCIÓN PARA GENERAR COOKIES
=============================================*/

function crearCookie(nombre, valor, diasExpedicion) {
  var hoy = new Date();

  hoy.setTime(hoy.getTime() + diasExpedicion * 24 * 60 * 60 * 1000);

  var fechaExpedicion = "expires=" + hoy.toUTCString();

  document.cookie =
    nombre +
    "=" +
    encodeURIComponent(valor) +
    "; " +
    fechaExpedicion +
    "; path=/; SameSite=Lax";
}

/*=============================================
CAPTURAR DATOS DE LA RESERVA
=============================================*/
// 1) Delegación: funciona incluso si el botón se inserta luego del AJAX
$(document).on("click", ".pagarReserva", function (e) {
  e.preventDefault();

  // (A) Toma los atributos del botón REAL que clickeaste
  const $btn = $(this);
  const idHabitacion = $btn.attr("idHabitacion");
  const imgHabitacion = $btn.attr("imgHabitacion");
  const infoHabitacion =
    $btn.attr("infoHabitacion") +
    " - " +
    $btn.attr("plan") +
    " - " +
    $btn.attr("personas") +
    " personas";
  const pagoReserva = $btn.attr("pagoReserva");
  const codigoReserva = $btn.attr("codigoReserva");
  const fechaIngreso = $btn.attr("fechaIngreso");
  const fechaSalida = $btn.attr("fechaSalida");

  // (B) DEBUG: confirma que sí estamos escribiendo
  console.log("[pagarReserva.click] userId=", window.userId, {
    idHabitacion,
    fechaIngreso,
    fechaSalida,
    codigoReserva,
  });

  // (C) Graba TODAS las cookies necesarias
  crearCookie("idHabitacion", idHabitacion, 1);
  crearCookie("imgHabitacion", imgHabitacion, 1);
  crearCookie("infoHabitacion", infoHabitacion, 1);
  crearCookie("pagoReserva", pagoReserva, 1);
  crearCookie("codigoReserva", codigoReserva, 1);
  crearCookie("fechaIngreso", fechaIngreso, 1);
  crearCookie("fechaSalida", fechaSalida, 1);
  crearCookie("reservaUid", window.userId || 0, 1);

  // (D) Espera breve y redirige a /perfil (evita la carrera al escribir cookies)
  setTimeout(function () {
    window.location = (window.urlPrincipal || "/") + "perfil";
  }, 60);

  setTimeout(() => console.log("Cookies tras click:", document.cookie), 30);
});
