// ==================== FUNCIONES DE AYUDA UI ====================
function changeQty(id, delta, isChange = false) {
    const input = document.getElementById(id);
    if (!input) return;

    let currentVal = parseInt(input.value) || 0;
    let newVal = currentVal + delta;

    if (newVal < 0) newVal = 0;
    input.value = newVal;

    // Disparar el evento input manualmente para que se ejecuten los cálculos
    if (isChange) {
        calcularCambio();
    } else {
        calcularEfectivo();
    }
}

// ==================== DESGLOSE DE EFECTIVO ====================
function calcularEfectivo() {
    const denominaciones = [
        { id: 'b1000', valor: 1000 }, { id: 'b500', valor: 500 },
        { id: 'b200', valor: 200 }, { id: 'b100', valor: 100 },
        { id: 'b50', valor: 50 }, { id: 'b20', valor: 20 },
        { id: 'm10', valor: 10 }, { id: 'm5', valor: 5 },
        { id: 'm2', valor: 2 }, { id: 'm1', valor: 1 },
        { id: 'm050', valor: 0.50 }
    ];

    let totalRecibido = 0;
    denominaciones.forEach(d => {
        const cantidad = parseInt(document.getElementById(d.id).value) || 0;
        totalRecibido += cantidad * d.valor;
    });

    document.getElementById('subtotalEfectivo').textContent = totalRecibido.toFixed(2);

    // Calcular "Monto a cubrir en Efectivo" (TotalActual - Transferencia)
    const totalActual = parseFloat(document.getElementById('total').value) || 0;
    const trans = parseFloat(document.getElementById('trans').value) || 0;
    const costoEfectivo = totalActual - trans;

    document.getElementById('montoEsperadoEfectivo').textContent = costoEfectivo.toFixed(2);

    // Cambio a devolver = Recibido - (TotalActual - Transferencia)
    const cambioADevolver = totalRecibido > 0 ? totalRecibido - costoEfectivo : 0;
    const elCambioADevolver = document.getElementById('cambioDevolver');
    elCambioADevolver.textContent = Math.max(0, cambioADevolver).toFixed(2);

    // Feedback visual: Si recibió menos de lo necesario
    const elSubtotal = document.getElementById('subtotalEfectivo');
    if (totalRecibido > 0 && totalRecibido < costoEfectivo) {
        elSubtotal.style.color = 'red';
    } else {
        elSubtotal.style.color = '#7c3aed';
    }

    actualizarEfectivoNeto();
}

function calcularCambio() {
    const denominacionesCambio = [
        { id: 'c1000', valor: 1000 }, { id: 'c500', valor: 500 },
        { id: 'c200', valor: 200 }, { id: 'c100', valor: 100 },
        { id: 'c50', valor: 50 }, { id: 'c20', valor: 20 },
        { id: 'cm10', valor: 10 }, { id: 'cm5', valor: 5 },
        { id: 'cm2', valor: 2 }, { id: 'cm1', valor: 1 },
        { id: 'cm050', valor: 0.50 }
    ];

    let totalCambioDado = 0;
    denominacionesCambio.forEach(d => {
        const cantidad = parseInt(document.getElementById(d.id).value) || 0;
        totalCambioDado += cantidad * d.valor;
    });

    const elTotalCambio = document.getElementById('totalCambioDado');
    elTotalCambio.textContent = totalCambioDado.toFixed(2);

    // Feedback visual: Si el cambio dado no coincide con el calculado
    const cambioADevolver = parseFloat(document.getElementById('cambioDevolver').textContent) || 0;
    if (totalCambioDado !== cambioADevolver) {
        elTotalCambio.style.color = 'red';
    } else {
        elTotalCambio.style.color = '#dc3545';
    }

    actualizarEfectivoNeto();
}

