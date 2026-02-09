<?php
header('Content-Type: application/json');
include('db.php');

$idservicio = $_POST['idservicio']; // ID de la clienta
$estatus = $_POST['estatus']; // Nuevo estatus (1 o 0)

$sql = "UPDATE servicios SET estatus = ? WHERE idservicio = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('ii', $estatus, $idservicio);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estatus de la clienta.']);
}

$stmt->close();
$conn->close();
?>
