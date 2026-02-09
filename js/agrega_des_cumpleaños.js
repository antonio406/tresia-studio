document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('clientaForm');
    const submitBtn = document.getElementById('submitBtn');

    // Manejo de parámetros en la URL para actualizar
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (id) {
        // Modo actualización
        submitBtn.value = 'Actualizar';
        document.getElementById('formTitle').textContent = 'Actualizar Clienta';
        cargarDatosFormulario(id);  // Función para cargar datos del formulario en modo actualización
    } else {
        // Modo creación
        submitBtn.value = 'Guardar';
        document.getElementById('formTitle').textContent = 'Registrar Clienta';
    }

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData();
        formData.append('id',urlParams.get('id'));
        formData.append('nombre', document.getElementById('nombre').value);
        formData.append('fecha_nacimiento', document.getElementById('fecha_nacimiento').value);
        formData.append('detalle', document.getElementById('detalle').value);

            enviarFormulario(formData);
    });

    function enviarFormulario(formData) {
        const xhr = new XMLHttpRequest();
        const url = id ? 'php/acualiza_desc_clienta.php' : 'php/agrega_clienta.php'; // Cambia la URL según el modo
        xhr.open('POST', url, true);
       
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: id ? '¡Actualización exitosa!' : '¡Registro exitoso!',
                        text: id ? 'La clienta ha sido actualizada.' : 'La clienta ha sido registrada.',
                        confirmButtonColor: '#d63384'
                    }).then(() => {
                        loginForm.reset();
                        if (window.opener && typeof window.opener.cargaCumpleaños === 'function') {
                            window.opener.cargaCumpleaños();
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

        xhr.send(formData); // Enviar el FormData con todos los datos, incluyendo la imagen
    }
});



window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (id) {
        // Cambiar el título y el texto del botón para el modo de edición
        document.getElementById('formTitle').textContent = 'Actualizar Clienta';
        document.getElementById('submitBtn').value = 'Actualizar';

        cargarDatosFormulario(id);
        cargaColaboradoras();
        cargaClientas();
        cargaServicios();
    } else {
        // Cambiar el título y el texto del botón para el modo de creación
        document.getElementById('formTitle').textContent = 'Registrar Clienta';
        document.getElementById('submitBtn').value = 'Guardar';
    }
};

function cargarDatosFormulario(id) {
    //alert("ME EJECUTE SOY " + id);
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_desc_clienta.php?id=${encodeURIComponent(id)}`, true);

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
                    document.getElementById('fecha_nacimiento').value = clienta.fecha_nacimiento
                    document.getElementById('detalle').value = clienta.descump;                    
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
  
