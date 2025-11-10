USE institucion_PromSocial;

-- Tabla para contenidos informativos (matrículas, traslados, retiros, etc.)
CREATE TABLE IF NOT EXISTS informacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seeds básicos
INSERT INTO informacion (slug, titulo, contenido) VALUES
('matriculas', 'Matrículas', 'Información sobre matrículas al inicio y final de año. Aquí puede añadir fechas, requisitos, documentos necesarios y procedimientos.'),
('traslados', 'Traslados', 'Información sobre los traslados entre sedes y otras instituciones: requisitos, plazos y contacto administrativo.'),
('retiros', 'Retiros', 'Procedimiento para retiros de estudiantes, plazos y recomendaciones.')
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), contenido = VALUES(contenido);
