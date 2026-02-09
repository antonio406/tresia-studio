const consultarBtn = document.getElementById('consultarBtn');
const clientasList = document.getElementById('clientasList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10; 
let offset = 0;   

consultarBtn.addEventListener('click', () => {
    offset = 0; // Reiniciar el desplazamiento
    cargarClientas();
});

prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        cargarClientas();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    cargarClientas();
});
function cargarClientas() {
    var fecha1 = document.getElementById("dateOne").value;
    var fecha2 = document.getElementById("dateTwo").value;
    var clientaa = document.getElementById("idclienta").value;
    var colaboradora = document.getElementById('idcolaboradora').value;
    document.getElementById("img_cargando").style.visibility = "visible";

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_citas2.php?fecha1=${encodeURIComponent(fecha1)}&fecha2=${encodeURIComponent(fecha2)}&limit=${limit}
    &offset=${offset}&colaboradora=${colaboradora}&clientaa=${clientaa}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const clientas = response.data;
                let output = '';
                let startIndex = offset + 1; // Calcular el índice inicial
                clientas.forEach((clienta, index) => {
                    output += `
                        <tr>
                            <td>${startIndex + index}</td>
                            <td>${clienta.dia}</td>
                            <td>${clienta.hora}</td>
                            <td>${clienta.colaboradora}</td>
                            <td>${clienta.clienta}</td>
                            <td>${clienta.servicio}</td>
                            <td>%${clienta.descuento}</td>
                            <td>$${clienta.transferencia}</td>
                            <td>$${clienta.efectivo}</td>
                            <td>$${clienta.propina}</td>
                            <td>$${clienta.total}</td>
                            <td align="center">
                                <button onclick="editar(${clienta.idcitas});" 
                                style="background-color: #e5e3e5;
                                color: #333; 
                                border: none;
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: pointer; 
                                transition: background-color 0.3s, 
                                color 0.3s;">
                                Editar
                                </button>
                            </td>
                            </button>
                            </td>
                            <td align="center">
                             <button onclick="eliminar(${clienta.idcitas});" 
                            style="
                                background-color: #d2d2d2; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: pointer; 
                                transition: background-color 0.3s, color 0.3s;">
                            Eliminar
                            </button>
                            </td>
                        </tr>
                    `;
                });

                clientasList.innerHTML = output;
                document.getElementById("img_cargando").style.visibility = "hidden";
                document.getElementById("totalGlobal").innerText = `Total General: $${response.totalGlobal}`;
                document.getElementById("comision").innerText = `Total Comision Jefa: $${response.total_sin_comision}`;
                document.getElementById("colaboradora").innerText = `Total Comision Colaboradora: $${response.comision_colaboradora}`;

                document.getElementById("t").innerText = `Total Transferencias: $${response.transferencia}`;
                document.getElementById("e").innerText = `Total Efectivo: $${response.efectivo}`;
                document.getElementById("p").innerText = `Total Propinas: $${response.propina}`;
                // Manejar habilitación/deshabilitación de botones de paginación
                prevBtn.disabled = offset <= 0;
                nextBtn.disabled = offset + limit >= response.total;
            } else {
                document.getElementById("img_cargando").style.visibility = "hidden";
                document.getElementById("totalGlobal").innerText = `Total Global: 0`;
                document.getElementById("comision").innerText = `Total Comision Gefa: 0`;
                document.getElementById("colaboradora").innerText = `Total Comision Colaboradora: 0`;

                document.getElementById("t").innerText = `Total Transferencias: 0`;
                document.getElementById("e").innerText = `Total Efectivo: 0`;
                document.getElementById("p").innerText = `Total Propinas: 0`;
                clientasList.innerHTML = '<tr><td colspan="10">No se encontraron citas.</td></tr>';
                prevBtn.disabled = true;
                nextBtn.disabled = true;
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en el servidor',
                text: 'Hubo un problema al obtener la lista de clientas.',
                confirmButtonColor: '#d63384'
            });
        }
    };
    xhr.send();
}

document.getElementById('exportarExcelBtn').addEventListener('click', function() {
    // Obtener las fechas de los campos de fecha
    var dateOne = document.getElementById('dateOne').value;
    var dateTwo = document.getElementById('dateTwo').value;
    var colaboradora = document.getElementById('idcolaboradora').value;
    var clientaa = document.getElementById("clienta").value;

    // Construir la URL con parámetros de fecha
    var url = `php/exporta_citas.php?dateOne=${encodeURIComponent(dateOne)}&dateTwo=${encodeURIComponent(dateTwo)}&colaboradora=${encodeURIComponent(colaboradora)}&clientaa=${encodeURIComponent(clientaa)}`;

    // Redirigir a la URL con los parámetros
    window.location.href = url;
});

document.addEventListener('DOMContentLoaded', function() {
    // Obtener la fecha local actual
    var today = new Date();
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript van de 0 a 11
    var day = String(today.getDate()).padStart(2, '0');

    // Formatear la fecha en 'YYYY-MM-DD'
    var formattedToday = `${year}-${month}-${day}`;
    
    // Asignar la fecha a los campos de fecha
    document.getElementById('dateOne').value = formattedToday;
    document.getElementById('dateTwo').value = formattedToday;
});
function editar(id) {
  var url = "./tabla.html";
  url += "?id=" + id;
  var nombreVentana = "ventanaEditar"; // Nombre para la ventana (opcional)
  var opciones = "width=800,height=600,scrollbars=yes,resizable=yes"; // Opciones para la ventana
  window.open(url, nombreVentana, opciones);
}

function eliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¡No podrás revertir esta acción!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d63384',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // El usuario confirmó la eliminación
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/eliminar_cita.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Se eliminó correctamente la cita!',
                            text: '¡Cita Eliminada!',
                            confirmButtonColor: '#d63384'
                        }).then(() => {
                            cargarClientas(); // Actualizar la lista de citas después de la eliminación
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
            xhr.send(`id=${encodeURIComponent(id)}`);
        }
    });
}


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

                    // Agregar una opción por defecto que simule "sin seleccionar"
                    const defaultOption = document.createElement('option');
                    defaultOption.value = ''; // Deja el valor vacío
                    defaultOption.textContent = 'Selecciona una clienta'; // Texto visible al usuario
                    defaultOption.disabled = true; // Deshabilita esta opción para que no se pueda seleccionar
                    defaultOption.selected = true; // La selecciona por defecto
                    selectClientas.appendChild(defaultOption);

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
            } else {
                // Si el input está vacío, vacía el select
                select.value = '';
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

