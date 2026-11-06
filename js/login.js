const formulario = document.getElementById("formularioLogin");
const mensaje = document.getElementById("mensaje");

formulario.addEventListener("submit", function (evento) {

    evento.preventDefault();

    const datos = {
        usuario: document.getElementById("usuario").value,
        password: document.getElementById("password").value
    };

    fetch("../api/login.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify(datos)

    })

        .then(respuesta => respuesta.json().then(datos => ({
            estado: respuesta.ok,
            datos: datos
        })))

        .then(resultado => {

            mensaje.textContent = resultado.datos.mensaje;

            if (resultado.estado) {
                window.location.href = "../index.php";
            }

        })

        .catch(error => {
            mensaje.textContent = "Error al iniciar sesión.";
            console.error(error);
        });

});