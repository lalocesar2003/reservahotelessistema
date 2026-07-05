<?php
header('Content-Type: application/json; charset=utf-8');

require_once "../controladores/recorrido.controlador.php";
require_once "../modelos/recorrido.modelo.php";

class TablaRecorrido{
  public function mostrarTabla(){
    $recorrido = ControladorRecorrido::ctrMostrarRecorrido(null, null);

    if(!$recorrido || !is_array($recorrido) || count($recorrido) === 0){
      echo json_encode(["data"=>[]], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
      return;
    }

    $data = [];
    foreach ($recorrido as $i => $value) {
      $titulo = htmlspecialchars($value["titulo"] ?? "", ENT_QUOTES, 'UTF-8');
      $desc   = htmlspecialchars($value["descripcion"] ?? "", ENT_QUOTES, 'UTF-8');

      $srcG = $value["foto_grande"] ?? "";
      $srcP = $value["foto_peq"] ?? "";

      $imgG = $srcG ? "<img src=\"{$srcG}\" class=\"img-fluid\" style=\"max-width:120px\">" : "";
      $imgP = $srcP ? "<img src=\"{$srcP}\" class=\"img-fluid\" style=\"max-width:120px\">" : "";

      $id = (int)($value["id"] ?? 0);

      $acciones = "<div class='btn-group'>
        <button class='btn btn-warning btn-sm editarRecorrido' data-toggle='modal' data-target='#editarRecorrido' idRecorrido='{$id}'><i class='fas fa-pencil-alt text-white'></i></button>
        <button class='btn btn-danger btn-sm eliminarRecorrido' idRecorrido='{$id}' imgGrandeRecorrido='{$srcG}' imgPeqRecorrido='{$srcP}'><i class='fas fa-trash-alt'></i></button>
      </div>";

      $data[] = [
        (string)($i+1),
        $titulo,
        $desc,
        $imgG,
        $imgP,
        $acciones
      ];
    }

    echo json_encode(["data"=>$data], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  }
}

$tabla = new TablaRecorrido();
$tabla->mostrarTabla();
