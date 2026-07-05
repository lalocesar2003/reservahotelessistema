<?php
// Inicia la sesión si aún no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Si la página de pago ya se completó (ok), no borres las cookies
if (isset($_GET['ok'])) {
  // No borrar cookies
} else {
  // Si la cookie 'codigoReserva' no está vacía y la sesión actual no es la misma que la de la cookie
  if (!empty($_COOKIE['codigoReserva'])) {
    $currentUser = (int)($_SESSION['id'] ?? 0);
    $ownerCookie = (int)($_COOKIE['reservaUid'] ?? 0);
    if ($ownerCookie && $ownerCookie !== $currentUser) {
      // No borrar las cookies si pertenecen a otro usuario
      // O puedes hacer una acción adicional como renovar la cookie para el usuario actual
    }
  }
}
