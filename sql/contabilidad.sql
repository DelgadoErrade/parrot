-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 08-08-2015 a las 02:27:46
-- Versión del servidor: 5.5.16
-- Versión de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `asovepar_condominio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblasientos`
--

CREATE TABLE IF NOT EXISTS `tblasientos` (
  `idasiento` bigint(20) unsigned NOT NULL,
  `descripcion_asiento` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`idasiento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblnivel_0`
--

CREATE TABLE IF NOT EXISTS `tblnivel_0` (
  `nivel_0` tinyint(3) unsigned NOT NULL,
  `descripcion_0` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`nivel_0`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `tblnivel_0`
--

INSERT INTO `tblnivel_0` (`nivel_0`, `descripcion_0`) VALUES
(1, 'ACTIVO'),
(2, 'PASIVO'),
(3, 'PATRIMONIO'),
(4, 'INGRESOS'),
(5, 'EGRESOS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblnivel_1`
--

CREATE TABLE IF NOT EXISTS `tblnivel_1` (
  `nivel_1` tinyint(3) unsigned NOT NULL,
  `nivel_0` tinyint(3) unsigned NOT NULL,
  `descripcion_1` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`nivel_1`),
  KEY `fk_reference_1` (`nivel_0`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `tblnivel_1`
--

INSERT INTO `tblnivel_1` (`nivel_1`, `nivel_0`, `descripcion_1`) VALUES
(11, 1, 'ACTIVOS CIRCULANTES'),
(12, 1, 'ACTIVOS FIJOS'),
(13, 1, 'CARGOS DIFERIDOS'),
(21, 2, 'PASIVO CIRCULANTE'),
(22, 2, 'PASIVO A LARGO PLAZO'),
(31, 3, 'FONDO DE RESERVAS'),
(32, 3, 'RESULTADO DEL EJERCICIO'),
(51, 5, 'GASTOS DE PERSONAL'),
(52, 5, 'GASTOS DEL PERSONAL DIRECTIVO'),
(53, 5, 'GASTOS DE ADMINISTRACION'),
(54, 5, 'SEGURIDAD PATRIMONIAL'),
(55, 5, 'MATERIALES Y SUMINISTROS'),
(56, 5, 'REPARACIÓN Y MANTENIMIENTO'),
(57, 5, 'GASTOS BANCARIOS'),
(58, 5, 'OTROS GASTOS DE ADMINISTRACIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblnivel_2`
--

CREATE TABLE IF NOT EXISTS `tblnivel_2` (
  `nivel_2` mediumint(8) unsigned NOT NULL,
  `nivel_1` tinyint(3) unsigned NOT NULL,
  `descripcion_2` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`nivel_2`),
  KEY `fk_reference_2` (`nivel_1`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `tblnivel_2`
--

INSERT INTO `tblnivel_2` (`nivel_2`, `nivel_1`, `descripcion_2`) VALUES
(111, 11, 'EFECTIVO'),
(112, 11, 'BANCOS'),
(113, 11, 'EFECTOS POR COBRAR'),
(114, 11, 'EFECTOS POR COBRAR'),
(121, 12, 'TANGIBLES'),
(131, 13, 'GASTOS DE ORGANIZACIÓN'),
(132, 13, 'PROGRAMAS DE COMPUTACIÓN'),
(211, 21, 'CUENTAS Y EFECTOS POR PAGAR'),
(221, 22, 'PROVISIÓN DE PRESTACIONES SOCIALES'),
(311, 31, 'RESERVA PARA CONTINGENCIAS'),
(321, 32, 'GANANCIAS O PÉRDIDAS DEL EJERCICIO ANTERIOR'),
(322, 32, 'GANANCIAS O PÉRDIDAS DEL EJERCICIO ACTUAL'),
(511, 51, 'REMUNERACIONES'),
(512, 51, 'BENEFICIOS SOCIALES Y CONTRACTUALES'),
(521, 52, 'GASTOS DE LA JUNTA DIRECTIVA'),
(522, 52, 'GASTOS DE ASAMBLEAS Y PROCESOS ELECTORALES'),
(531, 53, 'SERVICIOS PÚBLICOS'),
(532, 53, 'SERVICIOS PROFESIONALES Y TÉCNICOS'),
(541, 54, 'GASTOS DE PÓLIZA DE SEGURO'),
(551, 55, 'MATERIALES Y SUMINISTROS'),
(561, 56, 'REPARACIÓN Y MANTENIMIENTO'),
(571, 57, 'GASTOS BANCARIOS'),
(581, 58, 'OTROS GASTOS DE ADMINISTRACIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblnivel_3`
--

CREATE TABLE IF NOT EXISTS `tblnivel_3` (
  `nivel_3` smallint(5) unsigned NOT NULL,
  `nivel_2` mediumint(8) unsigned NOT NULL,
  `descripcion_3` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`nivel_3`),
  KEY `fk_reference_3` (`nivel_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `tblnivel_3`
--

INSERT INTO `tblnivel_3` (`nivel_3`, `nivel_2`, `descripcion_3`) VALUES
(11101, 111, 'CAJAS'),
(11201, 112, 'BANCO CUENTA CORRIENTE'),
(11301, 113, 'CUENTAS POR COBRAR X CONDOMINIO'),
(11401, 114, 'EFECTOS POR COBRAR'),
(12101, 121, 'TERRENOS'),
(12102, 121, 'MOBILIARIOS Y EQUIPOS'),
(12103, 121, 'VEHICULOS'),
(13101, 131, 'GASTOS DE ORGANIZACIÓN'),
(21101, 211, 'CUENTAS POR PAGAR'),
(21102, 211, 'EFECTOS POR PAGAR'),
(21103, 211, 'HONORARIOS PROFESIONALES POR PAGAR'),
(21104, 211, 'SUELDOS POR PAGAR'),
(21105, 211, 'CONTRIBUCIONES POR PAGAR'),
(22101, 221, 'PROVISIÓN DE PRESTACIONES SOCIALES'),
(31101, 311, 'RESERVA PARA CONTINGENCIAS'),
(32101, 321, 'GANANCIAS O PÉRDIDAS DEL EJERCICIO ANTERIOR'),
(32201, 322, 'GANANCIAS O PÉRDIDAS DEL EJERCICIO ACTUAL'),
(51101, 511, 'SUELDOS'),
(51102, 511, 'BONO DE TRANSPORTE'),
(51103, 511, 'BONO DE ALIMENTACIÓN'),
(51104, 511, 'HORAS EXTRAS'),
(51105, 511, 'OTRAS REMUNERACIONES Y BONIFICACIONES'),
(51201, 512, 'PRESTACIONES SOCIALES PERSONAL'),
(51202, 512, 'INTERESES SOBRE PRESTACIONES SOCIALES'),
(51203, 512, 'VACACIONES'),
(51204, 512, 'BONO VACACIONAL'),
(51205, 512, 'BONIFICACION DE FIN DE AÑO'),
(51206, 512, 'SEGURO SOCIAL OBLIGATORIO'),
(51207, 512, 'UNIFORMES'),
(51208, 512, 'CALZADOS'),
(52101, 521, 'DIETAS'),
(52102, 521, 'VIÁTICOS Y PASAJES'),
(52103, 521, 'GASTOS DE REPRESENTACIÓN'),
(52104, 521, 'GASTOS DE TRANSPORTE'),
(52105, 521, 'OTROS GASTOS DEL PERSONAL DIRECTIVO'),
(52201, 522, 'GASTOS DE NOTARÍA Y REGISTRO'),
(52202, 522, 'GASTOS DE REUNIONES Y ASAMBLEAS'),
(52203, 522, 'GASTOS DE COMISIÓN ELECTORAL'),
(52204, 522, 'GASTOS DE MATERIAL ELECTORAL'),
(52205, 522, 'OTROS GASTOS'),
(53101, 531, 'SERVICIO DE AGUA'),
(53102, 531, 'LUZ Y ASEO URBANO'),
(53103, 531, 'SERVICIO TELEFÓNICO'),
(53201, 532, 'PROCESAMIENTO DE DATOS'),
(53202, 532, 'ABOGADOS'),
(53203, 532, 'VIGILANCIA Y PROTECCION'),
(53204, 532, 'MANTENIMIENTO DE EQUIPOS'),
(53205, 532, 'ASEO Y LIMPIEZA'),
(54101, 541, 'SEGURO DE VEHÍCULOS'),
(54102, 541, 'SEGURO DE RESPONSABILIDAD CIVIL'),
(55101, 551, 'FORMAS IMPRESAS'),
(55102, 551, 'MATERIALES DE ASEO Y LIMPIEZA'),
(55103, 551, 'PAPELERÍA Y ÚTILES DE OFICINA'),
(56101, 561, 'EQUIPOS DE OFICINA'),
(57101, 571, 'GASTOS BANCARIOS'),
(57102, 571, 'COMISIONES BANCARIAS'),
(58101, 581, 'GASTOS DE COBRANZAS');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tblnivel_1`
--
ALTER TABLE `tblnivel_1`
  ADD CONSTRAINT `tblnivel_1_ibfk_1` FOREIGN KEY (`nivel_0`) REFERENCES `tblnivel_0` (`nivel_0`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tblnivel_2`
--
ALTER TABLE `tblnivel_2`
  ADD CONSTRAINT `tblnivel_2_ibfk_1` FOREIGN KEY (`nivel_1`) REFERENCES `tblnivel_1` (`nivel_1`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tblnivel_3`
--
ALTER TABLE `tblnivel_3`
  ADD CONSTRAINT `tblnivel_3_ibfk_1` FOREIGN KEY (`nivel_2`) REFERENCES `tblnivel_2` (`nivel_2`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
