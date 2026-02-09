<?php
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="citas.xls"');
header('Cache-Control: max-age=0');

include('db.php');

// Obtener las fechas de los parámetros de la URL
$dateOne = isset($_GET['dateOne']) ? $_GET['dateOne'] : '';
$dateTwo = isset($_GET['dateTwo']) ? $_GET['dateTwo'] : '';
$colaboradora = isset($_GET['colaboradora']) ? $_GET['colaboradora'] : '';
$clientaa = $_GET['clientaa'];

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
echo '<th style="background-color: #8e1ce8; color: #FFFFFF;">Propina</th>';
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
            $sql .= " and cl.idcolaboradora = $colaboradora";
            }
        if ($clientaa !== '') {
                $clientaa = $conn->real_escape_string($clientaa); // Escapar valores para evitar inyecciones SQL
                $sql .= " AND c.nombre LIKE '%$clientaa%'";
        }
/*---------------------------------------------------------------------------------*/
// Consultar el total global
$totalGlobalSql = "SELECT
                    SUM(total) AS total_global,
                    cl.comisión,
                    SUM(ci.total) - SUM((ci.total) * cl.comisión / 100) AS comision_jefa,
                    SUM((ci.total) * cl.comisión / 100) + SUM(ci.propina)  AS comision_colaboradora,
                    SUM(ci.transferencia) AS transferencia,
                    SUM(efectivo) AS efectivo,
                    SUM(ci.propina) AS propina
                   FROM citas ci
                   INNER JOIN colaboradoras cl ON ci.idcolaboradora = cl.idcolaboradora
                   INNER JOIN clientas c ON c.idclienta = ci.idclienta
                   INNER JOIN servicios s ON s.idservicio = ci.idservicio
                   WHERE dia BETWEEN '$dateOne' AND '$dateTwo'";
                    if($colaboradora != ''){
                        $totalGlobalSql .= " and cl.idcolaboradora = $colaboradora";
                    }
                    if ($clientaa !== '') {
                        $clientaa = $conn->real_escape_string($clientaa); // Escapar valores para evitar inyecciones SQL
                        $sql .= " AND c.nombre LIKE '%$clientaa%'";
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
                    } else {
                        $totalGlobal = 0;
                        $total_sin_comision = 0;
                    }
/*---------------------------------------------------------------------------------*/
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $dateOne, $dateTwo);
$stmt->execute();
$result = $stmt->get_result();
$totalGeneral = 0;

if ($result->num_rows > 0) {
    // Obtener los datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . (!empty($row['dia']) ? utf8_decode($row['dia']) : "0") . '</td>';
        echo '<td>' . (!empty($row['hora']) ? utf8_decode($row['hora']) : "0") . '</td>';
        echo '<td>' . (!empty($row['colaboradora']) ? utf8_decode($row['colaboradora']) : "0") . '</td>';
        echo '<td>' . (!empty($row['clienta']) ? utf8_decode($row['clienta']) : "0") . '</td>';
        echo '<td>' . (!empty($row['servicio']) ? utf8_decode($row['servicio']) : "0") . '</td>';
        echo '<td>' . (!empty($row['descuento']) ? utf8_decode($row['descuento']) : "0") . '</td>';
        echo '<td>' . (!empty($row['transferencia']) ? utf8_decode($row['transferencia']) : "0") . '</td>';
        echo '<td>' . (!empty($row['efectivo']) ? utf8_decode($row['efectivo']) : "0") . '</td>';
        echo '<td>' . (!empty($row['propina']) ? utf8_decode($row['propina']) : "0") . '</td>';
        echo '<td>' . (!empty($row['total']) ? utf8_decode(str_replace(["\n", "\r"], " ", $row['total'])) : "0") . '</td>';
        echo '</tr>';
        
        // Sumar al total general
        $totalGeneral += $row['total'];
    }

    // Mostrar la suma total al final
    echo '<tr>';
    echo '<td colspan="9" style="text-align:right; font-weight:bold;">Transferencia:</td>';
    echo '<td style="font-weight:bold;">' . number_format($transferencia, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="9" style="text-align:right; font-weight:bold;">Efectivo:</td>';
    echo '<td style="font-weight:bold;">' . number_format($efectivo, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="9" style="text-align:right; font-weight:bold;">Propinas:</td>';
    echo '<td style="font-weight:bold;">' . number_format($propina, 2) . '</td>';
    echo '</tr>';
    echo '<td colspan="9" style="text-align:right; font-weight:bold;">Comision Jefa:</td>';
    echo '<td style="font-weight:bold;">' . number_format($total_sin_comision, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="9" style="text-align:right; font-weight:bold;">Comision Colaboradora:</td>';
    echo '<td style="font-weight:bold;">' . number_format($comision_colaboradora, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="9" style="text-align:right; font-weight:bold;">Total General:</td>';
    echo '<td style="font-weight:bold;">' . number_format($totalGeneral, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
} else {
    echo '<tr><td colspan="10">No se encontraron citas</td></tr>';
}

// Cierra la tabla HTML
echo '</table>';

$stmt->close();
$conn->close();
?>
