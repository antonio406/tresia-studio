<?php
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="citas.xls"');
header('Cache-Control: max-age=0');

include('db.php');

// Obtener las fechas de los parámetros de la URL
$dateOne = isset($_GET['dateOne']) ? $_GET['dateOne'] : '';
$dateTwo = isset($_GET['dateTwo']) ? $_GET['dateTwo'] : '';
$colaboradora = isset($_GET['colaboradora']) ? $_GET['colaboradora'] : '';
$clientaa = isset($_GET['clientaa']) ? $_GET['clientaa'] : '';

// Inicia la tabla HTML
echo '<table border="1">';

// Encabezados de las columnas con estilo de color
echo '<tr>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Dia</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Hora</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Colaboradora</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Clienta</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Servicio</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Descuento</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Transferencia</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Efectivo</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">P. Efec.</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">P. Trans.</th>';
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Total</th>';
echo '</tr>';

// Consulta para obtener las citas filtradas por fecha
$sql = "SELECT ci.*, cl.nombre AS colaboradora, c.nombre AS clienta, s.nombre AS servicio
        FROM citas ci
        INNER JOIN colaboradoras cl ON ci.idcolaboradora = cl.idcolaboradora
        INNER JOIN clientas c ON c.idclienta = ci.idclienta
        INNER JOIN servicios s ON s.idservicio = ci.idservicio
        WHERE ci.dia BETWEEN ? AND ?";
        
        if($colaboradora != ''){
            $sql .= " AND cl.idcolaboradora = $colaboradora";
        }
        if ($clientaa !== '') {
            $clientaa = $conn->real_escape_string($clientaa); 
            $sql .= " AND c.nombre LIKE '%$clientaa%'";
        }

// Consultar el total global
$totalGlobalSql = "SELECT
                    SUM(total) AS total_global,
                    cl.comisión,
                    SUM(ci.total) - SUM((ci.total) * cl.comisión / 100) AS comision_jefa,
                    SUM((ci.total) * cl.comisión / 100) + SUM(ci.propina_efectivo + ci.propina_transferencia) AS comision_colaboradora,
                    SUM(ci.transferencia) AS transferencia,
                    SUM(efectivo) AS efectivo,
                    SUM(ci.propina_efectivo + ci.propina_transferencia) AS propina,
                    SUM(ci.propina_efectivo) AS propina_efectivo,
                    SUM(ci.propina_transferencia) AS propina_transferencia
                   FROM citas ci
                   INNER JOIN colaboradoras cl ON ci.idcolaboradora = cl.idcolaboradora
                   INNER JOIN clientas c ON c.idclienta = ci.idclienta
                   INNER JOIN servicios s ON s.idservicio = ci.idservicio
                   WHERE dia BETWEEN '$dateOne' AND '$dateTwo'";

                    if($colaboradora != ''){
                        $totalGlobalSql .= " AND cl.idcolaboradora = $colaboradora";
                    }
                    if ($clientaa !== '') {
                        $clientaa = $conn->real_escape_string($clientaa); 
                        $totalGlobalSql .= " AND c.nombre LIKE '%$clientaa%'";
                    }

                    $totalGlobalResult = $conn->query($totalGlobalSql);
                    if ($totalGlobalResult) {
                        $row = $totalGlobalResult->fetch_assoc();
                        $totalGlobal = $row['total_global'];
                        $total_sin_comision = $row['comision_jefa'];
                        $comision_colaboradora = $row['comision_colaboradora'];
                        $transferencia = $row['transferencia'];
                        $efectivo = $row['efectivo'];
                        $propina = $row['propina'];
                        $propina_efectivo = $row['propina_efectivo'];
                        $propina_transferencia = $row['propina_transferencia'];
                    } else {
                        $totalGlobal = 0;
                        $total_sin_comision = 0;
                        $propina = 0;
                        $propina_efectivo = 0;
                        $propina_transferencia = 0;
                    }

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $dateOne, $dateTwo);
$stmt->execute();
$result = $stmt->get_result();
$totalGeneral = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . (!empty($row['dia']) ? ($row['dia']) : "0") . '</td>';
        echo '<td>' . (!empty($row['hora']) ? ($row['hora']) : "0") . '</td>';
        echo '<td>' . (!empty($row['colaboradora']) ? ($row['colaboradora']) : "0") . '</td>';
        echo '<td>' . (!empty($row['clienta']) ? ($row['clienta']) : "0") . '</td>';
        echo '<td>' . (!empty($row['servicio']) ? ($row['servicio']) : "0") . '</td>';
        echo '<td>' . (!empty($row['descuento']) ? ($row['descuento']) : "0") . '</td>';
        echo '<td>' . (!empty($row['transferencia']) ? ($row['transferencia']) : "0") . '</td>';
        echo '<td>' . (!empty($row['efectivo']) ? ($row['efectivo']) : "0") . '</td>';
        echo '<td>' . (!empty($row['propina_efectivo']) ? ($row['propina_efectivo']) : "0") . '</td>';
        echo '<td>' . (!empty($row['propina_transferencia']) ? ($row['propina_transferencia']) : "0") . '</td>';
        echo '<td>' . (!empty($row['total']) ? (str_replace(["\n", "\r"], " ", $row['total'])) : "0") . '</td>';
        echo '</tr>';
        
        $totalGeneral += $row['total'];
    }

    echo '<tr>';
    echo '<td colspan="10" style="text-align:right; font-weight:bold;">Transferencias Bancarias:</td>';
    echo '<td style="font-weight:bold;">' . number_format($transferencia, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="10" style="text-align:right; font-weight:bold;">Efectivo en Caja:</td>';
    echo '<td style="font-weight:bold;">' . number_format($efectivo, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="10" style="text-align:right; font-weight:bold;">Total Propinas (Efe: ' . number_format($propina_efectivo,2) . ' | Trans: ' . number_format($propina_transferencia,2) . '):</td>';
    echo '<td style="font-weight:bold;">' . number_format($propina, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="10" style="text-align:right; font-weight:bold;">Comision Jefa:</td>';
    echo '<td style="font-weight:bold;">' . number_format($total_sin_comision, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="10" style="text-align:right; font-weight:bold;">Comision Colaboradora:</td>';
    echo '<td style="font-weight:bold;">' . number_format($comision_colaboradora, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="10" style="text-align:right; font-weight:bold;">Total General:</td>';
    echo '<td style="font-weight:bold;">' . number_format($totalGeneral, 2) . '</td>';
    echo '</tr>';
} else {
    echo '<tr><td colspan="11">No se encontraron citas</td></tr>';
}

echo '</table>';

$stmt->close();
$conn->close();
?>
