create database cevicheria;
use cevicheria;

-- Tabla de platos
CREATE TABLE platos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2)
);
select * from platos;
-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total DECIMAL(10,2),
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

select * from pedidos;
-- Tabla detalle del pedido
CREATE TABLE pedido_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    plato_id INT,
    cantidad INT,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (plato_id) REFERENCES platos(id)
);
select *from  pedidos;