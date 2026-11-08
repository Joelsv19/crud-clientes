<?php

class Cliente
{
    public function __construct(private mysqli $conexion)
    {
    }

    public function guardar(string $nombre, string $apellido, string $correo, string $telefono): void
    {
        $consulta = $this->conexion->prepare(
            'INSERT INTO clientes (nombre, apellido, correo, telefono) VALUES (?, ?, ?, ?)'
        );
        $consulta->bind_param('ssss', $nombre, $apellido, $correo, $telefono);
        $consulta->execute();
    }

    public function listar(): array
    {
        $resultado = $this->conexion->query(
            'SELECT id, nombre, apellido, correo, telefono FROM clientes ORDER BY id DESC'
        );

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId(int $id): ?array
    {
        $consulta = $this->conexion->prepare(
            'SELECT id, nombre, apellido, correo, telefono FROM clientes WHERE id = ?'
        );
        $consulta->bind_param('i', $id);
        $consulta->execute();

        return $consulta->get_result()->fetch_assoc();
    }

    public function actualizar(int $id, string $nombre, string $apellido, string $correo, string $telefono): bool
    {
        $consulta = $this->conexion->prepare(
            'UPDATE clientes SET nombre = ?, apellido = ?, correo = ?, telefono = ? WHERE id = ?'
        );
        $consulta->bind_param('ssssi', $nombre, $apellido, $correo, $telefono, $id);
        $consulta->execute();

        // La conexión cuenta las filas encontradas, incluso si los datos siguen iguales.
        return $consulta->affected_rows > 0;
    }

    public function eliminar(int $id): bool
    {
        $consulta = $this->conexion->prepare('DELETE FROM clientes WHERE id = ?');
        $consulta->bind_param('i', $id);
        $consulta->execute();

        return $consulta->affected_rows > 0;
    }
}
