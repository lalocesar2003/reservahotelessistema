<?php

if (!defined('HOTEL_ENV_LOADED')) {
    define('HOTEL_ENV_LOADED', true);
    $root = dirname(__DIR__);
    $autoload = $root . '/extensiones/vendor/autoload.php';

    if (!file_exists($autoload)) {
        throw new RuntimeException('Falta extensiones/vendor/autoload.php. Ejecute composer install en extensiones/.');
    }

    require_once $autoload;

    if (!file_exists($root . '/.env')) {
        throw new RuntimeException('Falta el archivo .env en la raiz del proyecto.');
    }

    $dotenv = Dotenv\Dotenv::createImmutable($root);
    $dotenv->load();
    $dotenv->required([
        'APP_URL', 'BACKEND_URL', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD',
        'SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'SMTP_ENCRYPTION',
        'SMTP_FROM_ADDRESS', 'SMTP_FROM_NAME', 'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET',
        'GOOGLE_REDIRECT_URI', 'MERCADOPAGO_PUBLIC_KEY', 'MERCADOPAGO_ACCESS_TOKEN',
    ])->notEmpty();
}

if (!function_exists('app_env')) {
    function app_env($key, $default = null)
    {
        return array_key_exists($key, $_ENV) ? $_ENV[$key] : $default;
    }
}
