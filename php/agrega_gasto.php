<?php
header('Content-Type: application/json');
session_start();
include('permisos.php');
if (!tienePermiso('gastos', 'crear')) {
    die(json_encode(['success' => false, 'message' => 'No tienes permiso para agregar gastos.']));
}
include('db.php');

$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];
$monto_transferencia = $_POST['monto_transferencia'];
$monto_efectivo = $_POST['monto_efectivo'];
$monto = $monto_transferencia + $monto_efectivo;
$tipogasto = $_POST['tipogasto'];

// Insertar los datos en la tabla
$sql = "INSERT INTO gastos (fecha, descripcion, monto, monto_transferencia, monto_efectivo, tipo) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('ssddds', $fecha, $descripcion, $monto, $monto_transferencia, $monto_efectivo, $tipogasto);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al agregar el gasto.']);
}

$stmt->close();
$conn->close();
?>
