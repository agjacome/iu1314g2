-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 23-11-2013 a las 21:41:20
-- Versión del servidor: 5.5.31
-- Versión de PHP: 5.4.4-14+deb7u4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `BID_AND_SELL`
--
DROP DATABASE `BID_AND_SELL`;
CREATE DATABASE `BID_AND_SELL` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `BID_AND_SELL`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CALIFICACION`
--

DROP TABLE IF EXISTS `CALIFICACION`;
CREATE TABLE IF NOT EXISTS `CALIFICACION` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de calificacion. Auto incrementado.',
  `idProducto` int(10) unsigned NOT NULL COMMENT 'Identificador del producto calificado (clave foranea a PRODUCTO.idProducto).',
  `login` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Login del usuario que ha realizado la calificacion (clave foranea a USUARIO.login). No debe ser NULO, se permite solo para continuar almacenando las puntuaciones despues de que un usuario haya sido eliminado (hacer un "SET NULL").',
  `puntuacion` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT 'Puntuacion dada por el usuario al producto. La base de datos lo limita de 0 a 255, la logica de la aplicacion debera velar por almacenar valores correctos.',
  `comentario` text COLLATE utf8_spanish_ci COMMENT 'Comentario dado junto a la puntuacion. Puede ser nulo.',
  PRIMARY KEY (`idCalificacion`),
  KEY `idProducto` (`idProducto`,`login`),
  KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de calificaciones de productos.' AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `CALIFICACION`
--

INSERT INTO `CALIFICACION` (`idCalificacion`, `idProducto`, `login`, `puntuacion`, `comentario`) VALUES
(1, 3, 'usuario3', 4, 'Muy bueno!!!'),
(2, 9, 'usuario3', 5, 'mmm tiene buena pinta!'),
(3, 6, 'usuario3', 5, 'Es verdad!!! la mejor pÃ¡gina web de ventas y subastas!!'),
(4, 12, 'usuario1', 1, 'No pude entregar la practica porque no hay disquetera en los pcs de la facultad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `COMPRA`
--

DROP TABLE IF EXISTS `COMPRA`;
CREATE TABLE IF NOT EXISTS `COMPRA` (
  `idCompra` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de compra. Auto incrementado.',
  `idVenta` int(10) unsigned DEFAULT NULL COMMENT 'Identificador de la venta asociada a esta compra (clave foranea a VENTA.idVenta).',
  `login` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Login del usuario que ha realizado la compra (clave foranea a USUARIO.login)',
  `cantidad` int(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Numero de unidades del producto compradas.',
  `fechaCompra` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora (TIMESTAMP) del momento de realizacion de la compra. Auto generado por la BD en el momento de insercion.',
  `idPago` int(10) unsigned DEFAULT NULL COMMENT 'Identificador del pago asociado a esta compra. Puede ser nulo mientras la compra no haya sido pagada.',
  PRIMARY KEY (`idCompra`),
  KEY `idVenta` (`idVenta`,`login`,`idPago`),
  KEY `login` (`login`),
  KEY `idPago` (`idPago`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de compras.' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `COMPRA`
--

INSERT INTO `COMPRA` (`idCompra`, `idVenta`, `login`, `cantidad`, `fechaCompra`, `idPago`) VALUES
(1, 4, 'usuario1', 3, '2013-11-23 20:37:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PAGO`
--

