document.addEventListener('DOMContentLoaded', () => {
    // Obtener los municipios y llenar el select
    function cargaColaboradoras() {
        const municipioColaboradora = document.getElementById('idcolaboradora');
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'php/consulta_colaboradoras_activas.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    const colaboradoras = response.data;
                    
                    colaboradoras.forEach(cita => {
                        const option = document.createElement('option');
                        option.value = cita.idcolaboradora;
                        option.id = cita.idcolaboradora;
                        option.textContent = cita.nombre;
                        municipioColaboradora.appendChild(option);
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

    cargaColaboradoras();
});
document.addEventListener('DOMContentLoaded', () => {
    const selectClientas = document.getElementById('idclienta');
    const inputClienta = document.getElementById('clientaSearch');
    const suggestionsClientas = document.getElementById('suggestions');

    function cargaClientas() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'php/consulta_clientas_activas.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (response.success) {
                    const clientas = response.data;

                    clientas.forEach(clienta => {
                        const option = document.createElement('option');
                        option.value = clienta.idclienta;
                        option.id = clienta.idclienta;
                        option.textContent = clienta.nombre;
                        selectClientas.appendChild(option);
                    });

                    // Inicia el filtrado
                    configurarFiltro(clientas, inputClienta, suggestionsClientas, selectClientas);
                } else {
                    console.error(response.message || 'Error al cargar las clientas');
                }
            } else {
                console.error('Error al cargar las clientas: ' + xhr.status);
            }
        };

        xhr.onerror = function() {
            console.error('Error de red al cargar las clientas');
        };

        xhr.send();
    }

    function configurarFiltro(clientas, input, suggestions, select) {
        input.addEventListener('input', () => {
            const query = input.value.toLowerCase();
            suggestions.innerHTML = '';
        
            if (query) {
                const filteredClientas = clientas.filter(clienta => clienta.nombre.toLowerCase().includes(query));
        
                filteredClientas.forEach(clienta => {
                    const div = document.createElement('div');
                    div.textContent = clienta.nombre;
                    div.addEventListener('click', () => {
                        input.value = clienta.nombre;
                        select.value = clienta.idclienta;
                        suggestions.innerHTML = '';
                    });
                    suggestions.appendChild(div);
                });
            }
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.innerHTML = '';
            }
        });
    }

    cargaClientas();
});

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('clientaForm');
    const messageDiv = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');
    
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (id) {
        // Modo actualización
        submitBtn.value = 'Actualizar';
        document.getElementById('formTitle').textContent = 'Actualizar Cita';
        cargarDatosFormulario(id);
    } else {
        // Modo creación
        submitBtn.value = 'Guardar';
        document.getElementById('formTitle').textContent = 'Registrar Cita';
    }

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const dia = document.getElementById('dia').value;
        let hora = document.getElementById('hora').value; // Obtén el valor de la hora
        const colaboradora = document.getElementById('idcolaboradora').value;
        const clienta = document.getElementById('idclienta').value;
        const servicio = document.getElementById('idservicio').value;
        const descuento = document.getElementById('descuento').value;
        const trans = document.getElementById('trans').value;
        const efectivo = document.getElementById('efectivo').value;
        const propina = document.getElementById('propina').value;
        const total = parseFloat(document.getElementById('total').value) || 0;

        // Verificar que la suma de efectivo y transferencia sea igual al total
        const totalEsperado = (parseFloat(efectivo) || 0) + (parseFloat(trans) || 0);
        
        if (total !== totalEsperado) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La suma de Efectivo y Transferencia debe ser igual al Total.',
                confirmButtonColor: '#d63384'
            });
            return; // Detener el envío del formulario
        }
        // Si la hora está en formato HH:MM:SS y termina en ":00", convertirla a HH:MM
        if (/^\d{2}:\d{2}:00$/.test(hora)) {
            hora = hora.slice(0, 5);
        }

        const xhr = new XMLHttpRequest();
        const url = id ? 'php/actualiza_cita.php' : 'php/agrega_cita.php'; // Cambia la URL según el modo
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: id ? '¡Actualización exitosa!' : '¡Registro exitoso!',
                        text: id ? 'La cita ha sido actualizada.' : 'La cita ha sido registrada.',
                        confirmButtonColor: '#d63384'
                    }).then(() => {
                        loginForm.reset();
                            if (window.opener && typeof window.opener.cargarClientas === 'function') {
                                window.opener.cargarClientas();
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

        xhr.send(`dia=${encodeURIComponent(dia)}&hora=${encodeURIComponent(hora)}
                &colaboradora=${encodeURIComponent(colaboradora)}&clienta=${encodeURIComponent(clienta)}
                &servicio=${encodeURIComponent(servicio)}&descuento=${encodeURIComponent(descuento)}
                &trans=${encodeURIComponent(trans)}&efectivo=${encodeURIComponent(efectivo)}
                &propina=${encodeURIComponent(propina)}&total=${encodeURIComponent(total)}&id=${encodeURIComponent(id)}`);
    });

    /* const descuento = document.getElementById('descuento');
    const trans = document.getElementById('trans');
    const efectivo = document.getElementById('efectivo');
    const propina = document.getElementById('propina');
    const total = document.getElementById('total');

    function calcularTotal() {
        const totalOriginal = parseFloat(total.value) || 0;
        const descuentoVal = parseFloat(descuento.value) || 0;
    
        // Calcular el descuento y el nuevo total
        const descuentoAplicado = (descuentoVal / 100) * totalOriginal;
        const nuevoTotal = totalOriginal - descuentoAplicado;
    
        // Mostrar el nuevo total en el campo
        total.value = nuevoTotal.toFixed(2); // Mantener dos decimales
    }
*/
    // Escuchar los cambios en los campos numéricos
    //descuento.addEventListener('input', calcularTotal);
});

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (id) {
        // Cambiar el título y el texto del botón para el modo de edición
        document.getElementById('formTitle').textContent = 'Actualizar Cita';
        document.getElementById('submitBtn').value = 'Actualizar';

        cargarDatosFormulario(id);
        cargaColaboradoras();
        cargaClientas();
        cargaServicios();
    } else {
        // Cambiar el título y el texto del botón para el modo de creación
        document.getElementById('formTitle').textContent = 'Registrar Cita';
        document.getElementById('submitBtn').value = 'Guardar';
    }
};

