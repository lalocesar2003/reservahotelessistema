<?php

require_once "conexion.php";

class ModeloReservas{

	/*=============================================
	MOSTRAR USUARIOS-RESERVAS CON INNER JOIN
	=============================================*/

	static public function mdlMostrarReservas($tabla1, $tabla2, $item, $valor){

		if($item != null && $valor != null){

			$stmt = Conexion::conectar()->prepare("SELECT $tabla1.*, $tabla2.* FROM $tabla1 INNER JOIN $tabla2 ON $tabla1.id_u = $tabla2.id_usuario WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT $tabla1.*, $tabla2.* FROM $tabla1 INNER JOIN $tabla2 ON $tabla1.id_u = $tabla2.id_usuario ORDER BY $tabla2.id_reserva DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	Cambiar reserva
	=============================================*/

	static public function mdlCambiarReserva($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha_ingreso = :fecha_ingreso, fecha_salida = :fecha_salida WHERE id_reserva = :id_reserva");

		$stmt->bindParam(":fecha_ingreso", $datos["fecha_ingreso"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_salida", $datos["fecha_salida"], PDO::PARAM_STR);
		$stmt->bindParam(":id_reserva", $datos["id_reserva"], PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";

		}else{

			echo "\nPDO::errorInfo():\n";
    		print_r(Conexion::conectar()->errorInfo());

		}

		$stmt-> close();

		$stmt = null;

	}

	/*=============================================
Crear Reserva Manual (Backend)
=============================================*/
static public function mdlCrearReservaManual($tabla, $datos){

    $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_habitacion, id_usuario, pago_reserva, numero_transaccion, codigo_reserva, descripcion_reserva, fecha_ingreso, fecha_salida) VALUES (:id_habitacion, :id_usuario, :pago_reserva, :numero_transaccion, :codigo_reserva, :descripcion_reserva, :fecha_ingreso, :fecha_salida)");

    $stmt->bindParam(":id_habitacion", $datos["id_habitacion"], PDO::PARAM_INT);
    $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
    $stmt->bindParam(":pago_reserva", $datos["pago_reserva"], PDO::PARAM_STR);
    $stmt->bindParam(":numero_transaccion", $datos["numero_transaccion"], PDO::PARAM_STR);
    $stmt->bindParam(":codigo_reserva", $datos["codigo_reserva"], PDO::PARAM_STR);
    $stmt->bindParam(":descripcion_reserva", $datos["descripcion_reserva"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_ingreso", $datos["fecha_ingreso"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_salida", $datos["fecha_salida"], PDO::PARAM_STR);

    if($stmt->execute()){
        return "ok";
    }else{
        return "error";
    }

    $stmt->close();
    $stmt = null;
}
/*=============================================
Mostrar Habitaciones para Reserva Manual
=============================================*/
static public function mdlMostrarHabitacionesActivas($tabla1, $tabla2){

    $stmt = Conexion::conectar()->prepare("SELECT $tabla1.id_h, $tabla1.estilo, $tabla2.tipo FROM $tabla1 INNER JOIN $tabla2 ON $tabla1.tipo_h = $tabla2.id");

    $stmt -> execute();

    return $stmt -> fetchAll();

    $stmt -> close();
    $stmt = null;

}
/*=============================================
Mostrar Categorías
=============================================*/
static public function mdlMostrarCategorias($tabla){

    $stmt = Conexion::conectar()->prepare("SELECT id, tipo FROM $tabla");
    $stmt -> execute();
    return $stmt -> fetchAll();
    $stmt -> close();
    $stmt = null;

}

/*=============================================
Mostrar Habitaciones por Categoría
=============================================*/
static public function mdlMostrarHabitacionesPorCategoria($tabla, $valor){

    $stmt = Conexion::conectar()->prepare("SELECT id_h, estilo FROM $tabla WHERE tipo_h = :tipo_h");
    $stmt -> bindParam(":tipo_h", $valor, PDO::PARAM_INT);
    $stmt -> execute();
    return $stmt -> fetchAll();
    $stmt -> close();
    $stmt = null;

}
}