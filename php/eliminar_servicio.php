<?php
header('Content-Type: application/json');
include('db.php');

// Recibir el ID de la cita a eliminar
$id = $_POST['id'];

if ($id) {
    // Preparar la consulta para eliminar la cita
    $sql = "DELETE FROM servicios WHERE idservicio = ?;";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Ejecutar la consulta y enviar la respuesta adecuada
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cita eliminada con éxito.']);
    } else {
        error_log('Error al ejecutar la consulta: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'El servicio cuenta con citas asociadas.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de la cita no proporcionado.']);
}

$conn->close();
?>
