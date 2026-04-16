<?php
require_once '../config/database.php';

class Bitacora
{
    private $conn;

    public function __construct()
    {
        $database = new Conexion();
        $this->conn = $database->getConexion();
    }

    // Método para registrar la acción
    public function registrar($id_cliente, $usuario_id, $tipo_accion, $datos_anteriores)
    {
        $query = "INSERT INTO bitacora (id_cliente, usuario_id, tipo_accion, datos_anteriores, fecha) 
                  VALUES (:id_cliente, :usuario_id, :tipo_accion, :datos_anteriores, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':tipo_accion', $tipo_accion);
        $stmt->bindParam(':datos_anteriores', $datos_anteriores);

        return $stmt->execute();
    }

    // Obtener transacciones con filtros dinámicos
    public function obtenerTransacciones($cliente_id = '', $tipo_accion = '')
    {
        $query = "SELECT b.*, u.usuario as nombre_usuario, c.nombre as nombre_cliente, c.apellido as apellido_cliente
                  FROM bitacora b
                  INNER JOIN usuarios u ON b.usuario_id = u.id
                  LEFT JOIN clientes c ON b.id_cliente = c.id_cliente
                  WHERE 1=1";

        $params = [];

        // Filtro por Cliente
        if (!empty($cliente_id)) {
            $query .= " AND b.id_cliente = :cliente_id";
            $params[':cliente_id'] = $cliente_id;
        }

        // Filtro por Tipo de Acción
        if (!empty($tipo_accion)) {
            $query .= " AND b.tipo_accion = :tipo_accion";
            $params[':tipo_accion'] = $tipo_accion;
        }

        // Ordenar por fecha descendente (lo más reciente primero)
        $query .= " ORDER BY b.fecha DESC";

        $stmt = $this->conn->prepare($query);

        // Enlazamos los parámetros dinámicamente
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
}