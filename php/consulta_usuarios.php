<?php
header('Content-Type: application/json');
session_start();
include('db.php');
include('permisos.php');

if (!tienePermiso('usuarios', 'ver')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para ver la lista de usuarios.']);
    exit;
}

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Contar total
if ($buscar !== '') {
    $sqlTotal = "SELECT COUNT(*) as total FROM users WHERE name LIKE ? OR user LIKE ?";
    $stmtTotal = $conn->prepare($sqlTotal);
    $buscarParam = '%' . $buscar . '%';
    $stmtTotal->bind_param('ss', $buscarParam, $buscarParam);
} else {
    $sqlTotal = "SELECT COUNT(*) as total FROM users";
    $stmtTotal = $conn->prepare($sqlTotal);
}
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$total = $resultTotal->fetch_assoc()['total'];
$stmtTotal->close();

// Consultar usuarios (sin password)
if ($buscar !== '') {
    $sql = "SELECT user, name, apellidop, apellidom, rol, estatus FROM users WHERE name LIKE ? OR user LIKE ? ORDER BY name LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssii', $buscarParam, $buscarParam, $limit, $offset);
} else {
    $sql = "SELECT user, name, apellidop, apellidom, rol, estatus FROM users ORDER BY name LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

echo json_encode(['success' => true, 'data' => $usuarios, 'total' => $total]);

$stmt->close();
$conn->close();
?>
