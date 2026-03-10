<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/consulta.css">
    <script src="js/calenda_citas.js" defer></script>
    <title>Calendario con Control de Citas</title>
    <style>
       
    </style>
</head>
<body>
    <body>
        <?php include('php/sidebar.php'); ?>
    <div class="content">
            <center>
                <h1>Calendario de Citas</h1>
                <label for="yearSelect">Selecciona el año:</label>
                <select id="yearSelect"></select>
                <div class="calendar" id="calendar"></div>
                <!-- Modal -->
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Horario de citas para el <span id="selectedDate"></span></h2>
                        <div class="time-slots" id="timeSlots"></div>
                    </div>
                </div>            
            </center>
        </div>
        
</body>
</html>