function actualizarEfectivoNeto() {
    const totalRecibido = parseFloat(document.getElementById('subtotalEfectivo').textContent) || 0;
    const totalCambioDado = parseFloat(document.getElementById('totalCambioDado').textContent) || 0;
    const neto = totalRecibido - totalCambioDado;

    const elNeto = document.getElementById('efectivoNeto');
    elNeto.textContent = neto.toFixed(2);

    // Feedback visual del neto final
    const totalCita = parseFloat(document.getElementById('total').value) || 0;
    const trans = parseFloat(document.getElementById('trans').value) || 0;
    const costoEfectivoEsperado = totalCita - trans;

    if (totalRecibido > 0 && Math.abs(neto - costoEfectivoEsperado) > 0.01) {
        elNeto.style.color = 'red';
    } else {
        elNeto.style.color = '#155724';
    }

    document.getElementById('efectivo').value = neto.toFixed(2);
}

function resetDesglose() {
    const ids = [
        'b1000', 'b500', 'b200', 'b100', 'b50', 'b20', 'm10', 'm5', 'm2', 'm1', 'm050',
        'c1000', 'c500', 'c200', 'c100', 'c50', 'c20', 'cm10', 'cm5', 'cm2', 'cm1', 'cm050'
    ];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = 0;
    });

    const fields = ['subtotalEfectivo', 'cambioDevolver', 'totalCambioDado', 'efectivoNeto'];
    fields.forEach(f => {
        const el = document.getElementById(f);
        if (el) {
            el.textContent = "0.00";
            el.style.color = ''; // Reset colors
        }
    });

    const desgloseRow = document.getElementById('desgloseRow');
    const toggleBtn = document.getElementById('toggleDesglose');
    if (desgloseRow) desgloseRow.style.display = 'none';
    if (toggleBtn) toggleBtn.textContent = '💵 Desglose';
}

