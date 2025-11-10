-- Migración: Añadir tablas para manuales, categorías, sedes, personal e instalaciones
USE institucion_PromSocial;

-- Tabla de categorías para noticias
CREATE TABLE IF NOT EXISTS categorias_noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Añadir columna categoria_id a noticias
ALTER TABLE noticias
    ADD COLUMN IF NOT EXISTS categoria_id INT NULL;

-- Tabla de manuales (manual de convivencia, etc.)
CREATE TABLE IF NOT EXISTS manuales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    archivo VARCHAR(255) NULL,
    creado_por INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de sedes
CREATE TABLE IF NOT EXISTS sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    direccion VARCHAR(255) NULL,
    telefono VARCHAR(50) NULL,
    descripcion TEXT NULL,
    imagen VARCHAR(255) NULL,
    tipo VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla unificada de personal (profesores, técnicos, instructores)
CREATE TABLE IF NOT EXISTS personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    educacion VARCHAR(255) NULL,
    edad INT NULL,
    telefono VARCHAR(50) NULL,
    horario VARCHAR(100) NULL,
    sede_id INT NULL,
    rol ENUM('Profesor','Tecnico','Instructor') DEFAULT 'Profesor',
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE SET NULL
);

-- Tabla de instalaciones
CREATE TABLE IF NOT EXISTS instalaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    imagen VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seeds iniciales para categorías
INSERT INTO categorias_noticias (nombre, tipo) VALUES
('Área/Asignatura','Academica'),
('Institucionales','Institucional')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Seeds iniciales para sedes (nombres existentes en la versión estática)
INSERT INTO sedes (nombre, direccion, telefono, descripcion, imagen, tipo) VALUES
('Sede Principal', 'Centro de Palermo', '', 'Sede principal con aulas y administración', 'I.E.jpeg', 'Principal'),
('Sede Eduardo Santos', 'Zona urbana - Palermo', '', 'Sede urbana que ofrece educación preescolar y primaria', 'sede_EduardoS.jpeg', 'Urbana'),
('Sede Benjamin Perez', 'Zona urbana - Palermo', '', 'Sede urbana que ofrece educación preescolar y primaria', 'sede_BenjaminP.jpg', 'Urbana'),
('Sede Camilo Torres', 'Zona urbana - Palermo', '', 'Sede urbana con jornada completa', 'sede_CamiloT.jpeg', 'Urbana'),
('Sede Mi Pequeño Mundo', 'Zona urbana - Palermo', '', 'Sede preescolar y primaria', 'sede_MPequeñoM.jpeg', 'Urbana'),
('Sede Farfan', 'Vereda Farfan', '', 'Sede rural ubicada en la vereda Farfan', 'sede_Farfan.jpg', 'Rural'),
('Sede Resguardo', 'Zona rural - Resguardo Indígena', '', 'Sede en resguardo indígena', 'sede_Resguardo.jpeg', 'Rural')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Seed manual de convivencia (apuntar al archivo existente si aplica)
INSERT INTO manuales (titulo, descripcion, archivo) VALUES
('Manual de Convivencia 2021', 'Manual de convivencia institucional.', 'manual de convivencia 2021.docx')
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);

-- Índices opcionales
CREATE INDEX IF NOT EXISTS idx_noticias_categoria ON noticias(categoria_id);
CREATE INDEX IF NOT EXISTS idx_sedes_nombre ON sedes(nombre);
