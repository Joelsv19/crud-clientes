CREATE DATABASE IF NOT EXISTS crud_clientes
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE crud_clientes;

CREATE TABLE IF NOT EXISTS clientes (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    correo VARCHAR(254) NOT NULL,
    telefono VARCHAR(25) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
