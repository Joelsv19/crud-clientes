<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ClienteController.php';

session_start();
$_SESSION['token_clientes'] ??= bin2hex(random_bytes(32));

try {
    $baseDatos = new Database();
    $controlador = new ClienteController($baseDatos->conectar());

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controlador->procesar();
    } else {
        $controlador->index();
    }
} catch (mysqli_sql_exception $excepcion) {
    error_log($excepcion->getMessage());
    http_response_code(500);
    $errorBaseDatos = match ($excepcion->getCode()) {
        1049, 1146 => 'Falta la base de datos o la tabla. Importa el archivo base_de_datos.sql.',
        1044, 1045, 2002, 2003 => 'No se pudo conectar. Revisa MySQL y los datos de config/database.php.',
        default => 'No se pudo completar la operación en la base de datos.',
    };

    require __DIR__ . '/views/clientes.php';
}
