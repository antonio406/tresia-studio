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

// Verificar si ya existe una cita con los mismos parámetros
$sql_check = "SELECT COUNT(*) AS count
              FROM citas
              WHERE idcolaboradora = ? 
                AND hora = ? 
                AND DAY(dia) = ? 
                AND MONTH(dia) = ?
                AND YEAR(dia) = ?";

$stmt_check = $conn->prepare($sql_check);
if (!$stmt_check) {
    error_log('Error en la preparación de la consulta de verificación: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta de verificación.']));
}

$stmt_check->bind_param('ssiii', $colaboradora, $hora, date('j', strtotime($dia)), date('n', strtotime($dia)), date('Y', strtotime($dia)));

$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row = $result_check->fetch_assoc();

if ($row['count'] > 0) {
    // Ya existe una cita con los mismos parámetros
    echo json_encode(['success' => false, 'message' => 'Ya existe una cita en esa fecha y hora para esta colaboradora.']);
} else {
    // Insertar los datos en la tabla
    $sql_insert = "INSERT INTO citas (dia, hora, descuento, transferencia, efectivo, propina, total, idcolaboradora, idclienta, idservicio) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) {
        error_log('Error en la preparación de la consulta de inserción: ' . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta de inserción.']));
    }

    $stmt_insert->bind_param('ssssssssss', $dia, $hora, $descuento, $trans, $efectivo, $propina, $total, $colaboradora, $clienta, $servicio);

    if ($stmt_insert->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log('Error al ejecutar la consulta de inserción: ' . $stmt_insert->error);
        echo json_encode(['success' => false, 'message' => 'Error al agregar la cita.']);
    }

    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();
?>
