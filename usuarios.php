<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

// Determinar si es edición o creación
$isEdit = isset($_GET['user']);
$permReq = $isEdit ? 'editar' : 'crear';

if (!tienePermiso('usuarios', $permReq)) {
    header('Location: alert.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Tresia Studio</title>
    <link rel="stylesheet" href="styles/agregaclienta.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/agrega_usuario.js" defer></script>
    <style>
        .permisos-container {
            margin-top: 15px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
        }
        .permisos-container h3 {
            text-align: center;
            color: #fff;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .permisos-table {
            width: 100%;
            border-collapse: collapse;
        }
        .permisos-table th {
            background-color: #d31fc4;
            color: #fff;
            padding: 8px;
            font-size: 13px;
            text-align: center;
        }
        .permisos-table td {
            padding: 6px 8px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            font-size: 13px;
            color: #fff;
        }
        .permisos-table td:first-child {
            text-align: left;
            font-weight: bold;
        }
        .permisos-table input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #ff007f;
        }
        .permisos-table tr:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .btn-select-all {
            background-color: #9b59b6;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }
        .btn-select-all:hover {
            background-color: #8e44ad;
        }
        .password-note {
            font-size: 11px;
            color: #ffd700;
            display: none;
        }
        .password-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .password-wrapper input {
            flex: 1;
        }
        .toggle-password {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #fff;
            padding: 4px 6px;
            line-height: 1;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        .toggle-password:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <div class="form-container" style="max-width: 600px;">
            <h2 id="formTitle">Registrar Usuario</h2>
            <form id="usuarioForm">
                <table>
                    <tr>
                        <th>Usuario</th>
                        <td><input type="text" name="user" id="user" required maxlength="10"></td>
                    </tr>
                    <tr>
                        <th>Contraseña</th>
                        <td>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" required maxlength="50">
                                <button type="button" class="toggle-password" id="togglePassword" title="Mostrar/Ocultar contraseña">👁️</button>
                            </div>
                            <span class="password-note" id="passwordNote">Dejar vacío para no modificar</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Nombre</th>
                        <td><input type="text" name="name" id="name" required maxlength="50"></td>
                    </tr>
                    <tr>
                        <th>Apellido Paterno</th>
                        <td><input type="text" name="apellidop" id="apellidop" required maxlength="50"></td>
                    </tr>
                    <tr>
                        <th>Apellido Materno</th>
                        <td><input type="text" name="apellidom" id="apellidom" required maxlength="50"></td>
                    </tr>
                    <tr>
                        <th>Rol</th>
                        <td>
                            <select name="rol" id="rol">
                                <option value="usuario">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <!-- Sección de Permisos -->
                <div class="permisos-container">
                    <h3>Permisos por Módulo</h3>
                    <button type="button" class="btn-select-all" id="btnSelectAll">✓ Seleccionar Todos</button>
                    <button type="button" class="btn-select-all" id="btnDeselectAll" style="background-color: #e74c3c;">✗ Quitar Todos</button>
                    <table class="permisos-table">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th>Ver</th>
                                <th>Crear</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                                <th>Corte</th>
                            </tr>
                        </thead>
                        <tbody id="permisosBody">
                            <tr>
                                <td>Citas</td>
                                <td><input type="checkbox" data-modulo="citas" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="citas" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="citas" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="citas" data-accion="eliminar"></td>
                                <td><input type="checkbox" data-modulo="citas" data-accion="corte"></td>
                            </tr>
                            <tr>
                                <td>Clientas</td>
                                <td><input type="checkbox" data-modulo="clientas" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="clientas" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="clientas" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="clientas" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Colaboradoras</td>
                                <td><input type="checkbox" data-modulo="colaboradoras" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="colaboradoras" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="colaboradoras" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="colaboradoras" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Servicios</td>
                                <td><input type="checkbox" data-modulo="servicios" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="servicios" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="servicios" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="servicios" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Municipios</td>
                                <td><input type="checkbox" data-modulo="municipios" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="municipios" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="municipios" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="municipios" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Gastos</td>
                                <td><input type="checkbox" data-modulo="gastos" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="gastos" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="gastos" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="gastos" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Inventario</td>
                                <td><input type="checkbox" data-modulo="inventario" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="inventario" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="inventario" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="inventario" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Usuarios</td>
                                <td><input type="checkbox" data-modulo="usuarios" data-accion="ver"></td>
                                <td><input type="checkbox" data-modulo="usuarios" data-accion="crear"></td>
                                <td><input type="checkbox" data-modulo="usuarios" data-accion="editar"></td>
                                <td><input type="checkbox" data-modulo="usuarios" data-accion="eliminar"></td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <input type="submit" value="Guardar" id="submitBtn" class="submit-btn">
                <div id="message"></div>
            </form>
        </div>
    </div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.textContent = '🔒';
            this.title = 'Ocultar contraseña';
        } else {
            passwordInput.type = 'password';
            this.textContent = '👁️';
            this.title = 'Mostrar contraseña';
        }
    });
</script>
</body>
</html>
