-- Ejecuta este archivo UNA SOLA VEZ para crear todas las tablas.
-- Opción 1: phpMyAdmin → pestaña "Importar" → selecciona este archivo
-- Opción 2: terminal → mysql -u root -p < db/schema.sql

CREATE DATABASE IF NOT EXISTS salazaras
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salazaras;

-- ── Tablas ──────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS materiales (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    descripcion TEXT,
    imagen      VARCHAR(300),
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    orden       INT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS noticias (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    titulo     VARCHAR(250) NOT NULL,
    resumen    TEXT,
    contenido  LONGTEXT,
    imagen     VARCHAR(300),
    activo     TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estadisticas (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    clave  VARCHAR(60) UNIQUE NOT NULL,
    valor  INT NOT NULL DEFAULT 0,
    label  VARCHAR(120) NOT NULL,
    icono  VARCHAR(60) NOT NULL DEFAULT 'ri-bar-chart-fill'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(80) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Datos iniciales ──────────────────────────────────────

INSERT IGNORE INTO estadisticas (clave, valor, label, icono) VALUES
    ('anos_experiencia',    15,   'Años de experiencia',  'ri-calendar-check-fill'),
    ('proyectos_ejecutados',1200, 'Proyectos ejecutados', 'ri-checkbox-circle-fill'),
    ('clientes_satisfechos',950,  'Clientes satisfechos', 'ri-user-heart-fill'),
    ('confiabilidad',       99,   '% de confiabilidad',   'ri-shield-check-fill');

INSERT IGNORE INTO materiales (nombre, descripcion, imagen, orden) VALUES
    ('Arena Fina',        'La arena fina se utiliza para acabados en construcción: tarrajeo, revoques y morteros.', 'img/arenafina.png',       1),
    ('Arena Gruesa',      'Se usa para concreto y cimientos, aportando resistencia estructural.',                   'img/arena-gruesa.jpg',    2),
    ('Piedra',            'Empleada en cimientos y obras estructurales por su alta resistencia.',                   'img/piedra.jpg',          3),
    ('Confetillado',      'Ideal para concretos y drenajes, mejora la compactación.',                               'img/confetillado.jpg',    4),
    ('Arena Fina Sílice', 'Material de relleno para jardines y nivelaciones.',                                      'img/silice.jpg',          5),
    ('Piedra Chancada',   'Se usa en concreto armado y bases; brinda mayor firmeza y durabilidad.',                 'img/piedra-chancada.jpg', 6);

-- PASO FINAL: Visita http://localhost/tu-proyecto/admin/setup.php para crear tu cuenta admin
