<?php
header('Content-Type: application/json');

include('db.php');

$id = $_GET['id'];

// Consultar la cita con desglose de efectivo (Recibido + Cambio) y propinas desglosadas
$sql = "SELECT ci.*, cl.nombre as colaboradora, c.nombre as clienta, s.nombre as servicio,
               ed.b1000, ed.b500, ed.b200, ed.b100, ed.b50, ed.b20,
               ed.m10, ed.m5, ed.m2, ed.m1, ed.m050,
               ed.c1000, ed.c500, ed.c200, ed.c100, ed.c50, ed.c20,
               ed.cm10, ed.cm5, ed.cm2, ed.cm1, ed.cm050
        FROM citas ci
        INNER JOIN colaboradoras cl on (ci.idcolaboradora = cl.idcolaboradora)
        INNER JOIN clientas c on (c.idclienta = ci.idclienta)
        INNER JOIN servicios s on (s.idservicio = ci.idservicio)
        LEFT JOIN efectivo_detalle ed on (ed.idcitas = ci.idcitas)
        WHERE ci.idcitas = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $clientas = [];
    while($row = $result->fetch_assoc()) {
        $clientas[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $clientas]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron Citas.']);
}

$stmt->close();
$conn->close();
?>
