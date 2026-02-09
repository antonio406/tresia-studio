<?php
header('Content-Type: application/json');
include('db.php');

// Obtener los datos del formulario
$descripcion = $_POST['descripcion'];
$Unidades = $_POST['Unidades'];
$Precio = $_POST['Precio'];
$id = $_POST['id'];

// Actualizar los datos en la tabla
$sql = "UPDATE inventario 
        SET descripcion=?, unidad=? ,precio=? 
        WHERE idproducto =?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

$stmt->bind_param('sssi', $descripcion, $Unidades, $Precio,$id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Error al ejecutar la consulta: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la el producto.']);
}

$stmt->close();
$conn->close();
?>
