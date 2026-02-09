<?php
header('Content-Type: application/json');
include('db.php');

// Directorio de subida
$uploadDir = 'uploads/';

// Verificar si se ha cargado una imagen
if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] === UPLOAD_ERR_OK) {
    $imageFile2 = $uploadDir . basename($_FILES['imagen2']['name']);
    $fileType = strtolower(pathinfo($imageFile2, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Verificación de tipo de archivo
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Archivo no permitido. Solo se permiten JPG, JPEG, PNG o GIF.']);
        exit;
    }

    // Verificar si hubo un error al cargar el archivo
    if ($_FILES['imagen2']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error en la carga del archivo.']);
        exit;
    }

    // Mover el archivo a la carpeta de subida
    if (!move_uploaded_file($_FILES['imagen2']['tmp_name'], $imageFile2)) {
        echo json_encode(['success' => false, 'message' => 'Error al mover el archivo subido.']);
        exit;
    }
} else {
    $imageFile2 = NA; // Si no se ha subido imagen, se puede dejar como null o un valor predeterminado
}


$nombre = $_POST['nombre'];
$edad = $_POST['edad'];
$experta = $_POST['experta'];
$horario = $_POST['horario'];
$diasla = $_POST['diasla'];
$comision = $_POST['comision'];
$antiguedad = $_POST['antiguedad'];
$uniforme = $_POST['uniforme'];
$materialtra = $_POST['materialtra'];
$observaciones = $_POST['observaciones'];
$telefono = $_POST['telefono'];

// Insertar los datos en la tabla
$sql = "INSERT INTO colaboradoras (nombre,edad,expertaen,horario_laboral,diaslaborales,comisión,antigüedad,
                                   uniforme_proporcionado,material_para_trabajo,observaciones,estatus,telefono,imagen) 
        VALUES (?,?,?,?,?,?,?,?,?,?,true,?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    exit;
}

$stmt->bind_param('ssssssssssss', $nombre,$edad,$experta,$horario,$diasla,$comision,$antiguedad,$uniforme,$materialtra,$observaciones,$telefono,$imageFile2);

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
