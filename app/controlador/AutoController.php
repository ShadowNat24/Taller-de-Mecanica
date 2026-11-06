<?php

require_once __DIR__ . "/../modelo/Auto.php";

class AutoController
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    private function responder($datos, $estadoHttp = 200)
    {
        http_response_code($estadoHttp);
        echo json_encode($datos);
    }

    private function obtenerDatos()
    {
        return json_decode(file_get_contents("php://input"), true) ?? [];
    }

    public function procesar()
    {
        $metodo = $_SERVER["REQUEST_METHOD"];
        $id = $_GET["id"] ?? null;

        try {
            if ($metodo === "GET") {
                if ($id !== null) {
                    $auto = Auto::buscarPorId($this->conexion, $id);

                    if (!$auto) {
                        $this->responder(["mensaje" => "Vehículo no encontrado"], 404);
                        return;
                    }

                    $this->responder($auto);
                    return;
                }

                $estado = $_GET["estado"] ?? "";
                $this->responder(Auto::listar($this->conexion, $estado));
                return;
            }

            if ($metodo === "POST") {
                $datos = $this->obtenerDatos();

                if (
                    empty($datos["modelo"]) ||
                    empty($datos["marca"]) ||
                    empty($datos["anio"]) ||
                    empty($datos["fecha_ingreso"])
                ) {
                    $this->responder(["mensaje" => "Faltan datos del vehículo"], 400);
                    return;
                }

                if (!is_numeric($datos["anio"])) {
                    $this->responder(["mensaje" => "El año debe ser numérico"], 400);
                    return;
                }

                $nuevoId = Auto::crear($this->conexion, $datos);

                $this->responder([
                    "mensaje" => "Vehículo registrado correctamente",
                    "id" => $nuevoId
                ], 201);
                return;
            }

            if ($metodo === "PUT") {
                $datos = $this->obtenerDatos();

                if (!$id) {
                    $this->responder(["mensaje" => "Falta el ID"], 400);
                    return;
                }

                if (
                    empty($datos["modelo"]) ||
                    empty($datos["marca"]) ||
                    empty($datos["anio"]) ||
                    empty($datos["fecha_ingreso"]) ||
                    !in_array($datos["estado"] ?? "", ["En mantenimiento", "Reparado"])
                ) {
                    $this->responder(["mensaje" => "Los datos del vehículo no son válidos"], 400);
                    return;
                }

                Auto::actualizar($this->conexion, $id, $datos);
                $this->responder(["mensaje" => "Vehículo actualizado"]);
                return;
            }

            if ($metodo === "PATCH") {
                $datos = $this->obtenerDatos();
                $estado = $datos["estado"] ?? "";

                if (!$id) {
                    $this->responder(["mensaje" => "Falta el ID"], 400);
                    return;
                }

                if (!in_array($estado, ["En mantenimiento", "Reparado"])) {
                    $this->responder(["mensaje" => "Estado no válido"], 400);
                    return;
                }

                Auto::actualizarEstado($this->conexion, $id, $estado);
                $this->responder(["mensaje" => "Estado actualizado"]);
                return;
            }

            if ($metodo === "DELETE") {
                if (!$id) {
                    $this->responder(["mensaje" => "Falta el ID"], 400);
                    return;
                }

                Auto::eliminar($this->conexion, $id);
                $this->responder(["mensaje" => "Vehículo eliminado"]);
                return;
            }

            $this->responder(["mensaje" => "Método no permitido"], 405);

        } catch (PDOException $error) {
            $this->responder(["mensaje" => "Error al trabajar con la base de datos"], 500);
        }
    }
}
