<?php
header('Content-Type: application/json');
include('db.php');

$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];
$monto = $_POST['monto'];
$tipogasto = $_POST['tipogasto'];

// Insertar los datos en la tabla
$sql = "INSERT INTO gastos (fecha, descripcion, monto, tipo) 
        VALUES (?, ?, ?,?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('ssss', $fecha, $descripcion, $monto,$tipogasto);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al agregar el gasto.']);
}

$stmt->close();
$conn->close();
?>
