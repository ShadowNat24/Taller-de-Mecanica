<?php

class AuthController
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function iniciarSesion($usuario, $password)
    {

        $consulta = $this->conexion->prepare(
            "SELECT * FROM usuarios WHERE usuario = ?"
        );

        $consulta->execute([$usuario]);

        $datos = $consulta->fetch();

        if ($datos && password_verify($password, $datos["password"])) {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);

            $_SESSION["id"] = $datos["id"];
            $_SESSION["nombre"] = $datos["nombre"];

            return true;
        }

        return false;
    }

    public function cerrarSesion()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
    }
}
