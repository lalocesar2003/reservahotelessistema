<?php

class ControladorReservas{

	/*=============================================
	MOSTRAR USUARIOS-RESERVAS CON INNER JOIN
	=============================================*/

	static public function ctrMostrarReservas($item, $valor){

		$tabla1 = "usuarios";
		$tabla2 = "reservas";

		$respuesta = ModeloReservas::mdlMostrarReservas($tabla1, $tabla2, $item, $valor);

		return $respuesta;

	}

	/*=============================================
	Cambiar Reserva
	=============================================*/

	static public function ctrCambiarReserva($datos){

		$tabla = "reservas";

		$respuesta = ModeloReservas::mdlCambiarReserva($tabla, $datos);

		return $respuesta;

	}

	/*=============================================
Crear Reserva Manual
=============================================*/
static public function ctrCrearReservaManual($datos){
    $tabla = "reservas";
    $respuesta = ModeloReservas::mdlCrearReservaManual($tabla, $datos);
    return $respuesta;
}

/*=============================================
Mostrar Habitaciones para Reserva Manual
=============================================*/
static public function ctrMostrarHabitacionesActivas(){

    $tabla1 = "habitaciones"; // Asegúrate de que este sea el nombre real de tu_tabla_1
    $tabla2 = "categorias";   // Asegúrate de que este sea el nombre real de tu_tabla_2

    $respuesta = ModeloReservas::mdlMostrarHabitacionesActivas($tabla1, $tabla2);

    return $respuesta;

}
/*=============================================
Mostrar Categorías
=============================================*/
static public function ctrMostrarCategorias(){
    $tabla = "categorias"; // El nombre de tu tabla 2
    $respuesta = ModeloReservas::mdlMostrarCategorias($tabla);
    return $respuesta;
}

/*=============================================
Mostrar Habitaciones por Categoría
=============================================*/
static public function ctrMostrarHabitacionesPorCategoria($valor){
    $tabla = "habitaciones"; // El nombre de tu tabla 1
    $respuesta = ModeloReservas::mdlMostrarHabitacionesPorCategoria($tabla, $valor);
    return $respuesta;
}
}