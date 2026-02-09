<?php
header('Content-Type: application/json');
include('db.php');

$idcolaboradora = $_POST['idcolaboradora']; // ID de la clienta
$estatus = $_POST['estatus']; // Nuevo estatus (1 o 0)

$sql = "UPDATE colaboradoras SET estatus = ? WHERE idcolaboradora = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('ii', $estatus, $idcolaboradora);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estatus de la clienta.']);
}

$stmt->close();
$conn->close();
?>
