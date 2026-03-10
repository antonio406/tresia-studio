<?php
session_start(); 
header('Content-Type: text/html; charset=utf-8');
include('php/permisos.php');

// Redirigir si no está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html'); 
    exit();
}
$isAdmin = esAdmin();
$puedeEditar = tienePermiso('colaboradoras', 'editar');
$puedeEliminar = tienePermiso('colaboradoras', 'eliminar');
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
        <h2>Colaboradoras</h2>
        <div class="search-container">
            <b>Filtros de Busqueda:</b>Colaboradora
            <select name="idcolaboradora" id="idcolaboradora" required>
                <option value="">Seleccione una Colaboradora</option>
            </select>
            <div class="loading-container">
                <img src="styles/cargando.gif" width="70px" height="30px" id="img_cargando" style="visibility: hidden;">
            </div>
        </div>
        <button id="consultarBtn">Consultar</button>
        <button id="exportarExcelBtn">Exportar a Excel</button>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Experta En:</th>
                    <th>Horario Laboral</th>
                    <th>Telefono</th>
                    <th>Dias Laborales</th>
                    <th>Comision</th>
                    <th>Antigüedad</th>
                    <th>Uniforme Proporcionado</th>
                    <th>Material Para Trabajo</th>
                    <th>Observaciones</th>
                    <th>Foto Colaboradora</th>
                    <th>Editar</th>
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
        const isAdmin = <?php echo json_encode($isAdmin); ?>;
        const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
        const puedeEliminar = <?php echo json_encode($puedeEliminar); ?>;
    </script>
    <script src="js/consulta_colaboradoras.js"></script>
</body>
</html>
