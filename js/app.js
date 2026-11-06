const API = "api/autos.php";

const formulario = document.getElementById("formularioAuto");
const tabla = document.getElementById("tablaAutos");
const mensaje = document.getElementById("mensaje");
const filtro = document.getElementById("filtroEstado");

// Mostrar vehículos
function cargarAutos() {

    let estado = filtro.value;

    let url = API;

    if (estado !== "") {
        url += "?estado=" + encodeURIComponent(estado);
    }

    fetch(url)
        .then(respuesta => respuesta.json())
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

// Registrar vehículo
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
        headers: {
            "Content-Type": "application/json"
        },
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

// Filtrar vehículos
filtro.addEventListener("change", cargarAutos);

// Cargar la lista al abrir la página
cargarAutos();