<?php
session_start(); 
header('Content-Type: application/json');

include('db.php');

$username = $_POST['username'];
$password = $_POST['password'];

// Obtener el usuario de la base de datos
$sql = "SELECT * FROM users WHERE user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Comparar la contraseña ingresada con la almacenada
    if ($password === $user['password']) {
        // Almacenar información del usuario en la sesión
        $_SESSION['usuario'] = $user['user'];
        $_SESSION['name'] = $user['name']; // Puedes almacenar más datos si lo deseas
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$stmt->close();
$conn->close();
?>
