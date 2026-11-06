<?php

class Token
{
    // Crear un token aleatorio y guardar su hash en la base de datos.
    public static function crear($conexion, $usuarioId)
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash("sha256", $token);
        $expira = date("Y-m-d H:i:s", time() + (8 * 60 * 60));

        $consulta = $conexion->prepare(
            "INSERT INTO tokens (usuario_id, token_hash, expira_en)
             VALUES (?, ?, ?)"
        );

        $consulta->execute([$usuarioId, $tokenHash, $expira]);

        return $token;
    }

    // Comprobar el token que llega en Authorization: Bearer ...
    public static function validar($conexion)
    {
        $encabezado = $_SERVER["HTTP_AUTHORIZATION"] ?? "";

        if ($encabezado === "" && function_exists("getallheaders")) {
            foreach (getallheaders() as $nombre => $valor) {
                if (strtolower($nombre) === "authorization") {
                    $encabezado = $valor;
                    break;
                }
            }
        }

        if (!preg_match("/^Bearer\s+(.+)$/i", $encabezado, $coincidencias)) {
            return null;
        }

        $tokenHash = hash("sha256", trim($coincidencias[1]));

        $consulta = $conexion->prepare(
            "SELECT usuario_id
             FROM tokens
             WHERE token_hash = ? AND expira_en > NOW()"
        );

        $consulta->execute([$tokenHash]);
        $resultado = $consulta->fetch();

        return $resultado ? $resultado["usuario_id"] : null;
    }
}
