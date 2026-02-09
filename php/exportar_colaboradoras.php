<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="colaboradoras.xls"');
header('Cache-Control: max-age=0');

include('db.php');

// Inicia la tabla HTML
echo '<table border="1">';

// Encabezados de las columnas con estilo de color
echo '<tr style="background-color: #4CAF50; color: #FFFFFF;">';
echo '<th>Nombre</th>';
echo '<th>Edad</th>';
echo '<th>Experta en</th>';
echo '<th>Horario Laboral</th>';
echo '<th>Dias Laborales</th>';
echo '<th>Comision</th>';
echo '<th>Antiguedad</th>';
echo '<th>Uniforme Proporcionado</th>';
echo '<th>Material para Trabajo</th>';
echo '<th>Observaciones</th>';
echo '</tr>';

// Consulta para obtener las colaboradoras
$sql = "SELECT * FROM colaboradoras";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener los datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . (!empty($row['nombre']) ? $row['nombre'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['edad']) ? $row['edad'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['expertaen']) ? $row['expertaen'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['horario_laboral']) ? $row['horario_laboral'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['diaslaborales']) ? $row['diaslaborales'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['comisión']) ? $row['comisión'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['antigüedad']) ? $row['antigüedad'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['uniforme_proporcionado']) ? $row['uniforme_proporcionado'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['material_para_trabajo']) ? $row['material_para_trabajo'] : "N/A") . '</td>';
        echo '<td>' . (!empty($row['observaciones']) ? str_replace(["\n", "\r"], " ", $row['observaciones']) : "N/A") . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="10">No se encontraron colaboradoras</td></tr>';
}

// Cierra la tabla HTML
echo '</table>';

$conn->close();
?>
