<?php
header('Content-Type: application/json');
include('db.php');

// Obtener los datos del formulario
$id = $_POST['id']; // Asegúrate de recibir el ID de la colaboradora
$municipio = $_POST['municipio'];

// Actualizar los datos en la tabla
$sql = "UPDATE municipios 
        SET nombre=?
        WHERE idmunicipio =?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('si', $municipio, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la colaboradora.']);
}

$stmt->close();
$conn->close();
?>
