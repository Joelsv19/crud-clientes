<?php
if (!isset($clientes) && !isset($errorBaseDatos)) {
    http_response_code(404);
    exit;
}

$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$idEdicion = $formulario['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de clientes</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <main class="contenedor">
        <header>
            <p class="materia">Aplicaciones Web · Semana 2</p>
            <h1>Gestión de clientes</h1>
        </header>

        <?php if (isset($errorBaseDatos)): ?>
            <p class="aviso error" role="alert"><?= $escapar($errorBaseDatos) ?></p>
        <?php else: ?>
            <?php if ($error !== null): ?>
                <p class="aviso error" role="alert"><?= $escapar($error) ?></p>
            <?php endif; ?>
            <?php if ($mensaje !== null): ?>
                <p class="aviso exito" role="status"><?= $escapar($mensaje) ?></p>
            <?php endif; ?>

            <?php if ($clienteEliminar !== null): ?>
                <section class="panel" aria-labelledby="titulo-confirmacion">
                    <h2 id="titulo-confirmacion">Confirmar eliminación</h2>
                    <p>¿Deseas eliminar a <strong><?= $escapar($clienteEliminar['nombre'] . ' ' . $clienteEliminar['apellido']) ?></strong>?</p>
                    <form method="post" action="index.php" class="acciones">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= $escapar($clienteEliminar['id']) ?>">
                        <input type="hidden" name="token" value="<?= $escapar($token) ?>">
                        <a href="index.php" class="boton">Cancelar</a>
                        <button type="submit" class="boton peligro">Confirmar eliminación</button>
                    </form>
                </section>
            <?php else: ?>
                <section class="panel" aria-labelledby="titulo-formulario">
                    <h2 id="titulo-formulario"><?= $idEdicion === null ? 'Registrar cliente' : 'Editar cliente' ?></h2>
                    <form method="post" action="index.php">
                        <input type="hidden" name="accion" value="<?= $idEdicion === null ? 'guardar' : 'actualizar' ?>">
                        <input type="hidden" name="token" value="<?= $escapar($token) ?>">
                        <?php if ($idEdicion !== null): ?>
                            <input type="hidden" name="id" value="<?= $escapar($idEdicion) ?>">
                        <?php endif; ?>

                        <div class="campos">
                            <div>
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre" name="nombre" maxlength="80" required
                                    value="<?= $escapar($formulario['nombre'] ?? '') ?>">
                            </div>
                            <div>
                                <label for="apellido">Apellido</label>
                                <input type="text" id="apellido" name="apellido" maxlength="80" required
                                    value="<?= $escapar($formulario['apellido'] ?? '') ?>">
                            </div>
                            <div>
                                <label for="correo">Correo electrónico</label>
                                <input type="email" id="correo" name="correo" maxlength="254" required
                                    value="<?= $escapar($formulario['correo'] ?? '') ?>">
                            </div>
                            <div>
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" maxlength="25" required
                                    value="<?= $escapar($formulario['telefono'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="acciones">
                            <button type="submit" class="boton primario"><?= $idEdicion === null ? 'Guardar' : 'Guardar cambios' ?></button>
                            <?php if ($idEdicion !== null): ?>
                                <a href="index.php" class="boton">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <section aria-labelledby="titulo-tabla">
                <h2 id="titulo-tabla">Clientes registrados</h2>
                <div class="tabla" role="region" aria-labelledby="titulo-tabla" tabindex="0">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido</th>
                                <th scope="col">Correo electrónico</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($clientes === []): ?>
                                <tr><td colspan="6" class="vacio">Aún no hay clientes registrados.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?= $escapar($cliente['id']) ?></td>
                                    <td><?= $escapar($cliente['nombre']) ?></td>
                                    <td><?= $escapar($cliente['apellido']) ?></td>
                                    <td><?= $escapar($cliente['correo']) ?></td>
                                    <td><?= $escapar($cliente['telefono']) ?></td>
                                    <td>
                                        <div class="acciones">
                                            <a class="boton" href="index.php?accion=editar&amp;id=<?= $escapar($cliente['id']) ?>">Editar</a>
                                            <a class="boton enlace-peligro" href="index.php?accion=confirmar&amp;id=<?= $escapar($cliente['id']) ?>">Eliminar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
