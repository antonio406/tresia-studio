const consultarBtn = document.getElementById('consultarBtn');
const clientasList = document.getElementById('clientasList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10; 
let offset = 0;   

consultarBtn.addEventListener('click', () => {
    offset = 0; // Reiniciar el desplazamiento
    consulta_gastos();
});

prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        consulta_gastos();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    consulta_gastos();
});

function consulta_gastos(){
    document.getElementById("img_cargando").style.visibility = "visible";
    var fecha1 = document.getElementById("dateOne").value;
    var fecha2 = document.getElementById("dateTwo").value;
        // Realizar la solicitud AJAX para obtener la lista de clientas
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `php/consulta_gastos.php?fecha1=${encodeURIComponent(fecha1)}&fecha2=${encodeURIComponent(fecha2)}&limit=${limit}&offset=${offset}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    const clientas = response.data;
                    if (clientas.length === 0 && offset > 0) {
                        offset = 0;
                        consulta_gastos(); 
                        return; 
                    }
                    let output = '';
                    let startIndex = offset + 1; // Calcular el índice inicial
                    clientas.forEach((clienta, index) => {
                        output += `
                            <tr>
                                <td>${startIndex + index}</td>
                                <td>${clienta.fecha}</td>
                                <td>${clienta.hora ? clienta.hora : 'NA'}</td>
                                <td>${clienta.descripcion}</td>
                                <td>$${clienta.monto}</td>
                                <td>${clienta.tipo}</td>
                                 <td align="center">
                                <button onclick="editar(${clienta.idgasto});" 
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
                                <button onclick="eliminar(${clienta.idgasto});" 
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
                    document.getElementById("t").innerText = `Total Ingresos: $${response.ingreso}`;
                    document.getElementById("e").innerText = `Total de Gastos: $${response.gasto}`;
                    prevBtn.disabled = offset <= 0;
                    nextBtn.disabled = offset + limit >= response.total;
                } else {
                    prevBtn.disabled = true;
                    nextBtn.disabled = true;
                    document.getElementById("img_cargando").style.visibility = "hidden";
                    document.getElementById("t").innerText = `Total Ingresos: 0`;
                    document.getElementById("e").innerText = `Total de Gastos: 0`;
                    clientasList.innerHTML = '<tr><td colspan="10">No se encontraron resultados.</td></tr>';
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el servidor',
                    text: 'Hubo un problema al obtener la lista de gastos.',
                    confirmButtonColor: '#d63384'
                });
            }
        };
        xhr.send();
    }
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
document.getElementById('exportarExcelBtn').addEventListener('click', function() {
    window.location.href = 'php/exportar_municipios_excel.php';
});

function editar(id) {
    var url = "./gastos.html";
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
              xhr.open('POST', 'php/elimina_gasto.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
              xhr.onload = function() {
                  if (xhr.status === 200) {
                      const response = JSON.parse(xhr.responseText);
                      if (response.success) {
                          Swal.fire({
                              icon: 'success',
                              title: '¡Se eliminó correctamente el gasto!',
                              text: 'Gasto Eliminado!',
                              confirmButtonColor: '#d63384'
                          }).then(() => {
                            consulta_gastos(); // Actualizar la lista de citas después de la eliminación
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

  
  