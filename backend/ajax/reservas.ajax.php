<?php

require_once "../controladores/reservas.controlador.php";
require_once "../modelos/reservas.modelo.php";

class AjaxReservas{

	/*=============================================
	Mostrar Reservas
	=============================================*/	

	public $idHabitacion;

	public function ajaxMostrarReservas(){

		$respuesta = ControladorReservas::ctrMostrarReservas("id_habitacion", $this->idHabitacion);

		echo json_encode($respuesta);

	}

	/*=============================================
	Cambiar Reservas
	=============================================*/	

	public $idReserva;
	public $fechaIngreso;
	public $fechaSalida;

	public function ajaxCambiarReserva(){

		$datos = array("id_reserva" => $this->idReserva,
					   "fecha_ingreso" => $this->fechaIngreso,
					   "fecha_salida" => $this->fechaSalida);

		$respuesta = ControladorReservas::ctrCambiarReserva($datos);

		echo $respuesta;

	}
/*=============================================
Mostrar Habitaciones por Categoría
=============================================*/ 
public $idCategoria;

public function ajaxMostrarHabitaciones(){
    $respuesta = ControladorReservas::ctrMostrarHabitacionesPorCategoria($this->idCategoria);
    echo json_encode($respuesta);
}

/*=============================================
Crear Reserva Manual
=============================================*/ 
public $idUsuarioManual;
public $pagoReserva;
public $descripcion;
public $idHabitacionNueva;

public function ajaxCrearReservaManual(){

    // Generar un código de reserva aleatorio similar al del frontend
    $codigoReserva = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 9);

    $datos = array(
        "id_habitacion" => $this->idHabitacionNueva,
        "id_usuario" => $this->idUsuarioManual, // ID del usuario "Presencial"
        "pago_reserva" => $this->pagoReserva,
        "numero_transaccion" => "EFECTIVO", // Marcador de pago en efectivo
        "codigo_reserva" => $codigoReserva,
        "descripcion_reserva" => $this->descripcion,
        "fecha_ingreso" => $this->fechaIngreso,
        "fecha_salida" => $this->fechaSalida
    );

    $respuesta = ControladorReservas::ctrCrearReservaManual($datos);
    echo $respuesta;
}


}

/*=============================================
Mostrar Reservas
=============================================*/	

if(isset($_POST["idHabitacion"])){

	$editar = new AjaxReservas();
	$editar -> idHabitacion = $_POST["idHabitacion"];
	$editar -> ajaxMostrarReservas();

}

/*=============================================
Cambiar Reservas
=============================================*/	

if(isset($_POST["idReserva"])){

	$guardar = new AjaxReservas();
	$guardar -> idReserva = $_POST["idReserva"];
	$guardar -> fechaIngreso = $_POST["fechaIngreso"];
	$guardar -> fechaSalida = $_POST["fechaSalida"];
	$guardar -> ajaxCambiarReserva();

}

/*=============================================
Crear Reserva Manual
=============================================*/ 
if(isset($_POST["idHabitacionNueva"])){
    $crear = new AjaxReservas();
    $crear -> idHabitacionNueva = $_POST["idHabitacionNueva"];
    $crear -> idUsuarioManual = $_POST["idUsuarioManual"];
    $crear -> fechaIngreso = $_POST["fechaIngresoNueva"];
    $crear -> fechaSalida = $_POST["fechaSalidaNueva"];
    $crear -> pagoReserva = $_POST["pagoReserva"];
    $crear -> descripcion = $_POST["descripcion"];
    $crear -> ajaxCrearReservaManual();
}

/*=============================================
Mostrar Habitaciones por Categoría
=============================================*/ 
if(isset($_POST["idCategoria"])){
    $habitaciones = new AjaxReservas();
    $habitaciones -> idCategoria = $_POST["idCategoria"];
    $habitaciones -> ajaxMostrarHabitaciones();
}