function cargarDatosFormulario(id) {
    //alert("ME EJECUTE SOY " + id);
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_cita_actualizar.php?id=${encodeURIComponent(id)}`, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
           // console.log(JSON.stringify(response)); // Verifica la estructura de la respuesta
            //alert(JSON.stringify(response));
            if (response.success) {
                const clientas = response.data; // Aquí es un array de objetos

                // Verifica si hay registros y usa el primer objeto del array
                if (clientas.length > 0) {
                    const clienta = clientas[0]; // Accede al primer registro

                    // Llenar los campos con los datos obtenidos
                    document.getElementById('dia').value = clienta.dia;
                    document.getElementById('hora').value = clienta.hora;

                    // Asignar los valores a los select
                    selectValueIfExists('idcolaboradora',clienta.idcolaboradora, clienta.colaboradora);

                    document.getElementById('idclienta').value = clienta.idclienta;
                    document.getElementById('idservicio').value = clienta.idservicio;

                    document.getElementById('descuento').value = clienta.descuento;
                    document.getElementById('trans').value = clienta.transferencia;
                    document.getElementById('efectivo').value = clienta.efectivo;
                    document.getElementById('propina').value = clienta.propina;
                    document.getElementById('total').value = clienta.total;
                    
                    document.getElementById('clientaSearch').value = clienta.clienta; // Update input field
                    document.getElementById('servSearch').value = clienta.servicio; // Update input field

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

document.addEventListener('DOMContentLoaded', () => {
    const inputServ = document.getElementById('servSearch');
    const sg = document.getElementById('sg');
    const total = document.getElementById('total');
    const selectedServiceId = document.getElementById('idservicio');
    
    let servicios = []; // Variable global para almacenar los servicios

    function cargaServicios() {
        fetch('php/consulta_servicios_activos.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los servicios: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    servicios = data.data; // Guardar servicios en la variable global
                    
                    // Inicia el filtrado
                    configurarFiltro();
                } else {
                    console.error(data.message || 'Error al cargar los servicios');
                }
            })
            .catch(error => {
                console.error('Error de red al cargar los servicios', error);
            });
    }

    function configurarFiltro() {
        inputServ.addEventListener('input', () => {
            const query = inputServ.value.toLowerCase();
            sg.innerHTML = '';

            if (query) {
                const filteredServicios = servicios.filter(servicio => servicio.nombre.toLowerCase().includes(query));

                filteredServicios.forEach(servicio => {
                    const div = document.createElement('div');
                    div.textContent = servicio.nombre;
                    div.addEventListener('click', () => {
                        inputServ.value = servicio.nombre;
                        sg.innerHTML = '';
                        selectedServiceId.value = servicio.idservicio; // Guardar el ID del servicio seleccionado
                        buscarPrecios(servicio.idservicio); // Llamar a buscarPrecios con el ID del servicio
                    });
                    sg.appendChild(div);
                });
            }
        });

        document.addEventListener('click', (e) => {
            if (!inputServ.contains(e.target) && !sg.contains(e.target)) {
                sg.innerHTML = '';
            }
        });
    }

    function buscarPrecios(id) {
        if (!id) {
            console.error('ID de servicio no proporcionado');
            return;
        }

        fetch(`php/consulta_precios_servicos.php?id=${encodeURIComponent(id)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los precios: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const servicios = data.data;

                    if (servicios.length > 0) {
                        const servicio = servicios[0];
                        total.value = servicio.precio; // Si es un input
                        total.textContent = servicio.precio; // Si es un elemento HTML
                    } else {
                        total.textContent = 'No se encontraron precios'; // Mensaje cuando no haya precios
                    }
                } else {
                    console.error(data.message || 'Error al cargar los precios');
                    total.textContent = 'Error al cargar los precios';
                }
            })
            .catch(error => {
                console.error('Error de red al cargar los precios', error);
                total.textContent = 'Error al cargar los precios';
            });
    }

    cargaServicios(); // Cargar los servicios al inicio
});
const descuento = document.getElementById('descuento');
    const trans = document.getElementById('trans');
    const efectivo = document.getElementById('efectivo');
    const propina = document.getElementById('propina');
    const total = document.getElementById('total');

    function calcularTotal() {
        const totalOriginal = parseFloat(total.value) || 0;
        const descuentoVal = parseFloat(descuento.value) || 0;
    
        // Calcular el descuento y el nuevo total
        const descuentoAplicado = (descuentoVal / 100) * totalOriginal;
        const nuevoTotal = totalOriginal - descuentoAplicado;
    
        // Mostrar el nuevo total en el campo
        total.value = nuevoTotal.toFixed(2); // Mantener dos decimales
    }