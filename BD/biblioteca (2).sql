-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-06-2025 a las 17:19:21
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
('F1', 'P1', 'P2', 'buscar_libro', 'usuario'),
('F1', 'P2', 'P3', 'solicitar_reserva', 'usuario'),
('F1', 'P3', 'P4', 'verificar_disponibilidad', 'bibliotecario'),
('F1', 'P4', 'P5', 'confirmar_reserva', 'usuario'),
('F1', 'P5', '-', 'retirar_libro', 'bibliotecario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id_libro` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `disponible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id_libro`, `titulo`, `autor`, `disponible`) VALUES
(1, 'Cien años de soledad', 'Gabriel García Márquez', 0),
(2, 'El principito', 'Antoine de Saint-Exupéry', 0),
(3, '1984', 'George Orwell', 0),
(4, 'Don Quijote de la Mancha', 'Miguel de Cervantes', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `usuario` varchar(15) NOT NULL,
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

INSERT INTO `reservas` (`id_reserva`, `usuario`, `flujo`, `proceso`, `id_libro`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 'juan luis', 'F1', 'P5', 3, '2025-06-11 01:39:32', '2025-06-11 02:23:19', 'completada'),
(2, 'andres flores', 'F1', 'P2', 3, '2025-06-11 01:42:17', '2025-06-11 02:27:14', 'confirmada'),
(3, 'cristian abel m', 'F1', 'P2', 1, '2025-06-11 01:51:35', '2025-06-11 01:53:41', 'cancelada'),
(4, 'andres', 'F1', 'P3', 1, '2025-06-11 02:43:08', '2025-06-11 02:53:35', 'pendiente'),
(5, 'luis vega', 'F1', 'P1', 2, '2025-06-11 02:57:36', NULL, 'pendiente'),
(6, 'luis vega', 'F1', 'P1', 2, '2025-06-11 03:00:04', NULL, 'pendiente'),
(7, 'luis vega', 'F1', 'P1', 2, '2025-06-11 03:02:56', NULL, 'pendiente'),
(8, 'luis ronal', 'F1', 'P1', 2, '2025-06-11 03:03:11', NULL, 'pendiente'),
(9, 'luis ronal', 'F1', 'P1', 2, '2025-06-11 03:03:45', NULL, 'pendiente'),
(10, 'abel', 'F1', 'P5', 3, '2025-06-11 03:03:56', '2025-06-11 03:09:05', 'completada'),
(11, 'kassandra', 'F1', 'P5', 3, '2025-06-11 10:44:19', '2025-06-11 11:03:13', 'completada'),
(12, 'rafael mendoza', 'F1', 'P4', 1, '2025-06-11 11:19:38', '2025-06-11 11:25:43', 'confirmada'),
(13, 'alan mendoza', 'F1', 'P5', 1, '2025-06-11 11:24:34', '2025-06-11 11:28:24', 'completada'),
(14, 'bibliotecario', 'F1', 'P1', 1, '2025-06-11 11:26:46', NULL, 'pendiente'),
(15, 'gloria', 'F1', 'P5', 3, '2025-06-11 11:29:25', '2025-06-11 11:30:17', 'completada'),
(16, 'tadeo', 'F1', 'P5', 2, '2025-06-11 11:34:32', '2025-06-11 11:36:53', 'completada');

--
-- Índices para tablas volcadas
--

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
  ADD PRIMARY KEY (`id_reserva`,`flujo`,`proceso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
