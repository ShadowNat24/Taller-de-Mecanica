<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../controlador/AuthController.php";
require_once __DIR__ . "/../controlador/AutoController.php";
require_once __DIR__ . "/../nucleo/Token.php";

$metodo = $_SERVER["REQUEST_METHOD"];

// Ruta de inicio de sesión.
if (API_RECURSO === "login") {
    if ($metodo !== "POST") {
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
        exit;
    }

    $datos = json_decode(file_get_contents("php://input"), true) ?? [];
    $usuario = $datos["usuario"] ?? "";
    $password = $datos["password"] ?? "";

    if ($usuario === "" || $password === "") {
        http_response_code(400);
        echo json_encode(["mensaje" => "Completá todos los campos."]);
        exit;
    }

    try {
        $controlador = new AuthController($conexion);
        $resultado = $controlador->iniciarSesion($usuario, $password);

        if (!$resultado) {
            http_response_code(401);
            echo json_encode(["mensaje" => "Usuario o contraseña incorrectos."]);
            exit;
        }

        echo json_encode([
            "mensaje" => "Inicio de sesión correcto.",
            "token" => $resultado["token"],
            "nombre" => $resultado["nombre"]
        ]);
    } catch (PDOException $error) {
        http_response_code(500);
        echo json_encode(["mensaje" => "No se pudo iniciar sesión. Revisá la base de datos."]);
    }

    exit;
}

// Las demás rutas necesitan un token válido.
$usuarioId = Token::validar($conexion);

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["mensaje" => "Token faltante o vencido. Iniciá sesión nuevamente."]);
    exit;
}

// Ruta de vehículos.
if (API_RECURSO === "autos") {
    $controlador = new AutoController($conexion);
    $controlador->procesar();
    exit;
}

http_response_code(404);
echo json_encode(["mensaje" => "Ruta no encontrada"]);
