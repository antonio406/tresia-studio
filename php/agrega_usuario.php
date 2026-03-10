<?php
header('Content-Type: application/json');
session_start();
include('db.php');
include('permisos.php');

// Verificar que el usuario tenga permiso para crear usuarios
if (!tienePermiso('usuarios', 'crear')) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para crear usuarios.']);
    exit;
}

$user = trim($_POST['user']);
$password = $_POST['password'];
$name = $_POST['name'];
$apellidop = $_POST['apellidop'];
$apellidom = $_POST['apellidom'];
$rol = $_POST['rol'];
$permisos_json = isset($_POST['permisos']) ? $_POST['permisos'] : '[]';

// Validar que el usuario no exista
$sqlCheck = "SELECT user FROM users WHERE user = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param('s', $user);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe.']);
    $stmtCheck->close();
    $conn->close();
    exit;
}
$stmtCheck->close();

// Insertar el usuario
$sql = "INSERT INTO users (user, password, name, apellidop, apellidom, rol, estatus) VALUES (?, ?, ?, ?, ?, ?, TRUE)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}
$stmt->bind_param('ssssss', $user, $password, $name, $apellidop, $apellidom, $rol);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar el usuario: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Insertar permisos
$permisos = json_decode($permisos_json, true);
if (is_array($permisos) && count($permisos) > 0) {
    $sqlPerm = "INSERT INTO permisos (user, modulo, ver, crear, editar, eliminar, corte) VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE ver = VALUES(ver), crear = VALUES(crear), editar = VALUES(editar), eliminar = VALUES(eliminar), corte = VALUES(corte)";
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

echo json_encode(['success' => true]);

$conn->close();
?>
