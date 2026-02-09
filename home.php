<?php
session_start(); // Inicia la sesión

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
    <div class="sidebar">
        <div class="logo">TRESIA STUDIO</div>
        <div class="logo">
        <h6 style="color: black;">BIENVENIDA <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Invitado'; ?></h6>
        </div>
        <ul class="nav-links">
            <li><a href="home.php">Inicio</a></li>
            <li class="dropdown">
                <a href="#">Citas</a>
                <ul class="sub-menu">
                    <li><a href="tabla.html">Registra Cita</a></li>
                    <li><a href="tabla3.php">Consulta Citas</a></li>
                    <li><a href="agenda.html">Agenda de Citas</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Clientas</a>
                <ul class="sub-menu">
                    <li><a href="agrega_cliente.html">Registra Clientas</a></li>
                    <li><a href="consulta_cllientes.html">Consulta Clientas</a></li>
                    <li><a href="cumpleaños.html">Cumpleaños Clientas</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Colaboradoras</a>
                <ul class="sub-menu">
                    <li><a href="colaboradoras.html">Registra Colaboradoras</a></li>
                    <li><a href="consulta_colaboradoras.php">Consulta Colaboradoras</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Servicios</a>
                <ul class="sub-menu">
                    <li><a href="servicios.html">Agrega Servicio</a></li>
                    <li><a href="consulta_servicios.html">Consulta Servicios</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Municipios</a>
                <ul class="sub-menu">
                    <li><a href="municipios.html">Municipios</a></li>
                    <li><a href="consulta_municipios.html">Consulta Municipios</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Gastos</a>
                <ul class="sub-menu">
                    <li><a href="gastos.html">Agrega Gasto</a></li>
                    <li><a href="consulta_gastos.php">Consulta Gastos</a></li>
                    <li><a href="gastos_totales.php">Consulta Gastos Totales</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Inventario</a>
                <ul class="sub-menu">
                    <li><a href="agrega_producto.html">Agrega Productos</a></li>
                    <li><a href="consulta_productos.html">Consulta Productos</a></li>
                </ul>
            </li>
            <li><a href="logout.php">Salir</a></li>
        </ul>
    </div>
    <div class="content">
        <center>
            <img src="./styles/logotresia.jpg" alt="Logo" class="swing-image" height="450px" width="420px">
        </center>
    </div>
</body>
</html>
