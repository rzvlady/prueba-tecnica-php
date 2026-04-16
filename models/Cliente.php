<?php
require_once '../config/database.php';

class Cliente
{
    private $conn;

    public function __construct()
    {
        $database = new Conexion();
        $this->conn = $database->getConexion();
    }

    // Obtener todos los clientes activos
    public function getAll()
    {
        $query = "SELECT * FROM clientes WHERE activo = 1 ORDER BY id_cliente DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener un cliente específico por su ID (Para la Bitácora)
    public function getById($id_cliente)
    {
        $query = "SELECT * FROM clientes WHERE id_cliente = :id_cliente LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Validar si el email ya existe (evitar duplicados)
    public function emailExists($email, $id_cliente = null)
    {
        $query = "SELECT id_cliente FROM clientes WHERE email = :email";
        // Si estamos editando, ignoramos el correo del propio cliente
        if ($id_cliente) {
            $query .= " AND id_cliente != :id_cliente";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($id_cliente) {
            $stmt->bindParam(':id_cliente', $id_cliente);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Crear cliente (fecha_registro es automática)
    public function create($nombre, $apellido, $email, $telefono)
    {
        $query = "INSERT INTO clientes (nombre, apellido, email, telefono, fecha_registro, activo) 
                  VALUES (:nombre, :apellido, :email, :telefono, CURDATE(), 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        return $stmt->execute();
    }

    // Actualizar cliente
    public function update($id_cliente, $nombre, $apellido, $email, $telefono)
    {
        $query = "UPDATE clientes SET nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono 
                  WHERE id_cliente = :id_cliente";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':id_cliente', $id_cliente);
        return $stmt->execute();
    }

    // Eliminación lógica
    public function delete($id_cliente)
    {
        $query = "UPDATE clientes SET activo = 0 WHERE id_cliente = :id_cliente";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cliente', $id_cliente);
        return $stmt->execute();
    }
}