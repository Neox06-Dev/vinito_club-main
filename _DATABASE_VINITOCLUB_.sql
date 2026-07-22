-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-07-2026 a las 19:01:58
-- Versión del servidor: 8.4.3
-- Versión de PHP: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vinitoclub_bbdd`
--
CREATE DATABASE IF NOT EXISTS `vinitoclub_bbdd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `vinitoclub_bbdd`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`) VALUES
(1, 'Tinto'),
(2, 'Blanco'),
(3, 'Rosé'),
(4, 'Espumante'),
(5, 'Dulce');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id_detalle` int NOT NULL,
  `id_pedido` int NOT NULL,
  `id_vino` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id_detalle`, `id_pedido`, `id_vino`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 5, 5, 1, 31000.00, 31000.00),
(2, 6, 22, 1, 25000.00, 25000.00),
(3, 7, 22, 1, 25000.00, 25000.00),
(4, 8, 8, 1, 11200.00, 11200.00),
(5, 9, 8, 1, 11200.00, 11200.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int NOT NULL,
  `id_usuario` int NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','Preparando','Enviado','Entregado','Cancelado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pendiente',
  `metodo_pago` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `metodo_envio` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `subtotal` decimal(10,2) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `cuotas` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha_pedido`, `estado`, `metodo_pago`, `metodo_envio`, `direccion`, `observaciones`, `subtotal`, `costo_envio`, `total`, `cuotas`) VALUES
