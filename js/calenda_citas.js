const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
const daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
const allHours = [
    "09:00:00", "11:00:00", "13:00:00",
    "14:00:00", "15:00:00", "16:00:00", "18:00:00"
];

async function fetchData() {
    try {
        const response = await fetch('php/citas.php'); // Cambia el nombre del archivo PHP si es diferente
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error al cargar datos:', error);
        return { citas: [], colaboradoras: [] };
    }
}

async function createCalendar(year) {
    const calendar = document.getElementById('calendar');
    const data = await fetchData();
    const citas = data.citas;
    const colaboradoras = data.colaboradoras;
    const citasMap = {};

    citas.forEach(cita => {
        const dateKey = `${cita.year}-${cita.month}-${cita.day}`;
        if (!citasMap[dateKey]) {
            citasMap[dateKey] = [];
        }
        citasMap[dateKey].push({ hora: cita.hora, colaboradora: cita.nombre, servicio: cita.servicio, clienta: cita.clienta });
    });

    calendar.innerHTML = '';

    for (let i = 0; i < 12; i++) {
        // Crear el contenedor del mes
        const monthDiv = document.createElement('div');
        monthDiv.className = 'month';

        // Título del mes
        const monthTitle = document.createElement('h3');
        monthTitle.innerText = monthNames[i] + ' ' + year;
        monthDiv.appendChild(monthTitle);

        // Contenedor de los días
        const daysDiv = document.createElement('div');
        daysDiv.className = 'days';

        // Obtener el primer día de la semana del mes
        const firstDay = new Date(year, i, 1).getDay();

        // Añadir días vacíos hasta el primer día del mes
        for (let j = 0; j < firstDay; j++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'day empty';
            daysDiv.appendChild(emptyDay);
        }

        // Añadir los días del mes
        for (let j = 1; j <= daysInMonth[i]; j++) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'day';
            dayDiv.innerText = j;
            const dateKey = `${year}-${i + 1}-${j}`;

            // Marcar el día si está ocupado
            if (citasMap[dateKey]) {
                dayDiv.style.backgroundColor = '#f8d7da'; // Ejemplo de color para días ocupados
                dayDiv.addEventListener('click', () => openModal(dateKey, citasMap[dateKey], colaboradoras));
            } else {
                dayDiv.addEventListener('click', () => openModal(dateKey, [], colaboradoras));
            }

            daysDiv.appendChild(dayDiv);
        }

        monthDiv.appendChild(daysDiv);
        calendar.appendChild(monthDiv);
    }
}

function populateYearSelect() {
    const yearSelect = document.getElementById('yearSelect');
    const currentYear = new Date().getFullYear();

    for (let year = currentYear - 10; year <= currentYear + 10; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.innerText = year;
        if (year === 2024) {
            option.selected = true; // Por defecto el año 2024
        }
        yearSelect.appendChild(option);
    }

    yearSelect.addEventListener('change', () => {
        createCalendar(yearSelect.value);
    });
}

function openModal(dateKey, citas, allColaboradoras) {
    const modal = document.getElementById('myModal');
    const selectedDate = document.getElementById('selectedDate');
    const timeSlots = document.getElementById('timeSlots');

    const [year, month, day] = dateKey.split('-');
    selectedDate.innerText = `${day} de ${monthNames[month - 1]} de ${year}`;

    timeSlots.innerHTML = '';

    // Agrupar citas por colaboradora
    const citasMap = {};
    citas.forEach(cita => {
        if (!citasMap[cita.colaboradora]) {
            citasMap[cita.colaboradora] = [];
        }
        citasMap[cita.colaboradora].push({ hora: cita.hora, servicio: cita.servicio, clienta: cita.clienta });
    });

    // Para cada colaboradora en la lista de todas las colaboradoras
    allColaboradoras.forEach(colaboradora => {
        const colaboradoraDiv = document.createElement('div');
        colaboradoraDiv.className = 'colaboradora-section';

        const colaboradoraTitle = document.createElement('h4');
        colaboradoraTitle.innerText = colaboradora;
        colaboradoraDiv.appendChild(colaboradoraTitle);

        // Crear filas de tres columnas combinando horas ocupadas y disponibles
        let row;
        allHours.forEach((hour, index) => {
            if (index % 3 === 0) {
                row = document.createElement('div');
                row.className = 'time-row';
                colaboradoraDiv.appendChild(row);
            }

            const timeSlot = document.createElement('div');
            timeSlot.className = 'time-slot';

            let isOccupied = false;
            let servicio = '';
            let clienta = '';

            if (citasMap[colaboradora]) {
                const cita = citasMap[colaboradora].find(cita => cita.hora === hour);
                if (cita) {
                    isOccupied = true;
                    servicio = cita.servicio;
                    clienta = cita.clienta;
                }
            }

            if (isOccupied) {
                timeSlot.innerText = `${hour} - Ocupado (Servicio: ${servicio}, Clienta: ${clienta})`;
                timeSlot.style.backgroundColor = '#f8d7da'; // Color para horas ocupadas
            } else {
                timeSlot.innerText = `${hour} - Disponible`;
                timeSlot.style.backgroundColor = '#d4edda'; // Color para horas disponibles
            }

            row.appendChild(timeSlot);
        });

        timeSlots.appendChild(colaboradoraDiv);
    });

    modal.style.display = "block";
}

document.addEventListener('DOMContentLoaded', () => {
    populateYearSelect();
    createCalendar(2024); 

    const modal = document.getElementById('myModal');
    const span = document.getElementsByClassName("close")[0];

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
