<?php

require_once "config/conexion.php";

$nombre = "Administrador";
$usuario = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);

$consulta = $conexion->prepare(
    "SELECT id FROM usuarios WHERE usuario = ?"
);

$consulta->execute([$usuario]);

if ($consulta->fetch()) {
    echo "El usuario admin ya existe.";
} else {

    $consulta = $conexion->prepare(
        "INSERT INTO usuarios (nombre, usuario, password)
         VALUES (?, ?, ?)"
    );

    $consulta->execute([$nombre, $usuario, $password]);

    echo "Usuario creado correctamente.";
}
