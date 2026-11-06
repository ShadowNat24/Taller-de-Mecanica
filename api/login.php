<?php

header("Content-Type: application/json");

require_once "../config/conexion.php";
require_once "../controlador/AuthController.php";

$datos = json_decode(file_get_contents("php://input"), true);

$usuario = $datos["usuario"] ?? "";
$password = $datos["password"] ?? "";

if ($usuario == "" || $password == "") {

    http_response_code(400);

    echo json_encode([
        "mensaje" => "Completá todos los campos."
    ]);

    exit;
}

$auth = new AuthController($conexion);

if ($auth->iniciarSesion($usuario, $password)) {

    echo json_encode([
        "mensaje" => "Inicio de sesión correcto."
    ]);
} else {

    http_response_code(401);

    echo json_encode([
        "mensaje" => "Usuario o contraseña incorrectos."
    ]);
}
