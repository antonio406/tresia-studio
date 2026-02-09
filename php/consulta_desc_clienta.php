<?php
header('Content-Type: application/json');

include('db.php');

$id = $_GET['id'];


// Consultar la lista de clientas
$sql = "SELECT c.*,c.detalle as descump
        FROM clientas c
        INNER JOIN municipios m on (m.idmunicipio = c.idmunicipio)
        WHERE c.idclienta = $id";
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
