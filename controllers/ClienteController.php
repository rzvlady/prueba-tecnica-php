<?php
session_start();
// Protección básica de ruta
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

require_once '../models/Cliente.php';
require_once '../models/Bitacora.php';

// Le decimos al navegador que responderemos con JSON
header('Content-Type: application/json');

$clienteModel = new Cliente();
$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch ($action) {
        case 'listar':
            $clientes = $clienteModel->getAll();
            echo json_encode(['status' => 'success', 'data' => $clientes]);
            break;

        case 'guardar':
            // Recibimos y sanitizamos
            $id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
            $telefono = trim($_POST['telefono']);

            // Validaciones
            if (empty($nombre) || empty($apellido) || empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Nombre, apellido y correo son obligatorios.']);
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'message' => 'El formato del correo es inválido.']);
                exit();
            }

            if ($clienteModel->emailExists($email, $id_cliente)) {
                echo json_encode(['status' => 'error', 'message' => 'Este correo ya está registrado en otro cliente.']);
                exit();
            }

            $bitacoraModel = new Bitacora();

            // Si hay ID, es Actualización
            if (!empty($id_cliente)) {
                // 1. ANTES DE ACTUALIZAR: Buscamos los datos actuales del cliente
                $cliente_viejo = $clienteModel->getById($id_cliente);
                // 2. Convertimos ese registro (array) a un string JSON
                $json_datos_anteriores = json_encode($cliente_viejo);

                // 3. Ejecutamos la actualización real
                $clienteModel->update($id_cliente, $nombre, $apellido, $email, $telefono);

                // 4. Registramos en la bitácora usando el ID del usuario en sesión
                $bitacoraModel->registrar($id_cliente, $_SESSION['usuario_id'], 'UPDATE', $json_datos_anteriores);

                echo json_encode(['status' => 'success', 'message' => 'Cliente actualizado y auditado correctamente.']);
            } else {
                // Si no hay ID, es Creación (La prueba no pide auditar los INSERT, solo UPDATE y DELETE)
                $clienteModel->create($nombre, $apellido, $email, $telefono);
                echo json_encode(['status' => 'success', 'message' => 'Cliente creado correctamente.']);
            }
            break;

        case 'eliminar':
            $id_cliente = $_POST['id_cliente'];
            $bitacoraModel = new Bitacora();

            // 1. ANTES DE ELIMINAR: Obtenemos cómo estaba el cliente
            $cliente_viejo = $clienteModel->getById($id_cliente);
            $json_datos_anteriores = json_encode($cliente_viejo);

            // 2. Ejecutamos la eliminación (lógica)
            $clienteModel->delete($id_cliente);

            // 3. Registramos la eliminación en la bitácora
            $bitacoraModel->registrar($id_cliente, $_SESSION['usuario_id'], 'DELETE', $json_datos_anteriores);

            echo json_encode(['status' => 'success', 'message' => 'Cliente eliminado y auditado correctamente.']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor: ' . $e->getMessage()]);
}