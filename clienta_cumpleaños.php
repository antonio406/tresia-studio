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
    <title>Menú Lateral y Formulario</title>
    <link rel="stylesheet" href="styles/agregaclienta.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/agrega_des_cumpleaños.js" defer></script>
</head>
<style>
    #videoContainer {
        position: relative;
        display: inline-block;
    }
    #photo {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
    }
</style>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <div class="form-container">
            <h2 id="formTitle">Registrar Clienta</h2>
            <form id="clientaForm">
                <table>
                    <tr>
                        <th>Nombre</th>
                        <td><input type="text" name="nombre" id="nombre" required></td>
                    </tr>
                    <tr>
                        <th>Fecha de Nacimiento</th>
                        <td><input type="date" name="fecha_nacimiento" id="fecha_nacimiento"></td>
                    </tr>
                    <tr>
                        <th>Incentivo Cumpleaños</th>
                        <td><input type="text" name="detalle" id="detalle"></td>
                    </tr>
                </table>
                <input type="submit" value="Guardar" id="submitBtn" class="submit-btn">
                <div id="message"></div>
            </form>
        </div>
    </div>
</body>
</html>
