<?php
header('Content-Type: application/json');

include('db.php');

// Obtener las fechas desde los parámetros GET
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Validar que las fechas no estén vacías
if (empty($fecha1) || empty($fecha2)) {
    echo json_encode(['success' => false, 'message' => 'Las fechas son requeridas.']);
    exit();
}

// Consultar la lista de clientas y otros datos
$sql = "SELECT
  	    c.total_citas,
            g.total_gastos,
            c.efectivo,
            c.transferencia,
            c.propina,
            g.total_ingresos_adicionales,
            (c.total_citas + g.total_ingresos_adicionales - g.total_gastos) AS total_general
        FROM (
            SELECT COALESCE(SUM(total), 0) AS total_citas, COALESCE(SUM(transferencia), 0) as transferencia ,COALESCE(SUM(efectivo), 0)  as efectivo ,COALESCE(SUM(propina), 0) as propina
            FROM citas
            WHERE dia BETWEEN ? AND ?
        ) c
        CROSS JOIN (
            SELECT
                COALESCE(SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END), 0) AS total_gastos,
                COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END), 0) AS total_ingresos_adicionales
            FROM gastos
            WHERE fecha BETWEEN ? AND ?
        ) g
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

// Vincular los parámetros de fecha y paginación
$stmt->bind_param('ssssii', $fecha1, $fecha2, $fecha1, $fecha2, $limit, $offset);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Obtener el número total de resultados para la paginación
    $stmt_total = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM (
            SELECT 1
            FROM citas
            WHERE dia BETWEEN ? AND ?
            UNION ALL
            SELECT 1
            FROM gastos
            WHERE fecha BETWEEN ? AND ?
        ) AS combined
    ");
    $stmt_total->bind_param('ssss', $fecha1, $fecha2, $fecha1, $fecha2);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();
    $total_row = $total_result->fetch_assoc();
    $total = $total_row['total'];

    echo json_encode(['success' => true, 'data' => $data, 'total' => $total]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron datos.']);
}

$stmt->close();
$conn->close();
?>
