-- Prueba Técnica SQL

-- 1. Creación de Tablas
-- Tabla de Usuarios (Módulo de Autenticación)

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Clientes (CRUD y Prueba SQL)
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- Tabla de Ventas (Prueba SQL)
CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    idcalendario DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);

-- Tabla de Bitácora (Módulo de Auditoría)
CREATE TABLE bitacora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo_accion VARCHAR(20) NOT NULL, -- 'UPDATE' o 'DELETE'
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    datos_anteriores JSON,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 2. Insertar 5 Clientes de prueba

INSERT INTO clientes (nombre, apellido, email, telefono, fecha_registro, activo) VALUES
('Ana', 'García', 'ana.garcia@email.com', '7000-1111', '2026-04-01', 1),
('Carlos', 'Méndez', 'carlos.mendez@email.com', '7000-2222', '2026-04-02', 1),
('Laura', 'Ríos', 'laura.rios@email.com', '7000-3333', '2026-04-03', 1),
('Roberto', 'Sánchez', 'roberto.sanchez@email.com', '7000-4444', '2026-04-04', 1),
('Elena', 'Torres', 'elena.torres@email.com', '7000-5555', '2026-04-05', 1);

-- 3. Insertar 10 Ventas de prueba (Con diferentes fechas y estados: PAGADA, PENDIENTE, CANCELADA)

INSERT INTO ventas (id_cliente, idcalendario, monto, estado) VALUES
-- Ventas del día 1 => $450.00
(1, '2026-04-10', 150.30, 'PAGADA'),
(2, '2026-04-10', 299.70, 'PENDIENTE'),

-- Ventas del día 2 => $579.15
(1, '2026-04-11', 70.85, 'CANCELADA'),
(3, '2026-04-11', 579.15, 'PAGADA'),

-- Ventas del día 3 => $800.00
(4, '2026-04-12', 450.00, 'PAGADA'),
(5, '2026-04-12', 350.00, 'PENDIENTE'),

-- Ventas del día 4 => $1000.00
(2, '2026-04-13', 500.00, 'PAGADA'),
(3, '2026-04-13', 500.00, 'PAGADA'),

-- Ventas del día 5 => $500.00
(1, '2026-04-14', 300.00, 'CANCELADA'),
(4, '2026-04-14', 500.00, 'PAGADA');

-- Consultas
-- 4. Consulta 1: JOIN
SELECT c.nombre, c.email, v.idcalendario, v.monto, v.estado
FROM clientes c
INNER JOIN ventas v
ON c.id_cliente = v.id_cliente;

-- Consulta 2: JOIN, GROUP BY, COUNT(DISTINCT ..), SUM()
SELECT v.idcalendario,
    COUNT(DISTINCT v.id_cliente) AS cantidad_clientes,
    SUM(v.monto) AS monto_total
FROM ventas v
INNER JOIN clientes c ON v.id_cliente = c.id_cliente
GROUP BY v.idcalendario;

-- Consulta 3: CASE
SELECT idcalendario AS fecha,
       SUM(monto)   AS monto_total,
       SUM(
               CASE
                   WHEN estado != 'CANCELADA' THEN monto
                   ELSE 0
                   END
       )            AS total_vendido,
       SUM(
               CASE
                   WHEN estado = 'PAGADA' THEN monto
                   ELSE 0
                   END
       )            AS total_pagado,
       SUM(
               CASE
                   WHEN estado = 'PENDIENTE' THEN monto
                   ELSE 0
                   END
       )            AS total_pendiente,
       SUM(
               CASE
                   WHEN estado = 'CANCELADA' THEN monto
                   ELSE 0
                   END
       )            AS total_cancelado
FROM ventas
GROUP BY idcalendario;

-- Consulta 4: HAVING
SELECT idcalendario,
       SUM(monto) AS total_vendido
FROM ventas
GROUP BY idcalendario
HAVING SUM(
               CASE
                   WHEN estado != 'CANCELADA' THEN monto
                   ELSE 0
                   END
       ) > 500;

-- Consulta 5: Subconsulta simple
SELECT nombre, apellido, email, telefono, activo
FROM clientes
WHERE id_cliente IN (SELECT id_cliente
                     FROM ventas
                     GROUP BY id_cliente
                     HAVING COUNT(id_cliente) > 2);
