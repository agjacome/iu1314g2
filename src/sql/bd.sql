-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny6
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 15-05-2013 a las 18:12:28
-- Versión del servidor: 5.0.51
-- Versión de PHP: 5.2.6-1+lenny9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `BIDDINGANDSALE`
--

DROP DATABASE IF EXISTS `BIDDINGANDSALE`;
CREATE DATABASE `BIDDINGANDSALE` DEFAULT CHARACTER SET latin1 COLLATE latin1_spanish_ci;
USE `BIDDINGANDSALE`;


--
-- USER iujulio
--
GRANT USAGE ON *.* TO 'iuser'@'localhost';
   DROP USER 'iuser'@'localhost';

CREATE USER 'iuser'@'localhost' IDENTIFIED BY  'iuser';

GRANT USAGE ON * . * TO  'iuser'@'localhost' IDENTIFIED BY  'iuser' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON  `BIDDINGANDSALE` . * TO  'iuser'@'localhost' WITH GRANT OPTION ;

-- --------------------------------------------------------


--
-- Estructura de tabla para la tabla `PRODUCTO`
--

DROP TABLE IF EXISTS `PRODUCTO`;
CREATE TABLE IF NOT EXISTS `PRODUCTO` (
  `idProducto` varchar(10) NOT NULL COMMENT 'ID DEL PRODUCTO',
  `descripcionProducto` text collate latin1_spanish_ci NOT NULL COMMENT 'DESCRIPCION DEL PRODUCTO',
  `cantidadProducto` int(3) collate latin1_spanish_ci NOT NULL COMMENT 'NUMERO DE PRODUCTOS EN STOCK',
  `tipo` varchar(10) collate latin1_spanish_ci NOT NULL COMMENT 'TIPO DE PRODUCTO',
  `nombreProducto` varchar(10) collate latin1_spanish_ci NOT NULL COMMENT 'NOMBRE DEL PRODUCTO',
  PRIMARY KEY  (`idProducto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;


--
-- Estructura de tabla para la tabla `SUBASTA`
--

DROP TABLE IF EXISTS `SUBASTA`;
CREATE TABLE IF NOT EXISTS `SUBASTA` (
  `idSubasta` varchar (10)NOT NULL,
  `pujaMinima` float(5,2) NOT NULL,
  `nombreSubasta` varchar(10) collate latin1_spanish_ci NOT NULL,
  `fechaSubasta` Date NOT NULL,
  `tiempoLimite` Datetime NOT NULL,
  `descripcionSubasta` Text collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`idSubasta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;


--
-- Estructura de tabla para la tabla `PUJA`
--

DROP TABLE IF EXISTS `PUJA`;
CREATE TABLE IF NOT EXISTS `PUJA` (
  `idSubasta` varchar(10) NOT NULL,
  `secuencia` int(3) NOT NULL,
  `fechaPuja` date NOT NULL,
  `hora` time NOT NULL,
  `cantidadPujada` float(5,2) NOT NULL,
  `login` varchar(10) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`idSubasta`,`secuencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;


--
-- Estructura de tabla para la tabla `VENTA`
--

DROP TABLE IF EXISTS `VENTA`;
CREATE TABLE IF NOT EXISTS `VENTA` (
  `idVenta` varchar(10) NOT NULL,
  `precio` float(5,2) NOT NULL,
  `idProducto` varchar(10) collate latin1_spanish_ci NOT NULL,
  `fechaLimite` date NOT NULL,
  `login` varchar(10) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`idVenta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Estructura de tabla para la tabla `USUARIO`
--

DROP TABLE IF EXISTS `USUARIO`;
CREATE TABLE IF NOT EXISTS `USUARIO` (
  `login` varchar(10) NOT NULL,
  `rol` tinyint(1) NOT NULL default '0' COMMENT 'administrador a 1, usuario normal a 0',
  `password` varchar(15) collate latin1_spanish_ci NOT NULL,
  `cuentaBancaria` int(20) NOT NULL,
  `email` varchar(20) collate latin1_spanish_ci NOT NULL,
  `cuentaPaypal` varchar(20) collate latin1_spanish_ci NOT NULL,
  `fechaNacimiento` DATE NOT NULL,
  `nombre` varchar(20) collate latin1_spanish_ci NOT NULL,
  `apellidos` varchar(20) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Estructura de tabla para la tabla `PAGO`
--

DROP TABLE IF EXISTS `PAGO`;
CREATE TABLE IF NOT EXISTS `PAGO` (
  `idPago` varchar(10) NOT NULL,
  `idVenta` varchar(10) collate latin1_spanish_ci NOT NULL,
    PRIMARY KEY  (`idPago`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Estructura de tabla para la tabla `TELEFONO`
--

DROP TABLE IF EXISTS `TELEFONO`;
CREATE TABLE IF NOT EXISTS `TELEFONO` (
  `login` varchar(10) NOT NULL,
  `telefono` int(9) NOT NULL,
    PRIMARY KEY  (`login`,`telefono`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Estructura de tabla para la tabla `MOVIL`
--

DROP TABLE IF EXISTS `MOVIL`;
CREATE TABLE IF NOT EXISTS `MOVIL` (
  `login` varchar(10) NOT NULL,
  `movil` int(9) NOT NULL,
    PRIMARY KEY  (`login`,`movil`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;




-- --------------------------------------------------------
