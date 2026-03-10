<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if (!tienePermiso('colaboradoras', 'crear')) {
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
    <script src="js/agrea_colaboradora.js" defer></script>
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
            <h2 id="formTitle">Agrega Colaboradora</h2>
            <form id="clientaForm">
                <table>
                    <tr>
                        <th>Nombre</th>
                        <td><input type="text" name="nombre" id="nombre" required></td>
                    </tr>
                    <tr>
                        <th>Edad</th>
                        <td><input type="number" name="edad" id="edad" min="0" required></td>
                    </tr>
                    <tr>
                        <th>Experta En:</th>
                        <td><input type="text" name="experta" id="experta" required></td>
                    </tr>
                    <tr>
                        <th>Horario Laboral</th>
                        <td><input type="text" name="horario" id="horario"></td>
                    </tr>
                    <tr>
                        <th>Telefono</th>
                        <td><input type="text" name="telefono" id="telefono"></td>
                    </tr>
                    <tr>
                        <th>Dias Laborales</th>
                        <td><input type="number" name="diasla" id="diasla"></td>
                    </tr>
                    <tr>
                        <th>Comision</th>
                        <td><input type="number" name="cp" id="comision"></td>
                    </tr>
                    <tr>
                        <th>Antigüedad</th>
                        <td><input type="date" name="antiguedad" id="antiguedad"></td>
                    </tr>
                    <tr>
                        <th>Uniforme Proporcionado</th>
                        <td><input type="text" name="uniforme" id="uniforme"></td>
                    </tr>
                    <tr>
                        <th>Material Para Trabajo</th>
                        <td><input type="text" name="materialtra" id="materialtra"></td>
                    </tr>
                    <tr>
                        <th>Observaciones</th>
                        <td><input type="text" name="observaciones" id="observaciones"></td>
                    </tr>
                    <tr>
                        <th>Imagen Clienta</th>
                        <td>
                            <div id="videoContainer">
                                <video id="video" autoplay></video>
                                <canvas id="photoCanvas" style="display:none;"></canvas>
                                <img id="photo" alt="Foto capturada"/>
                            </div>
                            <button type="button" id="startButton">Iniciar Cámara</button>
                            <button type="button" id="captureButton">Capturar Foto</button>
                        </td>
                    </tr>
                </table>
                <input type="hidden" id="imageData" name="imageData">

                <input type="submit" value="Guardar" id="submitBtn" class="submit-btn">
                <div id="message"></div>
            </form>
        </div>
    </div>
</body>
</html>
