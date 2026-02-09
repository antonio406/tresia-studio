<?php
header('Content-Type: application/json');
include('db.php');
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;  
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; 
// Consultar la lista de clientas
$sql = "SELECT g.*,TIME(fecha_registro) AS hora  FROM gastos g
        WHERE fecha BETWEEN '$fecha1' AND '$fecha2'
        ORDER BY tipo asc
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$totalSql = "SELECT COUNT(*) as total FROM gastos WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
$totalResult = $conn->query($totalSql);
$total = $totalResult->fetch_assoc()['total'];

$totalgastos = "SELECT COALESCE(SUM(monto), 0) as ingreso FROM gastos WHERE fecha BETWEEN '$fecha1' AND '$fecha2' and tipo = 'ingreso';";
$totalResult2 = $conn->query($totalgastos);
$ingreso = $totalResult2->fetch_assoc()['ingreso'];

$totalgasto2 = "SELECT COALESCE(SUM(monto), 0) AS gasto FROM gastos WHERE fecha BETWEEN '$fecha1' AND '$fecha2' and tipo = 'gasto';";
$totalResult3 = $conn->query($totalgasto2);
$gasto = $totalResult3->fetch_assoc()['gasto'];

if ($result->num_rows > 0) {
    $clientas = [];
    // Obtener los datos de cada fila
    while($row = $result->fetch_assoc()) {
        $clientas[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $clientas, 'total' => $total, 'ingreso' => $ingreso, 'gasto' => $gasto]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron clientas.']);
}

$conn->close();
?>
