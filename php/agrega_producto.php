<?php
header('Content-Type: application/json');
include('db.php');

$descripcion = $_POST['descripcion'];
$Unidades = $_POST['Unidades'];
$Precio = $_POST['Precio'];

// Insertar los datos en la tabla
$sql = "INSERT INTO inventario (descripcion,unidad,precio) 
        VALUES (?,?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('sss', $descripcion,$Unidades,$Precio);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al agregar la clienta.']);
}

$stmt->close();
$conn->close();
?>
