-- ============================================================
-- SCHEMA - Sistema Académico
-- Base de datos: PostgreSQL
-- ============================================================

CREATE TABLE IF NOT EXISTS usuario (
id SERIAL PRIMARY KEY,
nombres VARCHAR(100) NOT NULL,
apellidos VARCHAR(100) NOT NULL,
ci VARCHAR(20) NOT NULL,
email VARCHAR(150) UNIQUE NOT NULL,
telefono VARCHAR(30),
direccion VARCHAR(200),
fecha_nacimiento DATE,
password VARCHAR(200) NOT NULL,
estado VARCHAR(30) DEFAULT 'activo'
);

CREATE TABLE IF NOT EXISTS propietario (
id SERIAL PRIMARY KEY,
id_usuario INT UNIQUE NOT NULL,
codigo VARCHAR(50) UNIQUE NOT NULL,
cargo VARCHAR(100),
CONSTRAINT fk_propietario_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS secretaria (
id SERIAL PRIMARY KEY,
id_usuario INT UNIQUE NOT NULL,
codigo VARCHAR(50) UNIQUE NOT NULL,
turno_trabajo VARCHAR(50),
sueldo NUMERIC(10,2),
CONSTRAINT fk_secretaria_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS docente (
id SERIAL PRIMARY KEY,
id_usuario INT UNIQUE NOT NULL,
codigo VARCHAR(50) UNIQUE NOT NULL,
especialidad VARCHAR(100),
titulo VARCHAR(100),
registro_profesional VARCHAR(100),
CONSTRAINT fk_docente_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS alumno (
id SERIAL PRIMARY KEY,
id_usuario INT UNIQUE NOT NULL,
codigo VARCHAR(50) UNIQUE NOT NULL,
colegio_origen VARCHAR(150),
anio_bachillerato INT,
estado_academico VARCHAR(50),
CONSTRAINT fk_alumno_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS aula (
id SERIAL PRIMARY KEY,
codigo VARCHAR(50) UNIQUE NOT NULL,
nombre VARCHAR(100) NOT NULL,
ubicacion VARCHAR(150),
piso VARCHAR(30),
capacidad INT,
largo NUMERIC(10,2),
ancho NUMERIC(10,2),
disponible BOOLEAN DEFAULT TRUE,
id_usuario_registro INT,
CONSTRAINT fk_aula_usuario_registro
FOREIGN KEY (id_usuario_registro) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS carrera (
id SERIAL PRIMARY KEY,
codigo VARCHAR(50) UNIQUE NOT NULL,
nombre VARCHAR(150) NOT NULL,
duracion INT,
estado VARCHAR(30) DEFAULT 'activo',
regimen_academico VARCHAR(50),
id_usuario_registro INT,
CONSTRAINT fk_carrera_usuario_registro
FOREIGN KEY (id_usuario_registro) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS materia (
id SERIAL PRIMARY KEY,
codigo VARCHAR(50) UNIQUE NOT NULL,
nombre VARCHAR(150) NOT NULL,
carga_horaria INT,
estado VARCHAR(30) DEFAULT 'activo',
id_usuario_registro INT,
CONSTRAINT fk_materia_usuario_registro
FOREIGN KEY (id_usuario_registro) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS carrera_materia (
id SERIAL PRIMARY KEY,
id_carrera INT NOT NULL,
id_materia INT NOT NULL,
periodo_numero INT NOT NULL,
CONSTRAINT fk_carrera_materia_carrera
FOREIGN KEY (id_carrera) REFERENCES carrera(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_carrera_materia_materia
FOREIGN KEY (id_materia) REFERENCES materia(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT uq_carrera_materia
UNIQUE (id_carrera, id_materia)
);

CREATE TABLE IF NOT EXISTS periodo_academico (
id SERIAL PRIMARY KEY,
nombre VARCHAR(100) UNIQUE NOT NULL,
gestion INT NOT NULL,
tipo_periodo VARCHAR(50),
numero_periodo INT,
fecha_inicio DATE,
fecha_fin DATE,
estado VARCHAR(30) DEFAULT 'activo'
);

CREATE TABLE IF NOT EXISTS oferta_academica (
id SERIAL PRIMARY KEY,
id_carrera INT NOT NULL,
id_periodo_academico INT NOT NULL,
nombre VARCHAR(150) NOT NULL,
cantidad_cupos INT,
cupos_disponibles INT,
fecha_inicio DATE,
fecha_fin DATE,
CONSTRAINT fk_oferta_carrera
FOREIGN KEY (id_carrera) REFERENCES carrera(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_oferta_periodo
FOREIGN KEY (id_periodo_academico) REFERENCES periodo_academico(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT uq_oferta_academica
UNIQUE (id_carrera, id_periodo_academico)
);

CREATE TABLE IF NOT EXISTS horario (
id SERIAL PRIMARY KEY,
id_carrera_materia INT NOT NULL,
id_periodo_academico INT NOT NULL,
id_aula INT NOT NULL,
id_docente INT NOT NULL,
dia VARCHAR(30) NOT NULL,
hora_inicio TIME NOT NULL,
hora_fin TIME NOT NULL,
turno VARCHAR(50),
CONSTRAINT fk_horario_carrera_materia
FOREIGN KEY (id_carrera_materia) REFERENCES carrera_materia(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_horario_periodo
FOREIGN KEY (id_periodo_academico) REFERENCES periodo_academico(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_horario_aula
FOREIGN KEY (id_aula) REFERENCES aula(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_horario_docente
FOREIGN KEY (id_docente) REFERENCES docente(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS inscripcion (
id SERIAL PRIMARY KEY,
id_alumno INT NOT NULL,
id_oferta_academica INT NOT NULL,
id_usuario_registro INT,
periodo_numero INT NOT NULL,
fecha_inscripcion DATE,
observacion TEXT,
CONSTRAINT fk_inscripcion_alumno
FOREIGN KEY (id_alumno) REFERENCES alumno(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_inscripcion_oferta
FOREIGN KEY (id_oferta_academica) REFERENCES oferta_academica(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_inscripcion_usuario_registro
FOREIGN KEY (id_usuario_registro) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT uq_inscripcion_alumno_oferta
UNIQUE (id_alumno, id_oferta_academica)
);

CREATE TABLE IF NOT EXISTS inscripcion_materia (
id SERIAL PRIMARY KEY,
id_inscripcion INT NOT NULL,
id_carrera_materia INT NOT NULL,
estado VARCHAR(50) DEFAULT 'CURSANDO',
CONSTRAINT fk_inscripcion_materia_inscripcion
FOREIGN KEY (id_inscripcion) REFERENCES inscripcion(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_inscripcion_materia_carrera_materia
FOREIGN KEY (id_carrera_materia) REFERENCES carrera_materia(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT uq_inscripcion_materia
UNIQUE (id_inscripcion, id_carrera_materia)
);

CREATE TABLE IF NOT EXISTS seguimiento_academico (
id SERIAL PRIMARY KEY,
id_inscripcion_materia INT NOT NULL,
id_docente INT NOT NULL,
nota_final NUMERIC(5,2),
porcentaje_asistencia NUMERIC(5,2),
observacion TEXT,
estado_academico VARCHAR(50),
fecha_registro DATE,
CONSTRAINT fk_seguimiento_inscripcion_materia
FOREIGN KEY (id_inscripcion_materia) REFERENCES inscripcion_materia(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_seguimiento_docente
FOREIGN KEY (id_docente) REFERENCES docente(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT uq_seguimiento_inscripcion_materia
UNIQUE (id_inscripcion_materia)
);

CREATE TABLE IF NOT EXISTS concepto_pago (
id SERIAL PRIMARY KEY,
nombre VARCHAR(100) UNIQUE NOT NULL,
descripcion TEXT,
estado VARCHAR(30) DEFAULT 'activo'
);

CREATE TABLE IF NOT EXISTS pago_contado (
id SERIAL PRIMARY KEY,
id_inscripcion INT NOT NULL,
id_concepto_pago INT NOT NULL,
monto_pagado NUMERIC(10,2) NOT NULL,
fecha_pago DATE,
metodo_pago VARCHAR(50),
estado VARCHAR(30) DEFAULT 'PENDIENTE',
codigo_transaccion VARCHAR(120),
correo_solicitante VARCHAR(150),
observacion TEXT,
payment_number VARCHAR(100),
qr_path TEXT,
fecha_confirmacion TIMESTAMP,
CONSTRAINT fk_pago_contado_inscripcion
FOREIGN KEY (id_inscripcion) REFERENCES inscripcion(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_pago_contado_concepto
FOREIGN KEY (id_concepto_pago) REFERENCES concepto_pago(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS credito (
id SERIAL PRIMARY KEY,
id_inscripcion INT NOT NULL,
id_concepto_pago INT NOT NULL,
tipo_pago VARCHAR(50) DEFAULT 'CREDITO',
monto_total NUMERIC(10,2) NOT NULL,
saldo_pendiente NUMERIC(10,2),
cantidad_cuotas INT,
estado VARCHAR(30) DEFAULT 'pendiente',
CONSTRAINT fk_credito_inscripcion
FOREIGN KEY (id_inscripcion) REFERENCES inscripcion(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_credito_concepto
FOREIGN KEY (id_concepto_pago) REFERENCES concepto_pago(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS pago_cuota (
id SERIAL PRIMARY KEY,
id_credito INT NOT NULL,
monto NUMERIC(10,2) NOT NULL,
numero_cuota INT NOT NULL,
fecha_vencimiento DATE,
fecha_pago DATE,
estado_cuota VARCHAR(30) DEFAULT 'pendiente',
metodo_pago VARCHAR(50),
observacion TEXT,
codigo_transaccion VARCHAR(150),
correo_solicitante VARCHAR(150),
payment_number VARCHAR(100),
qr_path TEXT,
fecha_confirmacion TIMESTAMP,
CONSTRAINT fk_pago_cuota_credito
FOREIGN KEY (id_credito) REFERENCES credito(id)
ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT uq_pago_cuota_credito_numero
UNIQUE (id_credito, numero_cuota)
);

CREATE TABLE IF NOT EXISTS reporte (
id SERIAL PRIMARY KEY,
id_usuario INT,
correo_solicitante VARCHAR(150) NOT NULL,
tipo VARCHAR(100) NOT NULL,
periodo VARCHAR(50),
formato VARCHAR(50),
fecha_generacion DATE,
CONSTRAINT fk_reporte_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON UPDATE NO ACTION ON DELETE NO ACTION
);
