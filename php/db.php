<?php
// Conexión a la base de datos
$servername = 'localhost';
$dbname = 'tresia_studio';
$username = 'root';
$password = '12345678';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log('Conexión a la base de datos fallida: ' . $conn->connect_error);
    die(json_encode(['success' => false, 'message' => 'Conexión a la base de datos fallida']));
}
?>