-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: UtpIntegradorBuzzer
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `preferencias_notificacion` json DEFAULT NULL,
  `fecha_ultima_visita` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_clientes_telefono` (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedidos`
--

DROP TABLE IF EXISTS `detalle_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `productoDesc` varchar(100) DEFAULT NULL,
  `cantidad` int unsigned NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `instrucciones_especiales` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_detalle_pedido` (`pedido_id`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos`
--

LOCK TABLES `detalle_pedidos` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos` DISABLE KEYS */;
INSERT INTO `detalle_pedidos` VALUES (1,1,'dfsdsf',1,1.00,1.00,NULL,'2026-10-01 16:43:53','2026-10-01 16:43:53'),(2,2,'gfdhdfgh',1,2.00,2.00,NULL,'2026-10-01 16:44:11','2026-10-01 16:44:11'),(3,3,'sdfs',1,3.00,3.00,NULL,'2026-10-01 16:51:33','2026-10-01 16:51:33'),(4,4,'dfgsfdg',1,4.00,4.00,NULL,'2026-10-01 16:57:12','2026-10-01 16:57:12');
/*!40000 ALTER TABLE `detalle_pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formularios`
--

DROP TABLE IF EXISTS `formularios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formularios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `formulario` varchar(45) NOT NULL,
  `controller` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formulario_UNIQUE` (`formulario`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formularios`
--

LOCK TABLES `formularios` WRITE;
/*!40000 ALTER TABLE `formularios` DISABLE KEYS */;
INSERT INTO `formularios` VALUES (1,'Dashboard','DashboardController'),(2,'Pedidos Caja','CajaController'),(3,'Pedidos Cocina','CocinaController'),(4,'Pedidos Despacho','DespachoController'),(5,'Reporte','ReporteController'),(6,'Trabajadores','TrabajadoresController'),(7,'Empresa Contrata','RestaurantController'),(8,'Locales','LocalesController'),(9,'Clientes','ClientesController'),(10,'POS','PosPedidoController');
/*!40000 ALTER TABLE `formularios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_estados`
--

DROP TABLE IF EXISTS `historial_estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_estados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `trabajador_id` bigint unsigned DEFAULT NULL,
  `estado_anterior` enum('REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO') DEFAULT NULL,
  `estado_nuevo` enum('REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO') NOT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hist_pedido_fecha` (`pedido_id`,`fecha_cambio`),
  KEY `idx_hist_trabajador` (`trabajador_id`),
  CONSTRAINT `fk_hist_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_trabajador` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_estados`
--

LOCK TABLES `historial_estados` WRITE;
/*!40000 ALTER TABLE `historial_estados` DISABLE KEYS */;
INSERT INTO `historial_estados` VALUES (1,1,23,NULL,'REGISTRADO','2026-10-01 16:43:53','Pedido registrado en caja','2026-10-01 16:43:53','2026-10-01 16:43:53'),(2,2,23,NULL,'REGISTRADO','2026-10-01 16:44:11','Pedido registrado en caja','2026-10-01 16:44:11','2026-10-01 16:44:11'),(3,3,23,NULL,'REGISTRADO','2026-10-01 16:51:33','Pedido registrado en caja','2026-10-01 16:51:33','2026-10-01 16:51:33'),(4,4,23,NULL,'REGISTRADO','2026-10-01 16:57:12','Pedido registrado en caja','2026-10-01 16:57:12','2026-10-01 16:57:12'),(5,2,23,'REGISTRADO','CANCELADO','2026-10-01 16:58:24',NULL,'2026-10-01 16:58:24','2026-10-01 16:58:24'),(6,1,25,'REGISTRADO','PREPARANDO','2026-10-01 17:00:38',NULL,'2026-10-01 17:00:38','2026-10-01 17:00:38'),(7,1,25,'PREPARANDO','LISTO','2026-10-01 17:01:34',NULL,'2026-10-01 17:01:34','2026-10-01 17:01:34'),(8,4,25,'REGISTRADO','PREPARANDO','2026-10-01 17:24:12',NULL,'2026-10-01 17:24:12','2026-10-01 17:24:12'),(9,1,24,'LISTO','ENTREGADO','2026-10-01 17:25:49',NULL,'2026-10-01 17:25:49','2026-10-01 17:25:49');
/*!40000 ALTER TABLE `historial_estados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locales`
--

DROP TABLE IF EXISTS `locales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `api_token` varchar(64) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locales_api_token_unique` (`api_token`),
  KEY `idx_locales_restaurante` (`restaurante_id`),
  CONSTRAINT `fk_locales_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locales`
--

LOCK TABLES `locales` WRITE;
/*!40000 ALTER TABLE `locales` DISABLE KEYS */;
INSERT INTO `locales` VALUES (1,7,'Larco','Av. JosÃ© Larco 999, Miraflores, Lima','','2nQ194mS9qc1nDBMnMacH2hn5ydSWOXqcZM4TETa4L4TfhL2iGwyHBMi3ZGfPhuD','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(2,7,'Diagonal','Av. Diagonal 308, Miraflores, Lima','','n8HhRiUTcxqK3LrHXZFgsF3SE2H10FzmbHkHgT3fa7z4oL46HAKwddmMNr0Ix2sc','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(3,14,'Centro','Jr. Chancay 894, Cercado de Lima, Lima','','tlkIMQaAtu8jat7s9HYfAqOV3gPDVZQBD0cGUxse73lVCipxRH2HQpC8Td6ZP9dx','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(4,14,'Surco','Av. Caminos del Inca 1151, Santiago de Surco, Lima','','ZVabdhTlYeynGdKH167kMp0iOm6kbgHGxtANkI1mjVD04Z3Ld7emaGUYHBprkjPg','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(5,16,'Lince','Av. Gral. Juan Antonio Ãlvarez de Arenales 2499, Lince, Lima','','SchzwFbtlyd0FwvqVV4qvBL1ekoZnACUiN4c4aOYAHaqJddXTqFfKjsieHI4o8p1','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(6,10,'Aeropuerto','Av. Elmer Faucett s/n (Aeropuerto Internacional Jorge ChÃ¡vez), Callao','','PrkdhJjB5VYSkmuVOv3ybydUOjHJ6W5wHH0nrkLuiRiucTbxBmYJiHt0oP5YGbk5','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(7,2,'Larco','Av. JosÃ© Larco 401, Miraflores, Lima','','WX4X74RsE6yN0HD1HtV2wrO7KDto7qbQ2NlJzRexbHXQSeDoLutyckosQN6YQJuj','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(8,2,'Jockey Plaza','Av. Javier Prado Este 4200 (CC Jockey Plaza), Santiago de Surco, Lima','','AaKHsVhOZblNbXzv1dbfVuVlFajpwOUbcEN9nhpWFfK0hjJk3FAjyoiIv5TlGNRa','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(9,3,'Plaza San Miguel','Av. Universitaria 2000 (CC Plaza San Miguel), San Miguel, Lima','','Fz33otlcRNFjfYgfUySHJEUgDz5fhxZZpqSJo7bMibcTlDtz0wPGUwabMWGu0tCi','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(10,8,'Centro','Av. Abancay 601, Cercado de Lima, Lima','','QBZuT5gNzFWPtAqNjAid0KQuhdzCsFpnbkca2MuH6eKNFPOcIdunsDjGfIlUr4Ry','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(11,12,'Angamos','Av. Angamos Este 1502, Surquillo, Lima','','iR9WYVHJeXiQOO2pa7YvmALEassZYlrfjKurTyH6UzJRXr6WCwS9VIpdmMCIrfki','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(12,15,'Angamos','Av. Angamos Este 609, Surquillo, Lima','','0amK6OpR6GQvwmCcZrCLUjhSgHFBujv6QZjVObKdBMaHj3jaVhe7ScjtQTBz2ZEO','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(13,15,'Lince','Av. Arequipa 2394, Lince, Lima','','nwbXlgtkILAVkVJ8mn61x07y8I0Y53w5v1Lq0vzf4oQEUzfYmaYquFPxjbeXfQLA','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(14,13,'Barranco','Av. de la AviaciÃ³n 3005, San Borja, Lima','','ISrtkhUUo1n2gvr4WUSnd9utCoGCKR0XVLqh2CDjjSvj1iHTmhsU5q41aM1IgsgN','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(15,5,'La Mar','Av. Mariscal La Mar 1328, Miraflores, Lima','','stPEFZNIhMyedRjslZd5RabcnXG8iUY5lylGw5p2IdBblF7kD4RK39LONuicrsvn','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(16,9,'Larcomar','MalecÃ³n de la Reserva 610 (CC Larcomar), Miraflores, Lima','','xZFNhOUFYr2fBiLUssQSjqtIctUriRezDye2qklfHI4D5UwGEleuq0lcdAGWaRdh','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(17,11,'Megaplaza','Av. Alfredo Mendiola 3698 (CC MegaPlaza), Los Olivos, Lima','','5JYnUIps05dLrQPMDz3x2a20LluadTXaRDYRfvbFnuFxUtQSu17ndSoWgMuEtV0r','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(18,4,'Centro','Jr. de la UniÃ³n 500, Cercado de Lima, Lima','','RPwSPppCd8cBy92wVPygRANZMVwKzeN0JypZQXBBTYhiqfRsmSBxw5Vrb4bSnmis','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(19,6,'Canaval y Moreyra','Av. Canaval y Moreyra 501, San Isidro, Lima','','Vhs6q1G6FQ1FKGMYbOa0Ct1BSoQOezwcGy9PlygHSMws35A7JE9YyxCG6lRlGmOD','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL),(20,1,'Luna Pizarro','Av. Luna Pizarro 415, La Victoria, Lima','','rXp1vUEAYh3Gyb8tjxhdE4AFwpweCp1Ul9hIlESJzSRnppYHfd7hEavHZuBiMa4J','ACTIVO','2026-10-01 16:14:22','2026-10-01 16:14:22',NULL);
/*!40000 ALTER TABLE `locales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metricas`
--

DROP TABLE IF EXISTS `metricas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metricas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `local_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `total_pedidos` int unsigned NOT NULL DEFAULT '0',
  `tiempo_promedio_espera` decimal(5,2) DEFAULT NULL,
  `porcentaje_notificaciones_exitosas` decimal(5,2) DEFAULT NULL,
  `pedidos_gestionados_qr` int unsigned NOT NULL DEFAULT '0',
  `clientes_unicos` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_metrica_rest_fecha` (`restaurante_id`,`fecha`),
  KEY `fk_metricas_locales1_idx` (`local_id`),
  CONSTRAINT `fk_metrica_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_metricas_locales1` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metricas`
--

LOCK TABLES `metricas` WRITE;
/*!40000 ALTER TABLE `metricas` DISABLE KEYS */;
/*!40000 ALTER TABLE `metricas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_22_150000_add_must_change_password_to_users_table',2),(5,'2026_09_23_000000_add_imagen_url_to_trabajadores_table',3),(6,'2026_09_29_000000_add_api_token_to_locales_table',4),(7,'2026_09_30_000000_add_seguimiento_token_to_pedidos_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('SONIDO','VIBRACION','VISUAL','PUSH') NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_lectura` timestamp NULL DEFAULT NULL,
  `estado` enum('ENVIADA','ENTREGADA','LEIDA','FALLIDA') NOT NULL DEFAULT 'ENVIADA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_notif_pedido` (`pedido_id`),
  KEY `idx_notif_cliente` (`cliente_id`),
  KEY `idx_notif_estado` (`estado`),
  CONSTRAINT `fk_notif_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notif_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
INSERT INTO `notificaciones` VALUES (1,1,NULL,'VISUAL','Pedido 0001 registrado','2026-10-01 16:43:53',NULL,'ENVIADA','2026-10-01 16:43:53','2026-10-01 16:43:53'),(2,2,NULL,'VISUAL','Pedido 0002 registrado','2026-10-01 16:44:11',NULL,'ENVIADA','2026-10-01 16:44:11','2026-10-01 16:44:11'),(3,3,NULL,'VISUAL','Pedido 1047 registrado','2026-10-01 16:51:33',NULL,'ENVIADA','2026-10-01 16:51:33','2026-10-01 16:51:33'),(4,4,NULL,'VISUAL','Pedido 1048 registrado','2026-10-01 16:57:12',NULL,'ENVIADA','2026-10-01 16:57:12','2026-10-01 16:57:12'),(5,2,NULL,'VISUAL','Pedido 0002 ahora está CANCELADO','2026-10-01 16:58:24',NULL,'ENVIADA','2026-10-01 16:58:24','2026-10-01 16:58:24'),(6,1,NULL,'VISUAL','Pedido 0001 ahora está PREPARANDO','2026-10-01 17:00:38',NULL,'ENVIADA','2026-10-01 17:00:38','2026-10-01 17:00:38'),(7,1,NULL,'VISUAL','Pedido 0001 ahora está LISTO','2026-10-01 17:01:34',NULL,'ENVIADA','2026-10-01 17:01:34','2026-10-01 17:01:34'),(8,4,NULL,'VISUAL','Pedido 1048 ahora está PREPARANDO','2026-10-01 17:24:12',NULL,'ENVIADA','2026-10-01 17:24:12','2026-10-01 17:24:12'),(9,1,NULL,'VISUAL','Pedido 0001 ahora está ENTREGADO','2026-10-01 17:25:49',NULL,'ENVIADA','2026-10-01 17:25:49','2026-10-01 17:25:49');
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `local_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `trabajador_caja_id` bigint unsigned DEFAULT NULL,
  `codigo_pedido` varchar(20) NOT NULL,
  `codigo_qr` varchar(100) DEFAULT NULL,
  `seguimiento_token` varchar(64) DEFAULT NULL,
  `imagen_qr` varchar(200) DEFAULT NULL,
  `fecha_expira_qr` timestamp NULL DEFAULT NULL,
  `tipo` enum('PRESENCIAL','PARA_LLEVAR') NOT NULL DEFAULT 'PRESENCIAL',
  `estado` enum('REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO') NOT NULL DEFAULT 'REGISTRADO',
  `fecha_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tiempo_preparacion_estimado` int DEFAULT NULL,
  `tiempo_preparacion_real` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notas` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pedidos_codigo` (`codigo_pedido`),
  UNIQUE KEY `pedidos_seguimiento_token_unique` (`seguimiento_token`),
  KEY `idx_pedidos_local_estado` (`local_id`,`estado`),
  KEY `idx_pedidos_fecha` (`fecha_pedido`),
  KEY `idx_pedidos_cliente` (`cliente_id`),
  KEY `fk_pedidos_trabajador_caja` (`trabajador_caja_id`),
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_trabajador_caja` FOREIGN KEY (`trabajador_caja_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,20,NULL,23,'0001','20-0001','RhB2UUFQpKixUs07NlOxubAc1KSpmLq6GWuhPJhu','http://192.168.1.18:8282/storage/qr/1.svg','2026-10-01 17:43:53','PRESENCIAL','ENTREGADO','2026-10-01 16:43:53',20,NULL,1.00,NULL,'2026-10-01 16:43:53','2026-10-01 17:25:49',NULL),(2,20,NULL,23,'0002','20-0002','FOdUzU03sCUuNSWjKcNou2bEWPHlf5wQqYQiEtYa','http://192.168.1.18:8282/storage/qr/2.svg','2026-10-01 16:58:24','PRESENCIAL','CANCELADO','2026-10-01 16:44:11',20,-14,2.00,NULL,'2026-10-01 16:44:11','2026-10-01 16:58:24',NULL),(3,20,NULL,23,'1047','20-1047','dPodpeEI9mbMfDDd4sreXh2YwP6CFF0eqLMjzKj9','http://192.168.1.18:8282/storage/qr/3.svg','2026-10-01 17:51:33','PRESENCIAL','REGISTRADO','2026-10-01 16:51:33',20,NULL,3.00,'dsdf','2026-10-01 16:51:33','2026-10-01 16:51:34',NULL),(4,20,NULL,23,'1048','20-1048','BBCyMtilv0ixFDYNCmFMUBtqQLyM6iiYITcS1TWK','http://10.10.1.60:8282/storage/qr/4.svg','2026-10-01 17:57:12','PRESENCIAL','PREPARANDO','2026-10-01 16:57:12',20,NULL,4.00,NULL,'2026-10-01 16:57:12','2026-10-01 17:24:12',NULL);
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfilAccesos`
--

DROP TABLE IF EXISTS `perfilAccesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfilAccesos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `perfil` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfilAccesos`
--

LOCK TABLES `perfilAccesos` WRITE;
/*!40000 ALTER TABLE `perfilAccesos` DISABLE KEYS */;
INSERT INTO `perfilAccesos` VALUES (1,'SUPER_ADMIN'),(2,'ADMIN'),(3,'GERENTE'),(4,'CAJA'),(5,'COCINA'),(6,'DESPACHO'),(7,'POS');
/*!40000 ALTER TABLE `perfilAccesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfilesFormularios`
--

DROP TABLE IF EXISTS `perfilesFormularios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfilesFormularios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `perfilAcceso_id` bigint unsigned NOT NULL,
  `formulario_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`,`perfilAcceso_id`,`formulario_id`),
  KEY `fk_perfilesFormularios_perfilAccesos1_idx` (`perfilAcceso_id`),
  KEY `fk_perfilesFormularios_formularios1_idx` (`formulario_id`),
  CONSTRAINT `fk_perfilesFormularios_formularios1` FOREIGN KEY (`formulario_id`) REFERENCES `formularios` (`id`),
  CONSTRAINT `fk_perfilesFormularios_perfilAccesos1` FOREIGN KEY (`perfilAcceso_id`) REFERENCES `perfilAccesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfilesFormularios`
--

LOCK TABLES `perfilesFormularios` WRITE;
/*!40000 ALTER TABLE `perfilesFormularios` DISABLE KEYS */;
INSERT INTO `perfilesFormularios` VALUES (1,1,9),(2,1,1),(3,1,7),(4,1,8),(5,1,5),(8,2,9),(9,2,1),(10,2,8),(11,2,5),(12,2,6),(15,3,9),(16,3,1),(17,3,2),(18,3,3),(19,3,4),(20,3,5),(21,3,6),(22,4,2),(23,5,3),(24,6,4),(25,7,10);
/*!40000 ALTER TABLE `perfilesFormularios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurantes`
--

DROP TABLE IF EXISTS `restaurantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `plan` enum('BASICO','PRO','ENTERPRISE') NOT NULL DEFAULT 'BASICO',
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_restaurantes_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurantes`
--

LOCK TABLES `restaurantes` WRITE;
/*!40000 ALTER TABLE `restaurantes` DISABLE KEYS */;
INSERT INTO `restaurantes` VALUES (1,'Begui','Av. Luna Pizarro 415, La Victoria, Lima','014721050','contacto@begui.com.pe','BASICO','ACTIVO',NULL,NULL,NULL),(2,'Bembos','Av. Javier Prado Este 4200 (CC Jockey Plaza), Santiago de Surco, Lima','014191920','jockey@bembos.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(3,'China Wok','Av. Universitaria 2000 (CC Plaza San Miguel), San Miguel, Lima','016128000','contacto@chinawok.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(4,'D\'Onofrio HeladerÃ­a','Jr. de la UniÃ³n 500, Cercado de Lima, Lima','014260000','helados@donofrio.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(5,'Juicy Lucy','Av. Mariscal La Mar 1328, Miraflores, Lima','014411234','info@juicylucy.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(6,'La Caravana','Av. Canaval y Moreyra 501, San Isidro, Lima','014413030','informes@lacaravana.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(7,'La Lucha SangucherÃ­a Criolla','Av. Diagonal 308, Miraflores, Lima','014421112','diagonal@lalucha.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(8,'Norky\'s','Av. Abancay 601, Cercado de Lima, Lima','014284444','contacto@norkys.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(9,'Papacho\'s','MalecÃ³n de la Reserva 610 (CC Larcomar), Miraflores, Lima','014467000','larcomar@papachos.com','PRO','ACTIVO',NULL,NULL,NULL),(10,'Pardos Chicken','Av. Elmer Faucett s/n (Aeropuerto Internacional Jorge ChÃ¡vez), Callao','015173100','aeropuerto@pardoschicken.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(11,'Pasquale Hermanos','Av. Alfredo Mendiola 3698 (CC MegaPlaza), Los Olivos, Lima','015116000','contacto@pasquale.com.pe','BASICO','ACTIVO',NULL,NULL,NULL),(12,'Roky\'s','Av. Angamos Este 1502, Surquillo, Lima','016135000','servicio@rokys.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(13,'SÃ¡ndwiches Monstruos','Av. de la AviaciÃ³n 3005, San Borja, Lima','014761022','contacto@monstruos.com.pe','BASICO','ACTIVO',NULL,NULL,NULL),(14,'SangucherÃ­a El Chinito','Jr. Chancay 894, Cercado de Lima, Lima','014232190','pedidos@elchinito.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(15,'Siete Sopas','Av. Angamos Este 609, Surquillo, Lima','012136000','informes@sietesopas.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(16,'Tip Top','Av. Gral. Juan Antonio Ãlvarez de Arenales 2499, Lince, Lima','014713131','ventas@tiptop.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL);
/*!40000 ALTER TABLE `restaurantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajadores`
--

DROP TABLE IF EXISTS `trabajadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trabajadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `restaurante_id` bigint unsigned NOT NULL,
  `local_id` bigint unsigned DEFAULT NULL,
  `rol` enum('SUPER_ADMIN','ADMIN','GERENTE','CAJA','DESPACHO','COCINA','POS') NOT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trabajador_user_local` (`user_id`,`restaurante_id`,`local_id`),
  KEY `idx_trabajadores_local` (`local_id`),
  KEY `idx_trabajadores_restaurante` (`restaurante_id`),
  KEY `idx_trabajadores_rol` (`rol`),
  CONSTRAINT `fk_trabajadores_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_trabajadores_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_trabajadores_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajadores`
--

LOCK TABLES `trabajadores` WRITE;
/*!40000 ALTER TABLE `trabajadores` DISABLE KEYS */;
INSERT INTO `trabajadores` VALUES (1,2,7,1,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:17','2026-10-01 16:30:17',NULL),(2,3,7,2,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:17','2026-10-01 16:30:17',NULL),(3,4,14,3,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:17','2026-10-01 16:30:17',NULL),(4,5,14,4,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:17','2026-10-01 16:30:17',NULL),(5,6,16,5,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:18','2026-10-01 16:30:18',NULL),(6,7,10,6,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:18','2026-10-01 16:30:18',NULL),(7,8,2,7,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:18','2026-10-01 16:30:18',NULL),(8,9,2,8,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:18','2026-10-01 16:30:18',NULL),(9,10,3,9,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:18','2026-10-01 16:30:18',NULL),(10,11,8,10,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:18','2026-10-01 16:30:18',NULL),(11,12,12,11,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:19','2026-10-01 16:30:19',NULL),(12,13,15,12,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:19','2026-10-01 16:30:19',NULL),(13,14,15,13,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:19','2026-10-01 16:30:19',NULL),(14,15,13,14,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:19','2026-10-01 16:30:19',NULL),(15,16,5,15,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:20','2026-10-01 16:30:20',NULL),(16,17,9,16,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:20','2026-10-01 16:30:20',NULL),(17,18,11,17,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:20','2026-10-01 16:30:20',NULL),(18,19,4,18,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:20','2026-10-01 16:30:20',NULL),(19,20,6,19,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:20','2026-10-01 16:30:20',NULL),(20,21,1,20,'POS','POS',NULL,NULL,1,'2026-10-01 16:30:21','2026-10-01 16:30:21',NULL),(21,22,1,20,'ADMIN',NULL,NULL,NULL,1,'2026-10-01 16:32:15','2026-10-01 16:32:15',NULL),(22,23,1,20,'GERENTE',NULL,NULL,NULL,1,'2026-10-01 16:32:33','2026-10-01 16:32:33',NULL),(23,24,1,20,'CAJA',NULL,NULL,NULL,1,'2026-10-01 16:32:51','2026-10-01 16:32:51',NULL),(24,25,1,20,'DESPACHO',NULL,NULL,NULL,1,'2026-10-01 16:33:12','2026-10-01 16:33:12',NULL),(25,26,1,20,'COCINA',NULL,NULL,NULL,1,'2026-10-01 16:33:31','2026-10-01 16:33:31',NULL);
/*!40000 ALTER TABLE `trabajadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@integrador1.com','2026-10-01 16:16:23','$2y$12$Fbkk43msTM4n06stca4kgeJcIKLwV0IdjzirS04U.vCMeI0VzQ6vi',0,NULL,'2026-10-01 16:16:24','2026-10-01 16:16:24'),(2,'POS  1 Larco','pos.1@local.api','2026-10-01 16:30:17','$2y$12$uDvuDIvsuG1BVjx6wCO66.aoPJTTorqxKORAeD0X2yHDtf.GO6V1i',0,NULL,'2026-10-01 16:30:17','2026-10-01 16:30:17'),(3,'POS  1 Diagonal','pos.2@local.api','2026-10-01 16:30:17','$2y$12$0WNmMgBDAHots.hntL28qOPsBHVx1/2IQuh1ACO6mYk29tOJoJczu',0,NULL,'2026-10-01 16:30:17','2026-10-01 16:30:17'),(4,'POS  1 Centro','pos.3@local.api','2026-10-01 16:30:17','$2y$12$TAq2SEWF7dbMmla3T63SEOTpWrR8s344tcy/Aog/KS7FMMVqqM/2m',0,NULL,'2026-10-01 16:30:17','2026-10-01 16:30:17'),(5,'POS  1 Surco','pos.4@local.api','2026-10-01 16:30:17','$2y$12$7kyOe.dZJ0CJEazR5cfLnOkdZ/.qXTwikNWKVkgD63pgC0BRvc8z2',0,NULL,'2026-10-01 16:30:17','2026-10-01 16:30:17'),(6,'POS  1 Lince','pos.5@local.api','2026-10-01 16:30:18','$2y$12$mfbYs1IQGtZldzyQvntZXurqCHUVdt9kBfWCWXXf1ApYzGrnVmLHS',0,NULL,'2026-10-01 16:30:18','2026-10-01 16:30:18'),(7,'POS  1 Aeropuerto','pos.6@local.api','2026-10-01 16:30:18','$2y$12$ppDQrdavOa7E9.jC/IClIeADW3pmr9BJ0IkU35i1dGutyiuimE1AO',0,NULL,'2026-10-01 16:30:18','2026-10-01 16:30:18'),(8,'POS  1 Larco','pos.7@local.api','2026-10-01 16:30:18','$2y$12$4ZakLFC875O/aT2HujZ/6eHmw9jNEZ7RZAq2okPiRwMx1jY3VUYTm',0,NULL,'2026-10-01 16:30:18','2026-10-01 16:30:18'),(9,'POS  1 Jockey Plaza','pos.8@local.api','2026-10-01 16:30:18','$2y$12$6/BiOhvjgAYDB8idCbWoYe7Uf4TKwfTOjTQ7nMg8qjCdWL/E3NKWi',0,NULL,'2026-10-01 16:30:18','2026-10-01 16:30:18'),(10,'POS  1 Plaza San Miguel','pos.9@local.api','2026-10-01 16:30:18','$2y$12$NvHOv2uM5pR11zKrgWX0BeeH6VEZI.0NL4Z5Y9h/tbNY0U9q5nl9W',0,NULL,'2026-10-01 16:30:18','2026-10-01 16:30:18'),(11,'POS  1 Centro','pos.10@local.api','2026-10-01 16:30:18','$2y$12$aU.iZFxAckLwN9SVYG7vC.L/W1WR7FTVkFZx9Qes4UN9/taRikgLO',0,NULL,'2026-10-01 16:30:18','2026-10-01 16:30:18'),(12,'POS  1 Angamos','pos.11@local.api','2026-10-01 16:30:19','$2y$12$.DIsc1eJ9D8yZtZr2V8qou0vdhcgkDOyXAiBo/Kb8srtVcjM6Kksu',0,NULL,'2026-10-01 16:30:19','2026-10-01 16:30:19'),(13,'POS  1 Angamos','pos.12@local.api','2026-10-01 16:30:19','$2y$12$isrm4yIp9p5c/6Z8cjD5JOH4hNiNJC52PiZNGd1aqnEB3KkHqctpy',0,NULL,'2026-10-01 16:30:19','2026-10-01 16:30:19'),(14,'POS  1 Lince','pos.13@local.api','2026-10-01 16:30:19','$2y$12$FyjXoOxLq34zaO8RewFY/uI/vT7pcWNKM7GCNmlXcnpdBHhb9isE.',0,NULL,'2026-10-01 16:30:19','2026-10-01 16:30:19'),(15,'POS  1 Barranco','pos.14@local.api','2026-10-01 16:30:19','$2y$12$SsjEkU8fkfaoZFLDK6d9seSEUDv7KsYstzD/mir.Nq1CGkugpRqHy',0,NULL,'2026-10-01 16:30:19','2026-10-01 16:30:19'),(16,'POS  1 La Mar','pos.15@local.api','2026-10-01 16:30:20','$2y$12$2DtXs6lMyRsOHFRpruhUN.adi7RNwu2wHaL3lfEcLv37PVvEoneQ6',0,NULL,'2026-10-01 16:30:20','2026-10-01 16:30:20'),(17,'POS  1 Larcomar','pos.16@local.api','2026-10-01 16:30:20','$2y$12$q2uf52GYVPedMWDaqSPRueYlZo1Tdj3sVA3rm8.bQhZRBKbyCiGu2',0,NULL,'2026-10-01 16:30:20','2026-10-01 16:30:20'),(18,'POS  1 Megaplaza','pos.17@local.api','2026-10-01 16:30:20','$2y$12$lO7/gYRDoHt1J0LZrnlp1.WLRU1ZE/gFSczwetB3rx.L19eDfBhNC',0,NULL,'2026-10-01 16:30:20','2026-10-01 16:30:20'),(19,'POS  1 Centro','pos.18@local.api','2026-10-01 16:30:20','$2y$12$AbLEPt1X7L7LTOIO8covzODfNqsfdfBoGpa4mifnkANUeeGCklLEW',0,NULL,'2026-10-01 16:30:20','2026-10-01 16:30:20'),(20,'POS  1 Canaval y Moreyra','pos.19@local.api','2026-10-01 16:30:20','$2y$12$d7zWs9552R1KKxQE6uDE/OgLlzrlw9Tc/bTMxcq4tzikNV6LPl6ae',0,NULL,'2026-10-01 16:30:20','2026-10-01 16:30:20'),(21,'POS  1 Luna Pizarro','pos.20@local.api','2026-10-01 16:30:21','$2y$12$qtEkMfJUUCr.cR3dpBWEjO1HSUKVQhIsIj9l.I5093AiL0A/qGAOC',0,NULL,'2026-10-01 16:30:21','2026-10-01 16:30:21'),(22,'admin-begui-lp@prueba.com','admin-begui-lp@prueba.com','2026-10-01 16:32:15','$2y$12$axElQ8zmMmbNh.IQkkQ8E.LlmVFwpQjdoW/FIvOfRTMZoCTpzOCDS',0,NULL,'2026-10-01 16:32:15','2026-10-01 16:32:15'),(23,'gerente-begui-lp@prueba.com','gerente-begui-lp@prueba.com','2026-10-01 16:32:33','$2y$12$J9dLi8UMlUj3xIy5T2oXBOVPSh9QASFUrPmjMjnLK3tE2y3BkDu7G',0,NULL,'2026-10-01 16:32:33','2026-10-01 16:32:33'),(24,'caja-begui-lp@prueba.com','caja-begui-lp@prueba.com','2026-10-01 16:32:51','$2y$12$g5ByPW.pqyjTHMViwMViQ.jzFjpc/Ai1qMmY6VaBGuvlDJwk1jjmW',0,NULL,'2026-10-01 16:32:51','2026-10-01 16:32:51'),(25,'despacho-begui-lp@prueba.com','despacho-begui-lp@prueba.com','2026-10-01 16:33:12','$2y$12$Q3IFvl37E.rSPkZnZhPMHO4ypGJJH6NJBDoKtno7cuPyn0uENs5Tq',0,NULL,'2026-10-01 16:33:12','2026-10-01 16:33:12'),(26,'cocina-begui-lp@prueba.com','cocina-begui-lp@prueba.com','2026-10-01 16:33:31','$2y$12$p7pHfBqKZxBv2Ldw9DrQK.bvvDpP2OoYqD99eSpkT.mkOY5LnbHPm',0,NULL,'2026-10-01 16:33:31','2026-10-01 16:33:31');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'UtpIntegradorBuzzer'
--

--
-- Dumping routines for database 'UtpIntegradorBuzzer'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-01 12:36:21
