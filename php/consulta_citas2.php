<?php
header('Content-Type: application/json');

include('db.php');

$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$colaboradora = $_GET['colaboradora'];
$clientaa = $_GET['clientaa'];

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;  
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; 

// Consultar la lista de clientas
$sql = "SELECT ci.*, cl.nombre as colaboradora, c.nombre as clienta, s.nombre as servicio
        FROM citas ci
        INNER JOIN colaboradoras cl ON ci.idcolaboradora = cl.idcolaboradora
        INNER JOIN clientas c ON c.idclienta = ci.idclienta
        INNER JOIN servicios s ON s.idservicio = ci.idservicio
        WHERE dia BETWEEN '$fecha1' AND '$fecha2'";

        if($colaboradora != ''){
         $sql .= " AND cl.idcolaboradora = $colaboradora";
         }
         if ($clientaa !== '') {
            $clientaa = $conn->real_escape_string($clientaa); // Escapar valores para evitar inyecciones SQL
            $sql .= " AND c.idclienta = $clientaa ";
        }

        $sql .= " ORDER BY ci.dia ,ci.hora,c.nombre ASC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Consultar el total de registros
//$totalSql = "SELECT COUNT(*) as total FROM citas
//        WHERE dia BETWEEN '$fecha1' AND '$fecha2'";
//$totalResult = $conn->query($totalSql);
//$total = $totalResult->fetch_assoc()['total'];

$totalSql = "SELECT COUNT(*) as total FROM citas ci WHERE dia BETWEEN '$fecha1' AND '$fecha2'";
if($colaboradora != '') {
    $totalSql .= " AND idcolaboradora = $colaboradora"; // Asegúrate de usar el mismo filtro
}
if ($clientaa !== '') {
    $clientaa = $conn->real_escape_string($clientaa);
    $totalSql .= " AND ci.idclienta = $clientaa";
}
$totalResult = $conn->query($totalSql);
$total = $totalResult->fetch_assoc()['total'];


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
                   WHERE dia BETWEEN '$fecha1' AND '$fecha2'";
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

if ($result->num_rows > 0) {
    $clientas = [];

    // Obtener los datos de cada fila
    while($row = $result->fetch_assoc()) {
        $clientas[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $clientas, 'total' => $total, 'totalGlobal' => number_format($totalGlobal, 2), 'total_sin_comision' => number_format($total_sin_comision, 2)
    , 'comision_colaboradora' => number_format($comision_colaboradora, 2), 'transferencia' => number_format($transferencia, 2)
    , 'efectivo' => number_format($efectivo, 2), 'propina' => number_format($propina, 2)]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron clientas.']);
}

$conn->close();
?>
