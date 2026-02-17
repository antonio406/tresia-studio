-- Script para crear la tabla de desglose de efectivo por denominaciones
-- Ejecutar en la base de datos tresia_studio

USE tresia_studio;

CREATE TABLE IF NOT EXISTS efectivo_detalle (
    iddetalle INT PRIMARY KEY AUTO_INCREMENT,
    idcitas INT NOT NULL,
    b1000 INT DEFAULT 0,  -- Cantidad de billetes de $1,000
    b500 INT DEFAULT 0,   -- Cantidad de billetes de $500
    b200 INT DEFAULT 0,   -- Cantidad de billetes de $200
    b100 INT DEFAULT 0,   -- Cantidad de billetes de $100
    b50 INT DEFAULT 0,    -- Cantidad de billetes de $50
    b20 INT DEFAULT 0,    -- Cantidad de billetes de $20
    m10 INT DEFAULT 0,    -- Cantidad de monedas de $10
    m5 INT DEFAULT 0,     -- Cantidad de monedas de $5
    m2 INT DEFAULT 0,     -- Cantidad de monedas de $2
    m1 INT DEFAULT 0,     -- Cantidad de monedas de $1
    m050 INT DEFAULT 0,   -- Cantidad de monedas de $0.50
    FOREIGN KEY (idcitas) REFERENCES citas(idcitas) ON DELETE CASCADE
);
