<?php

require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../nucleo/Token.php";

class AuthController
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function iniciarSesion($usuario, $password)
    {
        $datos = Usuario::buscarPorUsuario($this->conexion, $usuario);

        if (!$datos || !password_verify($password, $datos["password"])) {
            return false;
        }

        $token = Token::crear($this->conexion, $datos["id"]);

        return [
            "token" => $token,
            "nombre" => $datos["nombre"]
        ];
    }
}
