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
  `producto_id` bigint unsigned NOT NULL,
  `cantidad` int unsigned NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `instrucciones_especiales` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_detalle_pedido` (`pedido_id`),
  KEY `idx_detalle_producto` (`producto_id`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos`
--

LOCK TABLES `detalle_pedidos` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formularios`
--

LOCK TABLES `formularios` WRITE;
/*!40000 ALTER TABLE `formularios` DISABLE KEYS */;
INSERT INTO `formularios` VALUES (1,'Dashboard','DashboardController'),(2,'Pedidos Caja','CajaController'),(3,'Pedidos Cocina','CocinaController'),(4,'Pedidos Despacho','DespachoController'),(5,'Reporte','ReporteController'),(6,'Trabajadores','TrabajadoresController'),(7,'Empresa Contrata','RestaurantController'),(8,'Locales','LocalesController');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_estados`
--

LOCK TABLES `historial_estados` WRITE;
/*!40000 ALTER TABLE `historial_estados` DISABLE KEYS */;
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
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_locales_restaurante` (`restaurante_id`),
  CONSTRAINT `fk_locales_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locales`
--

LOCK TABLES `locales` WRITE;
/*!40000 ALTER TABLE `locales` DISABLE KEYS */;
INSERT INTO `locales` VALUES (1,7,'Larco','Av. José Larco 999, Miraflores, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(2,7,'Diagonal','Av. Diagonal 308, Miraflores, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(3,14,'Centro','Jr. Chancay 894, Cercado de Lima, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(4,14,'Surco','Av. Caminos del Inca 1151, Santiago de Surco, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(5,16,'Lince','Av. Gral. Juan Antonio Álvarez de Arenales 2499, Lince, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(6,10,'Aeropuerto','Av. Elmer Faucett s/n (Aeropuerto Internacional Jorge Chávez), Callao','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(7,2,'Larco','Av. José Larco 401, Miraflores, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(8,2,'Jockey Plaza','Av. Javier Prado Este 4200 (CC Jockey Plaza), Santiago de Surco, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(9,3,'Plaza San Miguel','Av. Universitaria 2000 (CC Plaza San Miguel), San Miguel, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(10,8,'Centro','Av. Abancay 601, Cercado de Lima, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(11,12,'Angamos','Av. Angamos Este 1502, Surquillo, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(12,15,'Angamos','Av. Angamos Este 609, Surquillo, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(13,15,'Lince','Av. Arequipa 2394, Lince, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(14,13,'Barranco','Av. de la Aviación 3005, San Borja, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(15,5,'La Mar','Av. Mariscal La Mar 1328, Miraflores, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(16,9,'Larcomar','Malecón de la Reserva 610 (CC Larcomar), Miraflores, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(17,11,'Megaplaza','Av. Alfredo Mendiola 3698 (CC MegaPlaza), Los Olivos, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(18,4,'Centro','Jr. de la Unión 500, Cercado de Lima, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(19,6,'Canaval y Moreyra','Av. Canaval y Moreyra 501, San Isidro, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL),(20,1,'Luna Pizarro','Av. Luna Pizarro 415, La Victoria, Lima','','ACTIVO','2026-09-24 15:18:59','2026-09-24 15:18:59',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_22_150000_add_must_change_password_to_users_table',1),(5,'2026_09_23_000000_add_imagen_url_to_trabajadores_table',2);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
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
  KEY `idx_pedidos_local_estado` (`local_id`,`estado`),
  KEY `idx_pedidos_fecha` (`fecha_pedido`),
  KEY `idx_pedidos_cliente` (`cliente_id`),
  KEY `fk_pedidos_trabajador_caja` (`trabajador_caja_id`),
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_trabajador_caja` FOREIGN KEY (`trabajador_caja_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfilAccesos`
--

LOCK TABLES `perfilAccesos` WRITE;
/*!40000 ALTER TABLE `perfilAccesos` DISABLE KEYS */;
INSERT INTO `perfilAccesos` VALUES (1,'SUPER_ADMIN'),(2,'ADMIN_REST'),(3,'GERENTE_LOCAL'),(4,'CAJA'),(5,'COCINA'),(6,'DESPACHO');
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
) ENGINE=InnoDB AUTO_INCREMENT=499 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfilesFormularios`
--

LOCK TABLES `perfilesFormularios` WRITE;
/*!40000 ALTER TABLE `perfilesFormularios` DISABLE KEYS */;
INSERT INTO `perfilesFormularios` VALUES (475,1,1),(476,1,5),(477,1,7),(478,1,8),(482,2,1),(483,2,8),(484,2,5),(485,2,6),(489,3,1),(490,3,2),(491,3,3),(492,3,4),(493,3,5),(494,3,6),(496,4,2),(497,5,3),(498,6,4);
/*!40000 ALTER TABLE `perfilesFormularios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `local_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `url_imagen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_productos_local` (`local_id`),
  KEY `idx_productos_categoria` (`categoria`),
  CONSTRAINT `fk_productos_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
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
INSERT INTO `restaurantes` VALUES (1,'Begui','Av. Luna Pizarro 415, La Victoria, Lima','014721050','contacto@begui.com.pe','BASICO','ACTIVO',NULL,NULL,NULL),(2,'Bembos','Av. Javier Prado Este 4200 (CC Jockey Plaza), Santiago de Surco, Lima','014191920','jockey@bembos.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(3,'China Wok','Av. Universitaria 2000 (CC Plaza San Miguel), San Miguel, Lima','016128000','contacto@chinawok.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(4,'D\'Onofrio Heladería','Jr. de la Unión 500, Cercado de Lima, Lima','014260000','helados@donofrio.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(5,'Juicy Lucy','Av. Mariscal La Mar 1328, Miraflores, Lima','014411234','info@juicylucy.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(6,'La Caravana','Av. Canaval y Moreyra 501, San Isidro, Lima','014413030','informes@lacaravana.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(7,'La Lucha Sanguchería Criolla','Av. Diagonal 308, Miraflores, Lima','014421112','diagonal@lalucha.com.pe','PRO','ACTIVO',NULL,NULL,NULL),(8,'Norky\'s','Av. Abancay 601, Cercado de Lima, Lima','014284444','contacto@norkys.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(9,'Papacho\'s','Malecón de la Reserva 610 (CC Larcomar), Miraflores, Lima','014467000','larcomar@papachos.com','PRO','ACTIVO',NULL,NULL,NULL),(10,'Pardos Chicken','Av. Elmer Faucett s/n (Aeropuerto Internacional Jorge Chávez), Callao','015173100','aeropuerto@pardoschicken.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(11,'Pasquale Hermanos','Av. Alfredo Mendiola 3698 (CC MegaPlaza), Los Olivos, Lima','015116000','contacto@pasquale.com.pe','BASICO','ACTIVO',NULL,NULL,NULL),(12,'Roky\'s','Av. Angamos Este 1502, Surquillo, Lima','016135000','servicio@rokys.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(13,'Sándwiches Monstruos','Av. de la Aviación 3005, San Borja, Lima','014761022','contacto@monstruos.com.pe','BASICO','ACTIVO',NULL,NULL,NULL),(14,'Sanguchería El Chinito','Jr. Chancay 894, Cercado de Lima, Lima','014232190','pedidos@elchinito.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(15,'Siete Sopas','Av. Angamos Este 609, Surquillo, Lima','012136000','informes@sietesopas.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL),(16,'Tip Top','Av. Gral. Juan Antonio Álvarez de Arenales 2499, Lince, Lima','014713131','ventas@tiptop.com.pe','ENTERPRISE','ACTIVO',NULL,NULL,NULL);
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
  `rol` enum('SUPER_ADMIN','ADMIN_REST','GERENTE_LOCAL','CAJA','COCINA','DESPACHO') NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajadores`
--

LOCK TABLES `trabajadores` WRITE;
/*!40000 ALTER TABLE `trabajadores` DISABLE KEYS */;
INSERT INTO `trabajadores` VALUES (1,2,2,10,'CAJA','pppp','984318804','/storage/trabajadores/XiV0WsviDHRitaimOcWVfxC1vvMfV1bAV24qD1QM.jpg',1,'2026-09-24 15:34:22','2026-09-24 15:34:22',NULL),(2,3,12,NULL,'ADMIN_REST','pppp','4646',NULL,1,'2026-09-25 15:29:14','2026-09-25 15:29:14',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@integrador1.com','2026-09-24 15:20:53','$2y$12$EmosjNAphX.V2LscDHNdIuXnAYeQlO4KJXFwRcQGx9bhNo4pTNjKe',0,NULL,'2026-09-24 15:20:53','2026-09-24 15:20:53'),(2,'caja','caja@prueba.com','2026-09-24 15:34:22','$2y$12$.yQvm6rKcyJHWY2P0GY5PujFaqlQl3KjHX0i/lpuc8wex89CEu4We',0,NULL,'2026-09-24 15:34:22','2026-09-24 15:34:22'),(3,'carlos','carlos@prueba','2026-09-25 15:29:14','$2y$12$eV2mEtf28thJKSznVOn6Be3qqDOwS4d9Ie1MEeKZBkJeX2Dzjyzaq',0,NULL,'2026-09-25 15:29:14','2026-09-25 15:29:14');
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

-- Dump completed on 2026-09-25 13:10:09