// Toggle del panel de desglose
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleDesglose');
    const desgloseRow = document.getElementById('desgloseRow');
    if (toggleBtn && desgloseRow) {
        toggleBtn.addEventListener('click', () => {
            if (desgloseRow.style.display === 'none') {
                desgloseRow.style.display = 'table-row';
                toggleBtn.textContent = '💵 Ocultar';
            } else {
                desgloseRow.style.display = 'none';
                toggleBtn.textContent = '💵 Desglose';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Obtener los municipios y llenar el select
    function cargaColaboradoras() {
        const municipioColaboradora = document.getElementById('idcolaboradora');
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'php/consulta_colaboradoras_activas.php', true);

        xhr.onload = function () {
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

        xhr.onerror = function () {
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

        xhr.onload = function () {
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

        xhr.onerror = function () {
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

    loginForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const dia = document.getElementById('dia').value;
        let hora = document.getElementById('hora').value; // Obtén el valor de la hora
        const colaboradora = document.getElementById('idcolaboradora').value;
        const clienta = document.getElementById('idclienta').value;
        const servicio = document.getElementById('idservicio').value;
        const descuento = document.getElementById('descuento').value;
        const trans = document.getElementById('trans').value;
        const efectivo = document.getElementById('efectivo').value;
        const propina_efectivo = document.getElementById('propina_efectivo').value;
        const propina_trans = document.getElementById('propina_trans').value;
        const total = parseFloat(document.getElementById('total').value) || 0;

        // Leer denominaciones
        const b1000 = document.getElementById('b1000').value || 0;
        const b500 = document.getElementById('b500').value || 0;
        const b200 = document.getElementById('b200').value || 0;
        const b100 = document.getElementById('b100').value || 0;
        const b50 = document.getElementById('b50').value || 0;
        const b20 = document.getElementById('b20').value || 0;
        const m10 = document.getElementById('m10').value || 0;
        const m5 = document.getElementById('m5').value || 0;
        const m2 = document.getElementById('m2').value || 0;
        const m1 = document.getElementById('m1').value || 0;
        const m050 = document.getElementById('m050').value || 0;

        // Leer cambio
        const c1000 = document.getElementById('c1000').value || 0;
        const c500 = document.getElementById('c500').value || 0;
        const c200 = document.getElementById('c200').value || 0;
        const c100 = document.getElementById('c100').value || 0;
        const c50 = document.getElementById('c50').value || 0;
        const c20 = document.getElementById('c20').value || 0;
        const cm10 = document.getElementById('cm10').value || 0;
        const cm5 = document.getElementById('cm5').value || 0;
        const cm2 = document.getElementById('cm2').value || 0;
        const cm1 = document.getElementById('cm1').value || 0;
        const cm050 = document.getElementById('cm050').value || 0;

        // ===== VALIDACIONES DETALLADAS DE EFECTIVO =====
        const subtotalRecibido = parseFloat(document.getElementById('subtotalEfectivo').textContent) || 0;
        const totalCambioDado = parseFloat(document.getElementById('totalCambioDado').textContent) || 0;
        const cambioEsperado = parseFloat(document.getElementById('cambioDevolver').textContent) || 0;
        const montoEsperado = parseFloat(document.getElementById('montoEsperadoEfectivo').textContent) || 0;

        if (subtotalRecibido > 0) {
            // 1. Validar que lo recibido cubra al menos lo necesario
            if (subtotalRecibido < montoEsperado) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pago Insuficiente',
                    text: `El monto recibido ($${subtotalRecibido}) es menor al monto a cubrir ($${montoEsperado}).`,
                    confirmButtonColor: '#d63384'
                });
                return;
            }

            // 2. Validar que el desglose del cambio dado coincida con el cálculo
            if (totalCambioDado !== cambioEsperado) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error en Cambio',
                    text: `El desglose del cambio dado ($${totalCambioDado}) no coincide con el cambio a devolver calculado ($${cambioEsperado}).`,
                    confirmButtonColor: '#d63384'
                });
                return;
            }
        }

        // Verificar que la suma de efectivo y transferencia sea igual al total
        const totalEsperadoFinal = (parseFloat(efectivo) || 0) + (parseFloat(trans) || 0);

        if (total !== totalEsperadoFinal) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Totales',
                text: 'La suma de Efectivo Neto y Transferencia debe ser igual al Total.',
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

        xhr.onload = function () {
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
                        resetDesglose();
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

        xhr.send(`dia=${encodeURIComponent(dia)}&hora=${encodeURIComponent(hora)}&colaboradora=${encodeURIComponent(colaboradora)}&clienta=${encodeURIComponent(clienta)}&servicio=${encodeURIComponent(servicio)}&descuento=${encodeURIComponent(descuento)}&trans=${encodeURIComponent(trans)}&efectivo=${encodeURIComponent(efectivo)}&propina_efectivo=${encodeURIComponent(propina_efectivo)}&propina_trans=${encodeURIComponent(propina_trans)}&total=${encodeURIComponent(total)}&id=${encodeURIComponent(id)}&b1000=${encodeURIComponent(b1000)}&b500=${encodeURIComponent(b500)}&b200=${encodeURIComponent(b200)}&b100=${encodeURIComponent(b100)}&b50=${encodeURIComponent(b50)}&b20=${encodeURIComponent(b20)}&m10=${encodeURIComponent(m10)}&m5=${encodeURIComponent(m5)}&m2=${encodeURIComponent(m2)}&m1=${encodeURIComponent(m1)}&m050=${encodeURIComponent(m050)}&c1000=${encodeURIComponent(c1000)}&c500=${encodeURIComponent(c500)}&c200=${encodeURIComponent(c200)}&c100=${encodeURIComponent(c100)}&c50=${encodeURIComponent(c50)}&c20=${encodeURIComponent(c20)}&cm10=${encodeURIComponent(cm10)}&cm5=${encodeURIComponent(cm5)}&cm2=${encodeURIComponent(cm2)}&cm1=${encodeURIComponent(cm1)}&cm050=${encodeURIComponent(cm050)}`);
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

window.onload = function () {
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

    xhr.onload = function () {
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
                    selectValueIfExists('idcolaboradora', clienta.idcolaboradora, clienta.colaboradora);

                    document.getElementById('idclienta').value = clienta.idclienta;
                    document.getElementById('idservicio').value = clienta.idservicio;

                    document.getElementById('descuento').value = clienta.descuento;
                    document.getElementById('trans').value = clienta.transferencia;
                    document.getElementById('efectivo').value = clienta.efectivo;
                    document.getElementById('propina_efectivo').value = clienta.propina_efectivo;
                    document.getElementById('propina_trans').value = clienta.propina_transferencia;
                    document.getElementById('total').value = clienta.total;

                    // Cargar denominaciones si existen
                    if (clienta.b1000 !== undefined) {
                        document.getElementById('b1000').value = clienta.b1000 || 0;
                        document.getElementById('b500').value = clienta.b500 || 0;
                        document.getElementById('b200').value = clienta.b200 || 0;
                        document.getElementById('b100').value = clienta.b100 || 0;
                        document.getElementById('b50').value = clienta.b50 || 0;
                        document.getElementById('b20').value = clienta.b20 || 0;
                        document.getElementById('m10').value = clienta.m10 || 0;
                        document.getElementById('m5').value = clienta.m5 || 0;
                        document.getElementById('m2').value = clienta.m2 || 0;
                        document.getElementById('m1').value = clienta.m1 || 0;
                        document.getElementById('m050').value = clienta.m050 || 0;

                        // Cargar cambio
                        if (clienta.c1000 !== undefined) {
                            document.getElementById('c1000').value = clienta.c1000 || 0;
                            document.getElementById('c500').value = clienta.c500 || 0;
                            document.getElementById('c200').value = clienta.c200 || 0;
                            document.getElementById('c100').value = clienta.c100 || 0;
                            document.getElementById('c50').value = clienta.c50 || 0;
                            document.getElementById('c20').value = clienta.c20 || 0;
                            document.getElementById('cm10').value = clienta.cm10 || 0;
                            document.getElementById('cm5').value = clienta.cm5 || 0;
                            document.getElementById('cm2').value = clienta.cm2 || 0;
                            document.getElementById('cm1').value = clienta.cm1 || 0;
                            document.getElementById('cm050').value = clienta.cm050 || 0;
                        }

                        // Mostrar el desglose si hay datos
                        const desgloseRow = document.getElementById('desgloseRow');
                        const toggleBtn = document.getElementById('toggleDesglose');
                        if (desgloseRow) desgloseRow.style.display = 'table-row';
                        if (toggleBtn) toggleBtn.textContent = '💵 Ocultar';

                        calcularEfectivo();
                        calcularCambio();
                    }

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

    xhr.onerror = function () {
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
                        const precioBaseEl = document.getElementById("precio_base");
                        const totalEl = document.getElementById("total");
                        const costoEl = document.getElementById("costo");

                        if (precioBaseEl) precioBaseEl.value = servicio.precio;
                        if (totalEl) {
                            totalEl.value = servicio.precio;
                            totalEl.textContent = servicio.precio;
                        }
                        if (costoEl) costoEl.value = servicio.precio;

                        // Actualizar Monto a cubrir en Efectivo al cambiar el servicio
                        calcularEfectivo();
                    }
                } else {
                    console.error(data.message || 'Error al cargar los precios');
                }
            })
            .catch(error => {
                console.error('Error de red al cargar los precios', error);
            });
    }

    cargaServicios(); // Cargar los servicios al inicio
});

