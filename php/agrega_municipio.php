<?php
header('Content-Type: application/json');
include('db.php');

$municipio = $_POST['municipio'];
// Insertar los datos en la tabla
$sql = "INSERT INTO municipios (nombre) 
        VALUES (?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('s', $municipio);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al agregar la clienta.']);
}

$stmt->close();
$conn->close();
?>
