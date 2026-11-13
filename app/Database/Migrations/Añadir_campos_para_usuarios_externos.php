-- Añadir campos para usuarios externos
ALTER TABLE biblio2_prestamos 
ADD COLUMN tipo_prestatario ENUM('socio', 'externo') DEFAULT 'socio' AFTER id,
ADD COLUMN nombre_externo VARCHAR(255) NULL AFTER id_usuario,
ADD COLUMN email_externo VARCHAR(255) NULL AFTER nombre_externo,
ADD COLUMN telefono_externo VARCHAR(50) NULL AFTER email_externo,
ADD COLUMN dni_externo VARCHAR(20) NULL AFTER telefono_externo;

-- Modificar id_usuario para permitir NULL (externos no tienen usuario registrado)
ALTER TABLE biblio2_prestamos MODIFY id_usuario INT NULL;