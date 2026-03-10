<?php
header('Content-Type: application/json');
include('db.php');
include('permisos.php');
session_start();

if (!tienePermiso('servicios', 'editar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar servicios.']);
    exit;
}

// Obtener los datos del formulario
$id = $_POST['id']; // Asegúrate de recibir el ID de la colaboradora
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];

// Actualizar los datos en la tabla
$sql = "UPDATE servicios 
        SET nombre=?, descripción=? ,precio=? 
        WHERE idservicio =?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('sssi', $nombre, $descripcion,$precio,  $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la colaboradora.']);
}

$stmt->close();
$conn->close();
?>
