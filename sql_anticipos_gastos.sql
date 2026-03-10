-- Modificación para incluir 'anticipo' en el tipo de gasto
ALTER TABLE gastos MODIFY COLUMN tipo ENUM('ingreso', 'gasto', 'anticipo') NOT NULL;
