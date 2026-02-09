<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="municipios.xls"');
header('Cache-Control: max-age=0');

include('db.php');

// Consulta para obtener los municipios
$sql = "SELECT * FROM municipios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Municipio\n";
    // Obtener los datos de cada fila
    while($row = $result->fetch_assoc()) {
        echo $row['nombre'] . "\n";
    }
} else {
    echo "No se encontraron municipios\n";
}

$conn->close();
?>
