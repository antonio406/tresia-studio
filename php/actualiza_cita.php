<?php
header('Content-Type: application/json');
include('db.php');

// Recibir los datos del formulario
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$colaboradora = $_POST['colaboradora'];
$clienta = $_POST['clienta'];
$servicio = $_POST['servicio'];
$descuento = $_POST['descuento'];
$trans = $_POST['trans'];
$efectivo = $_POST['efectivo'];
$propina = $_POST['propina'];
$total = $_POST['total'];
$id = $_POST['id'];

// Si la hora está en formato HH:MM, asegurarse de convertirla a HH:MM:SS
if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
    $hora .= ':00';  // Añadir segundos si no están presentes
}

if ($id) {
    // Actualizar los datos en la tabla
    $sql = "UPDATE citas 
            SET dia = ?, hora = ?, descuento = ?, transferencia = ?, 
                efectivo = ?, propina = ?, total = ?, idcolaboradora = ?, 
                idclienta = ?, idservicio = ? 
            WHERE idcitas = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssi", $dia, $hora, $descuento, $trans, $efectivo, $propina, $total, $colaboradora, $clienta, $servicio, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log('Error al ejecutar la consulta: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la cita.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de la cita no proporcionado.']);
}

$conn->close();
?>
