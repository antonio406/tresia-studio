<?php
session_start(); 
header('Content-Type: application/json');

include('db.php');
include('permisos.php');

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

    // Verificar que el usuario esté activo
    if (isset($user['estatus']) && $user['estatus'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Usuario desactivado. Contacte al administrador.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Comparar la contraseña ingresada con la almacenada
    if ($password === $user['password']) {
        // Almacenar información del usuario en la sesión
        $_SESSION['usuario'] = $user['user'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['rol'] = isset($user['rol']) ? $user['rol'] : 'usuario';
        
        // Cargar permisos del usuario en la sesión
        cargarPermisos($conn, $user['user']);
        
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
