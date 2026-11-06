<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: vistas/login.html");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Taller de Mecánica</title>

    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

    <h1>Taller de Mecánica</h1>

    <h2>Registrar vehículo</h2>

    <form id="formularioAuto">

        <label>Modelo:</label>
        <input type="text" id="modelo" required>

        <br><br>

        <label>Marca:</label>
        <input type="text" id="marca" required>

        <br><br>

        <label>Año:</label>
        <input type="number" id="anio" required>

        <br><br>

        <label>Fecha de ingreso:</label>
        <input type="date" id="fecha_ingreso" required>

        <br><br>

        <button type="submit">Guardar vehículo</button>

    </form>

    <p id="mensaje"></p>

    <h2>Lista de vehículos</h2>

    <label>Filtrar por estado:</label>

    <select id="filtroEstado">
        <option value="">Todos</option>
        <option value="En mantenimiento">En mantenimiento</option>
        <option value="Reparado">Reparado</option>
    </select>

    <br><br>

    <table border="1">

        <thead>
            <tr>
                <th>ID</th>
                <th>Modelo</th>
                <th>Marca</th>
                <th>Año</th>
                <th>Fecha de ingreso</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody id="tablaAutos">
        </tbody>

    </table>

    <script src="js/app.js"></script>

</body>

</html>