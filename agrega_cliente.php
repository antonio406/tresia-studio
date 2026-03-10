<?php
session_start();
include('php/permisos.php');

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if (!tienePermiso('clientas', 'crear')) {
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
    <script src="js/agrega_cliente.js" defer></script>
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
                        <th>Fecha</th>
                        <td><input type="date" name="fecha" id="fecha" required></td>
                    </tr>
                    <tr>
                        <th>Edad</th>
                        <td><input type="number" name="edad" id="edad" min="0" required></td>
                    </tr>
                    <tr>
                        <th>Sexo</th>
                        <td>
                            <select name="sexo" id="sexo" required>
                                <option value="Femenino" id="Femenino">Femenino</option>
                                <option value="Masculino" id="Masculino">Masculino</option>
                                <option value="Otro" id="Otro">Otro</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Ocupación</th>
                        <td><input type="text" name="ocupacion" id="ocupacion" required></td>
                    </tr>
                    <tr>
                        <th>Colonia</th>
                        <td><input type="text" name="colonia" id="colonia"></td>
                    </tr>
                    <tr>
                        <th>Calle</th>
                        <td><input type="text" name="calle" id="calle"></td>
                    </tr>
                    <tr>
                        <th>Código Postal</th>
                        <td><input type="number" name="cp" id="cp"></td>
                    </tr>
                    <tr>
                        <th>Municipio</th>
                        <td>
                            <select name="idmunicipio" id="idmunicipio" required>
                                <option value="">Seleccione un municipio</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha de Nacimiento</th>
                        <td><input type="date" name="fecha_nacimiento" id="fecha_nacimiento"></td>
                    </tr>
                    <tr>
                        <th>Número Telefónico</th>
                        <td><input type="text" name="numerotelefonico" id="numerotelefonico"></td>
                    </tr>
                    <tr>
                        <th>Alergias</th>
                        <td><input type="text" name="alergias" id="alergias"></td>
                    </tr>
                    <tr>
                        <th>Patologías Activas</th>
                        <td><input type="text" name="patalogias_activas" id="patalogias_activas"></td>
                    </tr>
                    <tr>
                        <th>Lentes (Contacto/Armazón)</th>
                        <td><input type="text" name="lentes_contacto_armazon" id="lentes_contacto_armazon"></td>
                    </tr>
                    <tr>
                        <th>¿Cómo nos conoció?</th>
                        <td><input type="text" name="saber_nosotros" id="saber_nosotros"></td>
                    </tr>
                    <tr>
                        <th>Personas Recomendadas</th>
                        <td><input type="text" name="p_recomendadas" id="p_recomendadas"></td>
                    </tr>
                    <tr>
                        <th>Observaciones</th>
                        <td><input type="text" name="observaciones" id="observaciones"></td>
                    </tr>
                    <tr>
                        <th>Incentivo Cumpleaños</th>
                        <td><input type="text" name="detalle" id="detalle"></td>
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
                            <input type="hidden" id="imageData" name="imageData">
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
