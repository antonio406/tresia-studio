<?php
header('Content-Type: application/json');
include('db.php');
include('permisos.php');
session_start();

if (!tienePermiso('gastos', 'editar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar gastos.']);
    exit;
}

// Obtener los datos del formulario
$id = $_POST['id'];
$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];
$monto_transferencia = $_POST['monto_transferencia'];
$monto_efectivo = $_POST['monto_efectivo'];
$monto = $monto_transferencia + $monto_efectivo;
$tipogasto = $_POST['tipogasto'];
// Actualizar los datos en la tabla
$sql = "UPDATE gastos 
        SET fecha=?, descripcion=?, monto=?, monto_transferencia=?, monto_efectivo=?, tipo=? 
        WHERE idgasto =?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('ssdddsi', $fecha, $descripcion, $monto, $monto_transferencia, $monto_efectivo, $tipogasto, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la colaboradora.']);
}

$stmt->close();
$conn->close();
?>
