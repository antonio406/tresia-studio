document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('clientaForm');
    const messageDiv = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (id) {
        // Modo actualización
        submitBtn.value = 'Actualizar';
        document.getElementById('formTitle').textContent = 'Actualizar Servicio';
        cargarDatosFormulario(id);
    } else {
        // Modo creación
        submitBtn.value = 'Guardar';
        document.getElementById('formTitle').textContent = 'Registrar Servicio';
    }

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const nombre = document.getElementById('nombre').value;
        const descripcion = document.getElementById('descripcion').value;
        const precio = document.getElementById('precio').value;

        const xhr = new XMLHttpRequest();
        const url = id ? 'php/actualiza_servicio.php' : 'php/agrega_servicio.php'; // Cambia la URL según el modo
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: id ? '¡Actualización exitosa!' : '¡Registro exitoso!',
                        text: id ? 'El servicio ha sido actualizado.' : 'El servicio ha sido registrado.',
                        confirmButtonColor: '#d63384'
                    }).then(() => {
                        loginForm.reset();
                            if (window.opener && typeof window.opener.cargarServicios === 'function') {
                                window.opener.cargarServicios();
                                window.close();
                            }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#d63384'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el servidor',
                    text: 'Hubo un problema al procesar su solicitud.',
                    confirmButtonColor: '#d63384'
                });
            }
        };


        xhr.send(`nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}&id=${encodeURIComponent(id)}&precio=${encodeURIComponent(precio)}`);
    });
});
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (id) {
        // Cambiar el título y el texto del botón para el modo de edición
        document.getElementById('formTitle').textContent = 'Actualizar Servicio';
        document.getElementById('submitBtn').value = 'Actualizar';

        cargarDatosFormulario(id);
        cargaColaboradoras();
        cargaClientas();
        cargaServicios();
    } else {
        // Cambiar el título y el texto del botón para el modo de creación
        document.getElementById('formTitle').textContent = 'Registrar Servicio';
        document.getElementById('submitBtn').value = 'Guardar';
    }
};

function cargarDatosFormulario(id) {
    //alert("ME EJECUTE SOY " + id);
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_servicio_actualizar.php?id=${encodeURIComponent(id)}`, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            console.log(JSON.stringify(response)); // Verifica la estructura de la respuesta
            //alert(JSON.stringify(response));
            if (response.success) {
                const clientas = response.data; // Aquí es un array de objetos

                // Verifica si hay registros y usa el primer objeto del array
                if (clientas.length > 0) {
                    const clienta = clientas[0]; // Accede al primer registro

                    document.getElementById('nombre').value = clienta.nombre;
                    document.getElementById('descripcion').value = clienta.descripción;
                    document.getElementById('precio').value = clienta.precio;

                } else {
                    console.error('No se encontraron registros.');
                }
            } else {
                console.error('Error en la respuesta: ' + response.message);
            }
        } else {
            console.error('Error en la solicitud: ' + xhr.status);
        }
    };

    xhr.onerror = function() {
        console.error('Error en la solicitud AJAX');
    };

    xhr.send();
}
