<?php
header('Content-Type: application/json');
include('db.php');
// Directorio de subida
$uploadDir = 'uploads2/';
$imageFile = null;

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
}

// Obtener los datos del formulario
$id = $_POST['id']; // Asegúrate de recibir el ID de la colaboradora
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

// Actualizar los datos en la tabla
$sql = "UPDATE colaboradoras 
        SET nombre=?,
         edad=?, 
         expertaen=?,
          horario_laboral=?,
           diaslaborales=?,
            comisión=?,
             antigüedad=?,
            uniforme_proporcionado=?,
             material_para_trabajo=?, 
             observaciones=? ,
              telefono=?";
// Si hay una imagen cargada, añade el campo imagen a la consulta
if ($imageFile) {
    $sql .= ", imagen = ?";
}
$sql .= " WHERE idcolaboradora = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}
if ($imageFile) {
$stmt->bind_param('ssssssssssssi', $nombre, $edad, $experta, $horario, $diasla, $comision, $antiguedad, $uniforme, $materialtra, $observaciones,$telefono , $imageFile,$id);
}else {
$stmt->bind_param('sssssssssssi', $nombre, $edad, $experta, $horario, $diasla, $comision, $antiguedad, $uniforme, $materialtra, $observaciones,$telefono ,$id);
}
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la colaboradora.']);
}

$stmt->close();
$conn->close();
?>
