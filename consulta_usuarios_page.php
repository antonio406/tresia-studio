<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if (!tienePermiso('usuarios', 'ver')) {
    header('Location: alert.php');
    exit();
}

$puedeEditar = tienePermiso('usuarios', 'editar');
$puedeEliminar = tienePermiso('usuarios', 'eliminar');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles/consulta.css">
    <title>Consulta de Usuarios - Tresia Studio</title>
    <style>
        .rol-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .rol-admin {
            background-color: #9b59b6;
            color: #fff;
        }
        .rol-usuario {
            background-color: #3498db;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <h2>Consulta Usuarios</h2>
        <div class="search-container">
            <label for="buscarUsuario"><b>Buscar:</b></label>
            <input type="text" id="buscarUsuario" placeholder="Nombre o usuario..." style="padding: 5px; width: 200px;">
            <button id="consultarBtn">Consultar</button>
            <div class="loading-container">
                <img src="styles/cargando.gif" width="70px" height="30px" id="img_cargando" style="visibility: hidden;">
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido P.</th>
                    <th>Apellido M.</th>
                    <th>Rol</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody id="usuariosList">
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
    <script src="js/consulta_usuarios.js"></script>
</body>
</html>
