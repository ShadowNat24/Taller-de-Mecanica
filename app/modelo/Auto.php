<?php

class Auto
{
    public static function listar($conexion, $estado = "")
    {
        if ($estado !== "") {
            $consulta = $conexion->prepare(
                "SELECT * FROM autos WHERE estado = ?"
            );
            $consulta->execute([$estado]);
        } else {
            $consulta = $conexion->query("SELECT * FROM autos");
        }

        return $consulta->fetchAll();
    }

    public static function buscarPorId($conexion, $id)
    {
        $consulta = $conexion->prepare(
            "SELECT * FROM autos WHERE id = ?"
        );
        $consulta->execute([$id]);

        return $consulta->fetch();
    }

    public static function crear($conexion, $datos)
    {
        $consulta = $conexion->prepare(
            "INSERT INTO autos (modelo, marca, anio, fecha_ingreso)
             VALUES (?, ?, ?, ?)"
        );

        $consulta->execute([
            $datos["modelo"],
            $datos["marca"],
            $datos["anio"],
            $datos["fecha_ingreso"]
        ]);

        return $conexion->lastInsertId();
    }

    public static function actualizar($conexion, $id, $datos)
    {
        $consulta = $conexion->prepare(
            "UPDATE autos SET
                modelo = ?,
                marca = ?,
                anio = ?,
                fecha_ingreso = ?,
                fecha_salida = ?,
                estado = ?
             WHERE id = ?"
        );

        return $consulta->execute([
            $datos["modelo"],
            $datos["marca"],
            $datos["anio"],
            $datos["fecha_ingreso"],
            $datos["fecha_salida"] ?? null,
            $datos["estado"],
            $id
        ]);
    }

    public static function actualizarEstado($conexion, $id, $estado)
    {
        if ($estado === "Reparado") {
            $consulta = $conexion->prepare(
                "UPDATE autos
                 SET estado = ?, fecha_salida = CURDATE()
                 WHERE id = ?"
            );
        } else {
            $consulta = $conexion->prepare(
                "UPDATE autos
                 SET estado = ?, fecha_salida = NULL
                 WHERE id = ?"
            );
        }

        return $consulta->execute([$estado, $id]);
    }

    public static function eliminar($conexion, $id)
    {
        $consulta = $conexion->prepare(
            "DELETE FROM autos WHERE id = ?"
        );

        return $consulta->execute([$id]);
    }
}
