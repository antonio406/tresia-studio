<?php
header('Content-Type: application/json');
include('db.php');

// Recibir los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$detalle = $_POST['detalle'];

// Construir la consulta SQL dinámica
$sql = "UPDATE clientas SET 
    nombre = ?, 
    fecha_nacimiento = ?, 
    detalle = ?";

$sql .= " WHERE idclienta = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

// Bind de parámetros dinámico: si hay imagen, se incluye
$stmt->bind_param('sssi', $nombre,$fecha_nacimiento,$detalle,$id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la clienta.']);
}

$stmt->close();
$conn->close();
?>
