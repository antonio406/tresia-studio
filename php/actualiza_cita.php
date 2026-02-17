<?php
header('Content-Type: application/json');

include('db.php');

$id = $_POST['id'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$idcolaboradora = $_POST['colaboradora'];
$idclienta = $_POST['clienta'];
$idservicio = $_POST['servicio'];
$descuento = $_POST['descuento'] ?: 0;
$pago_trans = $_POST['trans'] ?: 0;
$efectivo = $_POST['efectivo'] ?: 0;
$propina_efectivo = $_POST['propina_efectivo'] ?: 0;
$propina_trans = $_POST['propina_trans'] ?: 0;
$total = $_POST['total'];

// Denominaciones Pago Recibido
$b1000 = $_POST['b1000'] ?: 0;
$b500 = $_POST['b500'] ?: 0;
$b200 = $_POST['b200'] ?: 0;
$b100 = $_POST['b100'] ?: 0;
$b50 = $_POST['b50'] ?: 0;
$b20 = $_POST['b20'] ?: 0;
$m10 = $_POST['m10'] ?: 0;
$m5 = $_POST['m5'] ?: 0;
$m2 = $_POST['m2'] ?: 0;
$m1 = $_POST['m1'] ?: 0;
$m050 = $_POST['m050'] ?: 0;

// Denominaciones Cambio Dado
$c1000 = $_POST['c1000'] ?: 0;
$c500 = $_POST['c500'] ?: 0;
$c200 = $_POST['c200'] ?: 0;
$c100 = $_POST['c100'] ?: 0;
$c50 = $_POST['c50'] ?: 0;
$c20 = $_POST['c20'] ?: 0;
$cm10 = $_POST['cm10'] ?: 0;
$cm5 = $_POST['cm5'] ?: 0;
$cm2 = $_POST['cm2'] ?: 0;
$cm1 = $_POST['cm1'] ?: 0;
$cm050 = $_POST['cm050'] ?: 0;

// 1. Actualizar tabla citas
$sql_update = "UPDATE citas SET dia=?, hora=?, idcolaboradora=?, idclienta=?, idservicio=?, descuento=?, transferencia=?, efectivo=?, propina_efectivo=?, propina_transferencia=?, total=? 
               WHERE idcitas=?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("ssiiiddddddi", $dia, $hora, $idcolaboradora, $idclienta, $idservicio, $descuento, $pago_trans, $efectivo, $propina_efectivo, $propina_trans, $total, $id);

if ($stmt->execute()) {
    // 2. Manejar desglose en efectivo_detalle (INSERT o UPDATE)
    $check = $conn->query("SELECT idcitas FROM efectivo_detalle WHERE idcitas = $id");
    
    if ($check->num_rows > 0) {
        $sql_det = "UPDATE efectivo_detalle SET 
                        b1000=?, b500=?, b200=?, b100=?, b50=?, b20=?, m10=?, m5=?, m2=?, m1=?, m050=?,
                        c1000=?, c500=?, c200=?, c100=?, c50=?, c20=?, cm10=?, cm5=?, cm2=?, cm1=?, cm050=?
                    WHERE idcitas=?";
    } else {
        $sql_det = "INSERT INTO efectivo_detalle (
                        b1000, b500, b200, b100, b50, b20, m10, m5, m2, m1, m050,
                        c1000, c500, c200, c100, c50, c20, cm10, cm5, cm2, cm1, cm050,
                        idcitas
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }
    
    $stmt_det = $conn->prepare($sql_det);
    $stmt_det->bind_param("iiiiiiiiiiiiiiiiiiiiiii", 
        $b1000, $b500, $b200, $b100, $b50, $b20, $m10, $m5, $m2, $m1, $m050,
        $c1000, $c500, $c200, $c100, $c50, $c20, $cm10, $cm5, $cm2, $cm1, $cm050,
        $id
    );
    $stmt_det->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la cita: ' . $conn->error]);
}

$conn->close();
?>
