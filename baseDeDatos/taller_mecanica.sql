CREATE DATABASE IF NOT EXISTS taller_mecanica
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE taller_mecanica;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS autos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    anio YEAR NOT NULL,
    fecha_ingreso DATE NOT NULL,
    fecha_salida DATE NULL,
    estado ENUM('En mantenimiento', 'Reparado')
        NOT NULL DEFAULT 'En mantenimiento'
);

CREATE TABLE IF NOT EXISTS tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expira_en DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
