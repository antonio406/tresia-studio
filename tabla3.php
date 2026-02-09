<?php
session_start(); 
header('Content-Type: text/html; charset=utf-8');

// Redirigir si no está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html'); 
    exit();
}
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario'] === 'admin';
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
    <div class="sidebar">
        <div class="logo">TRESIA STUDIO</div>
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
                    <th>Propina</th>
                    <th>Total</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody id="clientasList">
                
            </tbody>
        </table>
    <div id="t" class="<?php echo $isAdmin ? 'visible' : 'hidden'; ?>" style="margin-left: 812px; color: red;">
    </div>
    <div id="e" class="<?php echo $isAdmin ? 'visible' : 'hidden'; ?>" style="margin-left: 860px; color: red;">
    </div>
    <div id="p" class="<?php echo $isAdmin ? 'visible' : 'hidden'; ?>" style="margin-left: 855px; color: red;">
    </div>
    <div id="colaboradora" class="<?php echo $isAdmin ? 'visible' : 'hidden'; ?>" style="margin-left: 750px; color: red;">
    </div>
    <div id="comision" class="<?php echo $isAdmin ? 'visible' : 'hidden'; ?>" style="margin-left: 815px; color: red;">
    </div>
    <div id="totalGlobal" class="<?php echo $isAdmin ? 'visible' : 'hidden'; ?>" style="margin-left: 860px; color: red;">
    </div>
        <div class="pagination">
            <button id="prevBtn" disabled>Anterior</button>
            <button id="nextBtn" disabled>Siguiente</button>
        </div>
    </div>
    <script>
        const isAdmin = <?php echo json_encode($isAdmin); ?>;
    </script>
    <script src="js/consulta_citas.js"></script>
</body>
</html>