// Inicializar listeners y variables globales de forma segura
document.addEventListener('DOMContentLoaded', () => {
    const transInput = document.getElementById('trans');
    if (transInput) {
        transInput.addEventListener('input', calcularEfectivo);
    }

    const descuentoInput = document.getElementById('descuento');
    if (descuentoInput) {
        descuentoInput.addEventListener('input', calcularTotal);
    }
});

function calcularTotal() {
    const precioBaseEl = document.getElementById('precio_base');
    const totalEl = document.getElementById('total');
    const descuentoEl = document.getElementById('descuento');
    const montoDetalleEl = document.getElementById('monto_detalle');

    if (!precioBaseEl || !totalEl || !descuentoEl) return;

    const totalOriginal = parseFloat(precioBaseEl.value) || 0;
    const descuentoVal = parseFloat(descuentoEl.value) || 0;

    // Calcular el descuento y el nuevo total siempre desde el original
    const descuentoAplicado = (descuentoVal / 100) * totalOriginal;
    const nuevoTotal = totalOriginal - descuentoAplicado;

    // Mostrar el nuevo total en el campo
    totalEl.value = nuevoTotal.toFixed(2);

    if (montoDetalleEl) {
        montoDetalleEl.value = nuevoTotal.toFixed(2);
    }

    // Recalcular el desglose
    calcularEfectivo();
}
