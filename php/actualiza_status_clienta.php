<?php
header('Content-Type: application/json');
include('db.php');

$idclienta = $_POST['idclienta']; // ID de la clienta
$estatus = $_POST['estatus']; // Nuevo estatus (1 o 0)

$sql = "UPDATE clientas SET estatus = ? WHERE idclienta = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('ii', $estatus, $idclienta);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estatus de la clienta.']);
}

$stmt->close();
$conn->close();
?>
