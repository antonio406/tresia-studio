const consultarBtn = document.getElementById('consultarBtn');
const clientasList = document.getElementById('clientasList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10; 
let offset = 0;   

consultarBtn.addEventListener('click', () => {
    offset = 0; // Reiniciar el desplazamiento
    consultaClientes();
});
prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        consultaClientes();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    consultaClientes();
});
function consultaClientes(){
        //var fecha1 = document.getElementById("dateOne").value;
        var clienta = document.getElementById("idclienta").value;
        document.getElementById("img_cargando").style.visibility = "visible";

        // Realizar la solicitud AJAX para obtener la lista de clientas
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `php/consulta_clientes.php?limit=${limit}&offset=${offset}&clienta=${clienta}`, true);
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
                                <td>${clienta.nombre}</td>
                                <td>${clienta.fecha}</td>
                                <td>${clienta.edad}</td>
                                <td>${clienta.sexo}</td>
                                <td>${clienta.ocupacion}</td>
                                <td>${clienta.colonia}</td>
                                <td>${clienta.calle}</td>
                                <td>${clienta.cp}</td>
                                <td>${clienta.municipio}</td>
                                <td>${clienta.fecha_nacimiento}</td>
                                <td>${clienta.numerotelefonico}</td>
                                <td>${clienta.alergias}</td>
                                <td>${clienta.patalogias_activas}</td>
                                <td>${clienta.lentes_contacto_armazon}</td>
                                <td>${clienta.saber_nosotros}</td>
                                <td>${clienta.p_recomendadas}</td>
                                <td>${clienta.observaciones}</td>
                                 <td>
            <a href="javascript:void(0);" onclick="abrirVentanaEmergente('${clienta.imagen}')">
                <img src="./styles/verimagen.png" alt="${clienta.nombre}" style="width: 40px; height: 40px;">
            </a>
        </td>
                                <td align="center">
                            <button onclick="editar(${clienta.idclienta});" 
                            style="
                                background-color: #e5e3e5; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: pointer; 
                                transition: background-color 0.3s, color 0.3s;
                            ">
                            Editar
                            </button>
                            </td>
                            <td align="center">
                             <button onclick="eliminar(${clienta.idclienta});" 
                            style="
                                background-color: #d2d2d2; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: pointer; 
                                transition: background-color 0.3s, color 0.3s;
                            ">
                            Eliminar
                            </button>
                            </td>
                            <td align="center">
                                <label class="switch">
                                    <input type="checkbox" ${clienta.estatus == 1 ? 'checked' : ''} onclick="toggleStatus(${clienta.idclienta}, this)">
                                    <span class="slider"></span>
                                </label>
                            </td>
                        </tr>
                        `;
                    });

                    clientasList.innerHTML = output;
                    document.getElementById("img_cargando").style.visibility = "hidden";
                    // Manejar habilitación/deshabilitación de botones de paginación
                    prevBtn.disabled = offset <= 0;
                    nextBtn.disabled = offset + limit >= response.total;
                } else {
                    document.getElementById("img_cargando").style.visibility = "hidden";
                    clientasList.innerHTML = '<tr><td colspan="10">No se encontraron Clientas.</td></tr>';
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
// document.addEventListener('DOMContentLoaded', function() {
//     var today = new Date().toISOString().split('T')[0];
    
//     document.getElementById('dateOne').value = today;
//     document.getElementById('dateTwo').value = today;
// });
function editar(id) {
    var url = "./agrega_cliente.html";
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
              xhr.open('POST', 'php/elimina_clienta.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
              xhr.onload = function() {
                  if (xhr.status === 200) {
                      const response = JSON.parse(xhr.responseText);
                      if (response.success) {
                          Swal.fire({
                              icon: 'success',
                              title: '¡Se eliminó correctamente la clienta!',
                              text: '¡Clienta Eliminada!',
                              confirmButtonColor: '#d63384'
                          }).then(() => {
                            consultaClientes(); 
                          });
                      } else {
                          Swal.fire({
                              icon: 'Clienta Con Citas',
                              title: 'No se pudo realizar la accion',
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

  function toggleStatus(idclienta, checkbox) {
    const estatus = checkbox.checked ? 1 : 0;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/actualiza_status_clienta.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (!response.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el estatus.',
                    confirmButtonColor: '#d63384'
                });
                checkbox.checked = !checkbox.checked; 
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en el servidor',
                text: 'Hubo un problema al actualizar el estatus.',
                confirmButtonColor: '#d63384'
            });
            checkbox.checked = !checkbox.checked; 
        }
    };
    xhr.send(`idclienta=${idclienta}&estatus=${estatus}`);
}

function abrirVentanaEmergente(imagen) {
    const width = 600;
    const height = 400;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    window.open('php/' + imagen, 'Imagen', `width=${width},height=${height},top=${top},left=${left},resizable=yes`);
}
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
