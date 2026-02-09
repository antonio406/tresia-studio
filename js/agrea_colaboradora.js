document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('clientaForm');
    const submitBtn = document.getElementById('submitBtn');
    const imageDataInput = document.getElementById('imageData');

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (id) {
        // Modo actualización
        submitBtn.value = 'Actualizar';
        document.getElementById('formTitle').textContent = 'Actualizar Colaboradora';
        cargarDatosFormulario(id);
    } else {
        // Modo creación
        submitBtn.value = 'Guardar';
        document.getElementById('formTitle').textContent = 'Registrar Colaboradora';
    }

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData();
        formData.append('id',urlParams.get('id'));
        formData.append('nombre', document.getElementById('nombre').value);
        formData.append('edad', document.getElementById('edad').value);
        formData.append('experta', document.getElementById('experta').value);
        formData.append('horario', document.getElementById('horario').value);
        formData.append('diasla', document.getElementById('diasla').value);
        formData.append('comision', document.getElementById('comision').value);
        formData.append('antiguedad', document.getElementById('antiguedad').value);
        formData.append('uniforme', document.getElementById('uniforme').value);
        formData.append('materialtra', document.getElementById('materialtra').value);
        formData.append('observaciones', document.getElementById('observaciones').value);
        formData.append('telefono', document.getElementById('telefono').value);

        // Agregar la imagen capturada al FormData
        if (imageDataInput.value) {
            // Convertir la imagen en formato base64 a un Blob y agregarla al FormData
            fetch(imageDataInput.value)
                .then(res => res.blob())
                .then(blob => {
                    // Generar un nombre de archivo único con prefijo y número aleatorio
                    const uniqueFileName = `imagen_${Math.floor(Math.random() * 1000000)}.png`;
                    formData.append('imagen', blob, uniqueFileName);
                    // Enviar el formulario después de agregar la imagen
                    enviarFormulario(formData);
                })
                .catch(err => {
                    console.error('Error al convertir la imagen: ', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo procesar la imagen.',
                        confirmButtonColor: '#d63384'
                    });
                });
        } else {
            // Enviar el formulario sin imagen
            enviarFormulario(formData);
        }
    });

    function enviarFormulario(formData) {
        const xhr = new XMLHttpRequest();
        const url = id ? 'php/actualiza_colaboradora.php' : 'php/agrega_colaboradora.php'; // Cambia la URL según el modo
        xhr.open('POST', url, true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: id ? '¡Actualización exitosa!' : '¡Registro exitoso!',
                        text: id ? 'La colaboradora ha sido actualizada.' : 'La colaboradora ha sido registrada.',
                        confirmButtonColor: '#d63384'
                    }).then(() => {
                        loginForm.reset();
                        if (window.opener && typeof window.opener.consultaColaboradoras === 'function') {
                            window.opener.consultaColaboradoras();
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
        document.getElementById('formTitle').textContent = 'Actualizar Colaboradora';
        document.getElementById('submitBtn').value = 'Actualizar';
        cargarDatosFormulario(id);
    } else {
        // Cambiar el título y el texto del botón para el modo de creación
        document.getElementById('formTitle').textContent = 'Registrar Colaboradora';
        document.getElementById('submitBtn').value = 'Guardar';
    }
};

function cargarDatosFormulario(id) {
    //alert("ME EJECUTE SOY " + id);
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_colaboradora_actualizar.php?id=${encodeURIComponent(id)}`, true);

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

                    // Llenar los campos con los datos obtenidos
                    document.getElementById('nombre').value = clienta.nombre
                    document.getElementById('edad').value = clienta.edad
                    document.getElementById('experta').value = clienta.expertaen
                    document.getElementById('horario').value = clienta.horario_laboral
                    document.getElementById('diasla').value = clienta.diaslaborales
                    document.getElementById('comision').value = clienta.comisión
                    document.getElementById('antiguedad').value = clienta.antigüedad
                    document.getElementById('uniforme').value = clienta.uniforme_proporcionado
                    document.getElementById('materialtra').value = clienta.material_para_trabajo
                    document.getElementById('observaciones').value = clienta.observaciones
                    document.getElementById('telefono').value = clienta.telefono


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

function selectValueIfExists(selectId, optionId, value) {
    const select = document.getElementById(selectId);
    const option = select.querySelector(`option[value="${optionId}"]`);

    if (option) {
        option.selected = true;
    } else {
        console.warn(`La opción con id "${optionId}" y valor "${value}" no se encontró en el select con id "${selectId}"`);
    }
}



const video = document.getElementById('video');
const photoCanvas = document.getElementById('photoCanvas');
const photo = document.getElementById('photo');
const startButton = document.getElementById('startButton');
const captureButton = document.getElementById('captureButton');
const imageDataInput = document.getElementById('imageData');

function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            video.play();
        })
        .catch(err => {
            console.error("Error al acceder a la cámara: ", err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo acceder a la cámara.',
                confirmButtonColor: '#d63384'
            });
        });
}
function capturePhoto() {
    const context = photoCanvas.getContext('2d');
    photoCanvas.width = video.videoWidth;
    photoCanvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, photoCanvas.width, photoCanvas.height);
    photo.src = photoCanvas.toDataURL('image/png');
    photo.style.display = 'block';
    imageDataInput.value = photoCanvas.toDataURL('image/png');

}


startButton.addEventListener('click', startCamera);
captureButton.addEventListener('click', capturePhoto);
