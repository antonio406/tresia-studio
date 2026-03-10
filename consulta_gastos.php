<?php
session_start(); 
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if (!tienePermiso('gastos', 'ver')) {
    header('Location: alert.php');
    exit();
}

$puedeEditar = tienePermiso('gastos', 'editar');
$puedeEliminar = tienePermiso('gastos', 'eliminar');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles/consulta.css">
    <title>Consulta de Clientas</title>
</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <h2>Consulta Gastos</h2>
        <div class="search-container">
            <label for="startDatePicker"><b>Filtros de Busqueda:</b> Por fecha del:</label>
            <input type="date" id="dateOne"> al 
            <input type="date" id="dateTwo"> 
            
            <div class="loading-container">
                <img src="styles/cargando.gif" width="70px" height="30px" id="img_cargando" style="visibility: hidden;">
            </div>
        </div>
        <button id="consultarBtn">Consultar</button>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Fecha</th>
                    <th>Hora de Registro</th>
                    <th>Descripcion</th>
                    <th>Monto</th>
                    <th>Transferencia</th>
                    <th>Efectivo</th>
                    <th>Tipo</th>
                    <th>Actualizar</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody id="clientasList">
                <!-- Las filas de la tabla se generarán dinámicamente aquí -->
            </tbody>
        </table>
          <!-- Ocultar esta sección si el usuario no tiene el rol adecuado -->
          <div id="t" style="margin-left: 812px;color: red;"></div>
          <div id="e" style="margin-left: 800px;color: red;"></div>
          <div id="a" style="margin-left: 775px;color: red;"></div>
          <div id="a" style="margin-left: 775px;color: red;"></div>
        <div class="pagination">
            <button id="prevBtn" disabled>Anterior</button>
            <button id="nextBtn" disabled>Siguiente</button>
        </div>
    </div>

    <script>
        const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
        const puedeEliminar = <?php echo json_encode($puedeEliminar); ?>;
    </script>
    <script src="js/consulta_gastos.js"></script>
</body>
</html>
