<?php
header('Content-Type: application/json');
session_start();
include('db.php');
include('permisos.php');

if (!tienePermiso('usuarios', 'editar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para esta acción.']);
    exit;
}

$user = $_POST['user'];
$estatus = $_POST['estatus'];

// Verificar si el usuario es un administrador
$sqlCheckAdmin = "SELECT rol FROM users WHERE user = ?";
$stmtCheckAdmin = $conn->prepare($sqlCheckAdmin);
$stmtCheckAdmin->bind_param('s', $user);
$stmtCheckAdmin->execute();
$resultCheckAdmin = $stmtCheckAdmin->get_result();
if ($row = $resultCheckAdmin->fetch_assoc()) {
    if ($row['rol'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Los usuarios administradores son intocables y su estatus no puede ser modificado.']);
        $stmtCheckAdmin->close();
        $conn->close();
        exit;
    }
}
$stmtCheckAdmin->close();

// No permitir desactivar al propio usuario
if ($user === $_SESSION['usuario'] && $estatus == 0) {
    echo json_encode(['success' => false, 'message' => 'No puede desactivar su propio usuario.']);
    exit;
}

$sql = "UPDATE users SET estatus = ? WHERE user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $estatus, $user);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estatus: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
