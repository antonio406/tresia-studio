<?php
header('Content-Type: application/json');
session_start();
include('db.php');
include('permisos.php');

if (!tienePermiso('usuarios', 'ver')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para ver datos de usuarios.']);
    exit;
}

$user = isset($_GET['user']) ? trim($_GET['user']) : '';

if ($user === '') {
    echo json_encode(['success' => false, 'message' => 'Usuario no especificado.']);
    exit;
}

// Obtener datos del usuario (incluyendo password para edición)
$sql = "SELECT user, password, name, apellidop, apellidom, rol, estatus FROM users WHERE user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    $stmt->close();
    $conn->close();
    exit;
}

$userData = $result->fetch_assoc();
$stmt->close();

// Obtener permisos del usuario
$sqlPerm = "SELECT modulo, ver, crear, editar, eliminar, corte FROM permisos WHERE user = ?";
$stmtPerm = $conn->prepare($sqlPerm);
$stmtPerm->bind_param('s', $user);
$stmtPerm->execute();
$resultPerm = $stmtPerm->get_result();

$permisos = [];
while ($row = $resultPerm->fetch_assoc()) {
    $permisos[] = [
        'modulo' => $row['modulo'],
        'ver' => (bool)$row['ver'],
        'crear' => (bool)$row['crear'],
        'editar' => (bool)$row['editar'],
        'eliminar' => (bool)$row['eliminar'],
        'corte' => (bool)$row['corte']
    ];
}
$stmtPerm->close();

$userData['permisos'] = $permisos;

echo json_encode(['success' => true, 'data' => $userData]);

$conn->close();
?>
