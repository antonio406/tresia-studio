const consultarBtn = document.getElementById('consultarBtn');
const clientasList = document.getElementById('clientasList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10; 
let offset = 0;   

consultarBtn.addEventListener('click', () => {
    offset = 0; // Reiniciar el desplazamiento
    consultaMunicipios();
});
prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        consultaMunicipios();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    consultaMunicipios();
});
function consultaMunicipios(){
    document.getElementById("img_cargando").style.visibility = "visible";

        // Realizar la solicitud AJAX para obtener la lista de clientas
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `php/consulta_municipios.php?limit=${limit}&offset=${offset}`, true);
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
                                 <td align="center">
                                <button onclick="editar(${clienta.idmunicipio});" 
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
                                <button onclick="eliminar(${clienta.idmunicipio});" 
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
                    clientasList.innerHTML = '<tr><td colspan="10">No se encontraron xd.</td></tr>';
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
    window.location.href = 'php/exportar_municipios_excel.php';
});

function editar(id) {
    var url = "./municipios.html";
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
              xhr.open('POST', 'php/eliminar_municipio.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
              xhr.onload = function() {
                  if (xhr.status === 200) {
                      const response = JSON.parse(xhr.responseText);
                      if (response.success) {
                          Swal.fire({
                              icon: 'success',
                              title: '¡Se eliminó correctamente el municipio!',
                              text: 'Municipio Eliminado!',
                              confirmButtonColor: '#d63384'
                          }).then(() => {
                            consultaMunicipios(); // Actualizar la lista de citas después de la eliminación
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
  
  
  