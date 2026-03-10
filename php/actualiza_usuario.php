<?php
header('Content-Type: application/json');
session_start();
include('db.php');
include('permisos.php');

if (!tienePermiso('usuarios', 'editar')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar usuarios.']);
    exit;
}

$user = trim($_POST['user']);

// Verificar si el usuario a actualizar es un administrador
$isUpdatingAdmin = false;
$sqlCheckAdmin = "SELECT rol FROM users WHERE user = ?";
$stmtCheckAdmin = $conn->prepare($sqlCheckAdmin);
$stmtCheckAdmin->bind_param('s', $user);
$stmtCheckAdmin->execute();
$resultCheckAdmin = $stmtCheckAdmin->get_result();
if ($row = $resultCheckAdmin->fetch_assoc()) {
    if ($row['rol'] === 'admin') {
        $isUpdatingAdmin = true;
    }
}
$stmtCheckAdmin->close();

$password = isset($_POST['password']) ? $_POST['password'] : '';
$name = $_POST['name'];
$apellidop = $_POST['apellidop'];
$apellidom = $_POST['apellidom'];
$rol = $_POST['rol'];
$permisos_json = isset($_POST['permisos']) ? $_POST['permisos'] : '[]';

// Si es admin, forzar que el rol siga siendo admin
if ($isUpdatingAdmin) {
    $rol = 'admin';
}

// Actualizar datos del usuario
if ($password !== '') {
    $sql = "UPDATE users SET password = ?, name = ?, apellidop = ?, apellidom = ?, rol = ? WHERE user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', $password, $name, $apellidop, $apellidom, $rol, $user);
} else {
    $sql = "UPDATE users SET name = ?, apellidop = ?, apellidom = ?, rol = ? WHERE user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $name, $apellidop, $apellidom, $rol, $user);
}

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

if (!$isUpdatingAdmin) {
    // Eliminar permisos anteriores y reinsertar (Solo para no-admins)
    $sqlDel = "DELETE FROM permisos WHERE user = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param('s', $user);
    $stmtDel->execute();
    $stmtDel->close();

    // Insertar nuevos permisos
    $permisos = json_decode($permisos_json, true);
    if (is_array($permisos) && count($permisos) > 0) {
        $sqlPerm = "INSERT INTO permisos (user, modulo, ver, crear, editar, eliminar, corte) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtPerm = $conn->prepare($sqlPerm);
        
        foreach ($permisos as $perm) {
            $modulo = $perm['modulo'];
            $ver = $perm['ver'] ? 1 : 0;
            $crear = $perm['crear'] ? 1 : 0;
            $editar = $perm['editar'] ? 1 : 0;
            $eliminar = $perm['eliminar'] ? 1 : 0;
            $corte = isset($perm['corte']) && $perm['corte'] ? 1 : 0;
            $stmtPerm->bind_param('ssiiiii', $user, $modulo, $ver, $crear, $editar, $eliminar, $corte);
            $stmtPerm->execute();
        }
        $stmtPerm->close();
    }
}

echo json_encode(['success' => true]);

$conn->close();
?>
