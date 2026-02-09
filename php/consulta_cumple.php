
<?php
    header('Content-Type: application/json');
    
    include('db.php');

    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;  
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; 
    $dateOne = $_GET['dateOne']; // "MM-DD"
    $dateTwo = $_GET['dateTwo']; // "MM-DD"
    
    // Consultar la lista de clientas
    $sql = "SELECT nombre,fecha_nacimiento,detalle,idclienta
            FROM clientas 
            WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') BETWEEN '$dateOne' AND '$dateTwo'
            ORDER BY DATE_FORMAT(fecha_nacimiento, '%m-%d') ASC
            LIMIT $limit OFFSET $offset;";
    $result = $conn->query($sql);
    
    $totalSql = "SELECT COUNT(*) as total FROM clientas";
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
    