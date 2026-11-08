<?php

class Database
{
    private string $servidor = '127.0.0.1';
    private int $puerto = 3306;
    private string $usuario = 'usuario_mysql';
    private string $contrasena = '';
    private string $baseDatos = 'crud_clientes';

    public function conectar(): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conexion = mysqli_init();
        $conexion->real_connect(
            $this->servidor,
            $this->usuario,
            $this->contrasena,
            $this->baseDatos,
            $this->puerto,
            null,
            MYSQLI_CLIENT_FOUND_ROWS
        );
        $conexion->set_charset('utf8mb4');

        return $conexion;
    }
}
