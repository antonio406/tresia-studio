<?php
header('Content-Type: application/json');
session_start();
include('db.php');
include('permisos.php');

if (!tienePermiso('usuarios', 'eliminar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar usuarios.']);
    exit;
}

$user = $_POST['id'];

// Verificar si el usuario a eliminar es un administrador
$sqlCheckAdmin = "SELECT rol FROM users WHERE user = ?";
$stmtCheckAdmin = $conn->prepare($sqlCheckAdmin);
$stmtCheckAdmin->bind_param('s', $user);
$stmtCheckAdmin->execute();
$resultCheckAdmin = $stmtCheckAdmin->get_result();
if ($row = $resultCheckAdmin->fetch_assoc()) {
    if ($row['rol'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Los usuarios administradores son intocables y no pueden ser eliminados.']);
        $stmtCheckAdmin->close();
        $conn->close();
        exit;
    }
}
$stmtCheckAdmin->close();

// No permitir eliminar al propio usuario logueado
if ($user === $_SESSION['usuario']) {
    echo json_encode(['success' => false, 'message' => 'No puede eliminar su propio usuario.']);
    exit;
}

// Permisos se eliminan automáticamente por CASCADE
$sql = "DELETE FROM users WHERE user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
