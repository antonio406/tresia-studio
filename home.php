<?php
session_start(); // Inicia la sesión
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html'); // Redirige si no está autenticado
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Lateral</title>
    <link rel="stylesheet" href="styles/menu.css">
    <script src="js/home.js" defer></script>

</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <center>
            <img src="./styles/logotresia.jpg" alt="Logo" class="swing-image" height="450px" width="420px">
        </center>
    </div>
</body>
</html>