(5, 10, '2026-07-20 22:21:59', 'Pendiente', 'tarjeta', 'domicilio', 'kjgkgkh1342, CABA (CP 12341)', '', 31000.00, 0.00, 31000.00, 1),
(6, 10, '2026-07-20 22:34:10', 'Pendiente', 'tarjeta', 'retiro', 'Retiro en tienda — Av. del Libertador 1234, CABA', '', 25000.00, 0.00, 25000.00, 2),
(7, 10, '2026-07-20 22:34:10', 'Pendiente', 'tarjeta', 'retiro', 'Retiro en tienda — Av. del Libertador 1234, CABA', '', 25000.00, 0.00, 25000.00, 2),
(8, 10, '2026-07-20 22:55:00', 'Pendiente', 'efectivo', 'retiro', 'Retiro en tienda — Av. del Libertador 1234, CABA', '', 11200.00, 0.00, 11200.00, 1),
(9, 10, '2026-07-20 22:55:00', 'Pendiente', 'efectivo', 'retiro', 'Retiro en tienda — Av. del Libertador 1234, CABA', '', 11200.00, 0.00, 11200.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

CREATE TABLE `regiones` (
  `id_region` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`id_region`, `nombre`) VALUES
(10, 'Cafayate'),
(3, 'La Rioja'),
(1, 'Mendoza'),
(4, 'Patagonia'),
(5, 'San Rafael'),
(6, 'Tupungato'),
(7, 'Valle Calchaquí'),
(8, 'Valle de Uco'),
(9, 'Valle del Itata');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('cliente','admin') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cliente',
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password`, `rol`, `telefono`, `fecha_registro`) VALUES
(1, 'Administrador', 'admin@vinitoclub.com', '$2y$12$ev/UjO4P/lH1sNTfb9xyLOh6WT42h6m9haqX8wSJMNzkPhSx5gFd2', 'admin', '', '2026-07-17 21:23:57'),
(10, 'Nicolas Gonzalez', 'nico.gonzalez.1116@gmail.com', '$2y$12$XIV7AoBbbBX93V3ln7wmj.eBdG1ES2UUY10aHTrbomybfwjDNK/E.', 'cliente', '1131354138', '2026-07-17 21:23:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `varietales`
--

CREATE TABLE `varietales` (
  `id_varietal` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `varietales`
--

INSERT INTO `varietales` (`id_varietal`, `nombre`) VALUES
(1, 'Malbec'),
(2, 'Cabernet Sauvignon'),
(3, 'Petit Verdot'),
(4, 'Chardonnay'),
(5, 'Pinot Noir'),
(6, 'Bonarda'),
(7, 'Torrontés'),
(8, 'Sauvignon Blanc'),
(9, 'Cabernet Franc');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vinos`
--

CREATE TABLE `vinos` (
  `id_vino` int NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `anio_cosecha` date NOT NULL,
  `volumen_ml` int NOT NULL,
  `temperatura_servicio` int NOT NULL,
  `bodega` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `categoria_id` int NOT NULL,
  `maridaje` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `destacado` tinyint(1) NOT NULL,
  `region_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vinos`
--

INSERT INTO `vinos` (`id_vino`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `anio_cosecha`, `volumen_ml`, `temperatura_servicio`, `bodega`, `categoria_id`, `maridaje`, `destacado`, `region_id`) VALUES
(1, 'Camino del Valle', 'Un Malbec de carácter profundo, con aromas a frutas negras maduras, violetas y especias suaves. En boca es estructurado, con taninos firmes y un final prolongado que evoca la tierra mendocina.', 18900.00, 45, 'camino-del-valle.png', '2019-03-15', 750, 16, 'Zuccardi', 1, 'Carnes rojas, estofados, quesos curados', 1, 8),
(2, 'Cuvée Riche', 'Chardonnay fresco y expresivo, con notas de durazno, pera y un delicado toque de vainilla. Fermentado parcialmente en roble francés, logra equilibrio entre frescura y complejidad.', 14500.00, 35, 'cuvee-riche.png', '2021-02-10', 750, 10, 'Chandon', 2, 'Pescados, mariscos, pasta con crema', 1, 6),
(3, 'Stella Rosa', 'Rosé elegante y sutil, con aromas de frutilla, frambuesa y pétalos de rosa. Seco y refrescante, ideal para el aperitivo o para acompañar platos mediterráneos.', 12800.00, 28, 'rosa-de-otono.png', '2022-01-20', 750, 9, 'Achaval Ferrer', 3, 'Ensaladas, salmón, quesos frescos', 1, 8),
(4, 'Gran Espumante Brut Nature', 'Elaborado por el método champenoise, este espumante de burbuja fina y persistente ofrece notas de brioche, manzana verde y cítricos. Sin azúcar añadida, es seco y mineral.', 22500.00, 18, 'brut-nature.png', '2020-11-05', 750, 8, 'Crotta', 4, 'Ostras, caviar, sushi, aperitivos', 1, 5),
(5, 'Alma Negra', 'Un blend de autor que combina la potencia del Malbec con la estructura del Cabernet y la complejidad del Petit Verdot. Notas de ciruela, tabaco y grafito. Vino de guarda.', 31000.00, 12, 'alma-negra.png', '2018-04-22', 750, 17, 'Ernesto Catena', 1, 'Cordero, caza, quesos añejos', 1, 8),
(6, 'Torrontés Salteño', 'El varietal emblema del noroeste argentino. Perfumado, floral, con notas de rosa mosqueta y frutas tropicales. Levemente dulce, fresco y muy aromático. Un vino único.', 9800.00, 50, 'torrontes-salteno.png', '2022-02-14', 750, 8, 'El Esteco', 5, 'Postres de frutas, quesos azules, foie gras', 0, 10),
(7, 'Colonia Las Liebres Bonarda', 'Bonarda jugosa y accesible, con aromas de cerezas, ciruelas y un toque herbal. Taninos suaves y buena acidez hacen de este vino el compañero perfecto para el asado del domingo.', 8500.00, 60, 'colonia-liebres.png', '2021-03-10', 750, 16, 'Familia Zuccardi', 1, 'Asado, empanadas, pizza', 0, 1),
(8, 'Sauvignon Blanc Fresco', 'Vivo y refrescante, con intensos aromas a pomelo, hierba recién cortada y notas minerales. Acidez vibrante y final limpio. El blanco ideal para los días cálidos.', 11200.00, 45, 'sauvignon-blanc.webp', '2023-01-08', 750, 9, 'Clos de los Siete', 2, 'Ensaladas verdes, vegetales, mariscos', 0, 8),
(9, 'Cabernet Rutini Reserva', 'Elegante y con personalidad, el Cabernet Franc reserva muestra aromas a pimiento rojo, frutos negros y especias. En boca es fresco, con taninos sedosos y larga persistencia.', 16700.00, 22, 'rutini-cabernet.png', '2019-04-05', 750, 16, 'Nieto Senetiner', 1, 'Cordero, pato, quesos semiduros', 0, 1),
(10, 'Viña de Alba', 'Vino dulce de cosecha tardía, con uvas vendimiadas en su punto máximo de madurez. Aromas intensos de miel, albaricoque y flores blancas. Textura untuosa y final muy largo.', 13400.00, 15, 'vina-alba.png', '2021-05-15', 500, 10, 'Clos de Chacras', 5, 'Postres, frutas secas, quesos de cabra', 0, 3),
(11, 'Izadi Larrosa', 'Rosé vibrante y luminoso, elaborado con Malbec de San Rafael. Color salmón intenso, con aromas a sandía, frutilla y un toque de pétalos. Perfecto para disfrutar bien frío.', 10500.00, 33, 'izadi-larrosa.webp', '2023-02-01', 750, 9, 'Achaval Ferrer', 3, 'Brunch, ensaladas, carnes blancas', 0, 3),
(12, 'Extra Brut Prestige', 'El lujo en una copa. Elaborado con método tradicional champenoise, este espumante premium presenta aromas de levadura, tostados, manzana golden y frutos secos. Burbuja cremosa e inagotable.', 28900.00, 10, 'brut-prestige.png', '2020-08-20', 750, 7, 'Baron B', 4, 'Celebraciones, ostras, canapés de lujo', 1, 1),
(13, 'Malbec Clásico', 'El Malbec cotidiano por excelencia. Frutal, amable y versátil. Aromas a ciruela y mora con un toque de vainilla. Taninos suaves y final agradable. El vino de todos los días.', 7900.00, 80, 'malbec-clasico.png', '2022-03-20', 750, 16, 'Trapiche', 1, 'Pastas, pizza, carnes a la parrilla', 0, 1),
(14, 'Pinot Noir', 'Estilo alsaciano poco común en Argentina. Aromas ahumados, melocotón, jengibre y flores blancas. Textura untuosa y acidez refrescante. Un blanco que sorprende y enamora.', 15300.00, 20, 'pinot-noir.png', '2022-02-28', 750, 11, 'Zuccardi', 2, 'Carne de cerdo, curry suave, pasta al salmón', 0, 4),
(15, 'Los Haroldos Gran Corte', 'La cima de la vitivinicultura argentina. Blend icónico de Catena Zapata con 5 años de crianza en roble francés. Complejidad extraordinaria: cassis, chocolate amargo, cedro y tabaco. Vino de colección.', 48000.00, 5, 'haroldos-gran-corte.png', '2017-04-10', 750, 18, 'Catena Zapata', 1, 'Rib eye, ossobuco, quesos muy añejos', 0, 1),
(21, 'San Nicolas Wines', 'Descubrí la elegancia del San Nicolás Wines, el vino ícono de la bodega Catena Zapata. Elaborado con una exclusiva composición de Pinot Noir provenientes de viñedos de Valle de Uco, este tinto seco ofrece una experiencia única', 74800.00, 32, 'vino-nicolas.webp', '2021-05-15', 750, 16, 'Catena Zapata', 1, 'Risottos de hongos, pastas con salsa boloñesa, cocina mediterránea con hierbas.', 1, 8),
(22, 'P.S García', 'Un vino que desafía lo convencional. El Petit Verdot de P.S. García nace en el histórico valle del Itata, donde la brisa del Pacífico y los suelos graníticos dan vida a un tinto de carácter vibrante y personalidad única.', 25000.00, 14, 'ps-garcia.png', '2013-07-01', 750, 18, ' Viña P.S. García', 1, 'Carnes rojas, platos especiados, quesos curados', 1, 9),
(23, 'Colomé Estate Torrontes', 'El Colomé Estate Torrontés es la expresión más pura del blanco argentino de altura. Proveniente de los viñedos más altos del mundo, en el Valle Calchaquí (Salta), este vino combina frescura, elegancia y una intensidad aromática inconfundible.', 15830.00, 8, '6a32bfc8dfdd7-colome.webp', '2023-03-22', 750, 9, 'Colomé', 2, 'Pescados, mariscos, sushi, ensaladas frescas y quesos suaves.', 1, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vino_varietal`
--

CREATE TABLE `vino_varietal` (
  `vino_id` int NOT NULL,
  `varietal_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vino_varietal`
--

INSERT INTO `vino_varietal` (`vino_id`, `varietal_id`) VALUES
(1, 1),
(5, 1),
(11, 1),
(13, 1),
(15, 1),
(22, 3),
(2, 4),
(4, 4),
(12, 4),
(3, 5),
(14, 5),
(21, 5),
(7, 6),
(6, 7),
(10, 7),
(23, 7),
(8, 8),
(9, 9);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_vino` (`id_vino`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `regiones`
--
ALTER TABLE `regiones`
  ADD PRIMARY KEY (`id_region`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `varietales`
--
ALTER TABLE `varietales`
  ADD PRIMARY KEY (`id_varietal`);

--
-- Indices de la tabla `vinos`
--
ALTER TABLE `vinos`
  ADD PRIMARY KEY (`id_vino`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `fk_region` (`region_id`);

--
-- Indices de la tabla `vino_varietal`
--
ALTER TABLE `vino_varietal`
  ADD PRIMARY KEY (`vino_id`,`varietal_id`),
  ADD KEY `varietal_id` (`varietal_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id_detalle` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `regiones`
--
ALTER TABLE `regiones`
  MODIFY `id_region` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `varietales`
--
ALTER TABLE `varietales`
  MODIFY `id_varietal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `vinos`
--
ALTER TABLE `vinos`
  MODIFY `id_vino` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detalle_vino` FOREIGN KEY (`id_vino`) REFERENCES `vinos` (`id_vino`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `vinos`
--
ALTER TABLE `vinos`
  ADD CONSTRAINT `fk_region` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id_region`),
  ADD CONSTRAINT `vinos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `vino_varietal`
--
ALTER TABLE `vino_varietal`
  ADD CONSTRAINT `vino_varietal_ibfk_1` FOREIGN KEY (`vino_id`) REFERENCES `vinos` (`id_vino`),
  ADD CONSTRAINT `vino_varietal_ibfk_2` FOREIGN KEY (`varietal_id`) REFERENCES `varietales` (`id_varietal`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
