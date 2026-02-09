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
function consulta_gastos() {
    document.getElementById("img_cargando").style.visibility = "visible";
    const fecha1 = document.getElementById('dateOne').value;
    const fecha2 = document.getElementById('dateTwo').value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/gastos_totales_generales.php?fecha1=${fecha1}&fecha2=${fecha2}&offset=${offset}&limit=${limit}`, true);
    xhr.onload = function() {
        document.getElementById("img_cargando").style.visibility = "hidden";
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
                let startIndex = offset + 1; 
                clientas.forEach((clienta, index) => {
                    output += `
                        <tr>
                            <td>${startIndex + index}</td>
                            <td>$${clienta.transferencia}</td>
                            <td>$${clienta.efectivo}</td>
                            <td>$${clienta.propina}</td>
                            <td>$${clienta.total_citas}</td>
                            <td>$${clienta.total_gastos}</td>
                            <td>$${clienta.total_ingresos_adicionales}</td>
                            <td>$${clienta.total_general}</td>
                        </tr>
                    `;
                });

                clientasList.innerHTML = output;

                prevBtn.disabled = offset <= 0;
                nextBtn.disabled = offset + limit >= response.total;
            } else {
                clientasList.innerHTML = '<tr><td colspan="5">No se encontraron resultados.</td></tr>';
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
    const fecha1 = document.getElementById('dateOne').value;
    const fecha2 = document.getElementById('dateTwo').value;
    window.location.href = `php/exportar_gastos_totales.php?fecha1=${encodeURIComponent(fecha1)}&fecha2=${encodeURIComponent(fecha2)}`;
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