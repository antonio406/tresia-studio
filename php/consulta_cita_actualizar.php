<?php
header('Content-Type: application/json');

include('db.php');

$id = $_GET['id'];


// Consultar la lista de clientas
$sql = "SELECT ci.*, cl.nombre as colaboradora, c.nombre as clienta, s.nombre as servicio
        FROM citas ci
        INNER JOIN colaboradoras cl on (ci.idcolaboradora = cl.idcolaboradora)
        INNER JOIN clientas c on (c.idclienta = ci.idclienta)
        INNER JOIN servicios s on (s.idservicio = ci.idservicio)
        WHERE ci.idcitas = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $clientas = [];

    // Obtener los datos de cada fila
    while($row = $result->fetch_assoc()) {
        $clientas[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $clientas]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron Citas.']);
}

$conn->close();
?>
