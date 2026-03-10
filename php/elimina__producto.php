<?php
header('Content-Type: application/json');
include('db.php');
include('permisos.php');
session_start();

if (!tienePermiso('inventario', 'eliminar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar productos.']);
    exit;
}

// Recibir el ID de la cita a eliminar
$id = $_POST['id'];

if ($id) {
    // Preparar la consulta para eliminar la cita
    $sql = "DELETE FROM inventario WHERE idproducto = ?;";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Ejecutar la consulta y enviar la respuesta adecuada
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado con éxito.']);
    } else {
        error_log('Error al ejecutar la consulta: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Hubo un error en el servidor.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de la cita no proporcionado.']);
}

$conn->close();
?>
