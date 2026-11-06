<?php

class Usuario
{
    public static function buscarPorUsuario($conexion, $usuario)
    {
        $consulta = $conexion->prepare(
            "SELECT id, nombre, usuario, password
             FROM usuarios
             WHERE usuario = ?"
        );

        $consulta->execute([$usuario]);

        return $consulta->fetch();
    }
}
