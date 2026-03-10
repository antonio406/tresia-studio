<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

$puedeEditar = tienePermiso('servicios', 'editar');
$puedeEliminar = tienePermiso('servicios', 'eliminar');
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
        <h2>Servicios</h2>
        <div class="search-container">
            <label for="startDatePicker"><b>Filtros de Busqueda Por:</b></label>
            Nombre de Servicio <input type="text" id="servicio" onkeypress="cargarServicios(this.value);"> 
            
            <div class="loading-container">
                <img src="styles/cargando.gif" width="70px" height="30px" id="img_cargando" style="visibility: hidden;">
            </div>
        </div>
        <button id="consultarBtn">Consultar</button>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Precio</th>
                    <th>Actualizar</th>
                    <th>Eliminar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="clientasList">
                <!-- Las filas de la tabla se generarán dinámicamente aquí -->
            </tbody>
        </table>
        <div class="pagination">
            <button id="prevBtn" disabled>Anterior</button>
            <button id="nextBtn" disabled>Siguiente</button>
        </div>
    </div>

    <script>
        const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
        const puedeEliminar = <?php echo json_encode($puedeEliminar); ?>;
    </script>
    <script src="js/consulta_servicios.js"></script>
</body>
</html>
