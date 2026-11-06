<?php

header("Content-Type: application/json");
session_start();

if (!isset($_SESSION["id"])) {

    http_response_code(401);

    echo json_encode([
        "mensaje" => "Debés iniciar sesión."
    ]);

    exit;
}

require_once "../config/conexion.php";

// Obtener los datos enviados
function obtenerDatos()
{
    return json_decode(file_get_contents("php://input"), true) ?? [];
}

// Obtener el método HTTP
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtener el ID, si se envía por la URL
$id = $_GET["id"] ?? null;

try {

    // GET: listar vehículos o buscar uno
    if ($metodo == "GET") {

        if ($id) {

            $consulta = $conexion->prepare(
                "SELECT * FROM autos WHERE id = ?"
            );

            $consulta->execute([$id]);

            $auto = $consulta->fetch();

            echo json_encode($auto ?: ["mensaje" => "Vehículo no encontrado"]);
        } else {

            $estado = $_GET["estado"] ?? "";

            if ($estado != "") {

                $consulta = $conexion->prepare(
                    "SELECT * FROM autos WHERE estado = ?"
                );

                $consulta->execute([$estado]);
            } else {

                $consulta = $conexion->query(
                    "SELECT * FROM autos"
                );
            }

            echo json_encode($consulta->fetchAll());
        }
    }

    // POST: registrar vehículo
    elseif ($metodo == "POST") {

        $datos = obtenerDatos();

        if (
            empty($datos["modelo"]) ||
            empty($datos["marca"]) ||
            empty($datos["anio"]) ||
            empty($datos["fecha_ingreso"])
        ) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos del vehículo"]);
            exit;
        }

        $consulta = $conexion->prepare(
            "INSERT INTO autos
            (modelo, marca, anio, fecha_ingreso)
            VALUES (?, ?, ?, ?)"
        );

        $consulta->execute([
            $datos["modelo"],
            $datos["marca"],
            $datos["anio"],
            $datos["fecha_ingreso"]
        ]);

        echo json_encode([
            "mensaje" => "Vehículo registrado correctamente",
            "id" => $conexion->lastInsertId()
        ]);
    }

    // PUT: modificar todos los datos del vehículo
    elseif ($metodo == "PUT") {

        $datos = obtenerDatos();

        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta el ID"]);
            exit;
        }

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

        $consulta->execute([
            $datos["modelo"],
            $datos["marca"],
            $datos["anio"],
            $datos["fecha_ingreso"],
            $datos["fecha_salida"] ?? null,
            $datos["estado"],
            $id
        ]);

        echo json_encode([
            "mensaje" => "Vehículo actualizado"
        ]);
    }

    // PATCH: modificar solamente el estado
    elseif ($metodo == "PATCH") {

        $datos = obtenerDatos();

        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta el ID"]);
            exit;
        }

        $estado = $datos["estado"] ?? "";

        if (!in_array($estado, ["En mantenimiento", "Reparado"])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Estado no válido"]);
            exit;
        }

        if ($estado == "Reparado") {

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

        $consulta->execute([$estado, $id]);

        echo json_encode([
            "mensaje" => "Estado actualizado"
        ]);
    }

    // DELETE: eliminar vehículo
    elseif ($metodo == "DELETE") {

        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta el ID"]);
            exit;
        }

        $consulta = $conexion->prepare(
            "DELETE FROM autos WHERE id = ?"
        );

        $consulta->execute([$id]);

        echo json_encode([
            "mensaje" => "Vehículo eliminado"
        ]);
    } else {

        http_response_code(405);

        echo json_encode([
            "mensaje" => "Método no permitido"
        ]);
    }
} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "mensaje" => "Error al trabajar con la base de datos"
    ]);
}
