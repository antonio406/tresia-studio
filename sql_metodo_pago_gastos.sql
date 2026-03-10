-- Agregar columnas de método de pago a la tabla gastos
-- Se mantiene la columna 'monto' para los registros existentes
ALTER TABLE gastos ADD COLUMN monto_transferencia DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE gastos ADD COLUMN monto_efectivo DECIMAL(10,2) NOT NULL DEFAULT 0;

-- Permitir que monto tenga valor por defecto 0 para nuevos registros
ALTER TABLE gastos ALTER COLUMN monto SET DEFAULT 0;
