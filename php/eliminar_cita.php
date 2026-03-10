<?php
header('Content-Type: application/json');
include('db.php');
include('permisos.php');
session_start();

if (!tienePermiso('citas', 'eliminar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar citas.']);
    exit;
}

// Recibir el ID de la cita a eliminar
$id = $_POST['id'];

if ($id) {
    // Preparar la consulta para eliminar la cita
    $sql = "DELETE FROM citas WHERE idcitas = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Ejecutar la consulta y enviar la respuesta adecuada
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cita eliminada con éxito.']);
    } else {
        error_log('Error al ejecutar la consulta: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la cita.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de la cita no proporcionado.']);
}

$conn->close();
?>
