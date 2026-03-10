<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if (!tienePermiso('gastos', 'crear')) {
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
    <script src="js/agrega_gasto.js" defer></script>
</head>
<body>
    <?php include('php/sidebar.php'); ?>
    <div class="content">
        <div class="form-container">
            <h2 id="formTitle">Agrega Gasto</h2>
            <form id="clientaForm">
                <table>
                    <tr>
                        <th>Fecha</th>
                        <td><input type="date" name="fecha" id="fecha" required></td>
                    </tr>
                    <tr>
                        <th>Descripcion</th>
                        <td><input type="text" name="descripcion" id="descripcion" required></td>
                    </tr>
                    <tr>
                        <th>Monto Transferencia</th>
                        <td><input type="number" name="monto_transferencia" id="monto_transferencia" step="0.01" min="0" value="0"></td>
                    </tr>
                    <tr>
                        <th>Monto Efectivo</th>
                        <td><input type="number" name="monto_efectivo" id="monto_efectivo" step="0.01" min="0" value="0"></td>
                    </tr>
                    <tr>
                        <th>Tipo de Gasto</th>
                    <td>
                        <select name="tipogasto" id="tipogasto" required>
                            <option value="">Seleccione el tipo de gasto</option>
                            <option value="gasto">Gasto</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="anticipo">Anticipo</option>
                        </select>
                    </td>
                    </tr>
                </table>
                <input type="submit" id="submitBtn" value="Guardar" class="submit-btn">
                <div id="message"></div>
            </form>
        </div>
    </div>
</body>
</html>
