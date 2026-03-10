-- =====================================================
-- SQL para Gestor de Usuarios - Tresia Studio
-- Ejecutar este script en la base de datos tresia_studio
-- =====================================================

USE tresia_studio;

-- 1. Agregar columnas a la tabla users
ALTER TABLE users ADD COLUMN rol ENUM('admin','usuario') NOT NULL DEFAULT 'usuario';
ALTER TABLE users ADD COLUMN estatus BOOLEAN NOT NULL DEFAULT TRUE;

-- 2. Actualizar usuario existente como admin
UPDATE users SET rol = 'admin', estatus = TRUE WHERE user = 'admin';

-- 3. Crear tabla de permisos
CREATE TABLE IF NOT EXISTS permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user VARCHAR(10) NOT NULL,
    modulo ENUM('citas','clientas','colaboradoras','servicios',
                'municipios','gastos','inventario','usuarios') NOT NULL,
    ver BOOLEAN DEFAULT FALSE,
    crear BOOLEAN DEFAULT FALSE,
    editar BOOLEAN DEFAULT FALSE,
    eliminar BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user) REFERENCES users(user) ON DELETE CASCADE,
    UNIQUE KEY unique_user_modulo (user, modulo)
);

-- 4. Permisos completos para usuario tresia (admin)
INSERT INTO permisos (user, modulo, ver, crear, editar, eliminar) VALUES
('admin', 'citas', TRUE, TRUE, TRUE, TRUE),
('admin', 'clientas', TRUE, TRUE, TRUE, TRUE),
('admin', 'colaboradoras', TRUE, TRUE, TRUE, TRUE),
('admin', 'servicios', TRUE, TRUE, TRUE, TRUE),
('admin', 'municipios', TRUE, TRUE, TRUE, TRUE),
('admin', 'gastos', TRUE, TRUE, TRUE, TRUE),
('admin', 'inventario', TRUE, TRUE, TRUE, TRUE),
('admin', 'usuarios', TRUE, TRUE, TRUE, TRUE);
