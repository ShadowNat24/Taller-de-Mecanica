const API = "api/autos.php";

const formulario = document.getElementById("formularioAuto");
const tabla = document.getElementById("tablaAutos");
const mensaje = document.getElementById("mensaje");
const filtro = document.getElementById("filtroEstado");

const token = localStorage.getItem("token");

// Si no hay token, volver al login.
if (!token) {
    window.location.href = "vistas/login.html";
}

// Enviar el token en las peticiones a la API.
function encabezados() {
    return {
        "Content-Type": "application/json",
        "Authorization": "Bearer " + token
    };
}

// Mostrar vehículos.
function cargarAutos() {

    let estado = filtro.value;
    let url = API;

    if (estado !== "") {
        url += "?estado=" + encodeURIComponent(estado);
    }

    fetch(url, {
        headers: encabezados()
    })
        .then(respuesta => {
            if (respuesta.status === 401) {
                localStorage.removeItem("token");
                window.location.href = "vistas/login.html";
                return [];
            }
            return respuesta.json();
        })
        .then(autos => {

            tabla.innerHTML = "";

            autos.forEach(auto => {

                let fila = document.createElement("tr");

                [
                    auto.id,
                    auto.modelo,
                    auto.marca,
                    auto.anio,
                    auto.fecha_ingreso,
                    auto.estado
                ].forEach(valor => {

                    let celda = document.createElement("td");
                    celda.textContent = valor;
                    fila.appendChild(celda);

                });

                tabla.appendChild(fila);

            });

        })
        .catch(error => {
            mensaje.textContent = "Error al cargar los vehículos.";
            console.error(error);
        });
}

// Registrar vehículo.
formulario.addEventListener("submit", function (evento) {

    evento.preventDefault();

    let datos = {
        modelo: document.getElementById("modelo").value,
        marca: document.getElementById("marca").value,
        anio: document.getElementById("anio").value,
        fecha_ingreso: document.getElementById("fecha_ingreso").value
    };

    fetch(API, {
        method: "POST",
        headers: encabezados(),
        body: JSON.stringify(datos)
    })

        .then(respuesta => respuesta.json())
        .then(resultado => {

            mensaje.textContent = resultado.mensaje;

            if (resultado.id) {
                formulario.reset();
                cargarAutos();
            }

        })

        .catch(error => {
            mensaje.textContent = "Error al registrar el vehículo.";
            console.error(error);
        });

});

// Filtrar vehículos.
filtro.addEventListener("change", cargarAutos);

// Cargar la lista al abrir la página.
if (token) {
    cargarAutos();
}
