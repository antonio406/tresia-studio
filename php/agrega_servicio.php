<?php
header('Content-Type: application/json');
include('db.php');

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];

// Insertar los datos en la tabla
$sql = "INSERT INTO servicios (nombre,descripción,estatus,precio) 
        VALUES (?,?,true,?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('sss', $nombre,$descripcion,$precio);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al agregar el servicio.']);
}

$stmt->close();
$conn->close();
?>
