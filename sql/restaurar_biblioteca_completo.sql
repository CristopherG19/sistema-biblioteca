-- =====================================================
-- SCRIPT DE RESTAURACIÓN COMPLETA - SISTEMA BIBLIOTECA
-- =====================================================
-- 
-- DESCRIPCIÓN: Script único para restaurar completamente
--              la base de datos del Sistema de Biblioteca
-- 
-- CONTENIDO:
-- - 15 tablas con estructura y datos
-- - 73 procedimientos almacenados
-- - 2 vistas del sistema
-- - Configuraciones de charset UTF8MB4
-- - Claves foráneas y restricciones
-- 
-- FECHA: 30 de Septiembre de 2025
-- VERSIÓN: BiblioSys v1.0
-- 
-- INSTRUCCIONES DE USO:
-- 1. MySQL Command Line: mysql -u root -p < restaurar_biblioteca_completo.sql
-- 2. phpMyAdmin: Importar este archivo
-- 3. MySQL Workbench: Ejecutar como script
-- 
-- =====================================================


CREATE DATABASE  IF NOT EXISTS `biblioteca_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `biblioteca_db`;
-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: biblioteca_db
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activa` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idCategoria`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_activa` (`activa`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Ficción','Novelas y obras de ficción','2025-09-23 21:00:25','2025-09-23 21:50:38',1),(2,'Ciencia','Libros de ciencias naturales','2025-09-23 21:00:25','2025-09-23 21:50:38',1),(3,'Historia','Libros de historia universal','2025-09-23 21:00:25','2025-09-23 21:50:38',1),(4,'Tecnología','Libros de tecnología e informática','2025-09-23 21:00:25','2025-09-23 21:50:38',1),(5,'Matemáticas','Libros de matemáticas y estadística','2025-09-23 21:00:25','2025-09-23 21:50:38',1),(11,'CATEGORIA PRUEBA','DESCRIP PRUEBA2 ACTUALIZADO','2025-09-23 21:35:36','2025-09-23 21:51:02',0);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historiallectura`
--

DROP TABLE IF EXISTS `historiallectura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historiallectura` (
  `idHistorial` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idLibro` int NOT NULL,
  `fecha_inicio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `tipo` enum('Prestamo','Lectura','Reserva') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Prestamo',
  `calificacion` int DEFAULT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idHistorial`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_libro` (`idLibro`),
  KEY `idx_fecha_inicio` (`fecha_inicio`),
  KEY `idx_tipo` (`tipo`),
  CONSTRAINT `historiallectura_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `historiallectura_ibfk_2` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE,
  CONSTRAINT `historiallectura_chk_1` CHECK (((`calificacion` >= 1) and (`calificacion` <= 5)))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historiallectura`
--

LOCK TABLES `historiallectura` WRITE;
/*!40000 ALTER TABLE `historiallectura` DISABLE KEYS */;
INSERT INTO `historiallectura` VALUES (4,6,18,'2025-09-23 21:34:04',NULL,'Lectura',NULL,NULL);
/*!40000 ALTER TABLE `historiallectura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interesesusuario`
--

DROP TABLE IF EXISTS `interesesusuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interesesusuario` (
  `idInteresUsuario` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idCategoria` int NOT NULL,
  `fecha_agregado` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idInteresUsuario`),
  UNIQUE KEY `unique_interes` (`idUsuario`,`idCategoria`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_categoria` (`idCategoria`),
  CONSTRAINT `interesesusuario_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `interesesusuario_ibfk_2` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interesesusuario`
--

LOCK TABLES `interesesusuario` WRITE;
/*!40000 ALTER TABLE `interesesusuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `interesesusuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libros`
--

DROP TABLE IF EXISTS `libros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libros` (
  `idLibro` int NOT NULL AUTO_INCREMENT,
  `idCategoria` int NOT NULL,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `autor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `editorial` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio` int DEFAULT NULL,
  `isbn` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `disponible` int NOT NULL DEFAULT '0',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `portada` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivo_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_adicion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  `numero_paginas` int DEFAULT NULL,
  `tamano_archivo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_subida` datetime DEFAULT NULL,
  PRIMARY KEY (`idLibro`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `idx_titulo` (`titulo`),
  KEY `idx_autor` (`autor`),
  KEY `idx_isbn` (`isbn`),
  KEY `idx_categoria` (`idCategoria`),
  KEY `idx_disponible` (`disponible`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libros`
--

LOCK TABLES `libros` WRITE;
/*!40000 ALTER TABLE `libros` DISABLE KEYS */;
INSERT INTO `libros` VALUES (13,1,'1984','George Orwell','Debolsillo',1949,'978-84-9908-567-1',10,10,'Una novela distópica que describe una sociedad totalitaria donde el Gran Hermano vigila constantemente a los ciudadanos.',NULL,NULL,'2025-09-23 21:00:25','2025-09-23 21:56:28',1,NULL,NULL,NULL),(14,2,'El origen de las especies','Charles Darwin','Alianza Editorial',1859,'978-84-206-5600-2',2,2,'Obra fundamental de la biología que presenta la teoría de la evolución por selección natural.',NULL,NULL,'2025-09-23 21:00:25','2025-09-23 21:00:25',1,NULL,NULL,NULL),(15,3,'Sapiens: De animales a dioses','Yuval Noah Harari','Debate',2011,'978-84-9992-622-3',4,4,'Una exploración fascinante de cómo el Homo sapiens llegó a dominar el mundo.',NULL,NULL,'2025-09-23 21:00:25','2025-09-23 21:00:25',1,NULL,NULL,NULL),(16,4,'Clean Code','Robert C. Martin','Prentice Hall',2008,'978-0-13-235088-4',2,2,'Una guía para escribir código limpio y mantenible en programación.',NULL,NULL,'2025-09-23 21:00:25','2025-09-23 21:00:25',1,NULL,NULL,NULL),(17,5,'Cálculo Diferencial e Integral','Luis Leithold','McGraw-Hill',1998,'978-970-10-2754-5',3,3,'Libro de texto clásico para el estudio del cálculo diferencial e integral.',NULL,NULL,'2025-09-23 21:00:25','2025-09-23 21:00:25',1,NULL,NULL,NULL),(18,1,'LIBRO DE PRUEBA','AUTOR PRUEBA','EDITORIAL PRUEBA',2025,'123-4-56-789101-1',20,20,'DESCRIPCION LIBRO PUEBA',NULL,'libro_18_1758681227.pdf','2025-09-23 21:33:07','2025-09-23 21:34:58',0,1,'235971','2025-09-23 21:33:47'),(20,11,'LIBRO DE PRUEBA1','AUTOR PRUEBA','EDITORIAL PRUEBA',2025,'123-4-56-789101-112',10,9,'DDDDD',NULL,'libro_20_1758681681.pdf','2025-09-23 21:41:21','2025-09-23 21:41:54',1,1,'235971','2025-09-23 21:41:21');
/*!40000 ALTER TABLE `libros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `librosfavoritos`
--

DROP TABLE IF EXISTS `librosfavoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `librosfavoritos` (
  `idFavorito` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idLibro` int NOT NULL,
  `fecha_agregado` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFavorito`),
  UNIQUE KEY `unique_favorito` (`idUsuario`,`idLibro`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_libro` (`idLibro`),
  CONSTRAINT `librosfavoritos_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `librosfavoritos_ibfk_2` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `librosfavoritos`
--

LOCK TABLES `librosfavoritos` WRITE;
/*!40000 ALTER TABLE `librosfavoritos` DISABLE KEYS */;
/*!40000 ALTER TABLE `librosfavoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multas`
--

DROP TABLE IF EXISTS `multas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `multas` (
  `idMulta` int NOT NULL AUTO_INCREMENT,
  `idPrestamo` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pagada` tinyint(1) DEFAULT '0',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_pago` datetime DEFAULT NULL,
  PRIMARY KEY (`idMulta`),
  KEY `idx_prestamo` (`idPrestamo`),
  KEY `idx_pagada` (`pagada`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  CONSTRAINT `multas_ibfk_1` FOREIGN KEY (`idPrestamo`) REFERENCES `prestamos` (`idPrestamo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multas`
--

LOCK TABLES `multas` WRITE;
/*!40000 ALTER TABLE `multas` DISABLE KEYS */;
/*!40000 ALTER TABLE `multas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prestamos` (
  `idPrestamo` int NOT NULL AUTO_INCREMENT,
  `idLibro` int NOT NULL,
  `idUsuario` int NOT NULL,
  `fechaPrestamo` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaDevolucionEsperada` datetime NOT NULL,
  `fechaDevolucionReal` datetime DEFAULT NULL,
  `estado` enum('Activo','Devuelto','Vencido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `multa` decimal(10,2) DEFAULT '0.00',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPrestamo`),
  KEY `idx_libro` (`idLibro`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_prestamo` (`fechaPrestamo`),
  KEY `idx_fecha_devolucion` (`fechaDevolucionEsperada`),
  KEY `idx_prestamos_usuario_estado` (`idUsuario`,`estado`),
  KEY `idx_prestamos_libro_estado` (`idLibro`,`estado`),
  KEY `idx_prestamos_fecha_estado` (`fechaPrestamo`,`estado`),
  CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE RESTRICT,
  CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamos`
--

LOCK TABLES `prestamos` WRITE;
/*!40000 ALTER TABLE `prestamos` DISABLE KEYS */;
INSERT INTO `prestamos` VALUES (6,13,7,'2025-09-23 00:00:00','2025-09-27 00:00:00',NULL,'Activo','LIBRO PRESTADO PRUEBA',0.00,'2025-09-23 21:37:29','2025-09-23 21:37:29'),(7,13,7,'2025-09-23 21:40:11','2025-10-09 00:00:00',NULL,'Activo','',0.00,'2025-09-23 21:40:11','2025-09-23 21:40:11'),(8,13,7,'2025-09-23 21:40:16','2025-10-09 00:00:00',NULL,'Activo','',0.00,'2025-09-23 21:40:16','2025-09-23 21:40:16'),(9,20,7,'2025-09-23 21:41:54','2025-10-09 00:00:00',NULL,'Activo','DDDDDD',0.00,'2025-09-23 21:41:54','2025-09-23 21:41:54');
/*!40000 ALTER TABLE `prestamos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `idRol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Bibliotecario','Administrador del sistema','2025-09-23 20:56:02'),(2,'Lector','Usuario que puede solicitar préstamos','2025-09-23 20:56:02');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes_prestamo`
--

DROP TABLE IF EXISTS `solicitudes_prestamo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes_prestamo` (
  `idSolicitud` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `libro_id` int NOT NULL,
  `fecha_solicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','Aprobada','Rechazada','Convertida') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `observaciones_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `observaciones_bibliotecario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_respuesta` datetime DEFAULT NULL,
  `bibliotecario_id` int DEFAULT NULL,
  `prestamo_id` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSolicitud`),
  KEY `prestamo_id` (`prestamo_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_solicitud` (`fecha_solicitud`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_libro` (`libro_id`),
  KEY `idx_bibliotecario` (`bibliotecario_id`),
  KEY `idx_solicitudes_estado_fecha` (`estado`,`fecha_solicitud`),
  CONSTRAINT `solicitudes_prestamo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_prestamo_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_prestamo_ibfk_3` FOREIGN KEY (`bibliotecario_id`) REFERENCES `usuarios` (`idUsuario`) ON DELETE SET NULL,
  CONSTRAINT `solicitudes_prestamo_ibfk_4` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`idPrestamo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_prestamo`
--

LOCK TABLES `solicitudes_prestamo` WRITE;
/*!40000 ALTER TABLE `solicitudes_prestamo` DISABLE KEYS */;
INSERT INTO `solicitudes_prestamo` VALUES (2,7,13,'2025-09-23 21:14:40','Convertida','','','2025-09-23 21:40:11',6,7,'2025-09-23 21:14:40','2025-09-23 21:40:11'),(3,7,13,'2025-09-23 21:39:46','Convertida','','','2025-09-23 21:40:16',6,8,'2025-09-23 21:39:46','2025-09-23 21:40:16'),(4,7,20,'2025-09-23 21:41:35','Convertida','','DDDDDD','2025-09-23 21:41:54',6,9,'2025-09-23 21:41:35','2025-09-23 21:41:54');
/*!40000 ALTER TABLE `solicitudes_prestamo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudesampliacion`
--

DROP TABLE IF EXISTS `solicitudesampliacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudesampliacion` (
  `idSolicitud` int NOT NULL AUTO_INCREMENT,
  `idPrestamo` int NOT NULL,
  `diasAdicionales` int NOT NULL DEFAULT '7',
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fechaSolicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaRespuesta` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `respuestaBibliotecario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idBibliotecario` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSolicitud`),
  KEY `idx_prestamo` (`idPrestamo`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_solicitud` (`fechaSolicitud`),
  KEY `idx_bibliotecario` (`idBibliotecario`),
  KEY `idx_ampliaciones_estado_fecha` (`estado`,`fechaSolicitud`),
  CONSTRAINT `solicitudesampliacion_ibfk_1` FOREIGN KEY (`idPrestamo`) REFERENCES `prestamos` (`idPrestamo`) ON DELETE CASCADE,
  CONSTRAINT `solicitudesampliacion_ibfk_2` FOREIGN KEY (`idBibliotecario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudesampliacion`
--

LOCK TABLES `solicitudesampliacion` WRITE;
/*!40000 ALTER TABLE `solicitudesampliacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitudesampliacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` int NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuario` (`usuario`),
  KEY `idx_email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `roles` (`idRol`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (6,'Administrador','Sistema','admin','$2y$10$K9zaWrnglkewrYSGVvXjae39lPJnFz6ttmHw7VBZ/IC.KOfOHFovi',1,'admin@biblioteca.com','123456789',NULL,'2025-09-23 20:56:02','2025-09-23 21:29:06',1),(7,'Usuario','Prueba','usuario','$2y$10$0QTrhZs6FIXYQGLi0bxWpemWFpNz5Nf3xCCGYHNUGitMyhqYQRvem',2,'usuario@biblioteca.com','987654321',NULL,'2025-09-23 20:56:02','2025-09-23 21:30:20',1),(8,'USUARIO PRUEBA',NULL,'USERPRUEBA','$2y$10$l1SXtrkr8k497TVaV3PTY.OZybAPVqx.5Fm42Kh.QurRCTNYEd38G',2,'USER@GMAIL.COM','987466421',NULL,'2025-09-23 21:43:33','2025-09-23 21:43:49',1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_estadisticas_generales`
--

DROP TABLE IF EXISTS `vista_estadisticas_generales`;
/*!50001 DROP VIEW IF EXISTS `vista_estadisticas_generales`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_estadisticas_generales` AS SELECT 
 1 AS `total_usuarios`,
 1 AS `total_libros`,
 1 AS `total_prestamos`,
 1 AS `prestamos_activos`,
 1 AS `prestamos_devueltos`,
 1 AS `prestamos_vencidos`,
 1 AS `total_solicitudes`,
 1 AS `solicitudes_pendientes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_prestamos_activos`
--

DROP TABLE IF EXISTS `vista_prestamos_activos`;
/*!50001 DROP VIEW IF EXISTS `vista_prestamos_activos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_prestamos_activos` AS SELECT 
 1 AS `idPrestamo`,
 1 AS `fechaPrestamo`,
 1 AS `fechaDevolucionEsperada`,
 1 AS `observaciones`,
 1 AS `usuario_nombre`,
 1 AS `usuario_apellido`,
 1 AS `usuario_email`,
 1 AS `libro_titulo`,
 1 AS `libro_autor`,
 1 AS `libro_isbn`,
 1 AS `categoria_nombre`,
 1 AS `dias_restantes`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'biblioteca_db'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_actualizar_categoria` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_categoria`(
                IN p_id INT,
                IN p_nombre VARCHAR(100),
                IN p_descripcion TEXT
            )
BEGIN
                UPDATE Categorias 
                SET nombre = p_nombre,
                    descripcion = p_descripcion,
                    fecha_actualizacion = NOW()
                WHERE idCategoria = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_actualizar_libro` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_libro`(
                IN p_id INT,
                IN p_idCategoria INT,
                IN p_titulo VARCHAR(200),
                IN p_autor VARCHAR(100),
                IN p_editorial VARCHAR(100),
                IN p_anio INT,
                IN p_isbn VARCHAR(20),
                IN p_stock INT,
                IN p_disponible INT,
                IN p_descripcion TEXT
            )
BEGIN
                UPDATE Libros 
                SET idCategoria = p_idCategoria,
                    titulo = p_titulo,
                    autor = p_autor,
                    editorial = p_editorial,
                    anio = p_anio,
                    isbn = p_isbn,
                    stock = p_stock,
                    disponible = p_disponible,
                    descripcion = p_descripcion,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_actualizar_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_usuario`(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_usuario VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_rol INT,
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20)
)
BEGIN
    DECLARE v_usuario_existe INT DEFAULT 0;
    DECLARE v_email_existe INT DEFAULT 0;
    DECLARE v_error_message VARCHAR(255) DEFAULT '';
    
    -- Verificar si el nombre de usuario ya existe en otro registro
    SELECT COUNT(*) INTO v_usuario_existe
    FROM Usuarios 
    WHERE usuario = p_usuario AND idUsuario != p_id;
    
    -- Verificar si el email ya existe en otro registro
    SELECT COUNT(*) INTO v_email_existe
    FROM Usuarios 
    WHERE email = p_email AND idUsuario != p_id;
    
    -- Si hay duplicados, devolver error
    IF v_usuario_existe > 0 THEN
        SET v_error_message = 'El nombre de usuario ya está registrado por otro usuario';
        SELECT 0 as success, v_error_message as message;
    ELSEIF v_email_existe > 0 THEN
        SET v_error_message = 'El email ya está registrado por otro usuario';
        SELECT 0 as success, v_error_message as message;
    ELSE
        -- Actualizar el usuario (sin fecha_actualizacion que no existe)
        UPDATE Usuarios 
        SET nombre = p_nombre,
            usuario = p_usuario,
            password = p_password,
            rol = p_rol,
            email = p_email,
            telefono = p_telefono
        WHERE idUsuario = p_id;
        
        SELECT 1 as success, 'Usuario actualizado exitosamente' as message, ROW_COUNT() as affected_rows;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ampliacion_aprobar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_aprobar`(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    DECLARE v_prestamo_id INT;
    DECLARE v_dias_adicionales INT;
    DECLARE v_fecha_actual DATETIME;
    
    -- Obtener datos de la solicitud
    SELECT idPrestamo, diasAdicionales INTO v_prestamo_id, v_dias_adicionales
    FROM solicitudesampliacion
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    IF v_prestamo_id IS NOT NULL THEN
        -- Obtener fecha actual de devolución
        SELECT fechaDevolucionEsperada INTO v_fecha_actual
        FROM prestamos
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar fecha de devolución
        UPDATE prestamos
        SET fechaDevolucionEsperada = DATE_ADD(v_fecha_actual, INTERVAL v_dias_adicionales DAY),
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | Ampliación: Ampliado por ', v_dias_adicionales, ' días. Motivo: ', p_respuesta)
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar solicitud
        UPDATE solicitudesampliacion
        SET estado = 'Aprobada',
            idBibliotecario = p_bibliotecario_id,
            respuestaBibliotecario = p_respuesta,
            fechaRespuesta = NOW()
        WHERE idSolicitud = p_solicitud_id;
        
        SELECT 'success' as status, 'Ampliación aprobada exitosamente' as message;
    ELSE
        SELECT 'error' as status, 'Solicitud no encontrada o ya procesada' as message;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ampliacion_obtener_solicitudes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_obtener_solicitudes`(IN p_estado VARCHAR(20))
BEGIN
    SELECT 
        sa.idSolicitud,
        sa.idPrestamo,
        sa.diasAdicionales,
        sa.motivo,
        sa.fechaSolicitud,
        sa.estado,
        sa.respuestaBibliotecario,
        sa.fechaRespuesta,
        p.fechaDevolucionEsperada,
        u.nombre as usuario_nombre,
        u.apellido as usuario_apellido,
        l.titulo as libro_titulo,
        l.autor as libro_autor
    FROM solicitudesampliacion sa
    INNER JOIN prestamos p ON sa.idPrestamo = p.idPrestamo
    INNER JOIN usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN libros l ON p.idLibro = l.idLibro
    WHERE (p_estado IS NULL OR sa.estado = p_estado)
    ORDER BY sa.fechaSolicitud DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ampliacion_rechazar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_rechazar`(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    UPDATE solicitudesampliacion
    SET estado = 'Rechazada',
        idBibliotecario = p_bibliotecario_id,
        respuestaBibliotecario = p_respuesta,
        fechaRespuesta = NOW()
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    SELECT 'success' as status, 'Solicitud rechazada' as message;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ampliacion_solicitar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_solicitar`(
    IN p_prestamo_id INT,
    IN p_dias_adicionales INT,
    IN p_motivo TEXT
)
BEGIN
    DECLARE v_existe INT DEFAULT 0;
    
    -- Verificar que el préstamo existe y está activo
    SELECT COUNT(*) INTO v_existe
    FROM prestamos
    WHERE idPrestamo = p_prestamo_id AND fechaDevolucionReal IS NULL;
    
    IF v_existe > 0 THEN
        INSERT INTO solicitudesampliacion (idPrestamo, diasAdicionales, motivo, estado, fechaSolicitud)
        VALUES (p_prestamo_id, p_dias_adicionales, p_motivo, 'Pendiente', NOW());
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status, 'Solicitud de ampliación enviada' as message;
    ELSE
        SELECT 0 as idSolicitud, 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_categoria_obtener_por_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_obtener_por_id`(IN p_id INT)
BEGIN
                SELECT idCategoria, nombre, descripcion, fecha_creacion, activa
                FROM Categorias
                WHERE idCategoria = p_id AND activa = TRUE;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_eliminar_categoria` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_categoria`(IN p_id INT)
BEGIN
                UPDATE Categorias 
                SET activa = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idCategoria = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_eliminar_libro` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_libro`(IN p_id INT)
BEGIN
                UPDATE Libros 
                SET activo = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_eliminar_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_usuario`(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET activo = FALSE
    WHERE idUsuario = p_id;
    SELECT ROW_COUNT() as affected_rows;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_insertar_categoria` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_categoria`(
                IN p_nombre VARCHAR(100),
                IN p_descripcion TEXT
            )
BEGIN
                INSERT INTO Categorias (nombre, descripcion)
                VALUES (p_nombre, p_descripcion);
                SELECT LAST_INSERT_ID() as idCategoria;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_insertar_libro` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_libro`(
                IN p_idCategoria INT,
                IN p_titulo VARCHAR(200),
                IN p_autor VARCHAR(100),
                IN p_editorial VARCHAR(100),
                IN p_anio INT,
                IN p_isbn VARCHAR(20),
                IN p_stock INT,
                IN p_disponible INT,
                IN p_descripcion TEXT
            )
BEGIN
                INSERT INTO Libros (idCategoria, titulo, autor, editorial, anio, isbn, stock, disponible, descripcion)
                VALUES (p_idCategoria, p_titulo, p_autor, p_editorial, p_anio, p_isbn, p_stock, p_disponible, p_descripcion);
                SELECT LAST_INSERT_ID() as idLibro;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_insertar_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_usuario`(
                IN p_nombre VARCHAR(100),
                IN p_usuario VARCHAR(50),
                IN p_password VARCHAR(255),
                IN p_rol INT,
                IN p_email VARCHAR(100),
                IN p_telefono VARCHAR(20)
            )
BEGIN
                INSERT INTO Usuarios (nombre, usuario, password, rol, email, telefono)
                VALUES (p_nombre, p_usuario, p_password, p_rol, p_email, p_telefono);
                SELECT LAST_INSERT_ID() as idUsuario;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libros_disponibles_solicitud` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libros_disponibles_solicitud`()
BEGIN
        SELECT 
            l.idLibro,
            l.titulo,
            l.autor,
            l.editorial,
            l.anio,
            l.isbn,
            l.disponible,
            l.descripcion,
            c.nombre as categoria_nombre
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.disponible > 0
        ORDER BY l.titulo;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_actualizar_pdf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_pdf`(
        IN p_id INT,
        IN p_archivo_pdf VARCHAR(255),
        IN p_numero_paginas INT,
        IN p_tamano_archivo VARCHAR(50)
    )
BEGIN
        UPDATE Libros 
        SET archivo_pdf = p_archivo_pdf,
            numero_paginas = p_numero_paginas,
            tamano_archivo = p_tamano_archivo,
            fecha_subida = NOW(),
            fecha_actualizacion = NOW()
        WHERE idLibro = p_id;
        
        SELECT ROW_COUNT() as affected_rows;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_actualizar_stock_devolucion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_stock_devolucion`(IN p_id INT)
BEGIN
                UPDATE Libros 
                SET disponible = disponible + 1,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                
                SELECT 'success' as status, 'Stock actualizado' as message;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_actualizar_stock_prestamo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_stock_prestamo`(IN p_id INT)
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                
                SELECT disponible INTO v_disponible
                FROM Libros
                WHERE idLibro = p_id;
                
                IF v_disponible > 0 THEN
                    UPDATE Libros 
                    SET disponible = disponible - 1,
                        fecha_actualizacion = NOW()
                    WHERE idLibro = p_id;
                    
                    SELECT 'success' as status, 'Stock actualizado' as message;
                ELSE
                    SELECT 'error' as status, 'No hay ejemplares disponibles' as message;
                END IF;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_buscar_por_titulo_autor` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_buscar_por_titulo_autor`(IN p_termino VARCHAR(100))
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.activo = TRUE
    AND (l.titulo LIKE CONCAT('%', p_termino, '%')
         OR l.autor LIKE CONCAT('%', p_termino, '%')
         OR l.isbn LIKE CONCAT('%', p_termino, '%'))
    ORDER BY l.titulo;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_con_detalle_pdf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_con_detalle_pdf`(IN p_id INT)
BEGIN
        SELECT l.*, c.nombre as categoria_nombre,
               CASE WHEN l.archivo_pdf IS NOT NULL AND l.archivo_pdf <> '' THEN 1 ELSE 0 END as tiene_pdf,
               l.archivo_pdf, 
               l.numero_paginas, 
               l.tamano_archivo, 
               l.fecha_subida
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.idLibro = p_id AND l.activo = TRUE;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_con_prestamos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_con_prestamos`()
BEGIN
    SELECT l.*, c.nombre as categoria,
           COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
           COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
    WHERE l.activo = TRUE
    GROUP BY l.idLibro
    ORDER BY l.titulo;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_disponibles` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_disponibles`(IN p_limite INT)
BEGIN
    DECLARE v_limite INT DEFAULT 1000;
    
    -- Asignar el valor de p_limite a v_limite, usando 1000 si p_limite es NULL
    SET v_limite = COALESCE(p_limite, 1000);
    
    SELECT l.*, c.nombre AS categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.disponible > 0 AND l.activo = TRUE
    ORDER BY l.titulo
    LIMIT v_limite;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_estadisticas_pdf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_estadisticas_pdf`()
BEGIN
        SELECT 
            COUNT(*) as total_libros,
            COUNT(CASE WHEN archivo_pdf IS NOT NULL AND archivo_pdf <> '' THEN 1 END) as libros_con_pdf,
            COUNT(CASE WHEN archivo_pdf IS NULL OR archivo_pdf = '' THEN 1 END) as libros_sin_pdf,
            AVG(CASE WHEN numero_paginas IS NOT NULL THEN numero_paginas END) as promedio_paginas,
            SUM(CASE WHEN tamano_archivo IS NOT NULL THEN 
                CAST(REPLACE(REPLACE(tamano_archivo, 'MB', ''), 'KB', '') AS DECIMAL(10,2)) 
            END) as tamano_total_mb
        FROM Libros 
        WHERE activo = TRUE;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_por_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.idLibro = p_id AND l.activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_por_isbn` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_por_isbn`(IN p_isbn VARCHAR(20))
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.isbn = p_isbn AND l.activo = TRUE;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_obtener_recientes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_recientes`(IN p_limite INT)
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.activo = TRUE
                ORDER BY l.idLibro DESC
                LIMIT p_limite;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_registrar_lectura` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_registrar_lectura`(IN p_libro_id INT, IN p_usuario_id INT)
BEGIN
    INSERT INTO HistorialLectura (idUsuario, idLibro, tipo)
    VALUES (p_usuario_id, p_libro_id, 'Lectura');
    
    SELECT LAST_INSERT_ID() as id_historial;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_libro_verificar_isbn_existe` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_verificar_isbn_existe`(IN p_isbn VARCHAR(20), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Libros
    WHERE isbn = p_isbn 
    AND (p_excluir_id IS NULL OR idLibro != p_excluir_id)
    AND activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_listar_categorias` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_categorias`()
BEGIN
                SELECT idCategoria, nombre, descripcion, fecha_creacion, activa
                FROM Categorias
                WHERE activa = TRUE
                ORDER BY nombre;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_listar_libros` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_libros`()
BEGIN
        SELECT l.*, c.nombre as categoria_nombre
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.activo = TRUE
        ORDER BY l.idLibro ASC;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_listar_roles` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_roles`()
BEGIN
                SELECT idRol, nombre, descripcion, fecha_creacion
                FROM Roles
                ORDER BY idRol;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_listar_usuarios` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_usuarios`()
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    ORDER BY u.nombre, u.apellido;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_actualizar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_actualizar`(
            IN p_id INT,
            IN p_fecha_devolucion_esperada DATE,
            IN p_estado VARCHAR(20),
            IN p_observaciones TEXT,
            IN p_fecha_devolucion_real DATETIME
        )
BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            UPDATE Prestamos 
            SET fechaDevolucionEsperada = COALESCE(p_fecha_devolucion_esperada, fechaDevolucionEsperada),
                estado = COALESCE(p_estado, estado),
                observaciones = COALESCE(p_observaciones, observaciones),
                fechaDevolucionReal = COALESCE(p_fecha_devolucion_real, fechaDevolucionReal)
            WHERE idPrestamo = p_id;
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Préstamo actualizado exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo actualizar el préstamo' as message;
            END IF;
            
            COMMIT;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_actualizar_observaciones` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_actualizar_observaciones`(
        IN p_prestamo_id INT,
        IN p_observaciones TEXT
    )
BEGIN
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        UPDATE Prestamos 
        SET observaciones = p_observaciones
        WHERE idPrestamo = p_prestamo_id;
        
        IF ROW_COUNT() > 0 THEN
            SELECT 'success' as status, 'Observaciones actualizadas exitosamente' as message;
        ELSE
            SELECT 'error' as status, 'No se pudo actualizar las observaciones' as message;
        END IF;
        
        COMMIT;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_auto_devolver` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_auto_devolver`(IN p_prestamo_id INT)
BEGIN
            DECLARE v_libro_id INT;
            DECLARE v_estado VARCHAR(20);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            -- Verificar que el préstamo existe y está activo
            SELECT idLibro, estado INTO v_libro_id, v_estado 
            FROM Prestamos 
            WHERE idPrestamo = p_prestamo_id;
            
            IF v_libro_id IS NULL THEN
                SELECT 'error' as status, 'Préstamo no encontrado' as message;
            ELSEIF v_estado != 'Activo' THEN
                SELECT 'error' as status, 'El préstamo no está activo' as message;
            ELSE
                -- Marcar como devuelto automáticamente
                UPDATE Prestamos 
                SET estado = 'Devuelto',
                    fechaDevolucionReal = NOW(),
                    observaciones = CONCAT(COALESCE(observaciones, ''), ' [Auto-devolución]')
                WHERE idPrestamo = p_prestamo_id;
                
                -- Restaurar stock del libro
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
                
                SELECT 'success' as status, 'Préstamo devuelto automáticamente' as message;
            END IF;
            
            COMMIT;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_buscar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_buscar`(IN p_termino VARCHAR(255))
BEGIN
            SELECT 
                p.idPrestamo,
                p.idLibro,
                p.idUsuario,
                p.fechaPrestamo,
                p.fechaDevolucionEsperada,
                p.fechaDevolucionReal,
                p.estado,
                p.observaciones,
                u.nombre as usuario_nombre,
                u.apellido as usuario_apellido,
                l.titulo as libro_titulo,
                l.autor as libro_autor
            FROM Prestamos p
            INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
            INNER JOIN Libros l ON p.idLibro = l.idLibro
            WHERE p.idPrestamo LIKE CONCAT('%', p_termino, '%')
               OR u.nombre LIKE CONCAT('%', p_termino, '%')
               OR u.apellido LIKE CONCAT('%', p_termino, '%')
               OR l.titulo LIKE CONCAT('%', p_termino, '%')
               OR l.autor LIKE CONCAT('%', p_termino, '%')
               OR p.estado LIKE CONCAT('%', p_termino, '%')
            ORDER BY p.fechaPrestamo DESC;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_devolver_completo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_devolver_completo`(IN p_id INT)
BEGIN
                DECLARE v_libro_id INT;
                DECLARE v_affected_rows INT DEFAULT 0;
                
                -- Obtener el ID del libro
                SELECT idLibro INTO v_libro_id
                FROM Prestamos
                WHERE idPrestamo = p_id AND fechaDevolucionReal IS NULL;
                
                IF v_libro_id IS NOT NULL THEN
                    -- Marcar préstamo como devuelto
                    UPDATE Prestamos 
                    SET fechaDevolucionReal = NOW(),
                        estado = 'Devuelto'
                    WHERE idPrestamo = p_id;
                    
                    SET v_affected_rows = ROW_COUNT();
                    
                    -- Incrementar stock disponible
                    UPDATE Libros 
                    SET disponible = disponible + 1
                    WHERE idLibro = v_libro_id;
                    
                    SELECT 'success' as status, 'Préstamo devuelto exitosamente' as message, v_affected_rows as affected_rows;
                ELSE
                    SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message, 0 as affected_rows;
                END IF;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_eliminar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_eliminar`(
        IN p_prestamo_id INT
    )
BEGIN
        DECLARE v_libro_id INT;
        DECLARE v_estado VARCHAR(20);
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        -- Obtener información del préstamo
        SELECT idLibro, estado INTO v_libro_id, v_estado
        FROM Prestamos 
        WHERE idPrestamo = p_prestamo_id;
        
        IF v_libro_id IS NULL THEN
            SELECT 'error' as status, 'Préstamo no encontrado' as message;
        ELSE
            -- Si el préstamo está activo, restaurar stock del libro
            IF v_estado = 'Activo' THEN
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
            END IF;
            
            -- Eliminar el préstamo
            DELETE FROM Prestamos 
            WHERE idPrestamo = p_prestamo_id;
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Préstamo eliminado exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo eliminar el préstamo' as message;
            END IF;
        END IF;
        
        COMMIT;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_insertar_completo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_insertar_completo`(
                IN p_libro_id INT,
                IN p_usuario_id INT,
                IN p_fecha_prestamo DATE,
                IN p_fecha_devolucion_esperada DATE,
                IN p_observaciones TEXT
            )
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                DECLARE v_affected_rows INT DEFAULT 0;
                DECLARE v_prestamo_id INT;
                
                -- Verificar disponibilidad
                SELECT disponible INTO v_disponible
                FROM Libros
                WHERE idLibro = p_libro_id AND activo = 1;
                
                IF v_disponible > 0 THEN
                    -- Insertar préstamo
                    INSERT INTO Prestamos (idLibro, idUsuario, fechaPrestamo, fechaDevolucionEsperada, observaciones, estado)
                    VALUES (p_libro_id, p_usuario_id, p_fecha_prestamo, p_fecha_devolucion_esperada, p_observaciones, 'Activo');
                    
                    SET v_prestamo_id = LAST_INSERT_ID();
                    SET v_affected_rows = ROW_COUNT();
                    
                    -- Decrementar stock disponible
                    UPDATE Libros 
                    SET disponible = disponible - 1
                    WHERE idLibro = p_libro_id;
                    
                    SELECT 'success' as status, 'Préstamo creado exitosamente' as message, v_prestamo_id as idPrestamo;
                ELSE
                    SELECT 'error' as status, 'El libro no está disponible para préstamo' as message, 0 as idPrestamo;
                END IF;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_activos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_activos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_estadisticas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN fechaDevolucionReal IS NULL THEN 1 END) as activos,
        COUNT(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 END) as devueltos,
        COUNT(CASE WHEN fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < NOW() THEN 1 END) as vencidos
    FROM Prestamos;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_por_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_id`(IN p_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idPrestamo = p_id;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_por_libro` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_libro`(IN p_libro_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                WHERE p.idLibro = p_libro_id
                ORDER BY p.fechaPrestamo DESC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_por_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_usuario`(IN p_usuario_id INT)
BEGIN
                SELECT p.*, l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idUsuario = p_usuario_id
                ORDER BY p.fechaPrestamo DESC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_todos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_todos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                ORDER BY p.fechaPrestamo DESC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_usuario_libro` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_usuario_libro`(IN p_usuario_id INT, IN p_libro_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                WHERE p.idUsuario = p_usuario_id 
                AND p.idLibro = p_libro_id
                AND p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC
                LIMIT 1;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_obtener_vencidos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_vencidos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.fechaDevolucionReal IS NULL 
                AND p.fechaDevolucionEsperada < NOW()
                ORDER BY p.fechaDevolucionEsperada ASC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_registrar_devolucion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_registrar_devolucion`(
            IN p_prestamo_id INT,
            IN p_observaciones TEXT
        )
BEGIN
            DECLARE v_libro_id INT;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            -- Obtener el libro del préstamo
            SELECT idLibro INTO v_libro_id FROM Prestamos WHERE idPrestamo = p_prestamo_id AND estado = 'Activo';
            
            IF v_libro_id IS NULL THEN
                SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
            ELSE
                -- Actualizar el préstamo
                UPDATE Prestamos 
                SET estado = 'Devuelto',
                    fechaDevolucionReal = NOW(),
                    observaciones = COALESCE(p_observaciones, observaciones)
                WHERE idPrestamo = p_prestamo_id;
                
                -- Restaurar stock del libro
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
                
                SELECT 'success' as status, 'Devolución registrada exitosamente' as message;
            END IF;
            
            COMMIT;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_prestamo_validar_disponibilidad` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_validar_disponibilidad`(IN p_libro_id INT)
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                DECLARE v_activo TINYINT DEFAULT 0;
                
                SELECT disponible, activo INTO v_disponible, v_activo
                FROM Libros
                WHERE idLibro = p_libro_id;
                
                IF v_disponible > 0 AND v_activo = 1 THEN
                    SELECT 1 as disponible;
                ELSE
                    SELECT 0 as disponible;
                END IF;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitudes_estadisticas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas,
        SUM(CASE WHEN DATE(fecha_solicitud) = CURDATE() THEN 1 ELSE 0 END) as solicitudes_hoy
    FROM solicitudes_prestamo;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitudes_estadisticas_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_estadisticas_usuario`(IN p_usuario_id INT)
BEGIN
            SELECT 
                COUNT(*) as total_solicitudes,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
            FROM solicitudes_prestamo
            WHERE usuario_id = p_usuario_id;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitudes_listar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_listar`(IN p_estado VARCHAR(20))
BEGIN
    SELECT 
        s.idSolicitud,
        s.usuario_id,
        s.libro_id,
        s.fecha_solicitud,
        s.estado,
        s.observaciones_usuario,
        s.observaciones_bibliotecario,
        s.fecha_respuesta,
        s.bibliotecario_id,
        s.prestamo_id,
        u.nombre as usuario_nombre,
        u.apellido as usuario_apellido,
        u.email as usuario_email,
        u.telefono as usuario_telefono,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        l.disponible as libro_disponible,
        c.nombre as categoria_nombre,
        b.nombre as bibliotecario_nombre,
        b.apellido as bibliotecario_apellido
    FROM solicitudes_prestamo s
    INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    LEFT JOIN Usuarios b ON s.bibliotecario_id = b.idUsuario
    WHERE (p_estado IS NULL OR s.estado = p_estado)
    ORDER BY 
        CASE s.estado
            WHEN 'Pendiente' THEN 1
            WHEN 'Aprobada' THEN 2
            WHEN 'Rechazada' THEN 3
            WHEN 'Convertida' THEN 4
        END,
        s.fecha_solicitud DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitudes_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_usuario`(IN p_usuario_id INT)
BEGIN
    SELECT 
        s.idSolicitud,
        s.usuario_id,
        s.libro_id,
        s.fecha_solicitud,
        s.estado,
        s.observaciones_usuario,
        s.observaciones_bibliotecario,
        s.fecha_respuesta,
        s.prestamo_id,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        l.disponible as libro_disponible,
        c.nombre as categoria_nombre,
        b.nombre as bibliotecario_nombre,
        b.apellido as bibliotecario_apellido
    FROM solicitudes_prestamo s
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    LEFT JOIN Usuarios b ON s.bibliotecario_id = b.idUsuario
    WHERE s.usuario_id = p_usuario_id
    ORDER BY s.fecha_solicitud DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitud_aprobar_y_crear_prestamo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_aprobar_y_crear_prestamo`(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_fecha_devolucion DATETIME,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_usuario_id INT;
    DECLARE v_libro_id INT;
    DECLARE v_prestamo_id INT;
    DECLARE v_disponible INT DEFAULT 0;
    
    -- Obtener datos de la solicitud
    SELECT usuario_id, libro_id INTO v_usuario_id, v_libro_id
    FROM solicitudes_prestamo
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    IF v_usuario_id IS NOT NULL THEN
        -- Verificar disponibilidad
        SELECT disponible INTO v_disponible
        FROM Libros
        WHERE idLibro = v_libro_id;
        
        IF v_disponible > 0 THEN
            -- Crear préstamo
            INSERT INTO Prestamos (idLibro, idUsuario, fechaDevolucionEsperada, observaciones)
            VALUES (v_libro_id, v_usuario_id, p_fecha_devolucion, p_observaciones);
            
            SET v_prestamo_id = LAST_INSERT_ID();
            
            -- Actualizar stock
            UPDATE Libros 
            SET disponible = disponible - 1,
                fecha_actualizacion = NOW()
            WHERE idLibro = v_libro_id;
            
            -- Actualizar solicitud
            UPDATE solicitudes_prestamo 
            SET estado = 'Convertida',
                bibliotecario_id = p_bibliotecario_id,
                observaciones_bibliotecario = p_observaciones,
                fecha_respuesta = NOW(),
                prestamo_id = v_prestamo_id
            WHERE idSolicitud = p_solicitud_id;
            
            SELECT 'success' as status, v_prestamo_id as prestamo_id, 'Solicitud aprobada y préstamo creado' as message;
        ELSE
            SELECT 'error' as status, 0 as prestamo_id, 'No hay ejemplares disponibles' as message;
        END IF;
    ELSE
        SELECT 'error' as status, 0 as prestamo_id, 'Solicitud no encontrada o ya procesada' as message;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitud_cancelar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_cancelar`(
            IN p_solicitud_id INT,
            IN p_usuario_id INT
        )
BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            UPDATE solicitudes_prestamo 
            SET estado = 'Rechazada',
                observaciones_bibliotecario = 'Cancelada por el usuario',
                fecha_respuesta = NOW()
            WHERE idSolicitud = p_solicitud_id 
            AND usuario_id = p_usuario_id 
            AND estado = 'Pendiente';
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Solicitud cancelada exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo cancelar la solicitud' as message;
            END IF;
            
            COMMIT;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitud_insertar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_insertar`(
    IN p_usuario_id INT,
    IN p_libro_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    
    -- Verificar si el libro está disponible
    SELECT disponible INTO v_disponible 
    FROM Libros 
    WHERE idLibro = p_libro_id;
    
    -- Solo permitir solicitud si hay ejemplares disponibles
    IF v_disponible > 0 THEN
        INSERT INTO solicitudes_prestamo (usuario_id, libro_id, observaciones_usuario)
        VALUES (p_usuario_id, p_libro_id, p_observaciones);
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status;
    ELSE
        SELECT 0 as idSolicitud, 'no_disponible' as status;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitud_obtener_por_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_obtener_por_id`(IN p_id INT)
BEGIN
            SELECT 
                s.idSolicitud,
                s.usuario_id,
                s.libro_id,
                s.fecha_solicitud,
                s.observaciones_usuario,
                s.estado,
                s.bibliotecario_id,
                s.fecha_respuesta,
                s.observaciones_bibliotecario,
                u.nombre as usuario_nombre,
                u.apellido as usuario_apellido,
                u.email as usuario_email,
                l.titulo as libro_titulo,
                l.autor as libro_autor,
                c.nombre as categoria_nombre
            FROM solicitudes_prestamo s
            INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
            INNER JOIN Libros l ON s.libro_id = l.idLibro
            INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
            WHERE s.idSolicitud = p_id;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_solicitud_responder` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_responder`(
    IN p_solicitud_id INT,
    IN p_estado VARCHAR(20),
    IN p_bibliotecario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    UPDATE solicitudes_prestamo 
    SET 
        estado = p_estado,
        bibliotecario_id = p_bibliotecario_id,
        observaciones_bibliotecario = p_observaciones,
        fecha_respuesta = NOW()
    WHERE idSolicitud = p_solicitud_id 
    AND estado = 'Pendiente';
    
    SELECT ROW_COUNT() as affected_rows;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_activar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_activar`(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET activo = TRUE
    WHERE idUsuario = p_id;
    SELECT ROW_COUNT() as affected_rows;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_actualizar_ultimo_acceso` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_actualizar_ultimo_acceso`(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET ultimo_acceso = NOW() 
    WHERE idUsuario = p_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_buscar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_buscar`(IN p_termino VARCHAR(100))
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.activo = TRUE
    AND (u.nombre LIKE CONCAT('%', p_termino, '%')
         OR u.apellido LIKE CONCAT('%', p_termino, '%')
         OR u.usuario LIKE CONCAT('%', p_termino, '%')
         OR u.email LIKE CONCAT('%', p_termino, '%'))
    ORDER BY u.nombre, u.apellido;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_desactivar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_desactivar`(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET activo = FALSE
    WHERE idUsuario = p_id;
    SELECT ROW_COUNT() as affected_rows;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_estadisticas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total_usuarios,
        SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
        SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
        SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as nuevos_hoy
    FROM Usuarios
    WHERE activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_obtener_por_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.idUsuario = p_id AND u.activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_obtener_por_rol` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_rol`(IN p_rol INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.rol = p_rol AND u.activo = TRUE
    ORDER BY u.nombre, u.apellido;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_obtener_por_username` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_username`(IN p_usuario VARCHAR(50))
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.usuario = p_usuario AND u.activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_verificar_email` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_verificar_email`(IN p_email VARCHAR(100), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE email = p_email 
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id)
    AND activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usuario_verificar_existe` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_verificar_existe`(IN p_usuario VARCHAR(50))
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE usuario = p_usuario AND activo = TRUE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `vista_estadisticas_generales`
--

/*!50001 DROP VIEW IF EXISTS `vista_estadisticas_generales`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_estadisticas_generales` AS select (select count(0) from `usuarios` where (`usuarios`.`activo` = true)) AS `total_usuarios`,(select count(0) from `libros` where (`libros`.`activo` = true)) AS `total_libros`,(select count(0) from `prestamos`) AS `total_prestamos`,(select count(0) from `prestamos` where (`prestamos`.`fechaDevolucionReal` is null)) AS `prestamos_activos`,(select count(0) from `prestamos` where (`prestamos`.`fechaDevolucionReal` is not null)) AS `prestamos_devueltos`,(select count(0) from `prestamos` where ((`prestamos`.`fechaDevolucionReal` is null) and (`prestamos`.`fechaDevolucionEsperada` < curdate()))) AS `prestamos_vencidos`,(select count(0) from `solicitudes_prestamo`) AS `total_solicitudes`,(select count(0) from `solicitudes_prestamo` where (`solicitudes_prestamo`.`estado` = 'Pendiente')) AS `solicitudes_pendientes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_prestamos_activos`
--

/*!50001 DROP VIEW IF EXISTS `vista_prestamos_activos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_prestamos_activos` AS select `p`.`idPrestamo` AS `idPrestamo`,`p`.`fechaPrestamo` AS `fechaPrestamo`,`p`.`fechaDevolucionEsperada` AS `fechaDevolucionEsperada`,`p`.`observaciones` AS `observaciones`,`u`.`nombre` AS `usuario_nombre`,`u`.`apellido` AS `usuario_apellido`,`u`.`email` AS `usuario_email`,`l`.`titulo` AS `libro_titulo`,`l`.`autor` AS `libro_autor`,`l`.`isbn` AS `libro_isbn`,`c`.`nombre` AS `categoria_nombre`,(to_days(`p`.`fechaDevolucionEsperada`) - to_days(curdate())) AS `dias_restantes` from (((`prestamos` `p` join `usuarios` `u` on((`p`.`idUsuario` = `u`.`idUsuario`))) join `libros` `l` on((`p`.`idLibro` = `l`.`idLibro`))) join `categorias` `c` on((`l`.`idCategoria` = `c`.`idCategoria`))) where (`p`.`fechaDevolucionReal` is null) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-23 21:59:47

-- =====================================================
-- MENSAJE DE ÉXITO
-- =====================================================
SELECT '¡Base de datos restaurada exitosamente!' as mensaje,
       'Sistema de Biblioteca v1.0' as version,
       NOW() as fecha_restauracion;
