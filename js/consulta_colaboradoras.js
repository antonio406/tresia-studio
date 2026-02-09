const consultarBtn = document.getElementById('consultarBtn');
const clientasList = document.getElementById('clientasList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10; 
let offset = 0;   

consultarBtn.addEventListener('click', () => {
    offset = 0; // Reiniciar el desplazamiento
    consultaColaboradoras();
});
prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        consultaColaboradoras();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    consultaColaboradoras();
});

function consultaColaboradoras(){
        // Realizar la solicitud AJAX para obtener la lista de clientas
        document.getElementById("img_cargando").style.visibility = "visible";
        var colaboradoras = document.getElementById("idcolaboradora").value;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `php/consulta_colaboradoras.php?limit=${limit}&offset=${offset}&colaboradoras=${colaboradoras}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    const clientas = response.data;
                    if (clientas.length === 0 && offset > 0) {
                        offset = 0;
                        consultaColaboradoras(); 
                        return; 
                    }
                    let output = '';
                    let startIndex = offset + 1; // Calcular el índice inicial
                    clientas.forEach((clienta, index) => {
                        output += `
                            <tr>
                                <td>${startIndex + index}</td>
                                <td>${clienta.nombre}</td>
                                <td>${clienta.edad}</td>
                                <td>${clienta.expertaen}</td>
                                <td>${clienta.horario_laboral}</td>
                                <td>${clienta.telefono}</td>
                                <td>${clienta.diaslaborales}</td>
                                <td>${clienta.comisión}%</td>
                                <td>${clienta.antigüedad}</td>
                                <td>${clienta.uniforme_proporcionado}</td>
                                <td>${clienta.material_para_trabajo}</td>
                                <td>${clienta.observaciones}</td>
                                <td>
                                <a href="javascript:void(0);" onclick="abrirVentanaEmergente('${clienta.imagen}')">
                                    <img src="./styles/verimagen.png" alt="${clienta.nombre}" style="width: 40px; height: 40px;">
                                </a>
                                </td>
                                <td align="center">
                                <button onclick="editar(${clienta.idcolaboradora});" 
                                style="
                                background-color: #e5e3e5; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: pointer; 
                                transition: background-color 0.3s, color 0.3s;"
                                ${isAdmin ? '' : 'disabled'}>
                                Editar
                                </button>
                                </td>
                                <td align="center">
                                <button onclick="eliminar(${clienta.idcolaboradora});" 
                                style="
                                background-color: #d2d2d2; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: pointer; 
                                transition: background-color 0.3s, color 0.3s;"
                                ${isAdmin ? '' : 'disabled'}>
                                Eliminar
                                </button>
                                 </td>
                                 <td align="center">
                                <label class="switch">
                                    <input type="checkbox" ${clienta.estatus == 1 ? 'checked' : ''} onclick="toggleStatus(${clienta.idcolaboradora}, this)">
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
                    clientasList.innerHTML = '<tr><td colspan="10">No se encontraron colaboradoras.</td></tr>';
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
    window.location.href = 'php/exportar_colaboradoras.php';
});
function editar(id) {
    var url = "./colaboradoras.html";
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
              xhr.open('POST', 'php/eliminar_colaboradora.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
              xhr.onload = function() {
                  if (xhr.status === 200) {
                      const response = JSON.parse(xhr.responseText);
                      if (response.success) {
                          Swal.fire({
                              icon: 'success',
                              title: '¡Se eliminó correctamente la colaboradora!',
                              text: '¡Cita Eliminada!',
                              confirmButtonColor: '#d63384'
                          }).then(() => {
                            offset = 0;
                            consultaColaboradoras(); 
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
  function toggleStatus(idcolaboradora, checkbox) {
    const estatus = checkbox.checked ? 1 : 0;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/actualiza_status_colaboradora.php', true);
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
    xhr.send(`idcolaboradora=${idcolaboradora}&estatus=${estatus}`);
}
  
document.addEventListener('DOMContentLoaded', () => {
    // Obtener los municipios y llenar el select
    function cargaColaboradoras() {
        const municipioColaboradora = document.getElementById('idcolaboradora');
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'php/consulta_colaboradoras.php', true);

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
function abrirVentanaEmergente(imagen) {
    const width = 600;
    const height = 400;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    window.open('php/' + imagen, 'Imagen', `width=${width},height=${height},top=${top},left=${left},resizable=yes`);
}