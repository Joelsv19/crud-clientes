<?php

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    private Cliente $modelo;

    public function __construct(mysqli $conexion)
    {
        $this->modelo = new Cliente($conexion);
    }

    public function index(): void
    {
        $accion = $_GET['accion'] ?? 'listar';

        if ($accion === 'listar') {
            $this->mostrarVista();
            return;
        }

        if ($accion !== 'editar' && $accion !== 'confirmar') {
            $this->mostrarError('La acción solicitada no existe.', 404);
            return;
        }

        $id = $this->validarId($_GET['id'] ?? null);
        if ($id === false) {
            $this->mostrarError('El identificador del cliente no es válido.', 400);
            return;
        }

        $cliente = $this->modelo->buscarPorId($id);
        if ($cliente === null) {
            $this->mostrarError('No se encontró el cliente.', 404);
            return;
        }

        if ($accion === 'confirmar') {
            $this->mostrarVista(clienteEliminar: $cliente);
            return;
        }

        $this->mostrarVista(formulario: $cliente);
    }

    public function procesar(): void
    {
        $token = $_POST['token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['token_clientes'], $token)) {
            $this->mostrarError('La solicitud no es válida. Vuelve a intentarlo.', 403);
            return;
        }

        switch ($_POST['accion'] ?? '') {
            case 'guardar':
                $this->guardar(false);
                break;
            case 'actualizar':
                $this->guardar(true);
                break;
            case 'eliminar':
                $this->eliminar();
                break;
            default:
                $this->mostrarError('La acción solicitada no existe.', 404);
        }
    }

    private function guardar(bool $editar): void
    {
        $id = null;
        if ($editar) {
            $id = $this->validarId($_POST['id'] ?? null);
            if ($id === false) {
                $this->mostrarError('El identificador del cliente no es válido.', 400);
                return;
            }
        }

        $nombre = $this->leerCampo('nombre');
        $apellido = $this->leerCampo('apellido');
        $correo = $this->leerCampo('correo');
        $telefono = $this->leerCampo('telefono');
        $error = $this->validarCampos($nombre, $apellido, $correo, $telefono);

        if ($error !== null) {
            $this->mostrarError($error, 422, compact('id', 'nombre', 'apellido', 'correo', 'telefono'));
            return;
        }

        if ($editar) {
            if (!$this->modelo->actualizar($id, $nombre, $apellido, $correo, $telefono)) {
                $this->mostrarError('No se encontró el cliente.', 404);
                return;
            }

            $this->redirigir('Cliente actualizado correctamente.');
            return;
        }

        $this->modelo->guardar($nombre, $apellido, $correo, $telefono);
        $this->redirigir('Cliente registrado correctamente.');
    }

    private function eliminar(): void
    {
        $id = $this->validarId($_POST['id'] ?? null);
        if ($id === false) {
            $this->mostrarError('El identificador del cliente no es válido.', 400);
            return;
        }

        if (!$this->modelo->eliminar($id)) {
            $this->mostrarError('No se encontró el cliente.', 404);
            return;
        }

        $this->redirigir('Cliente eliminado correctamente.');
    }

    private function leerCampo(string $campo): string
    {
        $valor = $_POST[$campo] ?? '';
        return is_string($valor) ? trim($valor) : '';
    }

    private function validarCampos(string $nombre, string $apellido, string $correo, string $telefono): ?string
    {
        if ($nombre === '' || $apellido === '' || $correo === '' || $telefono === '') {
            return 'Completa todos los campos.';
        }

        if (!mb_check_encoding([$nombre, $apellido, $correo, $telefono], 'UTF-8')) {
            return 'Revisa los caracteres ingresados.';
        }

        if (mb_strlen($nombre, 'UTF-8') > 80 || mb_strlen($apellido, 'UTF-8') > 80) {
            return 'El nombre y el apellido admiten hasta 80 caracteres cada uno.';
        }

        if (strlen($correo) > 254 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return 'Ingresa un correo electrónico válido.';
        }

        if (strlen($telefono) > 25 || !preg_match('/\A\+?[0-9 ().-]+\z/', $telefono) || !preg_match('/[0-9]/', $telefono)) {
            return 'Ingresa un teléfono válido de hasta 25 caracteres.';
        }

        return null;
    }

    private function validarId(mixed $id): int|false
    {
        return filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 2147483647],
        ]);
    }

    private function mostrarError(string $error, int $codigo, array $formulario = []): void
    {
        http_response_code($codigo);
        $this->mostrarVista(formulario: $formulario, error: $error);
    }

    private function mostrarVista(array $formulario = [], ?array $clienteEliminar = null, ?string $error = null): void
    {
        $clientes = $this->modelo->listar();
        $mensaje = $_SESSION['mensaje_clientes'] ?? null;
        unset($_SESSION['mensaje_clientes']);
        $token = $_SESSION['token_clientes'];

        require __DIR__ . '/../views/clientes.php';
    }

    private function redirigir(string $mensaje): void
    {
        $_SESSION['mensaje_clientes'] = $mensaje;
        header('Location: index.php', true, 303);
        exit;
    }
}
