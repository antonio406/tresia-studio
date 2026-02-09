document.addEventListener('DOMContentLoaded', () => {
    const municipioSelect = document.getElementById('idmunicipio');

    // Obtener los municipios y llenar el select
    function cargarMunicipios() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'php/consulta_municipios.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    const municipios = response.data;
                    
                    municipios.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.idmunicipio;
                        option.id = municipio.idmunicipio;
                        option.textContent = municipio.nombre;
                        municipioSelect.appendChild(option);
                    });
                } else {
                    console.error(response.message || 'Error al cargar los municipios');
                }
            } else {
                console.error('Error al cargar los municipios: ' + xhr.status);
            }
        };

        xhr.onerror = function() {
            console.error('Error de red al cargar los municipios');
        };

        xhr.send();
    }

    cargarMunicipios();
});

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('clientaForm');
    const submitBtn = document.getElementById('submitBtn');
    const imageDataInput = document.getElementById('imageData');

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
        formData.append('fecha', document.getElementById('fecha').value);
        formData.append('edad', document.getElementById('edad').value);
        formData.append('sexo', document.getElementById('sexo').value);
        formData.append('ocupacion', document.getElementById('ocupacion').value);
        formData.append('colonia', document.getElementById('colonia').value);
        formData.append('calle', document.getElementById('calle').value);
        formData.append('cp', document.getElementById('cp').value);
        formData.append('idmunicipio', document.getElementById('idmunicipio').value);
        formData.append('fecha_nacimiento', document.getElementById('fecha_nacimiento').value);
        formData.append('numerotelefonico', document.getElementById('numerotelefonico').value);
        formData.append('alergias', document.getElementById('alergias').value);
        formData.append('patalogias_activas', document.getElementById('patalogias_activas').value);
        formData.append('lentes_contacto_armazon', document.getElementById('lentes_contacto_armazon').value);
        formData.append('saber_nosotros', document.getElementById('saber_nosotros').value);
        formData.append('p_recomendadas', document.getElementById('p_recomendadas').value);
        formData.append('observaciones', document.getElementById('observaciones').value);
        formData.append('detalle', document.getElementById('detalle').value);

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
        const url = id ? 'php/actualiza_clienta.php' : 'php/agrega_clienta.php'; // Cambia la URL según el modo
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
                        if (window.opener && typeof window.opener.consultaClientes === 'function') {
                            window.opener.consultaClientes();
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
    xhr.open('GET', `php/consulta_clienta_actualizar.php?id=${encodeURIComponent(id)}`, true);

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
                    document.getElementById('fecha').value = clienta.fecha;
                    document.getElementById('edad').value = clienta.edad;

                    selectValueIfExists('sexo', clienta.sexo);

                    document.getElementById('ocupacion').value = clienta.ocupacion;
                    document.getElementById('colonia').value =  clienta.colonia;
                    document.getElementById('calle').value = clienta.calle;
                    document.getElementById('cp').value = clienta.cp;
                    document.getElementById('idmunicipio').value = clienta.idmunicipio;
                    document.getElementById('fecha_nacimiento').value = clienta.fecha_nacimiento
                    document.getElementById('numerotelefonico').value = clienta.numerotelefonico;
                    document.getElementById('alergias').value = clienta.alergias;
                    document.getElementById('patalogias_activas').value = clienta.patalogias_activas;
                    document.getElementById('lentes_contacto_armazon').value = clienta.lentes_contacto_armazon;
                    document.getElementById('saber_nosotros').value = clienta.saber_nosotros;
                    document.getElementById('p_recomendadas').value = clienta.p_recomendadas;
                    document.getElementById('observaciones').value = clienta.observaciones;
                    document.getElementById('detalle').value = clienta.descump;                    
                    document.getElementById('cameraInput').value = clienta.imagen;
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

function selectValueIfExists(selectId, value) {
    const select = document.getElementById(selectId);
    
    if (select) {
        const optionToSelect = Array.from(select.options).find(option => option.value === value);
        if (optionToSelect) {
            select.value = value;
        } else {
            console.log(`No se encontró la opción con el valor "${value}" en el select con id "${selectId}".`);
        }
    } else {
        console.log(`No se encontró el elemento select con id "${selectId}".`);
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
