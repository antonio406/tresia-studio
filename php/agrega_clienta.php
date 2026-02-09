<?php
header('Content-Type: application/json');
include('db.php');

// Directorio de subida
$uploadDir = 'uploads/';

// Verificar si se ha cargado una imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $imageFile = $uploadDir . basename($_FILES['imagen']['name']);
    $fileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Verificación de tipo de archivo
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Archivo no permitido. Solo se permiten JPG, JPEG, PNG o GIF.']);
        exit;
    }

    // Verificar si hubo un error al cargar el archivo
    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error en la carga del archivo.']);
        exit;
    }

    // Mover el archivo a la carpeta de subida
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imageFile)) {
        echo json_encode(['success' => false, 'message' => 'Error al mover el archivo subido.']);
        exit;
    }
} else {
    $imageFile = null; // Si no se ha subido imagen, se puede dejar como null o un valor predeterminado
}

// Recibir los datos del formulario
$nombre = $_POST['nombre'];
$fecha = $_POST['fecha'];
$edad = $_POST['edad'];
$sexo = $_POST['sexo'];
$ocupacion = $_POST['ocupacion'];
$colonia = $_POST['colonia'];
$calle = $_POST['calle'];
$cp = $_POST['cp'];
$idmunicipio = $_POST['idmunicipio'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$numerotelefonico = $_POST['numerotelefonico'];
$alergias = $_POST['alergias'];
$patalogias_activas = $_POST['patalogias_activas'];
$lentes_contacto_armazon = $_POST['lentes_contacto_armazon'];
$saber_nosotros = $_POST['saber_nosotros'];
$p_recomendadas = $_POST['p_recomendadas'];
$observaciones = $_POST['observaciones'];
$detalle = $_POST['detalle'];

// Preparar la consulta SQL
$sql = "INSERT INTO clientas (
            nombre, fecha, edad, sexo, ocupacion, colonia, calle, cp, idmunicipio, fecha_nacimiento,
            numerotelefonico, alergias, patalogias_activas, lentes_contacto_armazon, saber_nosotros,
            p_recomendadas, observaciones, estatus, imagen,detalle) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, true, ?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    exit;
}

// Vincular parámetros
$stmt->bind_param('sssssssssssssssssss', $nombre, $fecha, $edad, $sexo, $ocupacion, $colonia, 
                                    $calle, $cp, $idmunicipio, $fecha_nacimiento,
                                    $numerotelefonico, $alergias, $patalogias_activas, 
                                    $lentes_contacto_armazon, $saber_nosotros, 
                                    $p_recomendadas, $observaciones, $imageFile,$detalle);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al agregar la clienta.']);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
