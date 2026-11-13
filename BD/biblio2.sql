-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 13-11-2026 a las 10:55:33
-- Versión del servidor: 8.2.0
-- Versión de PHP: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblio2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_autores`
--

CREATE TABLE `biblio2_autores` (
  `id` int NOT NULL,
  `nombreautor` varchar(250) NOT NULL,
  `lugar_nacimiento` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_muerte` date DEFAULT NULL,
  `biografia` text,
  `fecha_alta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_bibliotecas`
--

CREATE TABLE `biblio2_bibliotecas` (
  `id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `color_tema` varchar(7) DEFAULT '#0d6efd',
  `activa` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_editoriales`
--

CREATE TABLE `biblio2_editoriales` (
  `id` int NOT NULL,
  `nombreeditorial` varchar(250) NOT NULL,
  `descripcion` text,
  `fecha_alta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_eventos`
--

CREATE TABLE `biblio2_eventos` (
  `id` int UNSIGNED NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `url` varchar(150) DEFAULT NULL,
  `tipoevento` varchar(45) NOT NULL DEFAULT 'info',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `id_biblioteca` int NOT NULL,
  `creado_por` int NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_favoritos`
--

CREATE TABLE `biblio2_favoritos` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_libro` int NOT NULL,
  `fecha_alta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_biblioteca` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_generos`
--

CREATE TABLE `biblio2_generos` (
  `id` int NOT NULL,
  `genero` varchar(150) DEFAULT NULL,
  `descripcion` text,
  `fecha_alta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_libros`
--

CREATE TABLE `biblio2_libros` (
  `id` int NOT NULL,
  `id_usuario` tinyint NOT NULL,
  `tipo` tinyint(1) NOT NULL,
  `codigoISBN` varchar(75) DEFAULT NULL,
  `portada` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` int NOT NULL,
  `editorial` int NOT NULL,
  `genero` int UNSIGNED DEFAULT NULL,
  `n_paginas` int DEFAULT NULL,
  `ano_edicio` year DEFAULT NULL,
  `resena` text,
  `prestamo` tinyint(1) NOT NULL,
  `fecha_alta` date NOT NULL,
  `id_biblioteca` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_libros_autores`
--

CREATE TABLE `biblio2_libros_autores` (
  `id_libro` int NOT NULL,
  `id_autor` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_libros_backup`
--

CREATE TABLE `biblio2_libros_backup` (
  `id` int NOT NULL DEFAULT '0',
  `id_usuario` tinyint NOT NULL,
  `tipo` tinyint(1) NOT NULL,
  `codigoISBN` varchar(75) CHARACTER SET utf8mb3 DEFAULT NULL,
  `portada` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb3 NOT NULL,
  `autor` int NOT NULL,
  `editorial` int NOT NULL,
  `genero` varchar(255) CHARACTER SET utf8mb3 NOT NULL,
  `n_paginas` int DEFAULT NULL,
  `ano_edicio` year DEFAULT NULL,
  `resena` text CHARACTER SET utf8mb3,
  `prestamo` tinyint(1) NOT NULL,
  `fecha_alta` date NOT NULL,
  `id_biblioteca` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_libros_generos`
--

CREATE TABLE `biblio2_libros_generos` (
  `id_libro` int NOT NULL,
  `id_genero` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_migrations`
--

CREATE TABLE `biblio2_migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_paises`
--

CREATE TABLE `biblio2_paises` (
  `id` int NOT NULL,
  `code_iso3166` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `country_name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_prestamos`
--

CREATE TABLE `biblio2_prestamos` (
  `id` int NOT NULL,
  `tipo_prestatario` enum('socio','externo') CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT 'socio',
  `id_libro` int NOT NULL,
  `id_usuario` int DEFAULT NULL,
  `nombre_externo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `email_externo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `telefono_externo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `dni_externo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `usuario` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `isbn` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `titulo_libro` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `fecha_prestamo` date DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `fecha_entrada` date DEFAULT NULL,
  `fecha_devolucion_real` datetime DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci,
  `devuelto` tinyint DEFAULT '0',
  `fecha_creacion` datetime NOT NULL,
  `id_biblioteca` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_recordatorios`
--

CREATE TABLE `biblio2_recordatorios` (
  `id` int NOT NULL,
  `id_prestamo` int NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT 'email',
  `fecha_envio` date DEFAULT NULL,
  `estado` enum('pendiente','enviado','fallido') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT 'pendiente',
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_reservas`
--

CREATE TABLE `biblio2_reservas` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_libro` int NOT NULL,
  `estado` enum('pendiente','prestado','devuelto','cancelado') DEFAULT 'pendiente',
  `fecha_reserva` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_prestamo` date DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `observaciones` text,
  `notificado` tinyint(1) DEFAULT '0',
  `id_biblioteca` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_settings`
--

CREATE TABLE `biblio2_settings` (
  `id` int NOT NULL,
  `base_url` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `titulo_pagina` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `nombre_bd` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `nom_empresa` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `direccion` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `poblacion` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `provincia` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `cod_postal` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `pais` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `telefono` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `cif` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblio2_usuarios`
--

CREATE TABLE `biblio2_usuarios` (
  `id` int NOT NULL,
  `login` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pin` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `foto` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `nivel` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'usu',
  `activo` tinyint DEFAULT NULL,
  `key` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `nombre` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `apellidos` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `dni` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `direccion` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `poblacion` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `provincia` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `c_postal` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `mostrar_email` tinyint DEFAULT '0',
  `mostrar_nombre_real` tinyint DEFAULT '0',
  `permitir_contacto` tinyint DEFAULT '0',
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_ultima_modificacion` datetime NOT NULL,
  `id_biblioteca` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `biblio2_autores`
--
ALTER TABLE `biblio2_autores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombreautor` (`nombreautor`),
  ADD KEY `nacimiento` (`lugar_nacimiento`);
ALTER TABLE `biblio2_autores` ADD FULLTEXT KEY `biografia` (`biografia`);

--
-- Indices de la tabla `biblio2_bibliotecas`
--
ALTER TABLE `biblio2_bibliotecas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `biblio2_editoriales`
--
ALTER TABLE `biblio2_editoriales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombreeditorial` (`nombreeditorial`);

--
-- Indices de la tabla `biblio2_eventos`
--
ALTER TABLE `biblio2_eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `biblio2_favoritos`
--
ALTER TABLE `biblio2_favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorito` (`id_usuario`,`id_libro`),
  ADD KEY `id_libro` (`id_libro`),
  ADD KEY `id_biblioteca` (`id_biblioteca`);

--
-- Indices de la tabla `biblio2_generos`
--
ALTER TABLE `biblio2_generos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `biblio2_libros`
--
ALTER TABLE `biblio2_libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isbn+titulo+ano_edicio` (`codigoISBN`,`titulo`,`ano_edicio`),
  ADD KEY `id_biblioteca` (`id_biblioteca`);
ALTER TABLE `biblio2_libros` ADD FULLTEXT KEY `resena` (`resena`);

--
-- Indices de la tabla `biblio2_libros_autores`
--
ALTER TABLE `biblio2_libros_autores`
  ADD PRIMARY KEY (`id_libro`,`id_autor`),
  ADD KEY `id_autor` (`id_autor`);

--
-- Indices de la tabla `biblio2_libros_generos`
--
ALTER TABLE `biblio2_libros_generos`
  ADD PRIMARY KEY (`id_libro`,`id_genero`),
  ADD KEY `id_genero` (`id_genero`);

--
-- Indices de la tabla `biblio2_migrations`
--
ALTER TABLE `biblio2_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `biblio2_paises`
--
ALTER TABLE `biblio2_paises`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `biblio2_prestamos`
--
ALTER TABLE `biblio2_prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_biblioteca` (`id_biblioteca`);

--
-- Indices de la tabla `biblio2_recordatorios`
--
ALTER TABLE `biblio2_recordatorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_prestamo` (`id_prestamo`);

--
-- Indices de la tabla `biblio2_reservas`
--
ALTER TABLE `biblio2_reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_libro` (`id_libro`),
  ADD KEY `id_biblioteca` (`id_biblioteca`);

--
-- Indices de la tabla `biblio2_settings`
--
ALTER TABLE `biblio2_settings`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `biblio2_usuarios`
--
ALTER TABLE `biblio2_usuarios`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id_biblioteca` (`id_biblioteca`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `biblio2_autores`
--
ALTER TABLE `biblio2_autores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_bibliotecas`
--
ALTER TABLE `biblio2_bibliotecas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_editoriales`
--
ALTER TABLE `biblio2_editoriales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_eventos`
--
ALTER TABLE `biblio2_eventos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_favoritos`
--
ALTER TABLE `biblio2_favoritos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_generos`
--
ALTER TABLE `biblio2_generos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_libros`
--
ALTER TABLE `biblio2_libros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_migrations`
--
ALTER TABLE `biblio2_migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_paises`
--
ALTER TABLE `biblio2_paises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_prestamos`
--
ALTER TABLE `biblio2_prestamos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_recordatorios`
--
ALTER TABLE `biblio2_recordatorios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_reservas`
--
ALTER TABLE `biblio2_reservas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_settings`
--
ALTER TABLE `biblio2_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biblio2_usuarios`
--
ALTER TABLE `biblio2_usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `biblio2_favoritos`
--
ALTER TABLE `biblio2_favoritos`
  ADD CONSTRAINT `biblio2_favoritos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `biblio2_usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `biblio2_favoritos_ibfk_2` FOREIGN KEY (`id_libro`) REFERENCES `biblio2_libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `biblio2_favoritos_ibfk_3` FOREIGN KEY (`id_biblioteca`) REFERENCES `biblio2_bibliotecas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `biblio2_libros`
--
ALTER TABLE `biblio2_libros`
  ADD CONSTRAINT `biblio2_libros_ibfk_1` FOREIGN KEY (`id_biblioteca`) REFERENCES `biblio2_bibliotecas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `biblio2_prestamos`
--
ALTER TABLE `biblio2_prestamos`
  ADD CONSTRAINT `biblio2_prestamos_ibfk_1` FOREIGN KEY (`id_biblioteca`) REFERENCES `biblio2_bibliotecas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `biblio2_reservas`
--
ALTER TABLE `biblio2_reservas`
  ADD CONSTRAINT `biblio2_reservas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `biblio2_usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `biblio2_reservas_ibfk_2` FOREIGN KEY (`id_libro`) REFERENCES `biblio2_libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `biblio2_reservas_ibfk_3` FOREIGN KEY (`id_biblioteca`) REFERENCES `biblio2_bibliotecas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `biblio2_usuarios`
--
ALTER TABLE `biblio2_usuarios`
  ADD CONSTRAINT `biblio2_usuarios_ibfk_1` FOREIGN KEY (`id_biblioteca`) REFERENCES `biblio2_bibliotecas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
