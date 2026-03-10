<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if (!tienePermiso('municipios', 'crear')) {
    header('Location: alert.php');
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
    <script src="js/municipios.js" defer></script>
</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <div class="form-container">
            <h2 id="formTitle">Agrega Municipios</h2>
            <form id="clientaForm">
                <table>
                    <tr>
                        <th>Nombre Municipios</th>
                        <td><input type="text" name="municipio" id="municipio"></td>
                    </tr>
                </table>
                <input type="submit" id="submitBtn" value="Guardar" class="submit-btn">
                <div id="message"></div>
            </form>
        </div>
    </div>
</body>
</html>
