<?php
header('Content-Type: application/json');

include('db.php');

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;  
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; 
$clienta = isset($_GET['clienta']) ? intval($_GET['clienta']) : 0; 

// Consultar la lista de clientas
$sql = "SELECT c.*, m.nombre as municipio FROM clientas c
        INNER JOIN municipios m on (m.idmunicipio = c.idmunicipio)";

        if($clienta != ''){
            $sql .= " where c.idclienta = $clienta";
        }       
        $sql .= " order by c.nombre asc LIMIT $limit OFFSET $offset";

       /* WHERE dia BETWEEN '$fecha1' AND '$fecha2'";*/
$result = $conn->query($sql);

$totalSql = "SELECT COUNT(*) as total FROM clientas c ";
if($clienta != ''){
    $sql .= " where c.idclienta = $clienta";
}   
$totalResult = $conn->query($totalSql);
$total = $totalResult->fetch_assoc()['total'];

if ($result->num_rows > 0) {
    $clientas = [];

    // Obtener los datos de cada fila
    while($row = $result->fetch_assoc()) {
        $clientas[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $clientas, 'total' => $total]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron clientas.']);
}

$conn->close();
?>
