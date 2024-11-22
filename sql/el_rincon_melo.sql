-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-11-2024 a las 06:55:34
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `el_rincon_melo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `nombre`, `email`, `password`, `fecha_registro`) VALUES
(1, 'Lucas Soriano', 'lsoriano@gmail.com', '$2y$10$SJ7lKq59UyYDVPcjjfOpY.2rryIxvvGCc6DNZK1zvMtpgpGUqsTrC', '2024-11-07 23:00:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `email`, `password`, `fecha_registro`) VALUES
(1, 'Rosa Castillo', 'rosicap@gmail.com', '$2y$10$BCS8cKU7SfhPOqJdvixquOePsqh6kavvkLHNR/KxAYnJdJ1/Lu/qW', '2024-11-07 15:23:31'),
(2, 'Alejandro Soriano', 'alejandrogabrielsorianopalo@gmail.com', '$2y$10$hkFHcKO9dIcnR9jWHkGTDOVf2BRLpkJ2uiOikayblMtKTCaXnG3Om', '2024-11-07 16:05:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ordenes`
--

CREATE TABLE `detalle_ordenes` (
  `id` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ordenes`
--

INSERT INTO `detalle_ordenes` (`id`, `id_orden`, `id_producto`, `cantidad`, `subtotal`) VALUES
(31, 19, 12, 2, 4.00),
(32, 19, 3, 3, 9.00),
(33, 19, 15, 1, 0.60),
(34, 19, 14, 3, 1.50),
(35, 20, 14, 2, 6.00),
(36, 20, 15, 2, 6.00),
(37, 20, 3, 1, 3.00),
(38, 21, 12, 1, 0.50),
(39, 21, 14, 1, 0.50),
(40, 22, 4, 1, 0.50),
(41, 22, 14, 3, 1.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `id_cliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id`, `fecha`, `total`, `estado`, `id_cliente`) VALUES
(19, '2024-11-21 23:33:18', 15.10, 'Confirmada', 2),
(20, '2024-11-22 00:47:22', 5.20, 'Pendiente', 2),
(21, '2024-11-22 00:49:42', 2.50, 'Pendiente', 2),
(22, '2024-11-22 00:52:00', 2.50, 'Pendiente', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`) VALUES
(2, 'Inka Kola 500 ml', 'Botella de Inka Kola de 500ml', 3.00, 20, 'uploads/6731c855134dc-inka_kola.jpg', 1),
(3, 'Coca Cola 500 ml', 'Botella de Coca Cola de 500ml', 3.00, 20, 'uploads/6731c85e298e3-coca_cola.jpg', 1),
(4, 'Inka Chips', 'Bolsa PersonaL', 1.00, 20, 'uploads/6731c86acee73-inka_chips.jpg', 1),
(12, 'Frugo del Valle Naranja 500ml', 'Sabor naranja', 2.00, 20, 'uploads/6731c8737cd7b-frugo_del_valle_naranja_500ml.jpg', 1),
(14, 'Galletas de Soda Field con Chocolate', 'eeee', 0.50, 20, 'uploads/673193fe75cf4-galleta-soda.jpg', 1),
(15, 'Glacitas de Chocolate', 'Unidad de Glacitas', 0.60, 20, 'uploads/673248bc5fe1e-glacitas_chocolate.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$IgMyD7ge.X3J1.vWD288hO2VFLZXQXeMo3Z/H1MqRr1e7WNxjtTOK');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `detalle_ordenes`
--
ALTER TABLE `detalle_ordenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_orden` (`id_orden`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cliente` (`id_cliente`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `detalle_ordenes`
--
ALTER TABLE `detalle_ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ordenes`
--
ALTER TABLE `detalle_ordenes`
  ADD CONSTRAINT `detalle_ordenes_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id`),
  ADD CONSTRAINT `detalle_ordenes_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `fk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
