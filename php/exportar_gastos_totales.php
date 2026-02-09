<?php
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="Gastos Totales.xls"');
header('Cache-Control: max-age=0');

include('db.php');

// Obtener las fechas de los parámetros de la URL
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
// Validar que las fechas no estén vacías
if (empty($fecha1) || empty($fecha2)) {
    echo json_encode(['success' => false, 'message' => 'Las fechas son requeridas.']);
    exit();
}


// Inicia la tabla HTML
echo '<table border="1">';

// Encabezados de las columnas con estilo de color
echo '<tr>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Transferencias</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Efectivo</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Propina</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Ganancias Citas</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Gastos</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Ingresos Adicionales</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Ganancia Total</th>';
echo '</tr>';

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
        ) g";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Error en la preparación de la consulta: ' . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']));
}

// Vincular los parámetros de fecha y paginación
$stmt->bind_param('ssss', $fecha1, $fecha2, $fecha1, $fecha2);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Obtener los datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . (!empty($row['transferencia']) ? utf8_decode($row['transferencia']) : "0") . '</td>';
        echo '<td>' . (!empty($row['efectivo']) ? utf8_decode($row['efectivo']) : "0") . '</td>';
        echo '<td>' . (!empty($row['propina']) ? utf8_decode($row['propina']) : "0") . '</td>';
        echo '<td>' . (!empty($row['total_citas']) ? utf8_decode($row['total_citas']) : "0") . '</td>';
        echo '<td>' . (!empty($row['total_gastos']) ? utf8_decode($row['total_gastos']) : "0") . '</td>';
        echo '<td>' . (!empty($row['total_ingresos_adicionales']) ? utf8_decode($row['total_ingresos_adicionales']) : "0") . '</td>';
        echo '<td>' . (!empty($row['total_general']) ? utf8_decode($row['total_general']) : "0") . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="10">No se encontraron citas</td></tr>';
}

// Cierra la tabla HTML
echo '</table>';

$stmt->close();
$conn->close();
?>
