<?php
header('Content-Type: application/json');
include('db.php');

$sqlCitas = "SELECT YEAR(dia) as year, MONTH(dia) as month, DAY(dia) as day, hora, c.nombre,s.nombre as servicio ,cli.nombre as clienta
             FROM citas ci
             INNER JOIN colaboradoras c ON ci.idcolaboradora = c.idcolaboradora
             INNER JOIN servicios s ON s.idservicio = ci.idservicio
             INNER JOIN clientas cli ON cli.idclienta = ci.idclienta";
$resultCitas = $conn->query($sqlCitas);

$citas = array();
if ($resultCitas->num_rows > 0) {
    while($row = $resultCitas->fetch_assoc()) {
        $citas[] = $row;
    }
}

$sqlColaboradoras = "SELECT nombre FROM colaboradoras where estatus = 1";
$resultColaboradoras = $conn->query($sqlColaboradoras);

$colaboradoras = array();
if ($resultColaboradoras->num_rows > 0) {
    while($row = $resultColaboradoras->fetch_assoc()) {
        $colaboradoras[] = $row['nombre'];
    }
}

$conn->close();

$response = array(
    'citas' => $citas,
    'colaboradoras' => $colaboradoras
);

echo json_encode($response);
?>
