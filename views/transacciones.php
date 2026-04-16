<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../models/Bitacora.php';
require_once '../models/Cliente.php';

$bitacoraModel = new Bitacora();
$clienteModel = new Cliente();

// Capturamos los filtros de la URL (si existen)
$filtro_cliente = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : '';
$filtro_accion = isset($_GET['tipo_accion']) ? $_GET['tipo_accion'] : '';

// Obtenemos los datos pasándole los filtros
$transacciones = $bitacoraModel->obtenerTransacciones($filtro_cliente, $filtro_accion);
$clientes = $clienteModel->getAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Transacciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">⬅ Volver al Dashboard</a>
            <span class="navbar-text text-white">Auditoría del Sistema</span>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Bitácora de Movimientos</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="transacciones.php" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Cliente</label>
                        <select name="cliente_id" class="form-select">
                            <option value="">Todos los clientes...</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>" <?= ($filtro_cliente == $c['id_cliente']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Acción</label>
                        <select name="tipo_accion" class="form-select">
                            <option value="">Todas las acciones...</option>
                            <option value="UPDATE" <?= ($filtro_accion == 'UPDATE') ? 'selected' : '' ?>>Actualización
                                (UPDATE)</option>
                            <option value="DELETE" <?= ($filtro_accion == 'DELETE') ? 'selected' : '' ?>>Eliminación
                                (DELETE)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover m-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Cliente Afectado</th>
                            <th>Datos Anteriores (JSON)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($transacciones) > 0): ?>
                            <?php foreach ($transacciones as $t): ?>
                                <tr>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($t['fecha'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($t['nombre_usuario']) ?>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?= $t['tipo_accion'] == 'DELETE' ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                            <?= $t['tipo_accion'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($t['nombre_cliente'] . ' ' . $t['apellido_cliente']) ?>
                                    </td>
                                    <td>
                                        <small class="text-muted" style="word-break: break-all;">
                                            <?php
                                            // Convertimos el string JSON de vuelta a un array de PHP para leerlo
                                            $datos = json_decode($t['datos_anteriores'], true);
                                            // Si existe, mostramos un resumen (ej. correo anterior)
                                            if ($datos) {
                                                echo "<b>Email Antiguo:</b> " . htmlspecialchars($datos['email']) . "<br>";
                                                echo "<i>JSON Raw:</i> " . htmlspecialchars($t['datos_anteriores']);
                                            } else {
                                                echo "No hay datos";
                                            }
                                            ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">No se encontraron registros.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>