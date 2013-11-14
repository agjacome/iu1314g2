SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- creacion de base de datos
CREATE DATABASE `BID_AND_SELL` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;

-- creacion de usuario
GRANT USAGE ON *.* TO 'bid_and_sell'@'localhost';
DROP USER 'bid_and_sell'@'localhost';
CREATE USER 'bid_and_sell'@'localhost' IDENTIFIED BY 'bid_and_sell';
GRANT ALL PRIVILEGES ON `BID_AND_SELL`.* TO 'bid_and_sell'@'localhost' WITH GRANT OPTION;

-- todas las consultas posteriores pertenecen a la base de datos BID_AND_SELL
USE `BID_AND_SELL`;

-- creacion de tabla Calificacion
CREATE TABLE IF NOT EXISTS `CALIFICACION` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de calificacion. Auto incrementado.',
  `idProducto` int(10) unsigned NOT NULL COMMENT 'Identificador del producto calificado (clave foranea a PRODUCTO.idProducto).',
  `login` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Login del usuario que ha realizado la calificacion (clave foranea a USUARIO.login). No debe ser NULO, se permite solo para continuar almacenando las puntuaciones despues de que un usuario haya sido eliminado (hacer un "SET NULL").',
  `puntuacion` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT 'Puntuacion dada por el usuario al producto. La base de datos lo limita de 0 a 255, la logica de la aplicacion debera velar por almacenar valores correctos.',
  `comentario` text COLLATE utf8_spanish_ci COMMENT 'Comentario dado junto a la puntuacion. Puede ser nulo.',
  PRIMARY KEY (`idCalificacion`),
  KEY `idProducto` (`idProducto`,`login`),
  KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de calificaciones de productos.' AUTO_INCREMENT=1 ;

-- crecion de tabla compra
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de compras.' AUTO_INCREMENT=1 ;

-- creacion de tabla pago
CREATE TABLE IF NOT EXISTS `PAGO` (
  `idPago` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de pago. Auto incrementado.',
  `metodoPago` enum('paypal','tarjeta') COLLATE utf8_spanish_ci NOT NULL COMMENT 'Metodo de pago establecido. Puede ser "tarjeta" para referirse a tarjeta de credito o "paypal" para realizacion de pagos a traves de paypal.',
  `numTarjeta` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Numero de tarjeta de credito. Puede ser nulo, deberia tener valor solamente si dicho metodo ha sido seleccionado.',
  `cuentaPaypal` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Cuenta de paypal. Puede ser nulo, deberia tener valor solamente si dicho metodo ha sido seleccionado.',
  `comision` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Comision recibida por la empresa tras la realizacion del pago. Se descontara al pago recibido por el vendedor. Calculado en base al porcentaje de comision establecido en el momento del pago.',
  PRIMARY KEY (`idPago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de pagos de pujas y compras.' AUTO_INCREMENT=1 ;

-- creacion de tabla producto
CREATE TABLE IF NOT EXISTS `PRODUCTO` (
  `idProducto` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de producto. Auto incrementado.',
  `propietario` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Login del propietario (clave foranea a USUARIO.login).',
  `estado` enum('pendiente','subasta','venta') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'pendiente' COMMENT 'Estado del producto: en venta, en subasta o aun no establecido (pendiende).',
  `nombre` varchar(255) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del producto.',
  `descripcion` text COLLATE utf8_spanish_ci COMMENT 'Descripcion del producto. Puede ser nulo.',
  PRIMARY KEY (`idProducto`),
  KEY `propietario` (`propietario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para almacenamiento de productos.' AUTO_INCREMENT=1 ;

-- creacion de tabla puja
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

-- creacion de tabla subasta
CREATE TABLE IF NOT EXISTS `SUBASTA` (
  `idSubasta` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de subasta. Auto incrementado.',
  `idProducto` int(10) unsigned NOT NULL COMMENT 'Identificador del producto subastado (clave foranea a PRODUCTO.idProducto).',
  `pujaMinima` decimal(9,2) unsigned NOT NULL DEFAULT '5.00' COMMENT 'Puja minima para la subasta (en euros). Dos valores decimales admitidos.',
  `fechaLimite` timestamp NULL DEFAULT NULL COMMENT 'Fecha y hora (TIMESTAMP) limite para la subasta. Puede ser nulo.',
  PRIMARY KEY (`idSubasta`),
  KEY `idProducto` (`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de subastas.' AUTO_INCREMENT=1 ;

-- creacion de tabla usuario
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

-- creacion de tabla venta
CREATE TABLE IF NOT EXISTS `VENTA` (
  `idVenta` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de ventas. Auto incrementado.',
  `idProducto` int(10) unsigned NOT NULL COMMENT 'Identificador del producto en venta (clave foranea a PRODUCTO.idProducto).',
  `precio` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Precio de venta del producto (en euros).',
  `stock` int(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Cantidad de ejemplares del producto disponibles para venta.',
  PRIMARY KEY (`idVenta`),
  KEY `idProducto` (`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla para el almacenamiento de ventas.' AUTO_INCREMENT=1 ;

-- claves foraneas para calificacion
ALTER TABLE `CALIFICACION`
  ADD CONSTRAINT `CALIFICACION_ibfk_4` FOREIGN KEY (`login`) REFERENCES `USUARIO` (`login`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `CALIFICACION_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `PRODUCTO` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;

-- claves foraneas para compra
ALTER TABLE `COMPRA`
  ADD CONSTRAINT `COMPRA_ibfk_4` FOREIGN KEY (`idPago`) REFERENCES `PAGO` (`idPago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `COMPRA_ibfk_2` FOREIGN KEY (`idVenta`) REFERENCES `VENTA` (`idVenta`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `COMPRA_ibfk_3` FOREIGN KEY (`login`) REFERENCES `USUARIO` (`login`) ON DELETE NO ACTION ON UPDATE CASCADE;

-- claves foraneas para producto
ALTER TABLE `PRODUCTO`
  ADD CONSTRAINT `PRODUCTO_ibfk_1` FOREIGN KEY (`propietario`) REFERENCES `USUARIO` (`login`) ON DELETE CASCADE ON UPDATE CASCADE;

-- claves foraneas para puja
ALTER TABLE `PUJA`
  ADD CONSTRAINT `PUJA_ibfk_3` FOREIGN KEY (`login`) REFERENCES `USUARIO` (`login`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `PUJA_ibfk_4` FOREIGN KEY (`idPago`) REFERENCES `PAGO` (`idPago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `PUJA_ibfk_5` FOREIGN KEY (`idSubasta`) REFERENCES `SUBASTA` (`idSubasta`) ON DELETE SET NULL ON UPDATE CASCADE;

-- claves foraneas para subasta
ALTER TABLE `SUBASTA`
  ADD CONSTRAINT `SUBASTA_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `PRODUCTO` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;

-- claves foraneas para venta
ALTER TABLE `VENTA`
  ADD CONSTRAINT `VENTA_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `PRODUCTO` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;
