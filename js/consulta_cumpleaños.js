const consultarBtn = document.getElementById('consultarBtn');
const clientasList = document.getElementById('clientasList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10; 
let offset = 0;   

consultarBtn.addEventListener('click', () => {
    offset = 0; // Reiniciar el desplazamiento
    cargaCumpleaños();
});
prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        cargaCumpleaños();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    cargaCumpleaños();
});
function cargaCumpleaños(){
    const dateOne = document.getElementById('dateOne').value;
    const dateTwo = document.getElementById('dateTwo').value;

    // Extraer mes y día de las fechas seleccionadas
    const mesDiaUno = dateOne.slice(5); // "MM-DD"
    const mesDiaDos = dateTwo.slice(5); // "MM-DD"
    document.getElementById("img_cargando").style.visibility = "visible";

    // Realizar la solicitud AJAX para obtener la lista de clientas
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_cumple.php?limit=${limit}&offset=${offset}&dateOne=${mesDiaUno}&dateTwo=${mesDiaDos}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const clientas = response.data;
                if (clientas.length === 0 && offset > 0) {
                    offset = 0;
                    cargaCumpleaños(); 
                    return; 
                }
                let output = '';
                let startIndex = offset + 1; // Calcular el índice inicial
                clientas.forEach((clienta, index) => {
                    output += `
                        <tr>
                            <td>${startIndex + index}</td>
                            <td>${clienta.nombre}</td>
                            <td>${clienta.fecha_nacimiento}</td>
                            <td>${clienta.detalle ? clienta.detalle : ''}</td>
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
                            ">Editar
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
                clientasList.innerHTML = '<tr><td colspan="10">No se encontraron Cumpleaños el dia de hoy.</td></tr>';
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
        var url = "./clienta_cumpleaños.html";
        url += "?id=" + id;
        var nombreVentana = "ventanaEditar"; // Nombre para la ventana (opcional)
        var opciones = "width=800,height=600,scrollbars=yes,resizable=yes"; // Opciones para la ventana
        window.open(url, nombreVentana, opciones);
      }