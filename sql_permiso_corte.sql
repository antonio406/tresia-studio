-- Agregar columna corte a la tabla permisos
ALTER TABLE permisos ADD COLUMN corte TINYINT(1) NOT NULL DEFAULT 0;
