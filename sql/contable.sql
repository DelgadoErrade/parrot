-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Servidor: localhost
-- Tiempo de generación: 27-09-2008 a las 11:47:35
-- Versión del servidor: 5.0.45
-- Versión de PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Base de datos: `contab`
-- 

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblasientos`
-- 

CREATE TABLE `tblasientos` (
  `idAsiento` bigint(20) unsigned NOT NULL,
  `movimiento` bigint(20) unsigned NOT NULL,
  `nivel_4` smallint(5) unsigned NOT NULL,
  `descripcion_asiento` varchar(100) collate latin1_spanish_ci NOT NULL,
  `posicion` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`idAsiento`),
  KEY `fk_nivel4` (`nivel_4`),
  KEY `fk_movimientos_contables` (`movimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Movimientos contables';

-- 
-- Volcar la base de datos para la tabla `tblasientos`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblmovimientos_contables`
-- 

CREATE TABLE `tblmovimientos_contables` (
  `movimiento` bigint(20) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `descripcion_movimiento` varchar(100) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`movimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- 
-- Volcar la base de datos para la tabla `tblmovimientos_contables`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblnivel_0`
-- 

CREATE TABLE `tblnivel_0` (
  `nivel_0` smallint(6) unsigned NOT NULL,
  `descripcion_0` varchar(10) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`nivel_0`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Cuentas de orden 0';

-- 
-- Volcar la base de datos para la tabla `tblnivel_0`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblnivel_1`
-- 

CREATE TABLE `tblnivel_1` (
  `nivel_1` smallint(5) unsigned NOT NULL,
  `nivel_0` smallint(5) unsigned NOT NULL,
  `descripcion_1` varchar(30) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`nivel_1`),
  KEY `fk_0` (`nivel_0`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Cuentas de PRIMER ORDEN';

-- 
-- Volcar la base de datos para la tabla `tblnivel_1`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblnivel_2`
-- 

CREATE TABLE `tblnivel_2` (
  `nivel_2` smallint(5) unsigned NOT NULL,
  `nivel_1` smallint(5) unsigned NOT NULL,
  `descripcion_2` varchar(50) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`nivel_2`),
  KEY `fk_1` (`nivel_1`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Cuentas de SEGUNDO ORDEN';

-- 
-- Volcar la base de datos para la tabla `tblnivel_2`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblnivel_3`
-- 

CREATE TABLE `tblnivel_3` (
  `nivel_3` smallint(5) unsigned NOT NULL,
  `nivel_2` smallint(5) unsigned NOT NULL,
  `descripcion_3` varchar(50) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`nivel_3`),
  KEY `fk_3` (`nivel_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Cuentas de TERCER ORDEN';

-- 
-- Volcar la base de datos para la tabla `tblnivel_3`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tblnivel_4`
-- 

CREATE TABLE `tblnivel_4` (
  `nivel_4` smallint(5) unsigned NOT NULL,
  `nivel_3` smallint(5) unsigned NOT NULL,
  `descripcion_4` varchar(50) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`nivel_4`),
  KEY `fk_4` (`nivel_3`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Cuentas de TERCER ORDEN';

-- 
-- Volcar la base de datos para la tabla `tblnivel_4`
-- 


-- 
-- Filtros para las tablas descargadas (dump)
-- 

-- 
-- Filtros para la tabla `tblasientos`
-- 
ALTER TABLE `tblasientos`
  ADD CONSTRAINT `fk_movimientos_contables` FOREIGN KEY (`movimiento`) REFERENCES `tblmovimientos_contables` (`movimiento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nivel4` FOREIGN KEY (`nivel_4`) REFERENCES `tblnivel_4` (`nivel_4`) ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `tblnivel_1`
-- 
ALTER TABLE `tblnivel_1`
  ADD CONSTRAINT `fk_0` FOREIGN KEY (`nivel_0`) REFERENCES `tblnivel_0` (`nivel_0`) ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `tblnivel_2`
-- 
ALTER TABLE `tblnivel_2`
  ADD CONSTRAINT `fk_1` FOREIGN KEY (`nivel_1`) REFERENCES `tblnivel_1` (`nivel_1`) ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `tblnivel_3`
-- 
ALTER TABLE `tblnivel_3`
  ADD CONSTRAINT `fk_3` FOREIGN KEY (`nivel_2`) REFERENCES `tblnivel_2` (`nivel_2`) ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `tblnivel_4`
-- 
ALTER TABLE `tblnivel_4`
  ADD CONSTRAINT `fk_4` FOREIGN KEY (`nivel_3`) REFERENCES `tblnivel_3` (`nivel_3`) ON UPDATE CASCADE;