DROP TABLE IF EXISTS `PAGO`;
CREATE TABLE IF NOT EXISTS `PAGO` (
  `idPago` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de pago. Auto incrementado.',
  `metodoPago` enum('paypal','tarjeta') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Metodo de pago establecido. Puede ser "tarjeta" para referirse a tarjeta de credito o "paypal" para realizacion de pagos a traves de paypal.',
  `numTarjeta` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Numero de tarjeta de credito. Puede ser nulo, deberia tener valor solamente si dicho metodo ha sido seleccionado.',
  `cuentaPaypal` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Cuenta de paypal. Puede ser nulo, deberia tener valor solamente si dicho metodo ha sido seleccionado.',
  `comision` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Comision recibida por la empresa tras la realizacion del pago. Se descontara al pago recibido por el vendedor. Calculado en base al porcentaje de comision establecido en el momento del pago.',
  PRIMARY KEY (`idPago`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de pagos de pujas y compras.' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `PAGO`
--

INSERT INTO `PAGO` (`idPago`, `metodoPago`, `numTarjeta`, `cuentaPaypal`, `comision`) VALUES
(1, 'paypal', NULL, 'usuario1@usuario1.es', 3.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRODUCTO`
--

DROP TABLE IF EXISTS `PRODUCTO`;
CREATE TABLE IF NOT EXISTS `PRODUCTO` (
  `idProducto` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de producto. Auto incrementado.',
  `propietario` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Login del propietario (clave foranea a USUARIO.login).',
  `estado` enum('pendiente','subasta','venta') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'pendiente' COMMENT 'Estado del producto: en venta, en subasta o aun no establecido (pendiende).',
  `nombre` varchar(255) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del producto.',
  `descripcion` text COLLATE utf8_spanish_ci COMMENT 'Descripcion del producto. Puede ser nulo.',
  PRIMARY KEY (`idProducto`),
  KEY `propietario` (`propietario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para almacenamiento de productos.' AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `PRODUCTO`
--

INSERT INTO `PRODUCTO` (`idProducto`, `propietario`, `estado`, `nombre`, `descripcion`) VALUES
(1, 'usuario1', 'venta', 'Ordenador portatil', 'Ordenador portatil Samsung.'),
(2, 'usuario1', 'subasta', 'Teclado', 'Teclado retroiluminado inalÃ¡mbrico de marca logitech.'),
(3, 'usuario1', 'subasta', 'RatÃ³n Razer Diamondback', 'Razer Diamondback verde 1800dpi y 7 botones.'),
(4, 'usuario1', 'pendiente', 'RatÃ³n Logitech Laser G9', 'RatÃ³n logitech G9'),
(5, 'usuario2', 'pendiente', 'RatÃ³n Razer Spectre Starcraft Gaming Mouse', 'Razer Spectre multicolor retroiluminado'),
(6, 'usuario2', 'venta', 'Codigo fuente web de venta y subasta Bid & Sell', 'Compra el cÃ³digo fuente de la mejor web de ventas y subastas!! Realizada por el grupo 2 de la asignatura Interfaces de Usuario 2013-2014!!!'),
(7, 'usuario3', 'pendiente', 'Teclado Logitech G15 ', 'Luz naranja, 18 macros, pantalla retroiluminada con informaciÃ³n, 4 entradas usb.'),
(8, 'usuario3', 'pendiente', 'ATI Radeon HD 7990', 'Targeta grÃ¡fica doble gpu 6gb'),
(9, 'usuario3', 'subasta', 'Pizza', 'Una pizza'),
(10, 'usuario3', 'pendiente', 'Alfombrilla SteelSeries S&S', 'Alfombrilla steelpad S&S '),
(11, 'usuario3', 'venta', 'Alfombrilla SteelPad QcK+', 'Alfombrilla enorme negra'),
(12, 'usuario3', 'venta', 'Disquete de vilares', 'Disquette de 3/2 para entregar las prÃ¡cticas a Vilares');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PUJA`
--

DROP TABLE IF EXISTS `PUJA`;
CREATE TABLE IF NOT EXISTS `PUJA` (
  `idPuja` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de pujas. Auto incrementado.',
  `idSubasta` int(10) unsigned DEFAULT NULL COMMENT 'Identificador de la subasta donde se realiza la puja (clave foranea a SUBASTA.idSubasta). Puede ser nulo para permitir almacenamiento de pujas de subastas eliminadas (un "SET NULL").',
  `login` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Login del usuario que ha realizado la puja (clave foranea a USUARIO.login).',
  `cantidadPuja` decimal(9,2) unsigned NOT NULL COMMENT 'Cantidad (en euros) de la puja realizada. Comprobar que la puja realizada sea mas alta que la actual sera responsabilidad de la logica de la aplicacion.',
  `fechaPuja` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora (TIMESTAMP) de la relizacion de la puja. Se escribe automaticamente en el instante de insercion en la BD.',
  `idPago` int(10) unsigned DEFAULT NULL COMMENT 'Identificador de pago para esta puja (clave foranea a PAGO.idPago). Un valor NULO indicara que la puja no ha sido pagada (solo la puja ganadora de una subasta deberia tener un pago asociado).',
  PRIMARY KEY (`idPuja`),
  KEY `idSubasta` (`idSubasta`),
  KEY `login` (`login`),
  KEY `idPago` (`idPago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de pujas a subastas.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SUBASTA`
--

DROP TABLE IF EXISTS `SUBASTA`;
CREATE TABLE IF NOT EXISTS `SUBASTA` (
  `idSubasta` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de subasta. Auto incrementado.',
  `idProducto` int(10) unsigned NOT NULL COMMENT 'Identificador del producto subastado (clave foranea a PRODUCTO.idProducto).',
  `pujaMinima` decimal(9,2) unsigned NOT NULL DEFAULT '5.00' COMMENT 'Puja minima para la subasta (en euros). Dos valores decimales admitidos.',
  `fechaLimite` timestamp NULL DEFAULT NULL COMMENT 'Fecha y hora (TIMESTAMP) limite para la subasta. Puede ser nulo.',
  PRIMARY KEY (`idSubasta`),
  KEY `idProducto` (`idProducto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de subastas.' AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `SUBASTA`
--

INSERT INTO `SUBASTA` (`idSubasta`, `idProducto`, `pujaMinima`, `fechaLimite`) VALUES
(1, 2, 50.00, '2013-11-25 23:00:00'),
(2, 3, 10.00, '2014-10-09 22:00:00'),
(3, 9, 5.00, '2014-10-19 22:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `USUARIO`
--

DROP TABLE IF EXISTS `USUARIO`;
CREATE TABLE IF NOT EXISTS `USUARIO` (
  `login` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Login del usuario',
  `password` varchar(60) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Password del usuario, almacenada con un hash Bcrypt',
  `rol` enum('admin','usuario') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'usuario' COMMENT 'Rol del usuario (Administrador o Usuario normal)',
  `email` varchar(255) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Email del usuario, unico (ie, no puede haber dos usuarios con el mismo email)',
  `nombre` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Nombre y apellidos del usuario. Puede ser nulo.',
  `direccion` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Direccion postal (completa) del usuario. Puede ser nulo.',
  `telefono` int(9) DEFAULT NULL COMMENT 'Telefono de contacto (fijo o movil) del usuario. Puede ser nulo.',
  PRIMARY KEY (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para almacenamiento de usuarios';

--
-- Volcado de datos para la tabla `USUARIO`
--

INSERT INTO `USUARIO` (`login`, `password`, `rol`, `email`, `nombre`, `direccion`, `telefono`) VALUES
('admin', '$2y$10$1loCTeg/upWPAfmDaiYq5.FprgzuBi63/K4QHBZvdtRSJqY/MxWQK', 'admin', 'admin@admin.es', 'admin', 'direccion de admin', 982111111),
('usuario1', '$2y$10$3U7X8sbpb369DvY9K7FUO.sD6k3/b7aaI4nu4MnjEAFsBlHqrAtaq', 'usuario', 'usuario1@correo.es', 'usuario1', 'direccion de usuario1', 988222222),
('usuario2', '$2y$10$dET8D/AsXiwCCYsv2hXvX.NRiBV1KWZlbyXlxVi65M76xe38svVci', 'usuario', 'usuario2@correo.es', 'usuario2', 'direccion de usuario2', 988333333),
('usuario3', '$2y$10$UFVbRtK3xvMhF48thfkCAuQgYbLAUPtBlTSU.vX821WSMyb5cfFAi', 'usuario', 'usuario3@correo.es', 'usuario3', 'direccion de usuario3', 988444444);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `VENTA`
--

DROP TABLE IF EXISTS `VENTA`;
CREATE TABLE IF NOT EXISTS `VENTA` (
  `idVenta` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de ventas. Auto incrementado.',
  `idProducto` int(10) unsigned NOT NULL COMMENT 'Identificador del producto en venta (clave foranea a PRODUCTO.idProducto).',
  `precio` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Precio de venta del producto (en euros).',
  `stock` int(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Cantidad de ejemplares del producto disponibles para venta.',
  PRIMARY KEY (`idVenta`),
  KEY `idProducto` (`idProducto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de ventas.' AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `VENTA`
--

INSERT INTO `VENTA` (`idVenta`, `idProducto`, `precio`, `stock`) VALUES
(1, 1, 500.00, 2),
(2, 6, 3000.00, 1),
(3, 11, 10.00, 5),
(4, 12, 0.30, 197);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `CALIFICACION`
--
ALTER TABLE `CALIFICACION`
  ADD CONSTRAINT `CALIFICACION_ibfk_4` FOREIGN KEY (`login`) REFERENCES `USUARIO` (`login`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `CALIFICACION_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `PRODUCTO` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `COMPRA`
--
ALTER TABLE `COMPRA`
  ADD CONSTRAINT `COMPRA_ibfk_4` FOREIGN KEY (`idPago`) REFERENCES `PAGO` (`idPago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `COMPRA_ibfk_2` FOREIGN KEY (`idVenta`) REFERENCES `VENTA` (`idVenta`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `COMPRA_ibfk_3` FOREIGN KEY (`login`) REFERENCES `USUARIO` (`login`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `PRODUCTO`
--
ALTER TABLE `PRODUCTO`
  ADD CONSTRAINT `PRODUCTO_ibfk_1` FOREIGN KEY (`propietario`) REFERENCES `USUARIO` (`login`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `PUJA`
--
ALTER TABLE `PUJA`
  ADD CONSTRAINT `PUJA_ibfk_3` FOREIGN KEY (`login`) REFERENCES `USUARIO` (`login`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `PUJA_ibfk_4` FOREIGN KEY (`idPago`) REFERENCES `PAGO` (`idPago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `PUJA_ibfk_5` FOREIGN KEY (`idSubasta`) REFERENCES `SUBASTA` (`idSubasta`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `SUBASTA`
--
ALTER TABLE `SUBASTA`
  ADD CONSTRAINT `SUBASTA_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `PRODUCTO` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `VENTA`
--
ALTER TABLE `VENTA`
  ADD CONSTRAINT `VENTA_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `PRODUCTO` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
