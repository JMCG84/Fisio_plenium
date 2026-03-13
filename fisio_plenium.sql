-- =========================================================
-- BD mínima: 4 tablas con relaciones claras (PK/FK/Índices)
-- Tablas: usuarios, servicios, pedidos, lineas_pedidos
-- =========================================================
DROP DATABASE IF EXISTS fisio_plenium;
CREATE DATABASE fisio_plenium CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fisio_plenium;

-- 1) USUARIOS: pacientes y administradores
CREATE TABLE usuarios (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nombre         VARCHAR(120)      NOT NULL,
  email          VARCHAR(160)      NOT NULL UNIQUE,
  password_hash  VARCHAR(255)      NOT NULL,
  rol            ENUM('paciente','admin') NOT NULL DEFAULT 'paciente',
  creado_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE INDEX idx_usuarios_email ON usuarios(email);

-- 2) SERVICIOS: catálogo de la clínica
CREATE TABLE servicios (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(160) NOT NULL,
  descripcion TEXT NULL,
  precio      DECIMAL(8,2) NOT NULL CHECK (precio >= 0),
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  creado_en   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE INDEX idx_servicios_activo ON servicios(activo);

-- 3) PEDIDOS: cabecera de la reserva/compra
CREATE TABLE pedidos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id   INT NOT NULL,  -- quién realiza el pedido (usuario/paciente)
  total        DECIMAL(10,2) NOT NULL CHECK (total >= 0),
  estado       ENUM('pendiente','confirmado','cancelado') NOT NULL DEFAULT 'pendiente',
  creado_en    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pedido_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_pedidos_estado  ON pedidos(estado);
CREATE INDEX idx_pedidos_fecha   ON pedidos(creado_en);

-- 4) LINEAS_PEDIDOS: detalle de cada pedido (N servicios por pedido)
CREATE TABLE lineas_pedidos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id   INT NOT NULL,
  servicio_id INT NOT NULL,
  cantidad    INT NOT NULL DEFAULT 1 CHECK (cantidad > 0),
  precio_u    DECIMAL(8,2) NOT NULL CHECK (precio_u >= 0),  -- precio unitario histórico
  CONSTRAINT fk_linea_pedido
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_linea_servicio
    FOREIGN KEY (servicio_id) REFERENCES servicios(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_linea_pedido   ON lineas_pedidos(pedido_id);
CREATE INDEX idx_linea_servicio ON lineas_pedidos(servicio_id);
