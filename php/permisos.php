<?php
/**
 * Sistema de Permisos - Tresia Studio
 * Incluir este archivo en todas las páginas que requieran control de acceso.
 */

/**
 * Carga los permisos del usuario desde la BD y los guarda en $_SESSION['permisos']
 * Debe llamarse después de un login exitoso.
 */
function cargarPermisos($conn, $user) {
    $sql = "SELECT modulo, ver, crear, editar, eliminar, corte FROM permisos WHERE user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permisos = [];
    while ($row = $result->fetch_assoc()) {
        $permisos[$row['modulo']] = [
            'ver' => (bool)$row['ver'],
            'crear' => (bool)$row['crear'],
            'editar' => (bool)$row['editar'],
            'eliminar' => (bool)$row['eliminar'],
            'corte' => (bool)$row['corte']
        ];
    }
    $stmt->close();
    
    $_SESSION['permisos'] = $permisos;
    return $permisos;
}

/**
 * Verifica si el usuario actual tiene permiso para una acción en un módulo.
 * @param string $modulo - Nombre del módulo (citas, clientas, etc.)
 * @param string $accion - Acción (ver, crear, editar, eliminar)
 * @return bool
 */
function tienePermiso($modulo, $accion) {
    if (!isset($_SESSION['permisos'])) {
        return false;
    }
    if (!isset($_SESSION['permisos'][$modulo])) {
        return false;
    }
    return isset($_SESSION['permisos'][$modulo][$accion]) && $_SESSION['permisos'][$modulo][$accion];
}

/**
 * Verifica si el usuario tiene al menos un permiso para el módulo especificado.
 * @param string $modulo
 * @return bool
 */
function tieneCualquierPermiso($modulo) {
    if (!isset($_SESSION['permisos']) || !isset($_SESSION['permisos'][$modulo])) {
        return false;
    }
    $p = $_SESSION['permisos'][$modulo];
    return ($p['ver'] || $p['crear'] || $p['editar'] || $p['eliminar'] || (isset($p['corte']) && $p['corte']));
}

/**
 * Verifica permiso y redirige a alert.php si no tiene acceso.
 * Usar al inicio de páginas protegidas.
 */
function verificarAcceso($modulo, $accion = 'ver') {
    if (!tienePermiso($modulo, $accion)) {
        header('Location: alert.php');
        exit();
    }
}

/**
 * Retorna los permisos del usuario como JSON para usar en JavaScript.
 */
function permisosJSON() {
    return json_encode(isset($_SESSION['permisos']) ? $_SESSION['permisos'] : []);
}

/**
 * Verifica si el usuario actual es administrador.
 */
function esAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}
?>
