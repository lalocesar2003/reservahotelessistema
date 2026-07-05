<?php

require_once "../controladores/restaurante.controlador.php";
require_once "../modelos/restaurante.modelo.php";

class TablaRestaurante {

    /*=============================================
    Mostrar Tabla Restaurante
    =============================================*/ 

    public function mostrarTabla() {

        // Obtener los datos de la base de datos
        $restaurantes = ControladorRestaurante::ctrMostrarRestaurante(null, null);

        // Si no hay datos, devolver un JSON vacío
        if (count($restaurantes) == 0) {
            echo json_encode(["data" => []]);
            return;
        }

        // Crear un array para los datos de la tabla
        $data = [];

        // Recorrer los datos de los restaurantes
        foreach ($restaurantes as $key => $value) {

            // Imagen
            $img = "<img src='".$value["img"]."' class='img-fluid rounded-circle' width='100px'>";

            // Acciones (editar y eliminar)
            $acciones = "<div class='btn-group'>
                            <button class='btn btn-warning btn-sm editarRestaurante' data-toggle='modal' data-target='#editarRestaurante' idRestaurante='".$value["id"]."'>
                                <i class='fas fa-pencil-alt text-white'></i>
                            </button>
                            <button class='btn btn-danger btn-sm eliminarRestaurante' idRestaurante='".$value["id"]."' imgRestaurante='".$value["img"]."'>
                                <i class='fas fa-trash-alt'></i>
                            </button>
                        </div>";

            // Agregar los datos de cada restaurante a la array $data
            $data[] = [
                $key + 1, // Número de fila
                $img, // Imagen
                $value["descripcion"], // Descripción
                $acciones // Acciones
            ];
        }

        // Total de registros (sin filtros)
        $recordsTotal = count($restaurantes);
        // Total de registros después de aplicar filtros (si hay filtros, en este caso es igual a recordsTotal)
        $recordsFiltered = count($restaurantes);

        // Devolver los datos en formato JSON que DataTables espera
        echo json_encode([
            "draw" => isset($_POST['draw']) ? $_POST['draw'] : 0, // Asegurarse de que draw esté definido
            "recordsTotal" => $recordsTotal, // Total de registros
            "recordsFiltered" => $recordsFiltered, // Registros filtrados
            "data" => $data // Los datos de la tabla
        ]);
    }
}

/*=============================================
Mostrar Tabla Restaurante
=============================================*/ 

$tabla = new TablaRestaurante();
$tabla->mostrarTabla();

?>
