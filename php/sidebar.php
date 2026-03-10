<?php
/**
 * Sidebar reutilizable con permisos - Tresia Studio
 * Incluir este archivo en todas las páginas para tener un sidebar consistente.
 * 
 * Requiere que session_start() y include('php/permisos.php') se hayan llamado antes.
 * Si se incluye desde una página en la raíz, usar: include('php/sidebar.php')
 */

$isAdmin = esAdmin();
?>
<div class="sidebar">
    <div class="logo">TRESIA STUDIO</div>
    <div class="logo">
        <h6 style="color: black;">BIENVENIDA <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Invitado'; ?></h6>
    </div>
    <ul class="nav-links">
        <li><a href="home.php">Inicio</a></li>
        <?php if (tieneCualquierPermiso('citas')): ?>
        <li class="dropdown">
            <a href="#">Citas</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('citas', 'crear')): ?>
                <li><a href="tabla.php">Registra Cita</a></li>
                <?php endif; ?>
                <li><a href="tabla3.php">Consulta Citas</a></li>
                <li><a href="agenda.php">Agenda de Citas</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (tieneCualquierPermiso('clientas')): ?>
        <li class="dropdown">
            <a href="#">Clientas</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('clientas', 'crear')): ?>
                <li><a href="agrega_cliente.php">Registra Clientas</a></li>
                <?php endif; ?>
                <li><a href="consulta_cllientes.php">Consulta Clientas</a></li>
                <li><a href="cumpleaños.php">Cumpleaños Clientas</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (tieneCualquierPermiso('colaboradoras')): ?>
        <li class="dropdown">
            <a href="#">Colaboradoras</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('colaboradoras', 'crear')): ?>
                <li><a href="colaboradoras.php">Registra Colaboradoras</a></li>
                <?php endif; ?>
                <li><a href="consulta_colaboradoras.php">Consulta Colaboradoras</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (tieneCualquierPermiso('servicios')): ?>
        <li class="dropdown">
            <a href="#">Servicios</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('servicios', 'crear')): ?>
                <li><a href="servicios.php">Agrega Servicio</a></li>
                <?php endif; ?>
                <li><a href="consulta_servicios.php">Consulta Servicios</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (tieneCualquierPermiso('municipios')): ?>
        <li class="dropdown">
            <a href="#">Municipios</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('municipios', 'crear')): ?>
                <li><a href="municipios.php">Municipios</a></li>
                <?php endif; ?>
                <li><a href="consulta_municipios.php">Consulta Municipios</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (tieneCualquierPermiso('gastos')): ?>
        <li class="dropdown">
            <a href="#">Gastos</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('gastos', 'crear')): ?>
                <li><a href="gastos.php">Agrega Gasto</a></li>
                <?php endif; ?>
                <li><a href="consulta_gastos.php">Consulta Gastos</a></li>
                <li><a href="gastos_totales.php">Consulta Gastos Totales</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (tieneCualquierPermiso('inventario')): ?>
        <li class="dropdown">
            <a href="#">Inventario</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('inventario', 'crear')): ?>
                <li><a href="agrega_producto.php">Agrega Productos</a></li>
                <?php endif; ?>
                <li><a href="consulta_productos.php">Consulta Productos</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (tieneCualquierPermiso('usuarios')): ?>
        <li class="dropdown">
            <a href="#">Usuarios</a>
            <ul class="sub-menu">
                <?php if (tienePermiso('usuarios', 'crear')): ?>
                <li><a href="usuarios.php">Registra Usuario</a></li>
                <?php endif; ?>
                <li><a href="consulta_usuarios_page.php">Consulta Usuarios</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <li><a href="logout.php">Salir</a></li>
    </ul>
</div>
