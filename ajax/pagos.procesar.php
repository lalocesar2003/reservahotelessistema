<?php
// ajax/pagos.procesar.php
declare(strict_types=1);

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Exceptions\MPApiException;

require_once __DIR__ . '/../config/environment.php';

header('Content-Type: application/json; charset=UTF-8');
session_start();

/* Evitar que notices/warnings HTML rompan el JSON */
error_reporting(E_ALL);
ini_set('display_errors', '0');                 // no imprimir errores en la salida
ini_set('log_errors', '1');                     // registrar errores en archivo
@ini_set('error_log', __DIR__ . '/../logs/pagos.log'); // asegúrate de que /logs/ exista y sea escribible

try {
  // 1) Seguridad básica
  if (empty($_SESSION['id'])) {
    throw new Exception('Sesión expirada. Vuelva a iniciar sesión.');
  }
  $usuarioId = (int) $_SESSION['id'];

  // 2) Autoload de Composer (tu proyecto lo tiene en /extensiones/vendor)
  $root = dirname(__DIR__); // /HOTEL
  $autoload = $root . '/extensiones/vendor/autoload.php';
  if (!file_exists($autoload)) {
    throw new Exception('No se encontró extensiones/vendor/autoload.php');
  }
  require_once $autoload;

  // (opcional) Verificar que el SDK v3 esté cargado
  if (!class_exists(PaymentClient::class)) {
    throw new Exception('SDK v3 de Mercado Pago no disponible.');
  }

  // 3) Capas MVC
  require_once $root . '/controladores/reservas.controlador.php';
  require_once $root . '/modelos/reservas.modelo.php';
  require_once $root . '/controladores/habitaciones.controlador.php';
  require_once $root . '/modelos/habitaciones.modelo.php';
  require_once $root . '/controladores/categorias.controlador.php';
  require_once $root . '/modelos/categorias.modelo.php';
  require_once $root . '/controladores/planes.controlador.php';
  require_once $root . '/modelos/planes.modelo.php';

  // 4) Leer JSON del frontend
  $raw = file_get_contents('php://input');
  $input = json_decode($raw, true);
  if (!is_array($input)) {
    throw new Exception('JSON inválido.');
  }
  $card    = $input['cardFormData'] ?? [];
  $reserva = $input['reserva']      ?? []; // idHabitacion, fechas, plan, etc.

  // 5) Validar campos mínimos del pago
  $token           = $card['token'] ?? null;
  $paymentMethodId = $card['payment_method_id'] ?? ($card['paymentMethodId'] ?? null);
  $issuerId        = $card['issuer_id'] ?? ($card['issuerId'] ?? null);
  $installments    = isset($card['installments']) ? (int)$card['installments'] : 1;

  if (!$token || !$paymentMethodId) {
    throw new Exception('Faltan datos de la tarjeta.');
  }

  // 6) Validar datos de reserva
  $idHabitacion  = (int)($reserva['idHabitacion'] ?? 0);
  $fechaIngreso  = $reserva['fechaIngreso'] ?? null;
  $fechaSalida   = $reserva['fechaSalida']  ?? null;
  $plan          = $reserva['plan'] ?? 'Plan Continental';
  $personas      = (int)($reserva['personas'] ?? 2);
  $codigoReserva = $reserva['codigoReserva'] ?? null;

  if ($idHabitacion <= 0 || !$fechaIngreso || !$fechaSalida) {
    throw new Exception('Datos de reserva incompletos.');
  }
  if ($fechaIngreso >= $fechaSalida) {
    throw new Exception('Rango de fechas inválido.');
  }

  // 7) Configuración general
  date_default_timezone_set('America/Lima');

  // 8) Recalcular monto en el servidor (no confiar en el front)
  //    (Tu lógica original de precios/temporadas)
  $reservasPorHab = ControladorReservas::ctrMostrarReservas($idHabitacion);
  $planes         = ControladorPlanes::ctrMostrarPlanes();

  $hoy  = getdate();
  $alta =
    ($hoy["mon"] == 12 && $hoy["mday"] >= 15 && $hoy["mday"] <= 31) ||
    ($hoy["mon"] ==  1 && $hoy["mday"] >=  1 && $hoy["mday"] <= 15) ||
    ($hoy["mon"] ==  6 && $hoy["mday"] >= 15 && $hoy["mday"] <= 31) ||
    ($hoy["mon"] ==  7 && $hoy["mday"] >=  1 && $hoy["mday"] <= 15);

  $habitacion = ControladorHabitaciones::ctrMostrarHabitacion($idHabitacion);
  if (!$habitacion) throw new Exception('Habitación inexistente.');
  $categoria  = ControladorCategorias::ctrMostrarCategoria($habitacion['tipo_h']);
  if (!$categoria)  throw new Exception('Categoría de habitación no encontrada.');

  $precioBase = 0.0;
  if (stripos($plan, 'Continental') !== false) {
    $precioBase = $alta ? $categoria['continental_alta'] : $categoria['continental_baja'];
  } elseif (stripos($plan, 'Americano') !== false) {
    $precioBase = $alta ? $categoria['americano_alta'] : $categoria['americano_baja'];
  } else {
    $precioAmericano = $alta ? $categoria['americano_alta'] : $categoria['americano_baja'];
    $map = ['romantico'=>0,'luna de miel'=>1,'aventura'=>2,'spa'=>3];
    $clave  = strtolower(trim(str_replace('Plan', '', $plan)));
    $clave  = preg_replace('/\s+/', ' ', $clave);
    $indice = null; foreach ($map as $k => $i) { if (strpos($clave, $k) !== false) { $indice = $i; break; } }
    if ($indice === null || !isset($planes[$indice])) throw new Exception('Plan inválido.');
    $precioPlan = $alta ? $planes[$indice]['precio_alta'] : $planes[$indice]['precio_baja'];
    $precioBase = $precioAmericano + $precioPlan;
  }

  $ing  = new DateTime($fechaIngreso);
  $sal  = new DateTime($fechaSalida);
  $dias = max(1, (int)$ing->diff($sal)->days);
  $monto = (float)$precioBase * $dias;

  // 9) Evitar doble reserva (cruce de fechas)
  $existentes = ControladorReservas::ctrMostrarReservas($idHabitacion);
  if ($existentes) {
    foreach ($existentes as $r) {
      $fi = $r['fecha_ingreso'];
      $fs = $r['fecha_salida'];
      if ($fi === '0000-00-00' || $fs === '0000-00-00' || $fs <= $fi) {
      continue; // ignora filas inválidas
    }

      $op1 = ($fechaIngreso != $fi);
      $op2 = !($fechaIngreso > $fi && $fechaIngreso < $fs);
      $op3 = !($fechaIngreso < $fi && $fechaSalida  > $fi);
    if ($fechaIngreso < $fs && $fechaSalida > $fi) {
      
      throw new Exception('Fechas ocupadas, por favor elige otras.');
    }
    }
  }

  /* ========= SDK v3 (Bricks) ========= */
  // Reemplaza por tu Access Token de test o producción
  MercadoPagoConfig::setAccessToken(app_env('MERCADOPAGO_ACCESS_TOKEN'));

  $client         = new PaymentClient();
  $idempotencyKey = $codigoReserva ?: (string)uniqid('res_', true);
  $reqOpts        = new RequestOptions();
  $reqOpts->setCustomHeaders(['X-Idempotency-Key: ' . $idempotencyKey]);

  $payerEmail = $reserva['payerEmail'] ?? 'test_user@example.com';

  try {
    $payment = $client->create([
      'transaction_amount' => (float)$monto,
      'token'              => $token,
      'description'        => 'Reserva ' . ($reserva['infoHabitacion'] ?? ($categoria['tipo'].' '.$habitacion['estilo'].' - '.$plan)),
      'installments'       => (int)$installments,
      'payment_method_id'  => $paymentMethodId,
      'issuer_id'          => !empty($issuerId) ? (int)$issuerId : null,
      'payer'              => ['email' => $payerEmail],
    ], $reqOpts);
  } catch (MPApiException $e) {
    $api = $e->getApiResponse();
    http_response_code($api->getStatusCode() ?: 400);
    echo json_encode([
      'error'   => true,
      'message' => 'Mercado Pago: '.$api->getStatusCode().' '.json_encode($api->getContent(), JSON_UNESCAPED_UNICODE)
    ]);
    exit;
  }

  // 11) Responder según estado
  if ($payment->status === 'approved') {
    if (!$codigoReserva) {
      $codigoReserva = strtoupper(substr(md5($usuarioId.$idHabitacion.time()), 0, 9));
    }

    $datos = [
      'id_habitacion'       => $idHabitacion,
      'id_usuario'          => $usuarioId,
      'pago_reserva'        => $monto,
      'numero_transaccion'  => $payment->id,
      'codigo_reserva'      => $codigoReserva,
      'descripcion_reserva' => $payment->description,
      'fecha_ingreso'       => $fechaIngreso,
      'fecha_salida'        => $fechaSalida,
    ];

    $ok = ControladorReservas::ctrGuardarReserva($datos);

    echo json_encode([
      'status' => 'approved',
      'saved'  => ($ok === 'ok'),
      'id'     => $payment->id,
      'monto'  => $monto,
    ]);
    exit;
  }

  echo json_encode([
    'status'        => $payment->status,
    'status_detail' => $payment->status_detail,
  ]);
  exit;

} catch (Throwable $e) {
  http_response_code(400);
  echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
