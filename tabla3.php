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
$puedeEditar = tienePermiso('citas', 'editar');
$puedeEliminar = tienePermiso('citas', 'eliminar');
$tieneCorte = tienePermiso('citas', 'corte'); // Nuevo permiso especial
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles/consulta.css">
    <title>Consulta Citas</title>
    <style>
        .hidden {
            display: none; 
        }
        .visible {
            display: block; 
        }
        #suggestions {
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            background: #fff;
            color: black;
            width: calc(100% - 2px);
            z-index: 1000;
        }
        #suggestions div {
            padding: 8px;
            cursor: pointer;
        }
        #suggestions div:hover {
            background-color: #f0f0f0;
            color: black;
        }
        #sg {
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            background: #fff;
            color: black;
            width: calc(100% - 2px);
            z-index: 1000;
        }
        #sg div {
            padding: 8px;
            cursor: pointer;
        }
        #sg div:hover {
            background-color: #f0f0f0;
            color: black;
        }
    </style>
</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <h2>Consulta Citas</h2>
        <div class="search-container">
            <label for="startDatePicker"><b>Filtros de Busqueda:</b> Por fecha del:</label>
            <input type="date" id="dateOne"> al 
            <input type="date" id="dateTwo"> 
            Clienta<div style="position: relative;">
                    <input type="text" id="clientaSearch" autocomplete="off" placeholder="Buscar clienta...">
                    <div id="suggestions"></div>
                    <select name="idclienta" id="idclienta" style="display:none;">
                        <!-- Opciones se llenarán aquí -->
                    </select>
                     </div>
                Colaboradora
                    <select name="idcolaboradora" id="idcolaboradora" required>
                        <option value="">Seleccione una Colaboradora</option>
                    </select>
            <!-- Contenedor para centrar la imagen cargando -->
            <div class="loading-container">
                <img src="styles/cargando.gif" width="70px" height="30px" id="img_cargando" style="visibility: hidden;">
            </div>
        </div>
        <button id="consultarBtn">Consultar Citas</button>
        <button id="exportarExcelBtn">Exportar a Excel</button>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Día</th>
                    <th>Hora</th>
                    <th>Colaboradora</th>
                    <th>Clienta</th>
                    <th>Servicio</th>
                    <th>Descuento</th>
                    <th>Pago por Trans.</th>
                    <th>Efectivo</th>
                    <th>P. Efec.</th>
                    <th>P. Trans.</th>
                    <th>Total</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody id="clientasList">
                
            </tbody>
        </table>
    <div id="t" class="<?php echo ($isAdmin || $tieneCorte) ? 'visible' : 'hidden'; ?>" style="margin-left: 812px; color: red;">
    </div>
    <div id="e" class="<?php echo ($isAdmin || $tieneCorte) ? 'visible' : 'hidden'; ?>" style="margin-left: 860px; color: red;">
    </div>
    <div id="p" class="<?php echo ($isAdmin || $tieneCorte) ? 'visible' : 'hidden'; ?>" style="margin-left: 855px; color: red;">
    </div>
    <div id="colaboradora" class="<?php echo ($isAdmin || $tieneCorte) ? 'visible' : 'hidden'; ?>" style="margin-left: 750px; color: red;">
    </div>
    <div id="comision" class="<?php echo ($isAdmin || $tieneCorte) ? 'visible' : 'hidden'; ?>" style="margin-left: 815px; color: red;">
    </div>
    <div id="totalGlobal" class="<?php echo ($isAdmin || $tieneCorte) ? 'visible' : 'hidden'; ?>" style="margin-left: 860px; color: red;">
    </div>
        <div class="pagination">
            <button id="prevBtn" disabled>Anterior</button>
            <button id="nextBtn" disabled>Siguiente</button>
        </div>
    </div>
    <script>
        const isAdmin = <?php echo json_encode($isAdmin); ?>;
        const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
        const puedeEliminar = <?php echo json_encode($puedeEliminar); ?>;
        const tieneCorte = <?php echo json_encode($tieneCorte); ?>;
    </script>
    <script src="js/consulta_citas.js"></script>
</body>
</html>
