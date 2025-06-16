-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2025 a las 06:20:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adquisiciones`
--

CREATE TABLE `adquisiciones` (
  `id_adquisicion` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `flujo` varchar(50) NOT NULL,
  `proceso` varchar(10) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','en proceso','completada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adquisiciones`
--

INSERT INTO `adquisiciones` (`id_adquisicion`, `id_libro`, `id_usuario`, `flujo`, `proceso`, `fecha`, `estado`) VALUES
(1, 6, 3, 'F3', 'P3', '2025-06-15 22:29:32', 'completada'),
(2, 7, 3, 'F3', 'P3', '2025-06-15 22:30:32', 'completada'),
(3, 8, 3, 'F3', 'P3', '2025-06-15 22:31:01', 'completada'),
(4, 9, 3, 'F3', 'P3', '2025-06-15 22:39:32', 'completada'),
(5, 10, 3, 'F3', 'P3', '2025-06-16 00:47:25', 'en proceso'),
(6, 11, 3, 'F3', 'P3', '2025-06-16 01:11:55', 'en proceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flujo_proceso`
--

CREATE TABLE `flujo_proceso` (
  `flujo` varchar(3) NOT NULL,
  `proceso` varchar(3) NOT NULL,
  `siguiente` varchar(3) DEFAULT NULL,
  `pantalla` varchar(30) DEFAULT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `flujo_proceso`
--

INSERT INTO `flujo_proceso` (`flujo`, `proceso`, `siguiente`, `pantalla`, `rol`) VALUES
('F1', 'P1', 'P2', 'reservas_usuario', 'usuario'),
('F1', 'P2', 'P3', 'registrar_reserva', 'usuario'),
('F1', 'P3', 'P4', 'verificar_disponibilidad', 'bibliotecario'),
('F1', 'P4', 'P5', 'confirmar_reserva', 'bibliotecario'),
('F1', 'P5', '-', 'retirar_libro', 'bibliotecario'),
('F2', 'P1', 'P2', 'listar_reservas_usuario', 'usuario'),
('F2', 'P2', 'P3', 'listar_reservas_usuario', 'usuario'),
('F2', 'P3', '-', 'detalle_reserva', 'usuario'),
('F3', 'P1', 'P2', 'solicitar_registrar_libro', 'almacen'),
('F3', 'P2', 'P3', 'vericar_solicitar_reg_libro', 'almacen'),
('F3', 'P3', 'P4', 'registrar_libro', 'bibliotecario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id_libro` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id_libro`, `titulo`, `autor`, `disponible`, `stock`) VALUES
(1, 'Cien años de soledad', 'Gabriel García Márquez', 1, 0),
(2, 'El principito', 'Antoine de Saint-Exupéry', 1, 0),
(3, '1984', 'George Orwell', 1, 0),
(4, 'Don Quijote de la Mancha', 'Miguel de Cervantes', 1, 0),
(5, 'pruebas', 'silva', 1, 0),
(6, 'la vaca', 'silva', 1, 0),
(7, 'otoño', 'sss', 1, 0),
(8, 'otoño', 'ssssss', 1, 0),
(9, 'asdasdas', 'eeee', 1, 0),
(10, 'ing software', 'silva', 0, 0),
(11, 'prueba', 'ff', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `flujo` varchar(3) NOT NULL,
  `proceso` varchar(3) NOT NULL,
  `id_libro` int(11) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_usuario`, `flujo`, `proceso`, `id_libro`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(17, 1, 'F1', 'P3', 1, '2025-06-14 18:18:11', NULL, 'pendiente'),
(18, 1, 'F1', 'P3', 3, '2025-06-14 18:27:40', NULL, 'pendiente'),
(19, 1, 'F1', 'P2', 1, '2025-06-14 21:57:49', NULL, 'pendiente'),
(26, 1, 'F1', 'P5', 1, '2025-06-14 22:45:57', '2025-06-14 23:26:46', 'completada'),
(44, 5, 'F1', 'P2', 1, '2025-06-15 00:53:26', NULL, 'pendiente'),
(45, 5, 'F1', 'P2', 1, '2025-06-15 00:53:30', NULL, 'pendiente'),
(46, 5, 'F1', 'P2', 1, '2025-06-15 00:53:33', NULL, 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('usuario','bibliotecario','admin','almacen') NOT NULL DEFAULT 'usuario',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `correo`, `contrasena`, `rol`, `creado_en`) VALUES
(1, 'cristian', 'mamani', 'test1@gmail.com', '12345678', 'usuario', '2025-06-14 19:51:42'),
(2, 'alejandra', 'orosco', 'test2@gmail.com', '12345678', 'bibliotecario', '2025-06-14 19:52:48'),
(3, 'paola', 'calisaya', 'test3@gmail.com', '12345678', 'almacen', '2025-06-14 19:53:50'),
(5, 'cristian', 'mamani', 'test4@gmail.com', '12345678', 'usuario', '2025-06-15 02:56:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adquisiciones`
--
ALTER TABLE `adquisiciones`
  ADD PRIMARY KEY (`id_adquisicion`),
  ADD KEY `id_libro` (`id_libro`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `flujo_proceso`
--
ALTER TABLE `flujo_proceso`
  ADD PRIMARY KEY (`flujo`,`proceso`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id_libro`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`,`flujo`,`proceso`),
  ADD KEY `fk_reservas_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adquisiciones`
--
ALTER TABLE `adquisiciones`
  MODIFY `id_adquisicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adquisiciones`
--
ALTER TABLE `adquisiciones`
  ADD CONSTRAINT `adquisiciones_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id_libro`),
  ADD CONSTRAINT `adquisiciones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
